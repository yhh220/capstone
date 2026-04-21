<?php

namespace App\Filament\Resources\Feedback\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FeedbackForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'sm' => 2])
            ->components([
                \Filament\Schemas\Components\Section::make('Homepage Testimonial')
                    ->description('This feedback will be displayed in the "What Our Clients Say" section on the home page. Make sure to set "Active" to show it.')
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->directory('feedback-images')
                            ->helperText('Optional customer photo.'),
                        TextInput::make('name')
                            ->required()
                            ->placeholder('e.g. Ahmad Rizal'),
                        TextInput::make('location')
                            ->placeholder('e.g. Kuala Lumpur'),
                        Textarea::make('message')
                            ->required()
                            ->label('Testimonial Content')
                            ->columnSpanFull(),
                        TextInput::make('rating')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->default(5)
                            ->helperText('Star rating from 1 to 5.'),
                        Toggle::make('is_active')
                            ->label('Show on Homepage')
                            ->default(true)
                            ->required(),
                    ])->columns(['default' => 1, 'sm' => 2]),
            ]);
    }
}
