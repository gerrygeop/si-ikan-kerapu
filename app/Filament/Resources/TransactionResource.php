<?php

namespace App\Filament\Resources;

use App\Filament\Exports\TransactionExporter;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
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
    protected static ?int $navigationSort = 1;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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
                            ->label('Catatan')
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
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Nama Pedagang')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fish.name')
                    ->label('Jenis Ikan')
                    ->searchable()
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
                Tables\Filters\Filter::make('entry_datetime')
                    ->label('Tanggal masuk')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->placeholder(fn($state): string => 'Jan 01, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->placeholder(fn($state): string => now()->format('M d Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('entry_datetime', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('entry_datetime', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari_tanggal'] ?? null) {
                            $indicators['dari_tanggal'] = 'Dari Tanggal ' . Carbon::parse($data['dari_tanggal'])->toFormattedDateString();
                        }
                        if ($data['sampai_tanggal'] ?? null) {
                            $indicators['sampai_tanggal'] = 'Sampai Tanggal ' . Carbon::parse($data['sampai_tanggal'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(TransactionExporter::class)
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->selectable(function (Builder $query) {
                if (auth()->user()->hasRole(['operator', 'admin'])) {
                    return true;
                }
                return false;
                // return $query->where('user_id', auth()->id());
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                Infolists\Components\Section::make('Transaksi')
                    ->schema([
                        Infolists\Components\TextEntry::make('origin')
                            ->label('Sumber ikan'),
                        Infolists\Components\TextEntry::make('quantity')
                            ->label('Jumlah (Kg)')
                            ->suffix(' Kg'),
                        Infolists\Components\TextEntry::make('price_per_unit')
                            ->label('Harga per Kg')
                            ->money('IDR', locale: 'id')
                            ->badge(),
                        Infolists\Components\TextEntry::make('total_price')
                            ->label('Total Harga')
                            ->money('IDR', locale: 'id')
                            ->badge(),
                        Infolists\Components\TextEntry::make('entry_datetime')
                            ->label('Tanggal Masuk')
                            ->date()
                            ->icon('heroicon-o-calendar-days'),
                        Infolists\Components\TextEntry::make('entry_datetime')
                            ->label('Waktu Masuk')
                            ->time()
                            ->icon('heroicon-o-clock'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Diinput Oleh'),
                        Infolists\Components\Fieldset::make('Catatan')
                            ->schema([
                                Infolists\Components\TextEntry::make('notes')
                                    ->label('')
                                    ->default('-')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->collapsible()
                    ->columns(2)
                    ->columnSpan(2),

                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\Section::make('Jenis Ikan')
                            ->schema([
                                Infolists\Components\TextEntry::make('fish.name')->label('Jenis Ikan'),
                                Infolists\Components\TextEntry::make('fish.description')
                                    ->label('Keterangan')
                                    ->columnSpanFull(),
                            ])
                            ->collapsible(),

                        Infolists\Components\Section::make('Pemasok')
                            ->schema([
                                Infolists\Components\TextEntry::make('supplier.name')
                                    ->label('Nama Pemasok'),
                                Infolists\Components\TextEntry::make('supplier.phone_number')
                                    ->label('No. Telp'),
                                Infolists\Components\TextEntry::make('supplier.address')
                                    ->label('Alamat')
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->columns(2),

                        Infolists\Components\Section::make([
                            Infolists\Components\TextEntry::make('created_at'),
                            Infolists\Components\TextEntry::make('updated_at'),
                        ]),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewTransaction::class,
            Pages\EditTransaction::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
