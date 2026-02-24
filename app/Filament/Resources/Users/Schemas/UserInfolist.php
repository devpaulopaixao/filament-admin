<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados pessoais')
                    ->description('Informações de identificação do usuário')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nome')
                            ->icon('heroicon-o-user')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('email')
                            ->label('E-mail')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->copyMessage('E-mail copiado!')
                            ->copyMessageDuration(1500),
                    ])
                    ->columns(2),

                Section::make('Verificação & Segurança')
                    ->description('Status de verificação e dados de acesso')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        IconEntry::make('email_verified_at')
                            ->label('E-mail verificado')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-badge')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        TextEntry::make('email_verified_at')
                            ->label('Verificado em')
                            ->icon('heroicon-o-calendar-days')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Não verificado'),
                    ])
                    ->columns(2),

                Section::make('Roles')
                    ->description('Grupos de permissões atribuídos ao usuário')
                    ->icon('heroicon-o-key')
                    ->schema([
                        TextEntry::make('roles.name')
                            ->label('Roles atribuídas')
                            ->badge()
                            ->color('primary')
                            ->placeholder('Sem roles atribuídas'),
                    ]),

                Section::make('Informações do sistema')
                    ->description('Dados de registro e última atualização')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Criado em')
                            ->icon('heroicon-o-clock')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Atualizado em')
                            ->icon('heroicon-o-arrow-path')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }
}
