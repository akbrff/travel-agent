<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\PackageSchedule;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;


class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:package_schedules,id',
            'passengers' => 'required|numeric|min:1',
            'passengers_data' => 'required|array|min:' . $request->passengers,
            'passengers_data.*.name' => 'required|string|max:255',
            'passengers_data.*.id_number' => 'required|string|max:50',
            'passengers_data.*.phone' => 'nullable|string|max:20',
        ]);

        try {
            $booking = DB::transaction(function () use ($request) {
                // Lock baris jadwal di database untuk mencegah race condition / overbooking
                $schedule = PackageSchedule::where('id', $request->schedule_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($schedule->remaining_quota < $request->passengers) {
                    throw new \Exception('Sisa kuota tidak mencukupi untuk jumlah pax yang dipesan.');
                }

                $totalAmount = $schedule->price_per_person * $request->passengers;
                $bookingCode = 'TRV-' . strtoupper(Str::random(6));

                $booking = Booking::create([
                    'booking_code' => $bookingCode,
                    'user_id' => auth()->id(),
                    'package_schedule_id' => $schedule->id,
                    'total_passengers' => $request->passengers,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                ]);

                foreach ($request->passengers_data as $passengerData) {
                    BookingPassenger::create([
                        'booking_id' => $booking->id,
                        'name' => $passengerData['name'],
                        'id_number' => $passengerData['id_number'],
                        'phone' => $passengerData['phone'] ?? null,
                    ]);
                }

                // Kurangi sisa kuota paket
                $schedule->decrement('remaining_quota', $request->passengers);

                return $booking;
            });

            return redirect()->route('booking.checkout', $booking->booking_code);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function checkout($booking_code)
    {
        $booking = Booking::with(['packageSchedule.travelPackage', 'user', 'passengers'])
            ->where('booking_code', $booking_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('booking.checkout', compact('booking'));
    }

    public function pay(Request $request, $booking_code)
    {
        $booking = Booking::where('booking_code', $booking_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:100',
            'proof_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = $request->file('proof_image')->store('payment-proofs', 'public');

        Payment::create([
            'booking_id' => $booking->id,
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'proof_image' => $imagePath,
            'amount' => $booking->total_amount,
        ]);

        $booking->update([
            'status' => 'waiting_verification',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah! Mohon tunggu verifikasi admin.');
    }

    public function history()
    {
        $bookings = Booking::with(['packageSchedule.travelPackage'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('booking.history', compact('bookings'));
    }

    public function downloadTicket($booking_code)
    {
        $booking = Booking::with(['packageSchedule.travelPackage', 'user', 'passengers'])
            ->where('booking_code', $booking_code)
            ->where('user_id', auth()->id())
            ->where('status', 'paid') // Hanya pesanan yang sudah Paid/Lunas
            ->firstOrFail();

        // Load view khusus PDF E-Ticket
        $pdf = Pdf::loadView('pdf.eticket', compact('booking'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('E-Ticket-' . $booking->booking_code . '.pdf');
    }
}