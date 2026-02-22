<?php

namespace App\Filament\Resources\Panels\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PanelInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identificação')
                ->icon(Heroicon::OutlinedComputerDesktop)
                ->schema([
                    TextEntry::make('title')
                        ->label('Título'),
                    TextEntry::make('panelGroup.title')
                        ->label('Grupo')
                        ->placeholder('Sem grupo'),
                    TextEntry::make('user.name')
                        ->label('Criado por'),
                ])
                ->columns(2),

            Section::make('Configurações')
                ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                ->schema([
                    IconEntry::make('status')
                        ->label('Painel ativo')
                        ->boolean(),
                    IconEntry::make('show_controls')
                        ->label('Controles de navegação')
                        ->boolean(),
                ])
                ->columns(2),

            Section::make('Acesso')
                ->icon(Heroicon::OutlinedShieldCheck)
                ->schema([
                    // Painel com grupo: acesso herdado
                    TextEntry::make('panelGroup.title')
                        ->label('Acesso herdado do grupo')
                        ->badge()
                        ->color('primary')
                        ->visible(fn ($record) => ! is_null($record->panel_group_id))
                        ->columnSpanFull(),

                    // Painel sem grupo: utilizadores com acesso direto
                    TextEntry::make('allowedUsers.name')
                        ->label('Utilizadores com acesso direto')
                        ->badge()
                        ->color('gray')
                        ->placeholder('Apenas o criador')
                        ->visible(fn ($record) => is_null($record->panel_group_id))
                        ->columnSpanFull(),
                ]),

            Section::make('Registo')
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
