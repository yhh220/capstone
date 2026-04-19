<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Section;
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
                        TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText('Leave blank to keep current password.')
                            ->hiddenOn('view'),
                        Select::make('role')
                            ->options(function () {
                                if (auth()->user()->isOwner()) {
                                    return [
                                        'owner'  => 'Owner (Superadmin)',
                                        'admin'  => 'Admin',
                                        'staff'  => 'Staff',
                                        'client' => 'Client',
                                    ];
                                }
                                return [
                                    'admin'  => 'Admin',
                                    'staff'  => 'Staff',
                                    'client' => 'Client',
                                ];
                            })
                            ->required()
                            ->default('client'),
                    ])->columns(2),

                Section::make('Profile Details')
                    ->schema([
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20),
                        Select::make('gender')
                            ->options([
                                'male'   => 'Male',
                                'female' => 'Female',
                                'other'  => 'Other',
                            ]),
                        TextInput::make('address_line')
                            ->label('Street Address')
                            ->maxLength(255),
                        TextInput::make('city')
                            ->maxLength(255),
                        TextInput::make('postcode')
                            ->maxLength(10),
                        Select::make('state')
                            ->options([
                                'Selangor'        => 'Selangor',
                                'Kuala Lumpur'    => 'Kuala Lumpur',
                                'Johor'           => 'Johor',
                                'Penang'          => 'Penang',
                                'Perak'           => 'Perak',
                                'Pahang'          => 'Pahang',
                                'Negeri Sembilan' => 'Negeri Sembilan',
                                'Melaka'          => 'Melaka',
                                'Kedah'           => 'Kedah',
                                'Kelantan'        => 'Kelantan',
                                'Terengganu'      => 'Terengganu',
                                'Perlis'          => 'Perlis',
                                'Sabah'           => 'Sabah',
                                'Sarawak'         => 'Sarawak',
                                'Putrajaya'       => 'Putrajaya',
                                'Labuan'          => 'Labuan',
                            ]),
                    ])->columns(2),
            ]);
    }
}
