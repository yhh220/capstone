<?php

namespace App\Filament\Resources\Brands\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class BrandsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('logo')
                    ->square()
                    ->size(40)
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('display_type')
                    ->badge()
                    ->color(fn ($state) => $state === 'image' ? 'info' : 'gray')
                    ->sortable(),
                TextColumn::make('website_url')
                    ->label('Website')
                    ->url(fn ($record) => $record->website_url)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
