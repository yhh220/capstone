<?php

namespace App\Filament\Resources\Customers;

use App\Filament\Resources\Customers\Pages\CreateCustomer;
use App\Filament\Resources\Customers\Pages\EditCustomer;
use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Filament\Resources\Customers\Tables\CustomersTable;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Customer Interactions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Customers';

    protected static ?string $modelLabel = 'Customer';

    protected static ?string $pluralModelLabel = 'Customers';

    protected static ?int $navigationSort = 1;

    /**
     * Only show client role users.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'client');
    }

    /**
     * Only owner and admin can edit customers.
     */
    public static function canEdit(Model $record): bool
    {
        return Filament::auth()->user()?->isAdmin() ?? false;
    }

    /**
     * All staff/admin/owner can create customers.
     */
    public static function canCreate(): bool
    {
        return Filament::auth()->user()?->isAdmin() ?? false;
    }

    /**
     * Only owner and admin can delete customers.
     */
    public static function canDelete(Model $record): bool
    {
        return Filament::auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Customer Account')
                ->schema([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required(),
                    TextInput::make('phone')
                        ->label('Phone Number')
                        ->tel()
                        ->maxLength(20),
                    TextInput::make('password')
                        ->password()
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->helperText('Leave blank to keep current password.')
                        ->hiddenOn('view'),
                    Hidden::make('role')
                        ->default('client'),
                ])->columns(['default' => 1, 'sm' => 2]),

            Section::make('Address Details')
                ->schema([
                    TextInput::make('address_line')
                        ->label('Street Address')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('city')
                        ->maxLength(255),
                    TextInput::make('postcode')
                        ->maxLength(10),
                    Select::make('state')
                        ->options([
                            'Selangor' => 'Selangor',
                            'Kuala Lumpur' => 'Kuala Lumpur',
                            'Johor' => 'Johor',
                            'Penang' => 'Penang',
                            'Perak' => 'Perak',
                            'Pahang' => 'Pahang',
                            'Negeri Sembilan' => 'Negeri Sembilan',
                            'Melaka' => 'Melaka',
                            'Kedah' => 'Kedah',
                            'Kelantan' => 'Kelantan',
                            'Terengganu' => 'Terengganu',
                            'Perlis' => 'Perlis',
                            'Sabah' => 'Sabah',
                            'Sarawak' => 'Sarawak',
                            'Putrajaya' => 'Putrajaya',
                            'Labuan' => 'Labuan',
                        ]),
                ])->columns(['default' => 1, 'sm' => 2])
                ->collapsible()
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return CustomersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }
}
