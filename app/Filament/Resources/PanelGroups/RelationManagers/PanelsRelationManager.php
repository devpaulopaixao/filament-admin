<?php

namespace App\Filament\Resources\PanelGroups\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PanelsRelationManager extends RelationManager
{
    protected static string $relationship = 'panels';

    protected static ?string $title = 'Painéis';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Título')
                ->required()
                ->maxLength(255),
            Toggle::make('status')
                ->label('Ativo')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                TextColumn::make('hash')
                    ->label('Hash')
                    ->badge(),
                IconColumn::make('status')
                    ->label('Ativo')
                    ->boolean(),
                TextColumn::make('links_count')
                    ->label('Links')
                    ->counts('links')
                    ->badge(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
