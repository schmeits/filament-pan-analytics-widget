<?php

namespace Schmeits\FilamentPanAnalyticsWidget\Tests\Fixtures;

use Filament\Panel;
use Filament\PanelProvider;
use Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidgetPlugin;
use Schmeits\FilamentPanAnalyticsWidget\Widgets\PanAnalyticsTableWidget;

/**
 * Minimal Filament panel for the tests.
 *
 * The widget relies on FilamentPanAnalyticsWidgetPlugin::get(), and that helper
 * looks up the plugin on the CURRENT panel. Without a registered panel Filament
 * throws an exception, so the tests always need a panel that knows both the
 * plugin and the widget. This panel mirrors exactly what the README tells the
 * user to do, so a break in those public instructions surfaces here.
 */
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
