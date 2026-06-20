<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table->paginated([10, 25, 50])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image')
                    ->square()
                    ->size(48)
                    ->alignCenter(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->money('MYR', locale: 'ms_MY')
                    ->sortable()
                    ->placeholder('Contact for pricing')
                    ->tooltip(fn ($record) => $record->price ? 'Base service fee' : 'No set price; contact details will be shown to user'),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable()
                    ->alignCenter()
                    ->tooltip('Toggle service availability for customer booking'),
                TextColumn::make('duration')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('duration_minutes')
                    ->label('Minutes')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('buffer_after')
                    ->label('Buffer')
                    ->suffix(' min')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sort_order')
                    ->label('Rank')
                    ->alignCenter()
                    ->sortable()
                    ->tooltip('Rank order determining display position on public website list'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make()
                    ->tooltip('Edit service details'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
