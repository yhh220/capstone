<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'sm' => 2])
            ->components([
                TextInput::make('log_name'),
                TextInput::make('event'),
                TextInput::make('subject_type'),
                TextInput::make('subject_id')
                    ->numeric(),
                TextInput::make('causer_type'),
                TextInput::make('causer_id')
                    ->numeric(),
                TextInput::make('ip_address'),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('attribute_changes')
                    ->columnSpanFull(),
                Textarea::make('properties')
                    ->columnSpanFull(),
            ]);
    }
}
