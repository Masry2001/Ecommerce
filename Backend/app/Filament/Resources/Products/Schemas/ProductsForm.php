<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\KeyValue;
use Illuminate\Support\Str;

class ProductsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Product Information')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Basic Information')
                            ->icon(Heroicon::InformationCircle)
                            ->schema([
                                Section::make('Product Details')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(50),
                                        TextInput::make('slug')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->visibleOn('edit')
                                            ->maxLength(50),
                                        Select::make('category_id')
                                            ->required()
                                            ->relationship('category', 'name')
                                            ->preload()
                                            ->searchable()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(20),
                                            ]),
                                        Select::make('brand_id')
                                            ->relationship('brand', 'name')
                                            ->default(null)
                                            ->preload()
                                            ->searchable()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(20),
                                            ]),

                                    ]),
                                Section::make('Product Description')
                                    ->schema([
                                        Textarea::make('short_description')
                                            ->maxLength(500)
                                            ->columnSpanFull(),
                                        RichEditor::make('description')
                                            ->maxLength(5000)
                                            ->columnSpanFull(),
                                    ])

                            ]),
                        Tab::make('Pricing & Inventory')
                            ->icon(Heroicon::CurrencyDollar)
                            ->schema([
                                Section::make('Pricing')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('sku')
                                            ->label('SKU')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->placeholder('TSH-BLK-S')
                                            ->helperText('Stock Keeping Unit - Unique identifier for the product, e.g. a small black T-shirt might be TSH-BLK-S')
                                            ->maxLength(20)
                                            ->live(onBlur: true),
                                        TextInput::make('price')
                                            ->required()
                                            ->helperText('The Selling Price')
                                            ->numeric()
                                            ->minValue(0)
                                            ->prefix('£'),
                                        TextInput::make('compare_price')
                                            ->label('Compare at price')
                                            ->helperText('The Price it was before, the original price')
                                            ->numeric()
                                            ->minValue(0)
                                            ->prefix('£'),
                                        TextInput::make('cost_price')
                                            ->label('Cost per item')
                                            ->helperText('The Price you paid for the item (it will be used to calculate profit)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->prefix('£'),
                                    ]),
                                Section::make('Inventory')
                                    ->columns(2)
                                    ->schema([
                                        Toggle::make('manage_stock')
                                            ->columnSpanFull()
                                            ->required()
                                            ->default(false)
                                            ->helperText('Enable stock management for this product')
                                            ->live()
                                            ->afterStateUpdated(fn($get, $set) => self::refreshStockStatus($get, $set)),
                                        TextInput::make('stock_quantity')
                                            ->required(fn($get) => $get('manage_stock'))
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->visible(fn($get) => $get('manage_stock'))
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn($get, $set) => self::refreshStockStatus($get, $set)),
                                        TextInput::make('low_stock_threshold')
                                            ->required(fn($get) => $get('manage_stock'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(10)
                                            ->visible(fn($get) => $get('manage_stock'))
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn($get, $set) => self::refreshStockStatus($get, $set)),
                                        ToggleButtons::make('stock_status')
                                            ->columnSpanFull()
                                            ->required(fn($get) => $get('manage_stock'))
                                            ->options([
                                                'in_stock' => 'In Stock',
                                                'out_of_stock' => 'Out of Stock',
                                                'low_stock' => 'Low Stock',
                                                'on_backorder' => 'On Backorder',
                                            ])
                                            ->visible(fn($get) => $get('manage_stock'))
                                            ->default('out_of_stock')
                                            ->grouped(),
                                        TextInput::make('weight')
                                            ->label('Weight (kg)')
                                            ->helperText('Weight of the product in kilograms used for shipping calculation max 99.99 kg')
                                            ->placeholder('25.00')
                                            ->required(fn($get) => $get('manage_stock'))
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(99.99)
                                            ->visible(fn($get) => $get('manage_stock')),

                                    ])
                            ]),
                        Tab::make('Images & Media')
                            ->icon(Heroicon::Photo)
                            ->schema([
                                Section::make('Product Images')
                                    ->columns(2)
                                    ->description('Upload images of the product. The first image will be set as the primary (cover) image.')
                                    ->schema([
                                        Repeater::make('images')
                                            ->relationship('images')
                                            ->columnSpanFull()
                                            ->reorderable('sort_order')
                                            ->schema([
                                                FileUpload::make('image_path')
                                                    ->label('Image')
                                                    ->image()
                                                    ->preserveFilenames()
                                                    ->imageEditor()
                                                    ->downloadable()
                                                    ->maxSize(2048)
                                                    ->disk('public')
                                                    ->directory('product-images')
                                                    ->visibility('public')
                                                    ->columnSpanFull(),
                                                TextInput::make('alt_text')
                                                    ->label('Alt Text')
                                                    ->placeholder('Description for screen readers')
                                                    ->maxLength(125)
                                                    ->columnSpanFull(),
                                                Toggle::make('is_primary')
                                                    ->label('Primary Image')
                                                    ->columnSpanFull(),
                                            ])
                                            ->itemLabel(fn($state) => $state['alt_text'] ?? 'Product Image')
                                            ->collapsible()
                                            ->defaultItems(0),
                                    ]),
                            ]),
                        Tab::make('Product Variants')
                            ->icon(Heroicon::Squares2x2)
                            ->schema([
                                Toggle::make('has_variants')
                                    ->required()
                                    ->live(),
                                Section::make('Product Variants')
                                    ->description('Add variants to the product, like different sizes or colors')
                                    ->columnSpanFull()
                                    ->schema([
                                        Repeater::make('variants')
                                            ->addActionLabel('Add variants to this product')
                                            ->collapsible()
                                            ->itemLabel(fn($state) => $state['name'] ?? 'New variant')
                                            ->defaultItems(0)
                                            ->columnSpanFull()
                                            ->columns(2)
                                            ->relationship('variants')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->label('Variant Name')
                                                    ->placeholder('e.g. Red, Large'),
                                                TextInput::make('sku')
                                                    ->required()
                                                    ->label('SKU')
                                                    ->unique(ignoreRecord: true)
                                                    ->helperText('Unique identifier for the variant')
                                                    ->placeholder('e.g. RED-LARGE')
                                                    ->default(function (callable $get) {
                                                        $mainSku = $get('../../sku');
                                                        return $mainSku ? $mainSku . '-NEW' : 'VAR-' . strtoupper(Str::random(4));
                                                    }),
                                                KeyValue::make('options')
                                                    ->columnSpan(2),
                                                TextInput::make('price')
                                                    ->required()
                                                    ->label('Price')
                                                    ->numeric()
                                                    ->prefix('EGP')
                                                    ->minValue(0)
                                                    ->placeholder('e.g. 100'),
                                                TextInput::make('compare_price')
                                                    ->required()
                                                    ->label('Compare at Price')
                                                    ->numeric()
                                                    ->prefix('EGP')
                                                    ->minValue(0)
                                                    ->placeholder('e.g. 100'),
                                                TextInput::make('stock_quantity')
                                                    ->required()
                                                    ->label('Stock Quantity')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->placeholder('e.g. 10'),
                                                Select::make('stock_status')
                                                    ->options([
                                                        'in_stock' => 'In Stock',
                                                        'out_of_stock' => 'Out of Stock',
                                                        'low_stock' => 'Low Stock',
                                                        'on_backorder' => 'On Backorder',
                                                    ])
                                                    ->default('in_stock')
                                                    ->required()
                                                    ->native(false),
                                                Toggle::make('is_active')
                                                    ->default(true)
                                                    ->required(),
                                            ])
                                            ->required(),
                                    ])
                                    ->visible(fn($get) => $get('has_variants')),


                            ]),
                        Tab::make('Settings')
                            ->icon(Heroicon::Cog)
                            ->schema([
                                Section::make('Product Status')
                                    ->columns(2)
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->default(true)
                                            ->required(),
                                        Toggle::make('is_featured')
                                            ->default(false)
                                            ->required(),

                                    ]),
                                Section::make('Product Statistics')
                                    ->columns(2)
                                    ->schema([
                                        Placeholder::make('views_count')
                                            ->content(fn($record) => $record?->views_count ?? 0),
                                        Placeholder::make('created_at')
                                            ->content(fn($record) => $record?->created_at?->diffForHumans() ?? 'N/A'),
                                    ]),
                            ]),
                        Tab::make('SEO & Metadata')
                            ->icon(Heroicon::GlobeAlt)
                            ->schema([
                                Section::make('Search Engine Optimization')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('meta_title')
                                            ->label('Meta Title')
                                            ->maxLength(70)
                                            ->helperText('The title that appears in search engine results and browser tabs (max 70 characters)'),
                                        TextInput::make('meta_description')
                                            ->label('Meta Description')
                                            ->maxLength(160)
                                            ->helperText('The description that appears in search engine results (max 160 characters)'),
                                    ]),
                            ]),
                    ])
            ]);
    }

    protected static function refreshStockStatus($get, $set): void
    {
        if (! $get('manage_stock')) {
            return;
        }

        $quantity = (int) $get('stock_quantity');
        $threshold = (int) $get('low_stock_threshold');

        $status = match (true) {
            $quantity <= 0 => 'out_of_stock',
            $quantity <= $threshold => 'low_stock',
            default => 'in_stock',
        };

        $set('stock_status', $status);
    }
}
