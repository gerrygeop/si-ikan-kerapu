<?php

namespace App\Filament\Resources;

use App\Enums\ItemStatus;
use App\Filament\Resources\ItemResource\Pages;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page as PagesPage;
use Filament\Tables;
use Filament\Tables\Table;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $label = 'Barang';
    protected static ?string $navigationLabel = 'Barang';
    protected static ?string $slug = 'barang';
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Nama barang')
                            ->maxLength(255)
                            ->autofocus()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name'),

                        Forms\Components\TextInput::make('qty')
                            ->label('Jumlah')
                            ->required()
                            ->numeric()
                            ->minValue(0),

                        Forms\Components\ToggleButtons::make('condition')
                            ->label('Kondisi')
                            ->required()
                            ->inline()
                            ->options(ItemStatus::class),

                        Forms\Components\TextInput::make('location')
                            ->label('Lokasi')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->maxLength(65535)
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(['lg' => 2]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama barang')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('qty')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baru' => 'info',
                        'Rusak' => 'danger',
                        'Baik' => 'success',
                        'Perbaikan' => 'info',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make()
                    ->schema([
                        Components\TextEntry::make('name')
                            ->label('Nama barang'),

                        Components\TextEntry::make('category.name')
                            ->label('Kategori'),

                        Components\TextEntry::make('qty')
                            ->label('Jumlah'),

                        Components\TextEntry::make('location')
                            ->label('Lokasi'),

                        Components\TextEntry::make('condition')
                            ->label('Kondisi')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Baru' => 'success',
                                'Rusak' => 'danger',
                                'Baik' => 'warning',
                                'Perbaikan' => 'info',
                            }),

                        Components\TextEntry::make('decription')
                            ->label('Deskripsi')
                            ->columnSpan(2)
                            ->placeholder('-'),
                    ])
                    ->columns(2)

            ]);
    }

    public static function getRecordSubNavigation(PagesPage $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewItem::class,
            Pages\EditItem::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'view' => Pages\ViewItem::route('/{record}'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
