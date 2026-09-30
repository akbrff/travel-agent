<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    // Menggunakan method getHeading agar fungsi date() dipanggil secara dinamis
    public function getHeading(): ?string
    {
        return 'Grafik Pendapatan Bulanan (' . date('Y') . ')';
    }

    protected function getData(): array
    {
        $monthlyRevenue = Booking::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total')
        )
        ->whereIn('status', ['paid', 'completed'])
        ->whereYear('created_at', date('Y'))
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->pluck('total', 'month')
        ->toArray();

        $data = [];
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($month = 1; $month <= 12; $month++) {
            $data[] = $monthlyRevenue[$month] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}