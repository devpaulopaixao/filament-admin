<?php

namespace App\Filament\Resources\PanelGroups\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PanelGroupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('title')
                ->label('Título'),
            TextEntry::make('user.name')
                ->label('Criado por'),
            IconEntry::make('status')
                ->label('Ativo')
                ->boolean(),
            TextEntry::make('panels_count')
                ->label('Painéis')
                ->getStateUsing(function ($record) {
                    return $record->panels()->count();
                }),
            TextEntry::make('created_at')
                ->label('Criado em')
                ->dateTime('d/m/Y H:i'),
            TextEntry::make('updated_at')
                ->label('Atualizado em')
                ->dateTime('d/m/Y H:i'),
        ]);
    }
}
