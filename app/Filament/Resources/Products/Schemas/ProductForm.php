<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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
                TextInput::make('brand')
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('description_ms')
                    ->label('Description (MS)')
                    ->columnSpanFull(),
                Textarea::make('description_zh')
                    ->label('Description (ZH)')
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
                KeyValue::make('specs')
                    ->columnSpanFull()
                    ->keyLabel('Specification')
                    ->valueLabel('Value'),
                TagsInput::make('compatible_vehicles')
                    ->columnSpanFull()
                    ->placeholder('Add vehicle model'),
                TextInput::make('model_url')
                    ->label('3D Model URL'),
                Toggle::make('has_3d')
                    ->label('Has 3D Viewer')
                    ->default(false),
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
