<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\DeleteAction;


class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([


                TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('brand.name')
                    ->label('Brand')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->sortable(),

                ImageColumn::make('primaryImage.image_path')
                    ->label('Image')
                    ->disk('public')
                    ->circular(),

                TextColumn::make('cost_price')
                    ->label('Cost Price')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('compare_price')
                    ->label('Compare Price')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('manage_stock')
                    ->label('Manage Stock')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        true => 'success',
                        false => 'gray',
                    })
                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No'),

                TextColumn::make('stock_status')
                    ->label('Stock Status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'in_stock' => 'success',
                        'low_stock' => 'warning',
                        'out_of_stock' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('stock_quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('low_stock_threshold')
                    ->label('Low Stock Threshold')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        true => 'success',
                        false => 'gray',
                    })
                    ->formatStateUsing(fn($state) => $state ? 'Active' : 'Inactive'),

                TextColumn::make('is_featured')
                    ->label('Featured')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        true => 'success',
                        false => 'gray',
                    })
                    ->formatStateUsing(fn($state) => $state ? 'Featured' : 'Not Featured'),

                TextColumn::make('views_count')
                    ->label('Views Count')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('has_variants')
                    ->label('Has Variants')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        true => 'success',
                        false => 'gray',
                    })
                    ->formatStateUsing(fn($state) => $state ? 'Has Variants' : 'No Variants'),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
