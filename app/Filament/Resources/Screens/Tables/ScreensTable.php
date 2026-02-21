<?php

namespace App\Filament\Resources\Screens\Tables;

use App\Models\User;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ScreensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                TextColumn::make('panel.title')
                    ->label('Painel')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->panel && $record->panel->panelGroup) {
                            return '[' . $record->panel->panelGroup->title . '] ' . $state;
                        }
                        return $state;
                    })
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Criado por')
                    ->searchable(),
                IconColumn::make('status')
                    ->label('Ativo')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('status')
                    ->label('Estado')
                    ->trueLabel('Ativos')
                    ->falseLabel('Inativos'),
                SelectFilter::make('user_id')
                    ->label('Criador')
                    ->options(User::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(function ($record) {
                        $user = auth()->user();
                        return $user->hasRole('super_admin') || $record->user_id === $user->id;
                    }),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
