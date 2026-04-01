<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Status')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        ToggleButtons::make('order_status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                                'returned' => 'Returned',
                            ])
                            ->colors([
                                'pending' => 'gray',
                                'processing' => 'info',
                                'shipped' => 'warning',
                                'delivered' => 'success',
                                'cancelled' => 'danger',
                                'returned' => 'danger',
                            ])
                            ->icons([
                                'pending' => 'heroicon-m-clock',
                                'processing' => 'heroicon-m-arrow-path',
                                'shipped' => 'heroicon-m-truck',
                                'delivered' => 'heroicon-m-check-badge',
                                'cancelled' => 'heroicon-m-x-circle',
                                'returned' => 'heroicon-m-arrow-uturn-left',
                            ])
                            ->default('pending')
                            ->required()
                            ->inline()
                            ->columnSpanFull(),

                        TextInput::make('tracking_number')
                            ->helperText('Shipping tracking number')
                            ->maxLength(50)
                            ->required(),

                        ToggleButtons::make('payment_status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->colors([
                                'pending' => 'gray',
                                'paid' => 'success',
                                'failed' => 'danger',
                                'refunded' => 'warning',
                            ])
                            ->icons([
                                'pending' => 'heroicon-m-clock',
                                'paid' => 'heroicon-m-check-circle',
                                'failed' => 'heroicon-m-x-circle',
                                'refunded' => 'heroicon-m-arrow-path-rounded-square',
                            ])
                            ->default('pending')
                            ->required()
                            ->inline()
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->maxLength(255)
                            ->columnSpanFull(),
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
