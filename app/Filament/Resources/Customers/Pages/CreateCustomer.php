<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // `role` is excluded from User::$fillable deliberately, so the Hidden field
    // default is silently dropped and the DB default ('staff') would be used instead.
    // Force-fill here to ensure every admin-created customer gets role=client.
    protected function handleRecordCreation(array $data): Model
    {
        $user = new (static::getModel());
        $user->forceFill(array_merge($data, ['role' => 'client']))->save();

        return $user;
    }
}
