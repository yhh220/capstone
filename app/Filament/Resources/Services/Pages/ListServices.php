<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use Filament\Resources\Pages\ListRecords;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    // No CreateAction — the service menu is a fixed set (see the resource
    // docblock); this page exists to edit, reorder and hide/show the rows.
    protected function getHeaderActions(): array
    {
        return [];
    }
}
