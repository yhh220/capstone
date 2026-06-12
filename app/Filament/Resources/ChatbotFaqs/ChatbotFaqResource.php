<?php

namespace App\Filament\Resources\ChatbotFaqs;

use App\Models\ChatbotFaq;
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

class ChatbotFaqResource extends Resource
{
    protected static ?string $model = ChatbotFaq::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;
    protected static \UnitEnum|string|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 90;
    protected static ?string $navigationLabel = 'Chatbot FAQs';

    public static function canViewAny(): bool
    {
        return \Filament\Facades\Filament::auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('topic')
                ->required()
                ->maxLength(120)
                ->label('Topic')
                ->helperText('Internal label so you can find this entry later, e.g. "Trade-in policy".'),
            Forms\Components\TagsInput::make('keywords')
                ->required()
                ->label('Trigger keywords')
                ->placeholder('Add a keyword and press Enter')
                ->helperText('The bot answers when a customer message contains any of these words or phrases. Add keywords in English, Malay, and Chinese for full coverage. Typos within 1–2 letters still match.'),
            Forms\Components\TextInput::make('priority')
                ->numeric()
                ->default(80)
                ->minValue(1)
                ->maxValue(200)
                ->helperText('Higher priority wins when several topics match one message. Built-in topics use 10–75, so 80+ overrides them.'),
            Forms\Components\Textarea::make('reply_en')
                ->required()
                ->rows(4)
                ->label('Reply (English)'),
            Forms\Components\Textarea::make('reply_ms')
                ->rows(4)
                ->label('Reply (Bahasa Malaysia)')
                ->helperText('Optional — falls back to the English reply when empty.'),
            Forms\Components\Textarea::make('reply_zh')
                ->rows(4)
                ->label('Reply (Chinese)')
                ->helperText('Optional — falls back to the English reply when empty.'),
            Forms\Components\Toggle::make('is_active')
                ->default(true)
                ->label('Active'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('topic')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('keywords')
                    ->badge()
                    ->limitList(4)
                    ->separator(','),
                TextColumn::make('priority')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                TextColumn::make('updated_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('priority', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListChatbotFaqs::route('/'),
            'create' => Pages\CreateChatbotFaq::route('/create'),
            'edit'   => Pages\EditChatbotFaq::route('/{record}/edit'),
        ];
    }
}
