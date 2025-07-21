<?php

namespace App\Filament\Guest\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.guest.pages.dashboard';

    protected static ?string $title = 'Statistik Ikan Kerapu';

    protected static bool $shouldRegisterNavigation = false;

    public static function getSlug(): string
    {
        return ''; // Agar jadi root dashboard panel
    }

    public function getHeaderWidgets(): array
    {
        return [
            // Contoh widget yang ingin kamu tampilkan di header dashboard
            \App\Filament\Widgets\ManagerDashboardStats::class,
            \App\Filament\Guest\Widgets\MonthlyFishPriceTrendChart::class,
            \App\Filament\Widgets\MonthlyQuantityTrendChart::class,
            \App\Filament\Widgets\YearlyQuantityTrendChart::class,

            \App\Filament\Widgets\TopSuppliersChart::class,
            \App\Filament\Widgets\FishTypeValueBreakdownChart::class,
        ];
    }
}
