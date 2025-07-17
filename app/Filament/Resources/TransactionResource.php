<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('supplier_id')
                            ->label('Pedagang')
                            ->relationship('supplier', 'name')
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('phone_number')
                                    ->label('No. Telp')
                                    ->tel()
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('address')
                                    ->label('Alamat')
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                return $action
                                    ->modalHeading('Tambah Pedagang')
                                    ->modalWidth(\Filament\Support\Enums\MaxWidth::ThreeExtraLarge);
                            })
                            ->hidden(!auth()->user()->hasRole('operator')),

                        Forms\Components\Select::make('fish_id')
                            ->label('Jenis Ikan')
                            ->relationship('fish', 'name')
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('description')
                                    ->columnSpanFull(),
                            ])
                            ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                return $action
                                    ->modalHeading('Tambah Ikan')
                                    ->modalWidth(\Filament\Support\Enums\MaxWidth::ThreeExtraLarge);
                            })
                            ->hidden(!auth()->user()->hasRole('operator')),

                        Forms\Components\TextInput::make('origin')
                            ->label('Sumber')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah (Kg)')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->live(onBlur: true) // Memicu update saat field ini berubah dan kehilangan fokus
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                // Ambil nilai quantity dan price_per_unit
                                $quantity = (float) $get('quantity');
                                $pricePerUnit = (float) $get('price_per_unit');

                                // Hitung total_price
                                $totalPrice = $quantity * $pricePerUnit;

                                // Set nilai total_price
                                $set('total_price', $totalPrice);
                            }),

                        Forms\Components\TextInput::make('price_per_unit')
                            ->label('Harga per Kg')
                            ->prefix('Rp')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->live(onBlur: true) // Memicu update saat field ini berubah dan kehilangan fokus
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                // Ambil nilai quantity dan price_per_unit
                                $quantity = (float) $get('quantity');
                                $pricePerUnit = (float) $get('price_per_unit');

                                // Hitung total_price
                                $totalPrice = $quantity * $pricePerUnit;

                                // Set nilai total_price
                                $set('total_price', $totalPrice);
                            }),

                        Forms\Components\TextInput::make('total_price')
                            ->label('Total Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(0.00),

                        Forms\Components\DateTimePicker::make('entry_datetime')
                            ->label('Waktu Masuk')
                            ->default(now())
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entry_datetime')
                    ->label('Waktu masuk')
                    ->dateTime('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Nama Pedagang')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fish.name')
                    ->label('Jenis Ikan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('origin')
                    ->label('Sumber')
                    ->searchable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah (Kg)')
                    ->numeric()
                    ->suffix('Kg')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price_per_unit')
                    ->label('Harga per Kg')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
