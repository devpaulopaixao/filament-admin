<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function components(): array
    {
        return [
            Section::make('Dados pessoais')
                ->schema([
                    TextInput::make('name')
                        ->label('Nome')
                        ->required(),
                    TextInput::make('email')
                        ->label('E-mail')
                        ->email()
                        ->unique(null, null, null, true)
                        ->required(),
                ])
                ->columns(['default' => 2]),

            Section::make('Senha')
                ->schema([
                    TextInput::make('password')
                        ->label('Senha')
                        ->password()
                        ->revealable()
                        ->required(function ($operation) {
                            return $operation === 'create';
                        })
                        ->dehydrated(function ($state) {
                            return filled($state);
                        })
                        ->confirmed(),
                    TextInput::make('password_confirmation')
                        ->label('Confirmar senha')
                        ->password()
                        ->revealable()
                        ->required(function ($operation) {
                            return $operation === 'create';
                        })
                        ->dehydrated(false),
                ])
                ->columns(['default' => 2]),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components(static::components());
    }
}
