<?php

namespace App\Filament\Resources\Panels\Schemas;

use App\Models\PanelGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PanelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados do painel')
                ->schema([
                    TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('hash')
                        ->label('Hash')
                        ->readOnly()
                        ->dehydrated(false)
                        ->visibleOn('edit'),
                    Toggle::make('status')
                        ->label('Ativo')
                        ->default(true),
                    Select::make('panel_group_id')
                        ->label('Grupo')
                        ->relationship('panelGroup', 'title')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->placeholder('Sem grupo'),
                ])
                ->columns(2),

            Section::make('Acesso')
                ->schema([
                    Select::make('allowedUsers')
                        ->label('Utilizadores com acesso de visualização')
                        ->helperText('Apenas disponível para painéis sem grupo. Painéis num grupo herdam o acesso do grupo.')
                        ->multiple()
                        ->relationship('allowedUsers', 'name')
                        ->searchable()
                        ->preload()
                        ->columnSpanFull()
                        ->visible(function ($record) {
                            $user = auth()->user();

                            if ($user->hasRole('super_admin')) {
                                return true;
                            }

                            if ($record !== null && ! is_null($record->panel_group_id)) {
                                return false;
                            }

                            return $record === null || $record->user_id === $user->id;
                        }),
                ]),
        ]);
    }
}
