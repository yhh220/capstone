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
use Illuminate\Validation\Rule;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'sm' => 2])
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Category'),
                TextInput::make('brand')
                    ->maxLength(255),
                TextInput::make('name')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('slug')
                    ->required()
                    ->columnSpanFull()
                    ->rules(fn ($record) => [
                        'required',
                        Rule::unique('products', 'slug')->ignore($record?->id),
                    ]),
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
                    ->minValue(0)
                    ->maxValue(1000000)
                    ->prefix('RM'),
                TextInput::make('sale_price')
                    ->numeric()
                    ->minValue(0)
                    ->lte('price')
                    ->helperText('Optional. Must not exceed the price.')
                    ->prefix('RM'),
                TextInput::make('sku')
                    ->label('SKU'),
                TextInput::make('stock')
                    ->required()
                    ->integer()
                    ->minValue(0)
                    ->maxValue(999)
                    ->default(0)
                    ->helperText('On-hand units (0–999). Out-of-stock items can still be backordered.'),
                KeyValue::make('specs')
                    ->columnSpanFull()
                    ->keyLabel('Specification')
                    ->valueLabel('Value'),
                TagsInput::make('compatible_vehicles')
                    ->columnSpanFull()
                    ->placeholder('Add vehicle model'),

                Toggle::make('is_active')
                    ->required(),
                Toggle::make('is_featured')
                    ->label('Show on Homepage'),
                Toggle::make('has_3d')
                    ->label('Has 3D Model'),
                TextInput::make('model_url')
                    ->label('3D Model URL (.glb)')
                    ->columnSpanFull()
                    ->url()
                    ->visible(fn ($get) => (bool) $get('has_3d')),
                SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->multiple()
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(4096)
                    ->imageEditor()
                    ->columnSpanFull(),
            ]);
    }
}
