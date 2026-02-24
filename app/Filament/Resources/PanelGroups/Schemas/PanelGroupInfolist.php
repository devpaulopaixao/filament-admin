<?php

namespace App\Filament\Resources\PanelGroups\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PanelGroupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identificação')
                ->description('Informações básicas do grupo.')
                ->icon(Heroicon::OutlinedRectangleStack)
                ->schema([
                    TextEntry::make('title')
                        ->label('Título'),
                    IconEntry::make('status')
                        ->label('Ativo')
                        ->boolean(),
                ])
                ->columns(2),

            Section::make('Configuração')
                ->description('Criador e métricas do grupo.')
                ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                ->schema([
                    TextEntry::make('user.name')
                        ->label('Criado por'),
                    TextEntry::make('panels_count')
                        ->label('Total de painéis')
                        ->getStateUsing(fn ($record) => $record->panels()->count())
                        ->badge()
                        ->color('info'),
                ])
                ->columns(2),

            Section::make('Acesso')
                ->description('Utilizadores com permissão de visualização dos painéis do grupo.')
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