<?php

namespace App\Filament\Resources;

use App\Enums\LoanStatus;
use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Loan;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Components\Actions\Action as InfoAction;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page as PagesPage;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $label = 'Pinjaman';
    protected static ?string $navigationLabel = 'Pinjaman';
    protected static ?string $slug = 'pinjaman';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->required(fn (Page $livewire): bool => $livewire instanceof CreateRecord)
                                    ->minLength(8)
                                    ->same('passwordConfirmation')
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)),

                                Forms\Components\TextInput::make('passwordConfirmation')
                                    ->label('Password Confirmation')
                                    ->password()
                                    ->required(fn (Page $livewire): bool => $livewire instanceof CreateRecord)
                                    ->minLength(8)
                                    ->dehydrated(false),
                            ])
                            ->createOptionAction(function (Action $action) {
                                return $action
                                    ->modalHeading('Create user')
                                    ->modalWidth(\Filament\Support\Enums\MaxWidth::ThreeExtraLarge);
                            })
                            ->hidden(!auth()->user()->hasRole('admin')),

                        Forms\Components\Select::make('item_id')
                            ->relationship('item', 'name')
                            ->label('Barang')
                            ->required(),

                        Forms\Components\DatePicker::make('borrowing_date')
                            ->label('Tanggal peminjaman')
                            ->required(),

                        Forms\Components\DatePicker::make('return_date')
                            ->label('Tanggal Pengembalian')
                            ->required(),

                        Forms\Components\TextInput::make('qty')
                            ->label('Jumlah')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->columns(2)
                    ->columnSpan([
                        'lg' => fn (?Loan $record) => $record === null ? 3 : 2,
                    ]),

                Forms\Components\Section::make()
                    ->schema([
                        // Untuk admin
                        Forms\Components\ToggleButtons::make('status')
                            ->required()
                            ->inline()
                            ->options(LoanStatus::class)
                            ->hidden(!auth()->user()->hasRole('admin')),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Waktu dibuat')
                            ->content(fn (Loan $record): ?string => $record->created_at?->diffForHumans()),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir diperbarui')
                            ->content(fn (Loan $record): ?string => $record->updated_at?->diffForHumans()),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn (?Loan $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('item.name')
                    ->label('Nama barang')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('qty')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('borrowing_date')
                    ->label('Tanggal Peminjaman')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('return_date')
                    ->label('Tanggal Pengembalian')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Disetujui' => 'success',
                        'Ditolak' => 'danger',
                        'Pending' => 'warning',
                        'Dikembalikan' => 'info',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(LoanStatus::class),

                Tables\Filters\TernaryFilter::make('Kepemilikan')
                    ->trueLabel('Punya saya')
                    ->falseLabel('Semua')
                    ->queries(
                        true: fn (Builder $query) => $query->where('user_id', auth()->id()),
                        false: fn (Builder $query) => $query,
                        blank: fn (Builder $query) => $query
                    )
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
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make()
                    ->schema([
                        Components\TextEntry::make('user.name')
                            ->label('Nama pengguna'),

                        Components\TextEntry::make('item.name')
                            ->label('Nama barang'),

                        Components\TextEntry::make('qty')
                            ->label('Jumlah'),

                        Components\TextEntry::make('borrowing_date')
                            ->label('Tanggal peminjaman')
                            ->date(),

                        Components\TextEntry::make('return_date')
                            ->label('Tanggal pengembalian')
                            ->date(),

                        Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Disetujui' => 'success',
                                'Ditolak' => 'danger',
                                'Pending' => 'warning',
                                'Dikembalikan' => 'info',
                            }),

                        Components\Actions::make([
                            Components\Actions\Action::make('Telah dikembalikan')
                                ->color('info')
                                ->action(function (Loan $record) {
                                    $record->status = 'Dikembalikan';
                                    $record->save();
                                })
                                ->visible(function (Loan $record) {
                                    if ($record->status == 'Disetujui' && auth()->id() == $record->user_id) {
                                        return true;
                                    }
                                    return false;
                                }),
                        ])
                            ->columnSpan(2),
                    ])
                    ->columns(2)

            ]);
    }

    public static function getRecordSubNavigation(PagesPage $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewLoan::class,
            Pages\EditLoan::class,
        ]);
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::where('status', 'Pending')->count();
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
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'view' => Pages\ViewLoan::route('/{record}'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }
}
