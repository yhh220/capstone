<?php

namespace App\Filament\Resources\Customers\Tables;

use App\Models\User;
use App\Support\SocialLogin;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table->paginated([10, 25, 50, 100])
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
                TextColumn::make('address_line')
                    ->label('Address')
                    ->limit(30)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('city')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('state')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('orders_count')
                    ->label('Orders')
                    ->counts('orders')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('sign_in_method')
                    ->label('Sign-in')
                    ->badge()
                    ->getStateUsing(function (User $record): array {
                        $providers = $record->socialAccounts
                            ->pluck('provider')
                            ->map(fn (string $p) => SocialLogin::PROVIDERS[$p] ?? ucfirst($p))
                            ->all();

                        return $providers ?: ['Email / Password'];
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Google' => 'danger',
                        'Microsoft' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with('socialAccounts'))
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => Filament::auth()->user()?->isAdmin() ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => Filament::auth()->user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
