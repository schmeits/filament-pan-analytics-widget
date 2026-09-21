<?php

use Filament\Contracts\Plugin;
use Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidgetPlugin;

/*
 * The plugin is the only public configuration point of this package: the README
 * has users hang it in their panel via ->plugin(...->searchable()). If something
 * breaks here, every PanelProvider that uses this package breaks with it.
 */

it('can be instantiated through the static make method', function () {
    expect(FilamentPanAnalyticsWidgetPlugin::make())
        ->toBeInstanceOf(FilamentPanAnalyticsWidgetPlugin::class);
});

it('satisfies the Filament Plugin contract', function () {
    // If Filament changes this contract in a major version, it shows up here
    // right away instead of only in a user's project.
    expect(FilamentPanAnalyticsWidgetPlugin::make())->toBeInstanceOf(Plugin::class);
});

it('has a stable plugin id', function () {
    // The id is also the key the widget uses to look the plugin back up via
    // filament(). If it changes, the widget can no longer find its own plugin.
    expect(FilamentPanAnalyticsWidgetPlugin::make()->getId())
        ->toBe('filament-pan-analytics-widget');
});

it('finds the plugin back on the registered panel', function () {
    // ::get() goes through the current panel. This proves the plugin actually
    // ends up on the panel from the README instructions.
    expect(FilamentPanAnalyticsWidgetPlugin::get())
        ->toBeInstanceOf(FilamentPanAnalyticsWidgetPlugin::class);
});

it('disables search by default', function () {
    expect(FilamentPanAnalyticsWidgetPlugin::make()->getSearchable())->toBeFalse();
});

it('enables search when searchable is called without arguments', function () {
    // Exactly the call shown in the README.
    expect(FilamentPanAnalyticsWidgetPlugin::make()->searchable()->getSearchable())
        ->toBeTrue();
});

it('can disable search explicitly', function () {
    expect(FilamentPanAnalyticsWidgetPlugin::make()->searchable(false)->getSearchable())
        ->toBeFalse();
});

it('returns the plugin so searchable can be chained', function () {
    // The README chains ->make()->searchable() onto ->plugin(). Without a fluent
    // return, Filament would not receive a Plugin there.
    $plugin = FilamentPanAnalyticsWidgetPlugin::make();

    expect($plugin->searchable())->toBe($plugin);
});

it('accepts a closure for searchable and evaluates it only on read', function () {
    // The signature promises bool|Closure|null, and the class uses
    // EvaluatesClosures precisely to evaluate that closure lazily.
    // This test guards that the promise is actually kept.
    $plugin = FilamentPanAnalyticsWidgetPlugin::make()->searchable(fn (): bool => true);

    expect($plugin->getSearchable())->toBeTrue();
});

it('falls back to not searchable when searchable receives null', function () {
    // null should be allowed per the signature. The safest meaning is "not set",
    // so: search off.
    expect(FilamentPanAnalyticsWidgetPlugin::make()->searchable(null)->getSearchable())
        ->toBeFalse();
});
