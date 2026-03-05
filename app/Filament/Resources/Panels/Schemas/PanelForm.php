<?php

namespace App\Filament\Resources\Panels\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;

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
                    Toggle::make('show_title')
                        ->label('Exibir título do link')
                        ->helperText('Mostra o título do link atual na barra de estado da exibição pública. Obrigatório quando os controles de navegação estão ativos.')
                        ->default(true)
                        ->disabled(fn ($get) => $get('show_controls'))
                        ->dehydrated(true),
                    Toggle::make('show_controls')
                        ->label('Exibir controles de navegação')
                        ->helperText('Mostra botões de pausar, avançar e retroceder na exibição pública.')
                        ->default(false)
                        ->live()
                        ->afterStateUpdated(function ($state, $set) {
                            if ($state) {
                                $set('show_title', true);
                            }
                        }),
                    Toggle::make('blocked')
                        ->label('Proteger com senha')
                        ->helperText('Quando ativado, a exibição pública exigirá uma senha para acesso.')
                        ->default(false)
                        ->live(),
                    TextInput::make('password')
                        ->label('Senha de acesso')
                        ->password()
                        ->revealable()
                        ->helperText('Deixe em branco para manter a senha atual.')
                        ->visible(fn ($get) => (bool) $get('blocked'))
                        ->required(fn ($operation, $get) => $operation === 'create' && (bool) $get('blocked'))
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state)),
                ])
                ->columns(2),

            Section::make('Acesso')
                ->description('Defina quais utilizadores podem visualizar este painel.')
                ->icon(Heroicon::OutlinedShieldCheck)
                ->schema([
                    // Aviso informativo quando o painel pertence a um grupo (visível a não-admins no edit)
                    Placeholder::make('group_access_notice')
                        ->label('Acesso herdado do grupo')
                        ->content(function ($record) {
                            $group = $record?->panelGroup?->title ?? 'grupo';
                            return 'Este painel pertence ao grupo "' . $group . '". O acesso é gerido a nível do grupo.';
                        })
                        ->visible(function ($record) {
                            if ($record === null || is_null($record->panel_group_id)) {
                                return false;
                            }
                            $user = auth()->user();
                            return ! $user->hasRole('super_admin');
                        })
                        ->columnSpanFull(),

                    // Lista de utilizadores — apenas no create (no edit é gerida pelo RelationManager)
                    CheckboxList::make('allowedUsers')
                        ->label('Utilizadores com acesso de visualização')
                        ->helperText('Marque os utilizadores que podem visualizar este painel. Deixe vazio para que apenas o criador tenha acesso.')
                        ->relationship('allowedUsers', 'name')
                        ->searchable()
                        ->bulkToggleable()
                        ->columns(2)
                        ->columnSpanFull()
                        ->visibleOn('create'),
                ])
                ->visible(function ($record) {
                    // No create: sempre visível
                    if ($record === null) {
                        return true;
                    }
                    // No edit: visível para super_admin, criador, ou para mostrar o aviso de grupo
                    $user = auth()->user();
                    return $user->hasRole('super_admin')
                        || $record->user_id === $user->id
                        || ! is_null($record->panel_group_id);
                }),
        ]);
    }
}
