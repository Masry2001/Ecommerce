<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use App\Models\Order;
use Filament\Tables\Filters\SelectFilter;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->color('primary')
                    ->url(fn(Order $record): ?string => $record->customer_id ? route('filament.admin.resources.customers.edit', $record->customer_id) : null),
                TextColumn::make('coupon.code')
                    ->label('Coupon')
                    ->searchable()
                    ->color('primary')
                    ->url(fn(Order $record): ?string => $record->coupon_id ? route('filament.admin.resources.coupons.edit', $record->coupon_id) : null),
                TextColumn::make('subtotal')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('discount_amount')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('shipping_cost')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tax_amount')
                    ->money('EGP')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total')
                    ->money('EGP')
                    ->color('success')
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('order_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'info',
                        'shipped' => 'warning',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        'returned' => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'warning',
                    })
                    ->sortable(),
                TextColumn::make('tracking_number')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('items_count')
                    ->counts('orderItems')
                    ->badge()
                    ->color('info'),
                TextColumn::make('customer_ip')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make()->native(false),
                SelectFilter::make('order_status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        'returned' => 'Returned',
                    ])
                    ->multiple()
                    ->native(false),
                SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ])
                    ->multiple()
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
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
