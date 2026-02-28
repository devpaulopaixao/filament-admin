<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use STS\FilamentImpersonate\Actions\Impersonate;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('roles')
                    ->label('Perfil')
                    ->relationship('roles', 'name')
                    ->options(Role::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Impersonate::make(),
                    ViewAction::make()
                        ->label('Visualizar'),
                    EditAction::make()
                        ->label('Editar')
                        ->visible(fn ($record) => !$record->trashed()),
                    UserResource::duplicateAction()
                        ->label('Duplicar')
                        ->visible(fn ($record) => !$record->trashed()),
                    DeleteAction::make()
                        ->label('Excluir')
                        ->visible(fn ($record) => !$record->trashed()),
                    RestoreAction::make()
                        ->label('Restaurar')
                        ->visible(fn ($record) => $record->trashed()),
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
