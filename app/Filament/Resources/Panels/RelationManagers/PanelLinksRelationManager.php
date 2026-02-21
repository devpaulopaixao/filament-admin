<?php

namespace App\Filament\Resources\Panels\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PanelLinksRelationManager extends RelationManager
{
    protected static string $relationship = 'links';

    protected static ?string $title = 'Links';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Título')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            TextInput::make('url')
                ->label('URL')
                ->required()
                ->url()
                ->maxLength(2048)
                ->columnSpanFull(),
            TimePicker::make('duration_time')
                ->label('Duração')
                ->seconds(false)
                ->displayFormat('H:i')
                ->format('H:i')
                ->required(),
            Toggle::make('status')
                ->label('Ativo')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('display_number')
            ->columns([
                TextColumn::make('display_number')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                TextColumn::make('url')
                    ->label('URL')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('duration_time')
                    ->label('Duração'),
                IconColumn::make('status')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                CreateAction::make(),
                DeleteBulkAction::make(),
            ]);
    }
}
