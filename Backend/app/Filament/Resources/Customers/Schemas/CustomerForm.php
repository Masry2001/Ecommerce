<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Customer Information")
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->unique(ignoreRecord: true)
                            ->email()
                            ->required(),
                        TextInput::make('phone')
                            ->tel()
                            ->unique(ignoreRecord: true)
                            ->default(null),
                        Select::make('gender')
                            ->options(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'])
                            ->native(false)
                            ->default(null),
                        DatePicker::make('date_of_birth')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(null),


                        DateTimePicker::make('email_verified_at')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i:s')
                            ->default(null),
                        Toggle::make('is_active')
                            ->required(),
                    ]),
                Section::make("Password Information")
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn($operation) => $operation === 'create')
                            ->confirmed()
                            ->dehydrated(fn($state) => filled($state)),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->revealable()
                            ->required(fn($operation) => $operation === 'create')
                            ->dehydrated(false)
                    ]),
            ]);
    }
}
