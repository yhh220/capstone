<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                \Filament\Forms\Components\Select::make('role')
                    ->options(function () {
                        if (auth()->user()->isOwner()) {
                            return [
                                'owner' => 'Owner (Superadmin)',
                                'admin' => 'Admin',
                                'staff' => 'Staff',
                            ];
                        }
                        return [
                            'admin' => 'Admin',
                            'staff' => 'Staff',
                        ];
                    })
                    ->required()
                    ->default('staff'),
            ]);
    }
}
