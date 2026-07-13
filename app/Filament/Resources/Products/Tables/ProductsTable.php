<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Exports\ProductExporter;
use App\Models\Product;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
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
                    ->description(fn (Product $record): ?string => $record->sku ? "SKU: {$record->sku}" : null)
                    ->tooltip(fn (Product $record): string => $record->name),
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
                    ->visibleFrom('md')
                    ->placeholder('—'),
                TextColumn::make('price')
                    ->money('MYR', locale: 'ms_MY')
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->label('Sale')
                    ->money('MYR', locale: 'ms_MY')
                    ->sortable()
                    ->color('danger')
                    ->placeholder('—')
                    ->tooltip(fn (Product $record) => $record->sale_price ? 'Promotional discount price is active' : null),
                TextColumn::make('stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn (Product $record): string => $record->stock <= 0 ? 'danger' : ($record->stock < 5 ? 'warning' : 'gray'))
                    ->weight(fn (Product $record): string => $record->stock < 5 ? 'bold' : 'normal')
                    ->tooltip(fn (Product $record): string => $record->stock <= 0 ? 'Out of stock!' : ($record->stock < 5 ? 'Low stock warning!' : 'Stock count in inventory')),
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->alignCenter()
                    ->tooltip('Toggle product visibility in the store catalog')
                    ->disabled(fn () => !auth()->user()?->isStaffMember())
                    ->updateStateUsing(function ($record, $state) {
                        if (! auth()->user()?->isStaffMember()) {
                            return;
                        }
                        $record->update(['is_active' => (bool) $state]);
                    }),
                ToggleColumn::make('is_featured')
                    ->label('Show on Homepage')
                    ->alignCenter()
                    ->visibleFrom('md')
                    ->tooltip('Toggle showing this product in the homepage featured section')
                    ->disabled(fn () => !auth()->user()?->isStaffMember())
                    ->updateStateUsing(function ($record, $state) {
                        if (! auth()->user()?->isStaffMember()) {
                            return;
                        }
                        $record->update(['is_featured' => (bool) $state]);
                    }),
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
                EditAction::make()
                    ->tooltip('Edit product details'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->tooltip('Activate all selected products')
                        ->authorize(fn () => auth()->user()?->isStaffMember())
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->tooltip('Deactivate all selected products')
                        ->authorize(fn () => auth()->user()?->isStaffMember())
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('feature')
                        ->label('Show on homepage')
                        ->icon('heroicon-o-star')
                        ->tooltip('Feature all selected products on homepage')
                        ->authorize(fn () => auth()->user()?->isStaffMember())
                        ->action(fn (Collection $records) => $records->each->update(['is_featured' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('unfeature')
                        ->label('Remove from homepage')
                        ->icon('heroicon-o-star')
                        ->color('gray')
                        ->tooltip('Remove all selected products from homepage featured section')
                        ->authorize(fn () => auth()->user()?->isStaffMember())
                        ->action(fn (Collection $records) => $records->each->update(['is_featured' => false]))
                        ->deselectRecordsAfterCompletion(),
                    ExportBulkAction::make()
                        ->exporter(ProductExporter::class)
                        ->authorize(fn () => auth()->user()?->isAdmin()),
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->isStaffMember() ?? false),
                ]),
            ]);
    }
}
