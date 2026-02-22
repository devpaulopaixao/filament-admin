<?php

namespace App\Filament\Resources\Screens\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AllowedUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'allowedUsers';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Utilizadores com Acesso';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $ownerRecord->user_id === $user->id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->weight(FontWeight::Medium)
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Perfil')
                    ->badge(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Adicionar utilizador')
                    ->preloadRecordSelect()
                    ->multiple()
                    ->recordSelectSearchColumns(['name', 'email'])
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        $attachedIds = $this->getOwnerRecord()->allowedUsers()->pluck('users.id');
                        return $query->whereNotIn('id', $attachedIds);
                    }),
            ])
            ->recordActions([
                DetachAction::make()
                    ->label('Remover'),
            ])
            ->toolbarActions([
                DetachBulkAction::make()
                    ->label('Remover selecionados'),
            ]);
    }
}