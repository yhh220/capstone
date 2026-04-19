<?php

namespace App\Filament\Resources\Products\Pages;

use App\Contracts\AiServiceInterface;
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
                ->label('Generate Description (AI)')
                ->action(function (): void {
                    $result = app(AiServiceInterface::class)->generateDescription($this->record);

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
                        ->title('AI descriptions generated')
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
