<?php

namespace App\Filament\Resources\Bookings\Schemas;

use App\Models\Service;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        $times = [];
        for ($h = 10; $h <= 19; $h++) {
            foreach (['00', '30'] as $m) {
                if ($h === 10 && $m === '00') continue; // shop opens 10:30
                $times[sprintf('%02d:%s', $h, $m)] = sprintf('%02d:%s', $h, $m);
            }
        }

        return $schema
            ->columns(2)
            ->components([
                TextInput::make('customer_name')
                    ->label('Customer Name')
                    ->required()
                    ->maxLength(100),
                TextInput::make('customer_phone')
                    ->label('Phone Number')
                    ->required()
                    ->maxLength(20),
                TextInput::make('customer_email')
                    ->label('Email (optional)')
                    ->email()
                    ->maxLength(100),
                Select::make('service_id')
                    ->label('Service')
                    ->options(Service::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                DatePicker::make('preferred_date')
                    ->label('Preferred Date')
                    ->required()
                    ->native(false),
                Select::make('preferred_time')
                    ->label('Preferred Time')
                    ->options($times)
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ])
                    ->default('pending')
                    ->required(),
                Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull(),
            ]);
    }
}
