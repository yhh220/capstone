<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('duration')
                    ->placeholder('e.g. 1-2 hours')
                    ->maxLength(100),
                TextInput::make('duration_minutes')
                    ->numeric()
                    ->default(60)
                    ->required(),
                TextInput::make('buffer_after')
                    ->numeric()
                    ->default(15)
                    ->required(),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('RM')
                    ->placeholder('Leave blank for "Contact for pricing"'),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->image()
                    ->imageEditor()
                    ->optimize('webp')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
