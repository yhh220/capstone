<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    // 'role' is not mass-assignable; forceFill so trusted admins can update it
    // (this page is restricted to admins by UserResource::canAccess()). The
    // form's Select already hides 'owner' from non-owners, but that's a UI
    // convenience, not a security boundary — guard explicitly here too, in
    // case the form is ever refactored to omit the options() restriction.
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (($data['role'] ?? null) === 'owner' && ! Filament::auth()->user()?->isOwner()) {
            $data['role'] = $record->role;
        }

        $record->forceFill($data)->save();

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
