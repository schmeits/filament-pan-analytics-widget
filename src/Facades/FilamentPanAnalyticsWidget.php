<?php

namespace Schmeits\FilamentPanAnalyticsWidget\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidget
 */
class FilamentPanAnalyticsWidget extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidget::class;
    }
}
