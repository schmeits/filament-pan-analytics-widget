# Filament Pan Analytics Widget

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schmeits/filament-pan-analytics-widget.svg?style=flat-square)](https://packagist.org/packages/schmeits/filament-pan-analytics-widget)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/schmeits/filament-pan-analytics-widget/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/schmeits/filament-pan-analytics-widget/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/schmeits/filament-pan-analytics-widget.svg?style=flat-square)](https://packagist.org/packages/schmeits/filament-pan-analytics-widget)

A Filament dashboard widget for [panphp/pan](https://github.com/panphp/pan), the lightweight and privacy-focused product analytics library. It shows the impressions, hovers and clicks that pan collected, with the share of hovers and clicks per impression.

![example-screenshot.png](https://github.com/schmeits/filament-pan-analytics-widget/raw/main/docs-assets/screenshots/pan-analytics-widget.png)

## Compatibility

| Package version | Filament | Laravel | PHP |
|---|---|---|---|
| 5.x | 4.x and 5.x | 12 and 13 | 8.3+ |
| 4.x | 3.x | 11 and 12 | 8.3+ |

Version 4.x is for Filament 3, despite the number. It lives on the [`4.x` branch](https://github.com/schmeits/filament-pan-analytics-widget/tree/4.x) and still gets fixes.

## Installation

This widget reads the `pan_analytics` table of pan, so install and migrate pan first:

```bash
composer require panphp/pan
php artisan install:pan
```

Then install the widget:

```bash
composer require schmeits/filament-pan-analytics-widget
```

Numbers and percentages are formatted in the app locale with the PHP `intl` extension. Without `intl` the widget shows plain numbers.

Optionally, publish the translations (English and Dutch are included):

```bash
php artisan vendor:publish --tag="filament-pan-analytics-widget-translations"
```

## Usage

Add the plugin and the widget to your panel:

```php
use Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidgetPlugin;
use Schmeits\FilamentPanAnalyticsWidget\Widgets\PanAnalyticsTableWidget;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(FilamentPanAnalyticsWidgetPlugin::make())
        ->widgets([
            PanAnalyticsTableWidget::class,
        ]);
}
```

### Options

```php
FilamentPanAnalyticsWidgetPlugin::make()
    // Show a search field for the name column.
    ->searchable()
    // Decide who may see the widget. Everyone with access to the panel sees it by default.
    ->authorize(fn (): bool => auth()->user()?->can('viewAnalytics') ?? false)
```

Both options also accept a closure, which is evaluated when the widget renders.

Without the plugin on the panel the widget still works: it is visible, without search.

## Upgrading from 4.x to 5.x

- Requires Filament 4 or 5 (Laravel 12 or 13). Stay on 4.x for Filament 3.
- `FilamentPanAnalyticsWidgetPlugin::getSearchable()` is now `isSearchable()`.
- The page size options are 5, 10, 25 and 50. The "all" option is gone.
- The unused facade, `FilamentPanAnalyticsWidget` class, testing mixin, stubs and the `filament-pan-analytics-widget::` view namespace are removed.
- The `headers.id` translation key is removed. New keys: `not_available` and `empty.heading` / `empty.description`. Re-publish the translations if you published them before.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Tally Schmeits](https://github.com/schmeits)
- [Punyapal Shah](https://github.com/MrPunyapal) for his contribution to Pinkary where I got some inspiration for a refactor
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
