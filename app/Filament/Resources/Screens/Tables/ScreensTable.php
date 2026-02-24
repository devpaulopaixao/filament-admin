<?php

namespace App\Filament\Resources\Screens\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
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

class ScreensTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Tela')
                    ->weight(FontWeight::Medium)
                    ->description(function ($record) {
                        if ($record->panel && $record->panel->panelGroup) {
                            return '[' . $record->panel->panelGroup->title . '] ' . $record->panel->title;
                        }
                        return $record->panel?->title ?? 'Sem painel';
                    })
                    ->searchable(),
                IconColumn::make('status')
                    ->label('Ativa')
                    ->boolean(),
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
                    ->trueLabel('Ativas')
                    ->falseLabel('Inativas'),
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
                    DeleteAction::make()
                        ->visible(function ($record) {
                            $user = auth()->user();
                            return $user->hasRole('super_admin') || $record->user_id === $user->id;
                        }),
                    Action::make('open_display')
                        ->label('Abrir exibição')
                        ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                        ->url(fn ($record) => url('/tela/' . $record->id))
                        ->openUrlInNewTab(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}