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

    // 'role' is not mass-assignable; forceFill so the owner can update it
    // (this page is restricted to admins by UserResource::canAccess()). The
    // form locks the role Select for non-owners, but that's a UI convenience,
    // not a security boundary — guard explicitly here too: role changes are
    // owner-exclusive, so a crafted payload from anyone else always keeps the
    // record's current role.
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! Filament::auth()->user()?->isOwner()) {
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
