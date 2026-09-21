<?php

use Illuminate\Support\ServiceProvider;

/*
 * The service provider toggles features based on file_exists(). That is silent
 * behaviour: if resources/lang disappears, the package simply stops registering
 * translations and everything falls back to raw keys without anything crashing.
 * These tests make those silent dependencies explicit.
 */

it('has the resources/lang directory that enables the translations', function () {
    expect(is_dir(__DIR__ . '/../resources/lang'))->toBeTrue();
});

it('has the resources/views directory that enables the view namespace', function () {
    expect(is_dir(__DIR__ . '/../resources/views'))->toBeTrue();
});

it('has the stubs directory that packageBooted reads', function () {
    // packageBooted() calls Filesystem::files() on this directory. If the
    // directory does not exist, that throws an exception while booting any
    // console app.
    expect(is_dir(__DIR__ . '/../stubs'))->toBeTrue();
});

it('registers the translations publish tag mentioned in the README', function () {
    // The README literally tells users to run
    // vendor:publish --tag="filament-pan-analytics-widget-translations".
    // If the name changes in the provider, the documentation no longer matches.
    expect(ServiceProvider::$publishGroups)
        ->toHaveKey('filament-pan-analytics-widget-translations');
});

it('points every publishable translation source at an existing path', function () {
    $sources = array_keys(ServiceProvider::$publishGroups['filament-pan-analytics-widget-translations']);

    expect($sources)->not->toBeEmpty();

    foreach ($sources as $source) {
        expect(file_exists($source))->toBeTrue("Publishable path does not exist: {$source}");
    }
});

it('provides a Dutch translation for every English translation key', function () {
    // A missing key in nl silently falls back to the key name in the UI.
    $en = require __DIR__ . '/../resources/lang/en/translations.php';
    $nl = require __DIR__ . '/../resources/lang/nl/translations.php';

    expect(array_keys($nl))->toEqualCanonicalizing(array_keys($en))
        ->and(array_keys($nl['headers']))->toEqualCanonicalizing(array_keys($en['headers']));
});
