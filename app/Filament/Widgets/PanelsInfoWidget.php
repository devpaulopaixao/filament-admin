<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class PanelsInfoWidget extends Widget
{
    protected string $view = 'filament.pages.list-panels-header';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';
}
