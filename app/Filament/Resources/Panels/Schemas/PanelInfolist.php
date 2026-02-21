<?php

namespace App\Filament\Resources\Panels\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PanelInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('title')
                ->label('Título'),
            TextEntry::make('hash')
                ->label('Hash')
                ->badge(),
            TextEntry::make('panelGroup.title')
                ->label('Grupo')
                ->placeholder('Sem grupo'),
            TextEntry::make('user.name')
                ->label('Criado por'),
            IconEntry::make('status')
                ->label('Ativo')
                ->boolean(),
            TextEntry::make('created_at')
                ->label('Criado em')
                ->dateTime('d/m/Y H:i'),
            TextEntry::make('updated_at')
                ->label('Atualizado em')
                ->dateTime('d/m/Y H:i'),
        ]);
    }
}
