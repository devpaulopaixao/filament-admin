<?php

namespace App\Filament\Resources\PanelGroups\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PanelGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identificação')
                ->description('Informações básicas do grupo de painéis.')
                ->icon(Heroicon::OutlinedRectangleStack)
                ->schema([
                    TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Toggle::make('status')
                        ->label('Grupo ativo')
                        ->helperText('Quando desativado, os painéis do grupo ficam inacessíveis aos utilizadores com acesso via grupo.')
                        ->default(true),
                ])
                ->columns(2),

            Section::make('Acesso')
                ->description('Defina quais utilizadores podem visualizar os painéis deste grupo.')
                ->icon(Heroicon::OutlinedShieldCheck)
                ->schema([
                    // Apenas no create — no edit é gerida pelo RelationManager
                    CheckboxList::make('allowedUsers')
                        ->label('Utilizadores com acesso de visualização')
                        ->helperText('Marque os utilizadores que podem ver os painéis deste grupo. Deixe vazio para acesso apenas do criador.')
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