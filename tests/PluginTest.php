<?php

declare(strict_types=1);

/*
 * tests/PluginTest.php
 *
 * The plugin is the public configuration point: search and authorization.
 */

use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidgetPlugin;

it('satisfies the Filament plugin contract with a stable id', function () {
    $plugin = FilamentPanAnalyticsWidgetPlugin::make();

    expect($plugin)->toBeInstanceOf(Plugin::class)
        ->and($plugin->getId())->toBe('filament-pan-analytics-widget');
});

it('finds the plugin on the current panel', function () {
    expect(FilamentPanAnalyticsWidgetPlugin::get())->toBeInstanceOf(FilamentPanAnalyticsWidgetPlugin::class)
        ->and(FilamentPanAnalyticsWidgetPlugin::current())->toBe(FilamentPanAnalyticsWidgetPlugin::get());
});

it('returns null as the current plugin on a panel without the plugin', function () {
    $panel = Panel::make()->id('without-plugin')->path('without-plugin');
    Filament::registerPanel($panel);
    Filament::setCurrentPanel($panel);

    expect(FilamentPanAnalyticsWidgetPlugin::current())->toBeNull();
});

it('is not searchable by default and searchable when asked', function () {
    expect(FilamentPanAnalyticsWidgetPlugin::make()->isSearchable())->toBeFalse()
        ->and(FilamentPanAnalyticsWidgetPlugin::make()->searchable()->isSearchable())->toBeTrue()
        ->and(FilamentPanAnalyticsWidgetPlugin::make()->searchable(false)->isSearchable())->toBeFalse()
        ->and(FilamentPanAnalyticsWidgetPlugin::make()->searchable(null)->isSearchable())->toBeFalse();
});

it('evaluates a searchable closure only when it is read', function () {
    $calls = 0;
    $plugin = FilamentPanAnalyticsWidgetPlugin::make()->searchable(function () use (&$calls): bool {
        $calls++;

        return true;
    });

    expect($calls)->toBe(0)
        ->and($plugin->isSearchable())->toBeTrue()
        ->and($calls)->toBe(1);
});

it('is authorized by default and follows a bool or closure', function () {
    expect(FilamentPanAnalyticsWidgetPlugin::make()->isAuthorized())->toBeTrue()
        ->and(FilamentPanAnalyticsWidgetPlugin::make()->authorize(false)->isAuthorized())->toBeFalse()
        ->and(FilamentPanAnalyticsWidgetPlugin::make()->authorize(fn (): bool => false)->isAuthorized())->toBeFalse();
});

it('returns itself from the fluent setters', function () {
    $plugin = FilamentPanAnalyticsWidgetPlugin::make();

    expect($plugin->searchable())->toBe($plugin)
        ->and($plugin->authorize(true))->toBe($plugin);
});
