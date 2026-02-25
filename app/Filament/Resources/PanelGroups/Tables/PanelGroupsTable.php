<?php

namespace App\Filament\Resources\PanelGroups\Tables;

use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PanelGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Grupo')
                    ->weight(FontWeight::Medium)
                    ->description(fn ($record) => $record->user?->name ?? '')
                    ->searchable(),
                IconColumn::make('status')
                    ->label('Ativo')
                    ->boolean(),
                TextColumn::make('panels_count')
                    ->label('Painéis')
                    ->counts('panels')
                    ->badge()
                    ->color('info'),
                TextColumn::make('user.name')
                    ->label('Criado por')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
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
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Visualizar'),
                    EditAction::make()
                        ->label('Editar')
                        ->visible(function ($record) {
                            if ($record->trashed()) return false;
                            $user = auth()->user();
                            return $user->hasRole('super_admin') || $record->user_id === $user->id;
                        }),
                    DeleteAction::make()
                        ->label('Excluir')
                        ->visible(function ($record) {
                            if ($record->trashed()) return false;
                            $user = auth()->user();
                            return $user->hasRole('super_admin') || $record->user_id === $user->id;
                        }),
                    RestoreAction::make()
                        ->label('Restaurar')
                        ->visible(function ($record) {
                            if (!$record->trashed()) return false;
                            $user = auth()->user();
                            return $user->hasRole('super_admin') || $record->user_id === $user->id;
                        }),
                    ForceDeleteAction::make()
                        ->label('Excluir Permanentemente')
                        ->visible(function ($record) {
                            return $record->trashed() && auth()->user()->hasRole('super_admin');
                        }),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
                ForceDeleteBulkAction::make(),
            ]);
    }
}
