<?php

namespace App\Filament\Resources\Panels\Tables;

use App\Models\PanelGroup;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PanelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Painel')
                    ->weight(FontWeight::Medium)
                    ->description(fn ($record) => $record->panelGroup?->title ?? 'Sem grupo')
                    ->searchable(),
                IconColumn::make('status')
                    ->label('Ativo')
                    ->boolean(),
                IconColumn::make('show_controls')
                    ->label('Controles')
                    ->boolean(),
                TextColumn::make('links_count')
                    ->label('Links')
                    ->counts('links')
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
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return $user->hasRole('super_admin') || $record->user_id === $user->id;
                        }),
                    Action::make('open_display')
                        ->label('Abrir exibição')
                        ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                        ->url(fn ($record) => url('/painel/' . $record->hash))
                        ->openUrlInNewTab(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
