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
                        TextInput::make('slug')
                            ->unique(ignoreRecord: true)
                            ->visibleOn('edit')
                            ->readOnly(),
                        TextInput::make('name')
                            ->required(),
                        Textarea::make('description')
                            ->default(null)
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->disk('public')
                            ->directory('categories')
                            ->imageEditor()
                            ->downloadable()
                            ->preserveFilenames()
                            ->image(),
                    ]),
                Section::make('Display Settings')->columns(2)->schema([
                    TextInput::make('sort_order')
                        ->required()
                        ->numeric()
                        ->default(0),
                    Toggle::make('is_active')
                        ->required(),

                ]),
                Section::make('SEO Settings')->schema([
                    TextInput::make('meta_title')
                        ->default(null),
                    TextInput::make('meta_description')
                        ->default(null),
                ])
            ]);
    }
}
