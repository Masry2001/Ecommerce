<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Status')
                    ->columns(2)
                    ->schema([
                        Select::make('order_status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                                'returned' => 'Returned',
                            ])
                            ->native(false)
                            ->default('pending')
                            ->required(),

                        TextInput::make('tracking_number')
                            ->helperText('Shipping tracking number')
                            ->maxLength(50)
                            ->required(),

                        Select::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->native(false)
                            ->default('pending')
                            ->required(),
                    ]),
                Section::make('Order Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('order_number')
                            ->required()
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('customer_id')
                            ->default(null),
                        TextInput::make('coupon_id')
                            ->default(null),
                        TextInput::make('customer_ip')
                            ->default(null),
                    ]),
                Section::make('Amounts')
                    ->columns(2)
                    ->schema([
                        TextInput::make('subtotal')
                            ->required()
                            ->numeric(),
                        TextInput::make('discount_amount')
                            ->required()
                            ->numeric()
                            ->default(0.0),
                        TextInput::make('shipping_cost')
                            ->required()
                            ->numeric()
                            ->default(0.0)
                            ->prefix('$'),
                        TextInput::make('tax_amount')
                            ->required()
                            ->numeric()
                            ->default(0.0),
                        TextInput::make('total')
                            ->required()
                            ->numeric(),
                    ]),
            ]);
    }
}
