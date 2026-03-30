<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Laravel\SerializableClosure\Serializers\Native;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Coupon code copied to clipboard')
                    ->copyMessageDuration(1000)
                    ->weight('bold'),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn($state) => $state === 'percentage' ? 'success' : 'info'),
                TextColumn::make('value')
                    ->label('Discount')
                    ->numeric()
                    ->prefix(fn($record) => $record->type === 'fixed' ? '$' : '')
                    ->suffix(fn($record) => $record->type === 'percentage' ? '%' : '')
                    ->sortable(),
                TextColumn::make('min_order_value')
                    ->label('Min Order Value')
                    ->money('EGP')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_discount')
                    ->money('EGP')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('usage_limit')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('usage_count')
                    ->label('Used Count')
                    ->numeric()
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('usage_limit_per_customer')
                    ->label('Usage Limit Per Customer')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->color(fn($record) => $record->expires_at < now() ? 'danger' : 'success'),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->native(false)
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->native(false)
                    ->default(true),

            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
