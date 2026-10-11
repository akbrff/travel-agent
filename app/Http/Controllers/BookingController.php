<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\PackageSchedule;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $passengerCount = (int) $request->input('passengers', 0);

        $request->validate([
            'package_id' => 'required|exists:travel_packages,id',
            'schedule_id' => 'required|exists:package_schedules,id',
            'passengers' => 'required|integer|min:1|max:50',
            'passengers_data' => 'required|array|size:' . $passengerCount,
            'passengers_data.*.name' => 'required|string',
            'passengers_data.*.id_number' => 'required|string',
            'passengers_data.*.phone' => 'nullable|string',
            'addons' => 'nullable|array',
            'addons.*' => 'integer|in:150000,250000',
        ]);

        return DB::transaction(function () use ($request) {
            $schedule = PackageSchedule::where('id', $request->schedule_id)
                ->where('travel_package_id', $request->package_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($schedule->remaining_quota < $request->passengers) {
                return back()->with(
                    'error',
                    'Maaf, kuota tersisa hanya ' .
                    $schedule->remaining_quota . ' pax.'
                );
            }

            $basePrice = $schedule->price_per_person * $request->passengers;

            $addonTotal = 0;

            if ($request->has('addons')) {
                foreach ($request->addons as $addonCost) {
                    $addonTotal += (int) $addonCost;
                }
            }

            $totalAmount = $basePrice + $addonTotal;

            $booking = Booking::create([
                'booking_code' => 'BK-' . strtoupper(Str::random(8)),
                'user_id' => auth()->id(),
                'package_schedule_id' => $schedule->id,
                'total_passengers' => $request->passengers,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            $schedule->decrement(
                'remaining_quota',
                $request->passengers
            );

            foreach ($request->passengers_data as $pData) {
                BookingPassenger::create([
                    'booking_id' => $booking->id,
                    'name' => $pData['name'],
                    'id_number' => $pData['id_number'],
                    'phone' => $pData['phone'] ?? null,
                ]);
            }

            return redirect()
                ->route('booking.checkout', $booking->booking_code)
                ->with(
                    'success',
                    'Pemesanan berhasil dibuat! Silakan lakukan pembayaran.'
                );
        });
    }

    public function checkout($booking_code)
    {
        $booking = Booking::with([
            'packageSchedule.travelPackage',
            'user',
            'passengers',
        ])
            ->where('booking_code', $booking_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('booking.checkout', compact('booking'));
    }

    public function pay(Request $request, $booking_code)
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:100',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $booking = Booking::where('booking_code', $booking_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Simpan file baru terlebih dahulu.
        $newImagePath = $request->file('proof_image')
            ->store('payment-proofs', 'public');

        $oldImagePath = null;

        try {
            DB::transaction(function () use (
                $booking,
                $request,
                $newImagePath,
                &$oldImagePath
            ) {
                // Kunci booking agar proses pembayaran tidak bertabrakan.
                $lockedBooking = Booking::where('id', $booking->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Pembayaran hanya dapat dikirim untuk status ini.
                if (!in_array(
                    $lockedBooking->status,
                    ['pending', 'cancelled'],
                    true
                )) {
                    throw new \RuntimeException(
                        'Booking tidak dapat menerima pembayaran pada status saat ini.'
                    );
                }

                // Jika booking sebelumnya dibatalkan admin,
                // periksa dan ambil kembali kuotanya.
                if ($lockedBooking->status === 'cancelled') {
                    $schedule = PackageSchedule::where(
                        'id',
                        $lockedBooking->package_schedule_id
                    )
                        ->lockForUpdate()
                        ->firstOrFail();

                    if (
                        $schedule->remaining_quota <
                        $lockedBooking->total_passengers
                    ) {
                        throw new \RuntimeException(
                            'Kuota keberangkatan tidak mencukupi. Silakan hubungi admin.'
                        );
                    }

                    $schedule->decrement(
                        'remaining_quota',
                        $lockedBooking->total_passengers
                    );
                }

                // Cari pembayaran lama berdasarkan booking_id.
                // Jika ada, perbarui record tersebut.
                $payment = Payment::where(
                    'booking_id',
                    $lockedBooking->id
                )
                    ->lockForUpdate()
                    ->first();

                if ($payment) {
                    $oldImagePath = $payment->proof_image;

                    $payment->update([
                        'bank_name' => $request->bank_name,
                        'account_name' => $request->account_name,
                        'proof_image' => $newImagePath,
                        'amount' => $lockedBooking->total_amount,
                    ]);
                } else {
                    Payment::create([
                        'booking_id' => $lockedBooking->id,
                        'bank_name' => $request->bank_name,
                        'account_name' => $request->account_name,
                        'proof_image' => $newImagePath,
                        'amount' => $lockedBooking->total_amount,
                    ]);
                }

                // Tunggu verifikasi admin.
                $lockedBooking->update([
                    'status' => 'waiting_verification',
                ]);
            });
        } catch (\Throwable $e) {
            // Jika proses gagal, hapus file baru agar tidak menumpuk.
            Storage::disk('public')->delete($newImagePath);

            if ($e instanceof \RuntimeException) {
                return back()->with('error', $e->getMessage());
            }

            report($e);

            return back()->with(
                'error',
                'Bukti pembayaran gagal disimpan. Silakan coba kembali.'
            );
        }

        // Hapus bukti lama hanya setelah transaksi berhasil.
        if (
            $oldImagePath &&
            $oldImagePath !== $newImagePath
        ) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()
            ->route('booking.checkout', $booking->booking_code)
            ->with(
                'success',
                'Bukti pembayaran berhasil diunggah! Mohon tunggu verifikasi admin.'
            );
    }

    public function history()
    {
        $bookings = Booking::with([
            'packageSchedule.travelPackage',
        ])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('booking.history', compact('bookings'));
    }

    public function downloadTicket($booking_code)
    {
        $booking = Booking::with([
            'packageSchedule.travelPackage',
            'user',
            'passengers',
        ])
            ->where('booking_code', $booking_code)
            ->where('user_id', auth()->id())
            ->where('status', 'paid')
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.eticket', compact('booking'))
            ->setPaper('a4', 'portrait');

        return $pdf->download(
            'E-Ticket-' . $booking->booking_code . '.pdf'
        );
    }
}
