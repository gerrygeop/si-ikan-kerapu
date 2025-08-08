<?php

namespace App\Filament\Exports;

use App\Models\Transaction;
use Egulias\EmailValidator\Result\Reason\LabelTooLong;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;


class TransactionExporter extends Exporter
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),

            ExportColumn::make('user.name')
                ->label('Diinput Oleh'),

            ExportColumn::make('supplier.name')
                ->label('Nama Pemasok'),

            ExportColumn::make('fish.name')
                ->label('Jenis Ikan'),

            ExportColumn::make('origin')
                ->label('Asal Ikan'),

            ExportColumn::make('quantity')
                ->label('Jumlah (Kg)'),

            ExportColumn::make('price_per_unit')
                ->label('Harga per Kg'),

            ExportColumn::make('total_price')
                ->label('Total Harga'),

            ExportColumn::make('entry_datetime')
                ->label('Waktu Masuk'),

            ExportColumn::make('notes')
                ->label('Catatan'),

            ExportColumn::make('created_at')
                ->enabledByDefault(false),
            ExportColumn::make('updated_at')
                ->enabledByDefault(false),
            ExportColumn::make('deleted_at')
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your transaction export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    public function getXlsxHeaderCellStyle(): ?Style
    {
        return (new Style())
            ->setFontBold()
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
    }
}
