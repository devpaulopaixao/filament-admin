<?php

namespace App\Filament\Resources\Screens\Widgets;

use App\Models\ScreenAccessLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class ScreenAccessStatsWidget extends StatsOverviewWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        $screenId = $this->record?->id;

        $total   = ScreenAccessLog::where('screen_id', $screenId)->count();
        $last24h = ScreenAccessLog::where('screen_id', $screenId)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();
        $last7d  = ScreenAccessLog::where('screen_id', $screenId)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $devices = ScreenAccessLog::where('screen_id', $screenId)
            ->selectRaw('device_type, COUNT(*) as total')
            ->groupBy('device_type')
            ->orderByDesc('total')
            ->first();

        $topDevice = match ($devices?->device_type) {
            'mobile'  => 'Mobile',
            'tablet'  => 'Tablet',
            'desktop' => 'Desktop',
            default   => '—',
        };

        return [
            Stat::make('Total de acessos', $total)
                ->description('desde o início do registo')
                ->color('primary'),

            Stat::make('Últimas 24 horas', $last24h)
                ->description('acessos recentes')
                ->color('info'),

            Stat::make('Últimos 7 dias', $last7d)
                ->description('acessos na semana')
                ->color('success'),

            Stat::make('Dispositivo mais usado', $topDevice)
                ->description('com base em todos os acessos')
                ->color('gray'),
        ];
    }
}