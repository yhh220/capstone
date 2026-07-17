<?php

namespace App\Filament\Exports;

use App\Filament\Concerns\NotifiesImportExportCompletion;
use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    use NotifiesImportExportCompletion;

    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('sku')->label('SKU'),
            ExportColumn::make('name')->label('Name'),
            ExportColumn::make('brand')->label('Brand'),
            ExportColumn::make('category.slug')->label('Category Slug'),
            ExportColumn::make('category.name')->label('Category Name'),
            ExportColumn::make('price')->label('Price'),
            ExportColumn::make('sale_price')->label('Sale Price'),
            ExportColumn::make('stock')->label('Stock'),
            ExportColumn::make('short_description')->label('Short Description')
                ->formatStateUsing(fn (?string $state) => $state ? trim(preg_replace('/\s*\R+\s*/', ' ', $state)) : $state),
            ExportColumn::make('description')->label('Description')
                ->formatStateUsing(fn (?string $state) => $state ? trim(preg_replace('/\s*\R+\s*/', ' ', $state)) : $state),
            ExportColumn::make('is_active')->label('Is Active')
                ->formatStateUsing(fn ($state) => $state ? '1' : '0'),
            ExportColumn::make('is_featured')->label('Is Featured')
                ->formatStateUsing(fn ($state) => $state ? '1' : '0'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your product export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        $failedRowsCount = $export->getFailedRowsCount();
        if ($failedRowsCount) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        self::notifyCompletionToDatabase(
            $export->user,
            static::getCompletedNotificationTitle($export),
            $body,
            $failedRowsCount,
            $export->total_rows,
            $export, // exports get "Download CSV/XLSX" buttons on the bell notification
        );

        return $body;
    }

    public function getJobConnection(): ?string
    {
        return 'sync';
    }
}
