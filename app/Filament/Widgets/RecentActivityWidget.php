<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Spatie\Activitylog\Models\Activity;

class RecentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Recent Activity'))
            ->query(Activity::query()->latest()->limit(10))
            ->columns([
                TextColumn::make('log_name')
                    ->label(__('Log'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('Action'))
                    ->sortable(),
                TextColumn::make('subject_type')
                    ->label(__('Model'))
                    ->formatStateUsing(fn ($state) => class_basename($state ?? ''))
                    ->sortable(),
                TextColumn::make('causer.name')
                    ->label(__('By'))
                    ->default('—'),
                TextColumn::make('created_at')
                    ->label(__('When'))
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
