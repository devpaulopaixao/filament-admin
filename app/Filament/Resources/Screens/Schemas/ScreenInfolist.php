<?php

namespace App\Filament\Resources\Screens\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ScreenInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identificação')
                ->description('Informações básicas da tela.')
                ->icon(Heroicon::OutlinedTv)
                ->schema([
                    TextEntry::make('title')
                        ->label('Título'),
                    IconEntry::make('status')
                        ->label('Ativa')
                        ->boolean(),
                ])
                ->columns(2),

            Section::make('Configuração')
                ->description('Painel associado a esta tela.')
                ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                ->schema([
                    TextEntry::make('panel.title')
                        ->label('Painel')
                        ->formatStateUsing(function ($state, $record) {
                            if ($record->panel && $record->panel->panelGroup) {
                                return '[' . $record->panel->panelGroup->title . '] ' . $state;
                            }
                            return $state;
                        })
                        ->placeholder('Sem painel associado'),
                    TextEntry::make('user.name')
                        ->label('Criado por'),
                ])
                ->columns(2),

            Section::make('Acesso')
                ->description('Utilizadores com permissão de visualização.')
                ->icon(Heroicon::OutlinedShieldCheck)
                ->schema([
                    TextEntry::make('allowedUsers.name')
                        ->label('Utilizadores com acesso')
                        ->badge()
                        ->placeholder('Apenas o criador')
                        ->columnSpanFull(),
                ]),

            Section::make('Registro')
                ->description('Datas de criação e última atualização.')
                ->icon(Heroicon::OutlinedClock)
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Criado em')
                        ->dateTime('d/m/Y H:i'),
                    TextEntry::make('updated_at')
                        ->label('Atualizado em')
                        ->dateTime('d/m/Y H:i'),
                ])
                ->columns(2),
        ]);
    }
}
