<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // use BaseDashboard\Concerns\HasFiltersForm;

    // public function filtersForm(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Section::make()
    //                 ->schema([
    //                     DatePicker::make('startDate')
    //                         ->label('Dari Tanggal')
    //                         ->maxDate(fn(Get $get) => $get('endDate') ?: now()),
    //                     DatePicker::make('endDate')
    //                         ->label('Sampai Tanggal')
    //                         ->minDate(fn(Get $get) => $get('startDate') ?: now())
    //                         ->maxDate(now()),
    //                 ])
    //                 ->columns(2),
    //         ]);
    // }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}
