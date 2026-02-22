<?php

namespace App\Filament\Resources\Screens\Widgets;

use App\Models\ScreenAccessLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class ScreenAccessChartWidget extends ChartWidget
{
    protected ?string $heading = 'Acessos à exibição';

    protected int | string | array $columnSpan = 'full';

    public ?Model $record = null;

    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return [
            '7'   => 'Últimos 7 dias',
            '30'  => 'Últimos 30 dias',
            '90'  => 'Últimos 90 dias',
        ];
    }

    protected function getData(): array
    {
        $days     = (int) ($this->filter ?? 30);
        $screenId = $this->record?->id;
        $from     = now()->subDays($days - 1)->startOfDay();

        $logs = ScreenAccessLog::where('screen_id', $screenId)
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $labels = [];
        $data   = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date     = now()->subDays($i)->format('Y-m-d');
            $label    = now()->subDays($i)->format('d/m');
            $labels[] = $label;
            $data[]   = $logs[$date] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Acessos',
                    'data'            => $data,
                    'backgroundColor' => 'rgba(20, 184, 166, 0.15)',
                    'borderColor'     => 'rgb(20, 184, 166)',
                    'borderWidth'     => 2,
                    'fill'            => true,
                    'tension'         => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}