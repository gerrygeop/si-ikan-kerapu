<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        // Data Hari Ini
        $todayTransactions = Transaction::whereDate('entry_datetime', $today);
        $totalQuantityToday = $todayTransactions->sum('quantity');
        $totalValueToday = $todayTransactions->sum('total_price');

        // Data Minggu Ini
        $thisWeekTransactions = Transaction::whereBetween('entry_datetime', [$thisWeek, Carbon::now()->endOfWeek()]);
        $totalQuantityThisWeek = $thisWeekTransactions->sum('quantity');
        $totalValueThisWeek = $thisWeekTransactions->sum('total_price');

        // Data Bulan Ini
        $thisMonthTransactions = Transaction::whereBetween('entry_datetime', [$thisMonth, Carbon::now()->endOfMonth()]);
        $totalQuantityThisMonth = $thisMonthTransactions->sum('quantity');
        $totalValueThisMonth = $thisMonthTransactions->sum('total_price');


        return [
            Stat::make('Total Kerapu Hari Ini', number_format($totalQuantityToday, 2) . ' Kg')
                ->description('Jumlah pemasukan kerapu hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Nilai Pemasukan Hari Ini', 'Rp ' . number_format($totalValueToday, 0, ',', '.'))
                ->description('Estimasi nilai jual hari ini')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),

            Stat::make('Total Kerapu Minggu Ini', number_format($totalQuantityThisWeek, 2) . ' Kg')
                ->description('Jumlah pemasukan kerapu 7 hari terakhir')
                ->color('warning'),

            Stat::make('Nilai Pemasukan Bulan Ini', 'Rp ' . number_format($totalValueThisMonth, 0, ',', '.'))
                ->description('Estimasi nilai jual bulan ini')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),

            // Kamu bisa tambahkan lagi Stat lainnya, misalnya:
            // Stat::make('Jumlah Transaksi Hari Ini', $todayTransactions->count() . ' Transaksi')
            //     ->color('gray'),
            // Stat::make('Pemasok Unik Hari Ini', $todayTransactions->distinct('supplier_id')->count() . ' Pemasok')
            //     ->color('danger'),
        ];
    }
}
