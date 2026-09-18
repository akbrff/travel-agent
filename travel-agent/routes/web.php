<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PackageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Rute Publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::get('/packages/{slug}', [PackageController::class, 'show'])->name('packages.show');

// Rute yang membutuhkan Login (Auth)
Route::middleware(['auth'])->group(function () {
    // Pengalihan dari /dashboard bawaan Laravel ke Halaman Beranda
    Route::get('/dashboard', function () {
        return redirect()->route('home');
    });

    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/checkout/{booking_code}', [BookingController::class, 'checkout'])->name('booking.checkout');
    Route::get('/my-bookings', [BookingController::class, 'history'])->name('booking.history');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php'; // jika menggunakan Laravel Breeze