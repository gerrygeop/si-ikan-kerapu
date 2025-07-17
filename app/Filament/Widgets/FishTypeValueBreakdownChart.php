<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class FishTypeValueBreakdownChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Nilai Pemasukan Berdasarkan Jenis Kerapu';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $data = Transaction::select(
            'fish.name as fish_name',
            DB::raw('SUM(transactions.total_price) as total_value')
        )
            ->join('fish', 'transactions.fish_id', '=', 'fish.id')
            ->groupBy('fish_name')
            ->orderBy('total_value', 'desc')
            ->get();

        $colors = [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            '#4BC0C0',
            '#9966FF',
            '#FF9F40',
            '#00FFFF',
            '#8A2BE2',
            '#C70039',
            '#FFC300'
        ];

        return [
            'datasets' => [
                [
                    'data' => $data->pluck('total_value')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $data->pluck('fish_name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
