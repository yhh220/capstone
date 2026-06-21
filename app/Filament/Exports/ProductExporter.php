<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
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
            ExportColumn::make('short_description')->label('Short Description'),
            ExportColumn::make('description')->label('Description'),
            ExportColumn::make('is_active')->label('Is Active')
                ->formatStateUsing(fn ($state) => $state ? '1' : '0'),
            ExportColumn::make('is_featured')->label('Is Featured')
                ->formatStateUsing(fn ($state) => $state ? '1' : '0'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your product export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

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
