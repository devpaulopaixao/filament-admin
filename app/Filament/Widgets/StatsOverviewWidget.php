<?php

namespace App\Filament\Widgets;

use App\Models\Panel;
use App\Models\PanelGroup;
use App\Models\PanelLink;
use App\Models\Screen;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = -1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $user    = auth()->user();
        $isAdmin = $user->hasRole('super_admin') || $user->can('ViewAny:Panel');

        if ($isAdmin) {
            $totalPanels   = Panel::count();
            $activePanels  = Panel::where('status', true)->count();
            $totalLinks    = PanelLink::where('status', true)->count();
            $totalScreens  = Screen::count();
            $activeScreens = Screen::where('status', true)->count();
            $totalGroups   = PanelGroup::count();
            $totalUsers    = User::count();
        } else {
            $panelIds = Panel::where(function ($q) use ($user) {
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
            })->pluck('id');

            $totalPanels   = $panelIds->count();
            $activePanels  = Panel::whereIn('id', $panelIds)->where('status', true)->count();
            $totalLinks    = PanelLink::whereIn('panel_id', $panelIds)->where('status', true)->count();

            $screenBase    = Screen::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('allowedUsers', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    });
            });
            $totalScreens  = (clone $screenBase)->count();
            $activeScreens = (clone $screenBase)->where('status', true)->count();

            $totalGroups   = null;
            $totalUsers    = null;
        }

        $stats = [
            Stat::make('Painéis', $totalPanels)
                ->description($activePanels . ' ativos')
                ->icon(Heroicon::OutlinedComputerDesktop)
                ->color('primary'),

            Stat::make('Links ativos', $totalLinks)
                ->description('distribuídos nos painéis')
                ->icon(Heroicon::OutlinedGlobeAlt)
                ->color('info'),

            Stat::make('Telas', $totalScreens)
                ->description($activeScreens . ' ativas')
                ->icon(Heroicon::OutlinedTv)
                ->color('success'),
        ];

        if ($isAdmin) {
            $stats[] = Stat::make('Grupos de Painéis', $totalGroups)
                ->description('grupos de organização')
                ->icon(Heroicon::OutlinedRectangleStack)
                ->color('warning');

            $stats[] = Stat::make('Utilizadores', $totalUsers)
                ->description('registrados no sistema')
                ->icon(Heroicon::OutlinedUserGroup)
                ->color('gray');
        }

        return $stats;
    }
}
