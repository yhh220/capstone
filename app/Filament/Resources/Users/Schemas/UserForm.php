<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Facades\Filament;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Information')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required(),
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel(),
                        TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText('Leave blank to keep current password.')
                            ->hiddenOn('view'),
                        Select::make('role')
                            ->options(function () {
                                if (Filament::auth()->user()?->isOwner()) {
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
                    ])->columns(['default' => 1, 'sm' => 2]),
            ]);
    }
}
