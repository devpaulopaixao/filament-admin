<?php

namespace App\Filament\Resources\Screens\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ScreenInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('title')
                ->label('Título'),
            TextEntry::make('panel.title')
                ->label('Painel')
                ->formatStateUsing(function ($state, $record) {
                    if ($record->panel && $record->panel->panelGroup) {
                        return '[' . $record->panel->panelGroup->title . '] ' . $state;
                    }
                    return $state;
                }),
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
