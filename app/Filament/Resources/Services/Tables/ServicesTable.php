<?php

namespace App\Filament\Resources\Services\Tables;

use App\Models\Service;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

/**
 * Edit-only (see ServiceResource docblock): no create, no delete. Rows can be
 * reordered, edited, and hidden from the site with the Visible toggle.
 */
class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated(false)
            // Drag rows to set the order they appear on the public Services
            // page (the road animation rebuilds itself around the new order).
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                SpatieMediaLibraryImageColumn::make('images')
                    ->label('Photo')
                    ->collection('images')
                    ->conversion('thumb')
                    ->square()
                    ->size(40)
                    ->alignCenter()
                    ->visibleFrom('sm'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Service $record): string => \Illuminate\Support\Str::limit($record->description, 60)),
                TextColumn::make('duration_label')
                    ->label('Duration')
                    ->badge()
                    ->color('info')
                    ->tooltip('Typical job time — shown to customers by the chatbot; appointment slots use the global slot-length setting instead')
                    ->visibleFrom('md'),
                TextColumn::make('bookings_count')
                    ->counts('bookings')
                    ->label('Bookings')
                    ->alignCenter()
                    ->sortable()
                    ->visibleFrom('lg'),
                ToggleColumn::make('is_active')
                    ->label('Visible')
                    ->sortable()
                    ->alignCenter()
                    ->disabled(fn () => ! auth()->user()?->isAdmin())
                    ->updateStateUsing(function (Service $record, $state) {
                        if (! auth()->user()?->isAdmin()) {
                            return;
                        }
                        $record->update(['is_active' => (bool) $state]);
                    }),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
