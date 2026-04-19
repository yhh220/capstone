<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table->paginated([10, 25, 50, 100, 'all'])
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('gender')
                    ->formatStateUsing(fn (?string $state) => $state ? ucfirst($state) : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
                BadgeColumn::make('role')
                    ->colors([
                        'danger'  => 'owner',
                        'warning' => 'admin',
                        'info'    => 'staff',
                        'success' => 'client',
                    ])
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->sortable(),
                TextColumn::make('address_line')
                    ->label('Address')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('city')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('state')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('orders_count')
                    ->label('Orders')
                    ->counts('orders')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'owner'  => 'Owner',
                        'admin'  => 'Admin',
                        'staff'  => 'Staff',
                        'client' => 'Client',
                    ]),
            ])
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
