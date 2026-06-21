<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Exports\OrderExporter;
use App\Filament\Imports\OrderImporter;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(OrderExporter::class)
                ->authorize(fn () => auth()->user()?->isAdmin())
                ->modifyQueryUsing(fn ($query, array $options) => $query
                    ->when($options['fromDate'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                    ->when($options['untilDate'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date))),
            ImportAction::make()
                ->importer(OrderImporter::class)
                ->authorize(fn () => auth()->user()?->isAdmin()),
        ];
    }
}
