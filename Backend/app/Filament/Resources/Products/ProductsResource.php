<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProducts;
use App\Filament\Resources\Products\Pages\EditProducts;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Schemas\ProductsForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ProductsResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShoppingBag;
    protected static string|UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?string $recordTitleAttribute = 'slug';
    protected static int $globalSearchResultsLimit = 5; // Show only top 5 (Saves database memory)
    public static function form(Schema $schema): Schema
    {
        return ProductsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProducts::route('/create'),
            'edit' => EditProducts::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
