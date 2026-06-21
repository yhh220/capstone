<?php

namespace App\Filament\Exports;

use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\DatePicker;

class OrderExporter extends Exporter
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('order_number')->label('Order Number'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('payment_status')->label('Payment Status'),
            ExportColumn::make('customer_name')->label('Customer Name'),
            ExportColumn::make('customer_email')->label('Customer Email'),
            ExportColumn::make('customer_phone')->label('Customer Phone'),
            ExportColumn::make('tracking_number')->label('Tracking Number'),
            ExportColumn::make('shipping_address.street')->label('Street'),
            ExportColumn::make('shipping_address.city')->label('City'),
            ExportColumn::make('shipping_address.postcode')->label('Postcode'),
            ExportColumn::make('shipping_address.state')->label('State'),
            ExportColumn::make('subtotal')->label('Subtotal'),
            ExportColumn::make('shipping_fee')->label('Shipping Fee'),
            ExportColumn::make('total_amount')->label('Total Amount'),
            ExportColumn::make('created_at')->label('Created At'),
        ];
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

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    public function getJobConnection(): ?string
    {
        return 'sync';
    }
}
