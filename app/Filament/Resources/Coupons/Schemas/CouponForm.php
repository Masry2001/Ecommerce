<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section; // Changed from Forms to Schemas
use Filament\Forms\Components\ToggleButtons;


class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Coupon Information')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, $set) => $set('code', strtoupper($state)))
                            ->unique(ignoreRecord: true),
                        ToggleButtons::make('type')
                            ->options([
                                'percentage' => 'Percentage',
                                'fixed' => 'Fixed',
                            ])
                            ->default('percentage')
                            ->live()
                            ->columnSpanFull()
                            ->inline()
                            ->required(),
                        TextInput::make('value')
                            ->required()
                            ->numeric()
                            ->live(onBlur: true)
                            ->prefix(fn($get) => $get('type') === 'fixed' ? '$' : null)
                            ->suffix(fn($get) => $get('type') === 'percentage' ? '%' : null)
                            ->maxValue(fn($get) => $get('type') === 'percentage' ? 100 : 999999.99),





                        Toggle::make('is_active')
                            ->label('Active')
                            ->required()
                            ->columnSpanFull()
                            ->default(true),
                    ]),

                Section::make('Conditions & Limits')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([

                        TextInput::make('min_order_value')
                            ->numeric()
                            ->maxValue(99999999.99)
                            ->prefix('$')
                            ->default(null),
                        TextInput::make('max_discount')
                            ->numeric()
                            ->maxValue(999999.99)
                            ->prefix('$')
                            ->visible(fn($get) => $get('type') === 'percentage')
                            ->default(null),
                        TextInput::make('usage_limit')
                            ->numeric()
                            ->minValue(1)
                            ->default(null),
                        TextInput::make('usage_limit_per_customer')
                            ->numeric()
                            ->minValue(1)
                            ->default(null),

                    ]),

                Section::make('Validity Period')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->default(now())
                            ->native(false)
                            ->helperText('When the coupon becomes active'),
                        DateTimePicker::make('expires_at')
                            ->default(now()->addMonth())
                            ->native(false)
                            ->helperText('When the coupon expires'),
                    ]),
            ]);
    }
}
