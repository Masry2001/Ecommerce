<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;


class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->searchable()
                    ->url(fn($record) => route('products.edit', $record->product_id))
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make("customer_name")
                    ->searchable()
                    ->url(fn($record) => route('customers.edit', $record->customer_id))
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make("rating")
                    ->formatStateUsing(fn($state) => str_repeat('⭐', $state))
                    ->searchable()
                    ->color('warning')
                    ->sortable(),
                TextColumn::make("title")
                    ->limit(50)
                    ->searchable()
                    ->sortable(),
                TextColumn::make("comment")
                    ->limit(100)
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                TextColumn::make("is_verified_purchase")
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                    ->searchable(),
                TextColumn::make("is_approved")
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => $state ? 'Yes' : 'No')
                    ->searchable(),
                TextColumn::make("created_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make("updated_at")
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_approved')
                    ->label('Approved')
                    ->boolean()
                    ->trueLabel('Approved')
                    ->falseLabel('Not Approved')
                    ->native(false),
                TernaryFilter::make('is_verified_purchase')
                    ->label('Verified Purchase')
                    ->boolean()
                    ->trueLabel('Verified')
                    ->falseLabel('Not Verified')
                    ->native(false),
                SelectFilter::make('rating')
                    ->options([
                        1 => '1 Star',
                        2 => '2 Stars',
                        3 => '3 Stars',
                        4 => '4 Stars',
                        5 => '5 Stars',
                    ])
                    ->native(false),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update(['is_approved' => true]);
                    })
                    ->visible(fn($record) => !$record->is_approved)
                    ->requiresConfirmation(),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function ($record) {
                        $record->update(['is_approved' => false]);
                    })
                    ->visible(fn($record) => $record->is_approved)
                    ->requiresConfirmation(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    // add bulk approve and reject actions
                    BulkAction::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each->update(['is_approved' => true]);
                        })
                        ->visible(fn(Collection $records) => $records->isNotEmpty() && !$records->first()?->is_approved)
                        ->requiresConfirmation(),
                    BulkAction::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            $records->each->update(['is_approved' => false]);
                        })
                        ->visible(fn(Collection $records) => $records->isNotEmpty() && $records->first()?->is_approved)
                        ->requiresConfirmation(),
                ]),
            ]);
    }
}
