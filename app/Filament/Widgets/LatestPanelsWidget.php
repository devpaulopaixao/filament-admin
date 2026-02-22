<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Panels\PanelResource;
use App\Models\Panel;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestPanelsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 1;

    protected static ?string $heading = 'Painéis recentes';

    public function table(Table $table): Table
    {
        $user    = auth()->user();
        $isAdmin = $user->hasRole('super_admin') || $user->can('ViewAny:Panel');

        $query = Panel::query()->latest();

        if (! $isAdmin) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere(function ($q) use ($user) {
                        $q->whereNull('panel_group_id')
                            ->whereHas('allowedUsers', function ($q) use ($user) {
                                $q->where('users.id', $user->id);
                            });
                    })
                    ->orWhere(function ($q) use ($user) {
                        $q->whereNotNull('panel_group_id')
                            ->whereHas('panelGroup.allowedUsers', function ($q) use ($user) {
                                $q->where('users.id', $user->id);
                            });
                    });
            });
        }

        return $table
            ->query($query->limit(5))
            ->paginated(false)
            ->recordUrl(function ($record) {
                return PanelResource::getUrl('view', ['record' => $record]);
            })
            ->columns([
                TextColumn::make('title')
                    ->label('Painel')
                    ->weight(FontWeight::Medium)
                    ->description(function ($record) {
                        return $record->panelGroup?->title ?? 'Sem grupo';
                    })
                    ->searchable(false),
                TextColumn::make('hash')
                    ->label('Hash')
                    ->badge()
                    ->color('gray'),
                IconColumn::make('status')
                    ->label('Ativo')
                    ->boolean(),
                TextColumn::make('links_count')
                    ->label('Links')
                    ->counts('links')
                    ->badge()
                    ->color('info'),
            ]);
    }
}