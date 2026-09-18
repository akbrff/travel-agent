<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PackageSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:package_schedules,id',
            'passengers' => 'required|numeric|min:1',
        ]);

        $schedule = PackageSchedule::findOrFail($request->schedule_id);

        if ($schedule->remaining_quota < $request->passengers) {
            return back()->with('error', 'Sisa kuota tidak mencukupi.');
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

        $schedule->decrement('remaining_quota', $request->passengers);

        return redirect()->route('booking.checkout', $booking->booking_code);
    }

    public function checkout($booking_code)
    {
        $booking = Booking::with(['packageSchedule.travelPackage', 'user'])
            ->where('booking_code', $booking_code)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('booking.checkout', compact('booking'));
    }

    public function history()
    {
        $bookings = Booking::with('packageSchedule.travelPackage')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('booking.history', compact('bookings'));
    }
}