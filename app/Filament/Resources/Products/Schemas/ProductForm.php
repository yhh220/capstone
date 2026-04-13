<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Category'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('short_description')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('sale_price')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('sku')
                    ->label('SKU'),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->image()
                    ->imageEditor()
                    ->optimize('webp')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
            ]);
    }
}
