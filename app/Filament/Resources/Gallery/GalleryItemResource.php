<?php

namespace App\Filament\Resources\Gallery;

use App\Filament\Resources\Gallery\Pages\CreateGalleryItem;
use App\Filament\Resources\Gallery\Pages\EditGalleryItem;
use App\Filament\Resources\Gallery\Pages\ListGalleryItems;
use App\Filament\Resources\Gallery\Schemas\GalleryItemForm;
use App\Filament\Resources\Gallery\Tables\GalleryItemsTable;
use App\Models\GalleryItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GalleryItemResource extends Resource
{
    protected static ?string $model = GalleryItem::class;

    protected static ?string $navigationLabel = 'Gallery';

    protected static \UnitEnum|string|null $navigationGroup = 'Store Products';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    public static function form(Schema $schema): Schema
    {
        return GalleryItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GalleryItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListGalleryItems::route('/'),
            'create' => CreateGalleryItem::route('/create'),
            'edit'   => EditGalleryItem::route('/{record}/edit'),
        ];
    }
}
