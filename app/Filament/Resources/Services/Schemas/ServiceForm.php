<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Service')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull()
                        ->helperText('Shown on the Services page and as a choice in the booking form.'),
                    Textarea::make('description')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),
                    SpatieMediaLibraryFileUpload::make('images')
                        ->label('Photo')
                        ->collection('images')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(4096)
                        ->imageEditor()
                        ->columnSpanFull()
                        ->helperText('Optional. Without a photo the Services page shows a matching icon instead.'),
                    Toggle::make('is_active')
                        ->label('Visible to customers')
                        ->default(true)
                        ->helperText('Hidden services disappear from the Services page and booking form, but keep their past bookings.'),
                ])->columns(['default' => 1, 'sm' => 2]),

            Section::make('Translations')
                ->description('Optional. Anything left blank falls back to the English text above.')
                ->schema([
                    TextInput::make('name_ms')
                        ->label('Name (Malay)')
                        ->maxLength(255),
                    TextInput::make('name_zh')
                        ->label('Name (Chinese)')
                        ->maxLength(255),
                    Textarea::make('description_ms')
                        ->label('Description (Malay)')
                        ->rows(3),
                    Textarea::make('description_zh')
                        ->label('Description (Chinese)')
                        ->rows(3),
                ])->columns(['default' => 1, 'sm' => 2])
                ->collapsible(),

            Section::make('Typical Duration')
                ->schema([
                    // Display-only: quoted by the chatbot next to the service
                    // name (e.g. "(3 hr)"). Appointment slots deliberately do
                    // NOT depend on it — a booking is a fixed-length showroom
                    // visit sized by Settings → Appointment Slot Length.
                    TextInput::make('duration_minutes')
                        ->label('Typical job time (minutes)')
                        ->required()
                        ->integer()
                        ->minValue(0)
                        ->maxValue(480)
                        ->default(60)
                        ->helperText('Shown to customers by the chatbot, e.g. "(3 hr)". Appointment slot length is a separate global setting (Settings → Appointment Slot Length) and is not affected by this.'),
                ]),

            Section::make('Pricing')
                ->description('No price is ever shown on the website — customers are quoted on WhatsApp, at the showroom, or after booking.')
                ->schema([
                    TextInput::make('price')
                        ->label('Starting price (chatbot only)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100000)
                        ->prefix('RM')
                        ->helperText('Used only when the chatbot is asked about pricing, quoted as "from RM X" with a pointer to WhatsApp. Leave blank and the chatbot names the service without any figure.'),
                ])->collapsible()
                ->collapsed(fn (?\App\Models\Service $record) => $record === null || $record->price === null),
        ]);
    }
}
