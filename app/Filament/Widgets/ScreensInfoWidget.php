<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ScreensInfoWidget extends Widget
{
    protected string $view = 'filament.pages.list-screens-header';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';
}
