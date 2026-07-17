<?php

namespace App\Filament\Resources\ProductReviews;

use App\Filament\Resources\ProductReviews\Pages\EditProductReview;
use App\Filament\Resources\ProductReviews\Pages\ListProductReviews;
use App\Models\ProductReview;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductReviewResource extends Resource
{
    protected static ?string $model = ProductReview::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Customer Interactions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static ?string $navigationLabel = 'Product Reviews';

    protected static ?string $modelLabel = 'Product Review';

    protected static ?string $pluralModelLabel = 'Product Reviews';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Review')->schema([
                Select::make('product_id')->relationship('product', 'name')->disabled(),
                Select::make('user_id')->relationship('user', 'name')->disabled(),
                Select::make('rating')->options([5 => '5 stars', 4 => '4 stars', 3 => '3 stars', 2 => '2 stars', 1 => '1 star'])->required(),
                Toggle::make('is_approved')->label('Visible on storefront')->required(),
                Textarea::make('comment')->required()->rows(6)->columnSpanFull(),
            ])->columns(['default' => 1, 'sm' => 2]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            TextColumn::make('product.name')->label('Product')->searchable()->sortable(),
            TextColumn::make('user.name')->label('Customer')->searchable(),
            TextColumn::make('rating')->formatStateUsing(fn (int $state) => str_repeat('★', $state))->color('warning')->sortable(),
            TextColumn::make('comment')->limit(60)->wrap(),
            ToggleColumn::make('is_approved')->label('Visible'),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])->filters([TernaryFilter::make('is_approved')->label('Visibility')])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListProductReviews::route('/'), 'edit' => EditProductReview::route('/{record}/edit')];
    }
}
