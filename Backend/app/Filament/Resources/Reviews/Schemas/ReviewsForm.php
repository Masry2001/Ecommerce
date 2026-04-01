<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Toggle;

class ReviewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Moderation')
                    ->schema([
                        Toggle::make('is_approved')
                            ->label('Approve Review')
                            ->helperText('Approved Reviews will be visible to customers on product page')
                            ->onColor('success')
                            ->default(false)
                            ->required(),
                    ])


            ]);
    }
}
