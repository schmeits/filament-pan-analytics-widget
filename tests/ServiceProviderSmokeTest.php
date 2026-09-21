<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidget;
use Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidgetServiceProvider;
use Schmeits\FilamentPanAnalyticsWidget\Widgets\PanAnalyticsTableWidget;

/*
 * These tests prove that the service provider registers and boots in a real
 * (Testbench) app. Whether the framework loads the provider at all is the first
 * domino: if that falls, every project using this package is broken.
 */

it('registers the service provider in the container', function () {
    // getProvider returns null if the provider was never loaded. So we check not
    // only that the class exists, but that Laravel actually ran it.
    expect(app()->getProvider(FilamentPanAnalyticsWidgetServiceProvider::class))
        ->toBeInstanceOf(FilamentPanAnalyticsWidgetServiceProvider::class);
});

it('registers the package view namespace', function () {
    // configurePackage() only registers the namespace if resources/views exists
    // on disk. If that directory disappears in a refactor, this fails.
    expect(View::getFinder()->getHints())
        ->toHaveKey('filament-pan-analytics-widget');
});

it('registers the package translations', function () {
    // A missing translation makes Laravel return the key itself. That is silent
    // failure, so we compare against the expected English value.
    expect(trans('filament-pan-analytics-widget::translations.heading'))
        ->toBe('Pan Analytics Table')
        ->and(trans('filament-pan-analytics-widget::translations.headers.impressions'))
        ->toBe('Impressions');
});

it('registers the widget as a livewire component under its public alias', function () {
    // The alias is hard-coded in packageBooted(). If someone renames the widget
    // class without updating the alias, that breaks every dashboard loading it.
    //
    // Deliberately via Livewire::new() and not via the internal component
    // registry: that registry moved between Livewire 3 and 4, and this test
    // would then fail on a move rather than on a real defect.
    expect(Livewire::new('filament-pan-analytics-widget'))
        ->toBeInstanceOf(PanAnalyticsTableWidget::class);
});

it('can register the widget on a panel as the README prescribes', function () {
    // The README says: put the widget in your PanelProvider's ->widgets([]).
    // This proves Filament accepts and returns that class there, so the public
    // installation instruction still holds.
    expect(Filament::getPanel('test-admin')->getWidgets())
        ->toContain(PanAnalyticsTableWidget::class);
});

it('can instantiate the public main class', function () {
    // The facade points here, so the class must be resolvable via the container.
    expect(app(FilamentPanAnalyticsWidget::class))
        ->toBeInstanceOf(FilamentPanAnalyticsWidget::class);
});

it('runs on an in-memory sqlite database', function () {
    // Explicitly asserted: the suite must never touch a real database.
    expect(config('database.default'))->toBe('testing')
        ->and(config('database.connections.testing.driver'))->toBe('sqlite')
        ->and(config('database.connections.testing.database'))->toBe(':memory:');
});
