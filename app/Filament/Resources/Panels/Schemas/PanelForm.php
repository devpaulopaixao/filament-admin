<?php

namespace App\Filament\Resources\Panels\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PanelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identificação')
                ->description('Informações básicas do painel e sua organização em grupos.')
                ->icon(Heroicon::OutlinedComputerDesktop)
                ->schema([
                    TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('hash')
                        ->label('Hash de acesso')
                        ->helperText('Identificador único gerado automaticamente, usado na URL pública do painel.')
                        ->readOnly()
                        ->dehydrated(false)
                        ->visibleOn('edit'),
                    Select::make('panel_group_id')
                        ->label('Grupo')
                        ->relationship('panelGroup', 'title')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->placeholder('Sem grupo'),
                ])
                ->columns(2),

            Section::make('Configurações')
                ->description('Comportamento e visibilidade do painel na exibição pública.')
                ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                ->schema([
                    Toggle::make('status')
                        ->label('Painel ativo')
                        ->helperText('Quando desativado, a exibição pública mostrará uma tela de painel inativo.')
                        ->default(true),
                    Toggle::make('show_controls')
                        ->label('Exibir controles de navegação')
                        ->helperText('Mostra botões de pausar, avançar e retroceder na exibição pública.')
                        ->default(false),
                ])
                ->columns(2),

            Section::make('Acesso')
                ->description('Defina quais utilizadores podem visualizar este painel.')
                ->icon(Heroicon::OutlinedUserGroup)
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
