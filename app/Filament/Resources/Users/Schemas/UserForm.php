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
                            ->revealable()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->confirmed()
                            // Only the edit form has a "current password" to keep.
                            ->helperText(fn (string $context): ?string => $context === 'edit'
                                ? 'Leave blank to keep the current password.'
                                : null)
                            ->hiddenOn('view'),
                        TextInput::make('password_confirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->required(fn (string $context): bool => $context === 'create')
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
