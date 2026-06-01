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
            ->columns(['default' => 1, 'sm' => 2])
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('RM')
                    ->placeholder('Leave blank for "Contact for pricing"'),
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
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->multiple()
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(4096)
                    ->imageEditor()
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('Display Order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first. Drag-to-reorder also works in the list view.'),
            ]);
    }
}
