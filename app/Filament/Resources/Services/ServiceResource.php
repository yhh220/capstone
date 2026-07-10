<?php

namespace App\Filament\Resources\Services;

use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Filament\Resources\Services\Schemas\ServiceForm;
use App\Filament\Resources\Services\Tables\ServicesTable;
use App\Models\Service;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * The installation services shown on the public Services page and picked in
 * the booking form. Display-only content — no price appears anywhere on the
 * site; customers are directed to WhatsApp / the showroom for quotes.
 *
 * EDIT-ONLY by owner decision: the service menu is a fixed set (the shop is
 * not going to add or remove services), so there is no create page and no
 * delete action — the Services page keeps its curated keyword-matched icons
 * and the screenshots in the report stay accurate. Admins edit copy,
 * translations, photos, booking duration and visibility of the existing rows.
 */
class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationLabel = 'Services';

    protected static \UnitEnum|string|null $navigationGroup = 'Sales';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    // Right after Bookings — services are what bookings are made for.
    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return ServiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServicesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServices::route('/'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}
