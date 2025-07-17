<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ManagerDashboardStats extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user() && auth()->user()->hasRole(['manajer', 'operator']);
    }

    protected function getStats(): array
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

        // Data Bulan Ini
        $monthTransactions = Transaction::whereBetween('entry_datetime', [$thisMonth, Carbon::now()->endOfMonth()]);
        $totalQuantityMonth = $monthTransactions->sum('quantity');
        $totalValueMonth = $monthTransactions->sum('total_price');

        // Data Tahun Ini
        $yearTransactions = Transaction::whereBetween('entry_datetime', [$thisYear, Carbon::now()->endOfYear()]);
        $totalQuantityYear = $yearTransactions->sum('quantity');
        $totalValueYear = $yearTransactions->sum('total_price');

        // Rata-rata Harga (bisa dihitung dari data yang lebih banyak/periodik)
        $avgPricePerKg = Transaction::avg('price_per_unit'); // Rata-rata harga keseluruhan

        return [
            Stat::make('Total Kerapu Bulan Ini', number_format($totalQuantityMonth, 2) . ' Kg')
                ->description('Jumlah pemasukan bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Kerapu Tahun Ini', number_format($totalQuantityYear, 2) . ' Kg')
                ->description('Total pemasukan tahun ini')
                ->color('primary'),

            Stat::make('Nilai Pemasukan Bulan Ini', 'Rp ' . number_format($totalValueMonth, 0, ',', '.'))
                ->description('Estimasi nilai jual bulan ini')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),

            Stat::make('Rata-rata Harga Jual (Kg)', 'Rp ' . number_format($avgPricePerKg, 0, ',', '.'))
                ->description('Rata-rata harga keseluruhan')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),

            Stat::make('Nilai Pemasukan Tahun Ini', 'Rp ' . number_format($totalValueYear, 0, ',', '.'))
                ->description('Total nilai penjualan tahun ini')
                ->color('secondary'),
        ];
    }
}
