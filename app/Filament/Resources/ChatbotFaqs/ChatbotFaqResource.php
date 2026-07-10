<?php

namespace App\Filament\Resources\ChatbotFaqs;

use App\Models\ChatbotFaq;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
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

    protected static \UnitEnum|string|null $navigationGroup = 'FAQs';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Chatbot FAQs';

    public static function canViewAny(): bool
    {
        return Filament::auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('topic')
                ->required()
                ->maxLength(120)
                ->label('Topic')
                ->helperText('Internal label so you can find this entry later, e.g. "Trade-in policy".'),
            Forms\Components\Select::make('priority')
                ->label('Answer priority')
                ->options([
                    15 => 'Low — only if nothing better matches',
                    50 => 'Normal (default)',
                    75 => 'High — prefer this answer',
                    100 => 'Highest — always answer this first',
                ])
                ->default(50)
                ->selectablePlaceholder(false)
                ->native(false)
                ->helperText('If a customer message could match several topics, the higher priority is answered first. Leave most on "Normal".'),
            Forms\Components\TagsInput::make('keywords')
                ->required()
                ->label('Trigger keywords')
                ->placeholder('Add a keyword and press Enter')
                ->helperText('The bot answers when a customer message contains any of these words or phrases. Add keywords in English, Malay, and Chinese for full coverage. Typos within 1–2 letters still match.')
                ->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')
                ->default(true)
                ->label('Active')
                ->columnSpanFull(),
            Forms\Components\Textarea::make('reply_en')
                ->required()
                ->rows(4)
                ->label('Reply (English)')
                ->columnSpanFull(),
            Forms\Components\Textarea::make('reply_ms')
                ->rows(4)
                ->label('Reply (Bahasa Malaysia)')
                ->helperText('Optional — falls back to the English reply when empty.')
                ->columnSpanFull(),
            Forms\Components\Textarea::make('reply_zh')
                ->rows(4)
                ->label('Reply (Chinese)')
                ->helperText('Optional — falls back to the English reply when empty.')
                ->columnSpanFull(),
        ])
            ->columns(['default' => 1, 'sm' => 2]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('topic')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->tooltip('Internal topic category for the chatbot FAQ'),
                TextColumn::make('keywords')
                    ->badge()
                    ->limitList(4)
                    ->separator(',')
                    ->tooltip(fn ($record) => 'Triggers on: '.(is_array($record->keywords) ? implode(', ', $record->keywords) : $record->keywords)),
                TextColumn::make('priority')
                    ->label('Priority')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => match (true) {
                        $state >= 100 => 'Highest',
                        $state >= 75 => 'High',
                        $state >= 40 => 'Normal',
                        default => 'Low',
                    })
                    ->color(fn (int $state): string => match (true) {
                        $state >= 100 => 'danger',
                        $state >= 75 => 'warning',
                        $state >= 40 => 'gray',
                        default => 'gray',
                    })
                    ->tooltip('Determines priority order when multiple triggering keywords match'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->tooltip('Active status of this FAQ response'),
                TextColumn::make('updated_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('priority', 'desc')
            ->recordActions([
                EditAction::make()
                    ->tooltip('Edit FAQ entry'),
                DeleteAction::make()
                    ->tooltip('Delete FAQ entry'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChatbotFaqs::route('/'),
            'create' => Pages\CreateChatbotFaq::route('/create'),
            'edit' => Pages\EditChatbotFaq::route('/{record}/edit'),
        ];
    }
}
