<?php

namespace App\Filament\Resources\PanelGroups\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
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
            Section::make('Identificação')
                ->description('Informações básicas do painel.')
                ->icon(Heroicon::OutlinedComputerDesktop)
                ->schema([
                    TextInput::make('title')
                        ->label('Título')
                        ->placeholder('Ex: Painel de monitoramento')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Section::make('Configurações')
                ->description('Comportamento e visibilidade na exibição pública.')
                ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                ->schema([
                    Toggle::make('status')
                        ->label('Painel ativo')
                        ->helperText('Quando desativado, mostra uma tela de painel inativo.')
                        ->default(true),
                    Toggle::make('show_controls')
                        ->label('Exibir controles de navegação')
                        ->helperText('Mostra botões de pausar, avançar e retroceder.')
                        ->default(false),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->weight(\Filament\Support\Enums\FontWeight::Medium),
                IconColumn::make('status')
                    ->label('Ativo')
                    ->boolean(),
                IconColumn::make('show_controls')
                    ->label('Controles')
                    ->boolean(),
                TextColumn::make('links_count')
                    ->label('Links')
                    ->counts('links')
                    ->badge()
                    ->color('info'),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::Large)
                    ->slideOver(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->label('Novo painel')
                    ->icon(Heroicon::OutlinedPlus)
                    ->modalWidth(Width::Large)
                    ->slideOver()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();

                        return $data;
                    }),
            ]);
    }
}
