<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    // 'role' is not mass-assignable; forceFill so trusted admins can set it here
    // (this page is restricted to admins by UserResource::canAccess()). Same
    // explicit guard as EditUser — don't rely solely on the form hiding
    // roles from non-owners: only the owner may mint admins (or another
    // owner); an admin can only ever create staff, whatever the payload says.
    protected function handleRecordCreation(array $data): Model
    {
        if (! Filament::auth()->user()?->isOwner()) {
            $data['role'] = 'staff';
        }

        $user = new (static::getModel());
        $user->forceFill($data)->save();

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
