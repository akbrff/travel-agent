<?php

namespace App\Observers;

use App\Models\Booking;

class BookingObserver
{
    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Cek apakah kolom status berubah dan status terbarunya adalah 'cancelled'
        if ($booking->isDirty('status') && $booking->status === 'cancelled') {
            
            // Pastikan status sebelumnya bukan 'cancelled' agar kuota tidak bertambah dua kali
            if ($booking->getOriginal('status') !== 'cancelled') {
                $schedule = $booking->packageSchedule;
                
                if ($schedule) {
                    $schedule->increment('remaining_quota', $booking->total_passengers);
                }
            }
        }
    }
}