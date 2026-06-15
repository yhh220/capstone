<?php

namespace App\Filament\Resources\Products\Pages;

use App\Contracts\ChatServiceInterface;
use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateDescriptions')
                ->label('Auto Generate Description')
                ->icon('heroicon-o-sparkles')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Auto-generate descriptions?')
                ->modalDescription('This writes fresh English, Malay, and Chinese descriptions from this product\'s brand, category, price, specs and compatible vehicles. It will overwrite the current descriptions.')
                ->modalSubmitActionLabel('Generate')
                ->action(function (): void {
                    $result = app(ChatServiceInterface::class)->generateDescription($this->record);

                    $this->record->update([
                        'description' => $result['en'] ?: $this->record->description,
                        'description_ms' => $result['ms'] ?: $this->record->description_ms,
                        'description_zh' => $result['zh'] ?: $this->record->description_zh,
                    ]);

                    $this->refreshFormData([
                        'description',
                        'description_ms',
                        'description_zh',
                    ]);

                    Notification::make()
                        ->title('Descriptions generated in all three languages')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
