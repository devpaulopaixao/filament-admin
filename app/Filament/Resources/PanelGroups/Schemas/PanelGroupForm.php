<?php

namespace App\Filament\Resources\PanelGroups\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PanelGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dados do grupo')
                ->schema([
                    TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Toggle::make('status')
                        ->label('Ativo')
                        ->default(true),
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
