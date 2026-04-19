<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use App\Models\Setting;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function afterSave(): void
    {
        // Bust the cache for the updated setting
        cache()->forget('setting_' . $this->record->key);
    }
}
