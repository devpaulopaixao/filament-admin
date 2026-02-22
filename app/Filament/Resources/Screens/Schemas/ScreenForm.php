<?php

namespace App\Filament\Resources\Screens\Schemas;

use App\Models\Panel;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ScreenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identificação')
                ->description('Informações básicas da tela.')
                ->icon(Heroicon::OutlinedTv)
                ->schema([
                    TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Toggle::make('status')
                        ->label('Tela ativa')
                        ->helperText('Quando desativada, a exibição pública mostrará uma tela inativa.')
                        ->default(true),
                ])
                ->columns(2),

            Section::make('Configuração')
                ->description('Defina o painel que será exibido nesta tela.')
                ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                ->schema([
                    Select::make('panel_id')
                        ->label('Painel')
                        ->options(function () {
                            $user = auth()->user();

                            $query = Panel::with('panelGroup');

                            if (! $user->can('ViewAny:Panel')) {
                                $query->where(function ($q) use ($user) {
                                    $q->where('user_id', $user->id)
                                        ->orWhere(function ($q) use ($user) {
                                            $q->whereNull('panel_group_id')
                                                ->whereHas('allowedUsers', function ($q) use ($user) {
                                                    $q->where('users.id', $user->id);
                                                });
                                        })
                                        ->orWhere(function ($q) use ($user) {
                                            $q->whereNotNull('panel_group_id')
                                                ->whereHas('panelGroup.allowedUsers', function ($q) use ($user) {
                                                    $q->where('users.id', $user->id);
                                                });
                                        });
                                });
                            }

                            return $query->get()->mapWithKeys(function ($panel) {
                                $label = $panel->title;
                                if ($panel->panelGroup) {
                                    $label = '[' . $panel->panelGroup->title . '] ' . $label;
                                }
                                return [$panel->id => $label];
                            });
                        })
                        ->required()
                        ->searchable()
                        ->columnSpanFull(),
                ]),

            Section::make('Acesso')
                ->description('Defina quais utilizadores podem visualizar esta tela.')
                ->icon(Heroicon::OutlinedShieldCheck)
                ->schema([
                    // Apenas no create — no edit é gerida pelo RelationManager
                    CheckboxList::make('allowedUsers')
                        ->label('Utilizadores com acesso de visualização')
                        ->helperText('Marque os utilizadores que podem visualizar esta tela. Deixe vazio para que apenas o criador tenha acesso.')
                        ->relationship('allowedUsers', 'name')
                        ->searchable()
                        ->bulkToggleable()
                        ->columns(2)
                        ->columnSpanFull()
                        ->visibleOn('create'),
                ])
                ->visible(function ($record) {
                    $user = auth()->user();
                    return $user->hasRole('super_admin')
                        || $record === null
                        || $record->user_id === $user->id;
                }),
        ]);
    }
}