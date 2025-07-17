<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopSuppliersChart extends ChartWidget
{
    protected static ?string $heading = 'Top Pemasok (Berdasarkan Kuantitas)';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Transaction::select(
            'suppliers.name as supplier_name',
            DB::raw('SUM(transactions.quantity) as total_quantity')
        )
            ->join('suppliers', 'transactions.supplier_id', '=', 'suppliers.id')
            ->groupBy('supplier_name')
            ->orderBy('total_quantity', 'desc')
            ->limit(5) // Tampilkan 5 pemasok teratas
            ->get();

        $labels = $data->pluck('supplier_name')->toArray();
        $quantities = $data->pluck('total_quantity')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Kuantitas (Kg)',
                    'data' => $quantities,
                    'backgroundColor' => ['#4BC0C0', '#FFCE56', '#FF6384', '#36A2EB', '#9966FF'],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
