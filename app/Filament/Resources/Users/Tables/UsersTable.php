<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table->paginated([10, 25, 50])
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
                    ->searchable(),
                BadgeColumn::make('role')
                    ->colors([
                        'danger'  => 'owner',
                        'warning' => 'admin',
                        'info'    => 'staff',
                    ])
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'owner' => 'Owner',
                        'admin' => 'Admin',
                        'staff' => 'Staff',
                    ]),
                // Deleted admin/staff accounts (e.g. a self-deletion mistake) are
                // otherwise only recoverable by editing the database directly.
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => Filament::auth()->user()?->isAdmin() ?? false),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => Filament::auth()->user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
