<?php

declare(strict_types=1);

/*
 * tests/ServiceProviderTest.php
 *
 * The provider boots, registers the translations and the Livewire alias.
 */

use Filament\Facades\Filament;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidgetServiceProvider;
use Schmeits\FilamentPanAnalyticsWidget\Widgets\PanAnalyticsTableWidget;

it('registers the service provider', function () {
    expect(app()->getProvider(FilamentPanAnalyticsWidgetServiceProvider::class))
        ->toBeInstanceOf(FilamentPanAnalyticsWidgetServiceProvider::class);
});

it('registers the package translations', function () {
    expect(__('filament-pan-analytics-widget::translations.heading'))->toBe('Pan Analytics Table')
        ->and(__('filament-pan-analytics-widget::translations.headers.impressions'))->toBe('Impressions');
});

it('registers the translations publish tag from the README', function () {
    $paths = ServiceProvider::pathsToPublish(group: 'filament-pan-analytics-widget-translations');

    expect($paths)->not->toBeEmpty();

    foreach (array_keys($paths) as $source) {
        expect(is_string($source) && file_exists($source))->toBeTrue("Publishable path does not exist: {$source}");
    }
});

it('has a Dutch translation for every English key', function () {
    expect(translationKeys('nl'))->toEqualCanonicalizing(translationKeys('en'));
});

it('registers the widget as a Livewire component under its public alias', function () {
    expect(Livewire::new('filament-pan-analytics-widget'))->toBeInstanceOf(PanAnalyticsTableWidget::class);
});

it('accepts the widget on a panel as the README prescribes', function () {
    expect(Filament::getPanel('test-admin')->getWidgets())->toContain(PanAnalyticsTableWidget::class);
});

/**
 * @return array<int, string>
 */
function translationKeys(string $locale): array
{
    $lines = require __DIR__ . "/../resources/lang/{$locale}/translations.php";

    if (! is_array($lines)) {
        throw new UnexpectedValueException("Translation file for [{$locale}] does not return an array.");
    }

    return array_map('strval', array_keys(Arr::dot($lines)));
}
