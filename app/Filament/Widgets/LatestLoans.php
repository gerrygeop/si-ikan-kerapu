<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\LoanResource;
use App\Models\Loan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestLoans extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LoanResource::getEloquentQuery()
            )
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('item.name')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        'Pending' => 'warning',
                        'Dikembalikan' => 'info',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('qty')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('borrowing_date')
                    ->label('Tanggal Peminjaman')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('return_date')
                    ->label('Tanggal Pengembalian')
                    ->date()
                    ->sortable()
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->url(fn (Loan $record): string => LoanResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
