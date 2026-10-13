<?php

declare(strict_types=1);

/*
 * tests/Fixtures/TestPanelProvider.php
 *
 * Minimal panel set up exactly as the README tells users to.
 */

namespace Schmeits\FilamentPanAnalyticsWidget\Tests\Fixtures;

use Filament\Panel;
use Filament\PanelProvider;
use Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidgetPlugin;
use Schmeits\FilamentPanAnalyticsWidget\Widgets\PanAnalyticsTableWidget;

class TestPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('test-admin')
            ->path('test-admin')
            ->plugin(FilamentPanAnalyticsWidgetPlugin::make())
            ->widgets([
                PanAnalyticsTableWidget::class,
            ]);
    }
}
