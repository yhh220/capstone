<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    // No DeleteAction — the service menu is a fixed set; use the "Visible to
    // customers" toggle to take a service off the site temporarily.

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
