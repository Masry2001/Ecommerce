<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Information')
                    ->columnSpanFull()->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(20),
                        TextInput::make('slug')
                            ->unique(ignoreRecord: true)
                            ->visibleOn('edit')
                            ->readOnly()
                            ->maxLength(20),
                        Textarea::make('description')
                            ->default(null)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->disk('public')
                            ->directory('categories')
                            ->imageEditor()
                            ->downloadable()
                            ->preserveFilenames()
                            ->image(),
                    ]),
                Section::make('Display Settings')->columnSpanFull()->columns(2)->schema([
                    TextInput::make('sort_order')
                        ->required()
                        ->numeric()
                        ->default(0),
                    Toggle::make('is_active')
                        ->required()
                        ->default(true),

                ]),
                Section::make('SEO Settings')->columnSpanFull()->columns(2)->schema([
                    TextInput::make('meta_title')
                        ->default(null),
                    Textarea::make('meta_description')
                        ->columnSpanFull()
                        ->default(null),
                ])
            ]);
    }
}
