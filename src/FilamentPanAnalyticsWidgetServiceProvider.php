<?php

declare(strict_types=1);

/*
 * src/FilamentPanAnalyticsWidgetServiceProvider.php
 *
 * Registers the translations and the widget as a Livewire component.
 */

namespace Schmeits\FilamentPanAnalyticsWidget;

use Livewire\Livewire;
use Schmeits\FilamentPanAnalyticsWidget\Widgets\PanAnalyticsTableWidget;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentPanAnalyticsWidgetServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-pan-analytics-widget';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        Livewire::component('filament-pan-analytics-widget', PanAnalyticsTableWidget::class);
    }
}
