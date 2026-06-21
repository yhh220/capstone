<?php

namespace App\Filament\Exports;

use App\Filament\Concerns\NotifiesImportExportCompletion;
use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\DatePicker;

class OrderExporter extends Exporter
{
    use NotifiesImportExportCompletion;

    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('order_number')->label('Order Number'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('payment_status')->label('Payment Status'),
            ExportColumn::make('customer_name')->label('Customer Name')
                ->formatStateUsing(fn ($state) => self::sanitizeCsv($state)),
            ExportColumn::make('customer_email')->label('Customer Email'),
            ExportColumn::make('customer_phone')->label('Customer Phone'),
            ExportColumn::make('tracking_number')->label('Tracking Number')
                ->formatStateUsing(fn ($state) => self::sanitizeCsv($state)),
            ExportColumn::make('shipping_address.street')->label('Street')
                ->formatStateUsing(fn ($state) => self::sanitizeCsv($state)),
            ExportColumn::make('shipping_address.city')->label('City')
                ->formatStateUsing(fn ($state) => self::sanitizeCsv($state)),
            ExportColumn::make('shipping_address.postcode')->label('Postcode'),
            ExportColumn::make('shipping_address.state')->label('State')
                ->formatStateUsing(fn ($state) => self::sanitizeCsv($state)),
            ExportColumn::make('subtotal')->label('Subtotal'),
            ExportColumn::make('shipping_fee')->label('Shipping Fee'),
            ExportColumn::make('total_amount')->label('Total Amount'),
            ExportColumn::make('created_at')->label('Created At'),
        ];
    }

    /**
     * Prevent CSV formula injection (CWE-1236). Values beginning with a
     * spreadsheet formula trigger character are prefixed with a tab so Excel
     * and Google Sheets treat them as plain text, not executable formulas.
     * The tab is invisible in most editors and does not affect re-import since
     * none of these columns are imported back.
     */
    private static function sanitizeCsv(mixed $state): mixed
    {
        if (! is_string($state) || $state === '') {
            return $state;
        }

        return in_array($state[0], ['=', '+', '-', '@', "\t", "\r"], true)
            ? "\t" . $state
            : $state;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            DatePicker::make('fromDate')->label('From date'),
            DatePicker::make('untilDate')->label('Until date'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your order export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        $failedRowsCount = $export->getFailedRowsCount();
        if ($failedRowsCount) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        self::notifyCompletionToDatabase(
            $export->user,
            static::getCompletedNotificationTitle($export),
            $body,
            $failedRowsCount,
            $export->total_rows,
        );

        return $body;
    }

    public function getJobConnection(): ?string
    {
        return 'sync';
    }
}
