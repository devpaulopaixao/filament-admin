<?php

namespace App\Filament\Resources\Panels\Tables;

use App\Models\PanelGroup;
use App\Models\User;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PanelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                TextColumn::make('hash')
                    ->label('Hash')
                    ->badge(),
                TextColumn::make('panelGroup.title')
                    ->label('Grupo')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Criado por')
                    ->searchable(),
                IconColumn::make('status')
                    ->label('Ativo')
                    ->boolean(),
                TextColumn::make('links_count')
                    ->label('Links')
                    ->counts('links')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('panel_group_id')
                    ->label('Tem grupo')
                    ->nullable()
                    ->trueLabel('Com grupo')
                    ->falseLabel('Sem grupo'),
                TernaryFilter::make('status')
                    ->label('Estado')
                    ->trueLabel('Ativos')
                    ->falseLabel('Inativos'),
                SelectFilter::make('panel_group_id')
                    ->label('Grupo')
                    ->options(PanelGroup::orderBy('title')->pluck('title', 'id'))
                    ->searchable(),
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
