<?php

namespace App\Filament\Resources\Bookings\Schemas;

use App\Models\Service;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'sm' => 2])
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
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(100),
                TextInput::make('vehicle_model')
                    ->maxLength(120),
                TextInput::make('vehicle_plate')
                    ->maxLength(30),
                Select::make('service_id')
                    ->label('About (service)')
                    ->options(function ($record) {
                        $query = Service::where('is_active', true);
                        if ($record?->service_id) {
                            $query->orWhere('id', $record->service_id);
                        }
                        return $query->orderBy('name')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->placeholder('General visit')
                    ->helperText('Leave blank for a general showroom visit.'),
                DatePicker::make('preferred_date')
                    ->label('Preferred Date')
                    ->required()
                    ->native(false),
                DateTimePicker::make('start_at')
                    ->label('Start Time')
                    ->required()
                    ->native(false)
                    ->seconds(false),
                DateTimePicker::make('end_at')
                    ->label('End Time')
                    ->required()
                    ->native(false)
                    ->seconds(false),
                TextInput::make('reference')
                    ->label('Booking Reference (auto-generated)')
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn('edit')
                    ->columnSpanFull(),
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
