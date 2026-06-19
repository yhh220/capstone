<?php

namespace App\Filament\Resources\Faqs;

use App\Models\Faq;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;
    protected static \UnitEnum|string|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 89;
    protected static ?string $navigationLabel = 'FAQ Page';

    public static function canViewAny(): bool
    {
        return \Filament\Facades\Filament::auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('category')
                ->label('Category')
                ->options([
                    'Orders & Payment'    => 'Orders & Payment',
                    'Bookings & Showroom' => 'Bookings & Showroom',
                    'Products & Support'  => 'Products & Support',
                ])
                ->native(false)
                ->searchable()
                ->placeholder('No category')
                ->helperText('Groups questions on the public FAQ page. New categories can be typed in.')
                ->createOptionForm([
                    Forms\Components\TextInput::make('category')->required(),
                ]),
            Forms\Components\TextInput::make('sort_order')
                ->label('Display order')
                ->numeric()
                ->default(0)
                ->helperText('Lower numbers show first.'),
            Forms\Components\Toggle::make('is_active')
                ->default(true)
                ->label('Show on the FAQ page'),

            Forms\Components\TextInput::make('question_en')
                ->required()->maxLength(255)->label('Question (English)')->columnSpanFull(),
            Forms\Components\Textarea::make('answer_en')
                ->required()->rows(3)->label('Answer (English)')->columnSpanFull(),

            Forms\Components\TextInput::make('question_ms')
                ->maxLength(255)->label('Question (Bahasa Malaysia)')->columnSpanFull(),
            Forms\Components\Textarea::make('answer_ms')
                ->rows(3)->label('Answer (Bahasa Malaysia)')
                ->helperText('Optional — falls back to English when empty.')->columnSpanFull(),

            Forms\Components\TextInput::make('question_zh')
                ->maxLength(255)->label('Question (Chinese)')->columnSpanFull(),
            Forms\Components\Textarea::make('answer_zh')
                ->rows(3)->label('Answer (Chinese)')
                ->helperText('Optional — falls back to English when empty.')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->label('#')->sortable()->width('1%'),
                TextColumn::make('question_en')
                    ->label('Question')
                    ->searchable()
                    ->wrap()
                    ->weight('bold'),
                TextColumn::make('category')
                    ->badge()
                    ->placeholder('—')
                    ->sortable(),
                IconColumn::make('is_active')->boolean()->label('Shown'),
                TextColumn::make('updated_at')->dateTime('d M Y H:i')->sortable()->toggleable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit'   => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
