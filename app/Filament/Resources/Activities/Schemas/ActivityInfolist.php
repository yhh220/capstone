<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('causer.name')
                    ->label('User')
                    ->placeholder('System'),
                TextEntry::make('ip_address')
                    ->label('IP Address'),
                TextEntry::make('description')
                    ->label('Action Performed'),
                TextEntry::make('subject_type')
                    ->label('Model')
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                \Filament\Infolists\Components\Section::make('Data Changes (After vs Before)')
                    ->schema([
                        \Filament\Infolists\Components\KeyValueEntry::make('properties.attributes')
                            ->label('New Values (After)')
                            ->columnSpan(1),
                        \Filament\Infolists\Components\KeyValueEntry::make('properties.old')
                            ->label('Old Values (Before)')
                            ->columnSpan(1),
                    ])->columns(2),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
