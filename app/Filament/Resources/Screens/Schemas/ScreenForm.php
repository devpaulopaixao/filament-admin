<?php

namespace App\Filament\Resources\Screens\Schemas;

use App\Models\Panel;
use App\Models\PanelGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ScreenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados da tela')
                ->schema([
                    TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255),
                    Toggle::make('status')
                        ->label('Ativo')
                        ->default(true),
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
                ])
                ->columns(2),

            Section::make('Acesso')
                ->schema([
                    Select::make('allowedUsers')
                        ->label('Utilizadores com acesso de visualização')
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

                            return $record === null || $record->user_id === $user->id;
                        }),
                ]),
        ]);
    }
}
