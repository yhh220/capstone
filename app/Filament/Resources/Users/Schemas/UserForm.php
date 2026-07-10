<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

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
                            ->required()
                            ->unique(User::class, 'email', ignoreRecord: true),
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel(),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->rule(Password::defaults())
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
                            // Role assignment is owner-exclusive. Admins see the
                            // field locked: they can create accounts (forced to
                            // Staff server-side) and edit staff details, but they
                            // can never re-role anyone — not staff, not peers,
                            // not themselves.
                            ->options([
                                'owner' => 'Owner (Superadmin)',
                                'admin' => 'Admin',
                                'staff' => 'Staff',
                            ])
                            ->disabled(fn (): bool => ! (Filament::auth()->user()?->isOwner() ?? false))
                            ->dehydrated(fn (): bool => Filament::auth()->user()?->isOwner() ?? false)
                            ->helperText(fn (): ?string => (Filament::auth()->user()?->isOwner() ?? false) ? null : 'Only the owner can assign roles.')
                            ->required()
                            ->default('staff'),
                    ])->columns(['default' => 1, 'sm' => 2]),
            ]);
    }
}
