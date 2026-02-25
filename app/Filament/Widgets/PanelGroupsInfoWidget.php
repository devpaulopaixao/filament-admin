<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class PanelGroupsInfoWidget extends Widget
{
    protected string $view = 'filament.pages.list-panel-groups-header';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';
}
