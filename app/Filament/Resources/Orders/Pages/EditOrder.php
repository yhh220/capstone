<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Mirrors the table row action's guard: pending/processing orders already
            // decremented stock at checkout, which is only returned by the cancel
            // action's restock step. Deleting one here instead would leak that stock
            // permanently with no way to recover it.
            DeleteAction::make()
                ->visible(fn (Order $record): bool => ! in_array($record->status, ['pending', 'processing'], true)),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
