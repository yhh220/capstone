<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table->paginated([10, 25, 50, 100, 'all'])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->columns([
                ImageColumn::make('image')
                    ->alignCenter(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Product $record): ?string => $record->sku ? "SKU: {$record->sku}" : null),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),
                TextColumn::make('brand')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('price')
                    ->money('MYR', locale: 'ms_MY')
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->label('Sale')
                    ->money('MYR', locale: 'ms_MY')
                    ->sortable()
                    ->color('danger')
                    ->placeholder('—'),
                TextColumn::make('stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn (Product $record): string => $record->stock <= 0 ? 'danger' : ($record->stock < 5 ? 'warning' : 'gray'))
                    ->weight(fn (Product $record): string => $record->stock < 5 ? 'bold' : 'normal'),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->alignCenter(),
                ToggleColumn::make('is_featured')
                    ->label('Show on Homepage')
                    ->alignCenter(),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('brand')
                    ->multiple()
                    ->options(fn (): array => Product::query()
                        ->whereNotNull('brand')
                        ->where('brand', '!=', '')
                        ->distinct()
                        ->orderBy('brand')
                        ->pluck('brand', 'brand')
                        ->all()),
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All products')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
                Filter::make('on_sale')
                    ->label('On sale')
                    ->toggle()
                    ->query(fn ($query) => $query->whereNotNull('sale_price')),
                Filter::make('low_stock')
                    ->label('Low stock (< 5)')
                    ->toggle()
                    ->query(fn ($query) => $query->where('stock', '<', 5)),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('feature')
                        ->label('Show on homepage')
                        ->icon('heroicon-o-star')
                        ->action(fn (Collection $records) => $records->each->update(['is_featured' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('unfeature')
                        ->label('Remove from homepage')
                        ->icon('heroicon-o-star')
                        ->color('gray')
                        ->action(fn (Collection $records) => $records->each->update(['is_featured' => false]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
