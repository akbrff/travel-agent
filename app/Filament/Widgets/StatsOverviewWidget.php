<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Total Pendapatan dari status 'paid' dan 'completed'
        $totalRevenue = Booking::whereIn('status', ['paid', 'completed'])->sum('total_amount');
        
        // Total Pemesanan Keseluruhan
        $totalBookings = Booking::count();

        // Pesanan Menunggu Verifikasi
        $pendingVerification = Booking::where('status', 'waiting_verification')->count();

        // Total Pelanggan Terdaftar
        $totalCustomers = User::where('role', 'customer')->count();

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Pendapatan dari transaksi lunas')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Menunggu Verifikasi', $pendingVerification . ' Pesanan')
                ->description('Perlu tindakan verifikasi admin')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingVerification > 0 ? 'warning' : 'gray'),

            Stat::make('Total Pesanan', $totalBookings)
                ->description('Total seluruh transaksi masuk')
                ->color('info'),

            Stat::make('Total Customer', $totalCustomers . ' User')
                ->description('Pengguna terdaftar di platform')
                ->color('primary'),
        ];
    }
}