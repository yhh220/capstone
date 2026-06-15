<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'sm' => 2])
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),
                Select::make('display_type')
                    ->options(['text' => 'Text only', 'image' => 'Logo image'])
                    ->default('text')
                    ->required()
                    ->live(),
                TextInput::make('website_url')
                    ->url()
                    ->placeholder('https://example.com')
                    ->maxLength(255),
                FileUpload::make('logo')
                    ->image()
                    ->disk('public')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(4096)
                    ->directory('brand-logos')
                    ->helperText('Upload brand logo (PNG/JPG/WebP). Only shown when display type is "Logo image".')
                    ->visible(fn ($get) => $get('display_type') === 'image')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_active')
                    ->label('Show in carousel')
                    ->default(true),
            ]);
    }
}
