<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Exports\ProductExporter;
use App\Filament\Imports\ProductImporter;
use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()->exporter(ProductExporter::class),
            ImportAction::make()
                ->importer(ProductImporter::class)
                ->authorize(fn () => auth()->user()?->isAdmin()),
            CreateAction::make(),
        ];
    }
}
