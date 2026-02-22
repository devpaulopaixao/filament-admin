<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Screens\ScreenResource;
use App\Models\Screen;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestScreensWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 1;

    protected static ?string $heading = 'Telas recentes';

    public function table(Table $table): Table
    {
        $user    = auth()->user();
        $isAdmin = $user->hasRole('super_admin') || $user->can('ViewAny:Screen');

        $query = Screen::query()->with('panel')->latest();

        if (! $isAdmin) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('allowedUsers', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            });
        }

        return $table
            ->query($query->limit(5))
            ->paginated(false)
            ->recordUrl(function ($record) {
                return ScreenResource::getUrl('view', ['record' => $record]);
            })
            ->columns([
                TextColumn::make('title')
                    ->label('Tela')
                    ->weight(FontWeight::Medium)
                    ->searchable(false),
                TextColumn::make('panel.title')
                    ->label('Painel')
                    ->placeholder('Sem painel')
                    ->searchable(false),
                IconColumn::make('status')
                    ->label('Ativa')
                    ->boolean(),
            ]);
    }
}