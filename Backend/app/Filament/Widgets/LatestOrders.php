<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order;
use Filament\Tables\Columns\TextColumn;

class LatestOrders extends TableWidget
{
    protected static ?int $sort = 2;
    protected  int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Order::query())
            ->columns([
                TextColumn::make('order_number')
                    ->weight('bold')
                    ->copyable()
                    ->url(fn(Order $record): ?string => route('filament.admin.resources.orders.edit', $record->id)),
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->color('primary')
                    ->url(fn(Order $record): ?string => $record->customer_id ? route('filament.admin.resources.customers.edit', $record->customer_id) : null),
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
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->heading('Latest Orders')
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
