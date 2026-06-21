<?php

namespace App\Filament\Imports;

use App\Models\Category;
use App\Models\Product;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('sku')
                ->label('SKU')
                ->requiredMapping()
                ->guess(['SKU', 'sku']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('brand')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('category_slug')
                ->label('Category Slug')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->fillRecordUsing(fn () => null),
            ImportColumn::make('price')
                ->requiredMapping()
                ->numeric(decimalPlaces: 2)
                ->rules(['required', 'numeric', 'min:0']),
            ImportColumn::make('sale_price')
                ->label('Sale Price')
                ->numeric(decimalPlaces: 2)
                ->rules(['nullable', 'numeric', 'min:0', 'lt:price']),
            ImportColumn::make('stock')
                ->integer()
                ->rules(['nullable', 'integer', 'min:0'])
                ->ignoreBlankState(),
            ImportColumn::make('short_description')
                ->label('Short Description')
                ->rules(['nullable', 'string']),
            ImportColumn::make('description')
                ->rules(['nullable', 'string']),
            ImportColumn::make('is_active')
                ->label('Is Active')
                ->boolean()
                ->ignoreBlankState(),
            ImportColumn::make('is_featured')
                ->label('Is Featured')
                ->boolean()
                ->ignoreBlankState(),
        ];
    }

    /**
     * Match is case-insensitive and trims whitespace, so a stray space or a typed
     * "ABC-123" vs exported "abc-123" doesn't silently create a duplicate product
     * instead of updating the intended one. A blank/unmatched SKU creates new.
     */
    public function resolveRecord(): Product
    {
        $sku = $this->data['sku'] ?? null;

        if (blank($sku)) {
            return new Product();
        }

        return Product::whereRaw('LOWER(sku) = ?', [Str::lower($sku)])->first()
            ?? new Product();
    }

    protected function beforeSave(): void
    {
        $slug = $this->data['category_slug'] ?? null;

        $category = Category::whereRaw('LOWER(slug) = ?', [Str::lower($slug)])->first();

        if (! $category) {
            throw new RowImportFailedException("No category found with slug \"{$slug}\".");
        }

        $this->record->category_id = $category->id;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    public function getJobConnection(): ?string
    {
        return 'sync';
    }
}
