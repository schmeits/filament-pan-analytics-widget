# A widget for pan (lightweight and privacy-focused PHP product analytics library)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schmeits/filament-pan-analytics-widget.svg?style=flat-square)](https://packagist.org/packages/schmeits/filament-pan-analytics-widget)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/schmeits/filament-pan-analytics-widget/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/schmeits/filament-pan-analytics-widget/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/schmeits/filament-pan-analytics-widget.svg?style=flat-square)](https://packagist.org/packages/schmeits/filament-pan-analytics-widget)

A Filament Widget for the [PanPHP plugin](https://github.com/panphp/pan) provides a user-friendly interface to monitor and interact with PAN instances directly from your Filament admin dashboard.

![example-screenshot.png](https://github.com/schmeits/filament-pan-analytics-widget/raw/main/docs-assets/screenshots/pan-analytics-widget.png)

## Installation

| Package version | Filament | Laravel | PHP |
|---|---|---|---|
| 5.x | 4.x and 5.x | 11, 12 and 13 | 8.3+ |
| 4.x (this branch) | 3.x | 11 and 12 | 8.3+ |

Version 4.x is for Filament 3, despite the number. For Filament 4 or 5, use version 5.x.

This widget reads the table of [panphp/pan](https://github.com/panphp/pan), so install and migrate pan first. Numbers are formatted with the PHP intl extension when it is available.

You can install the package via composer:

```bash
# Filament 3
composer require schmeits/filament-pan-analytics-widget:^4.1
```

Optionally, you can publish the translations
```bash
php artisan vendor:publish --tag="filament-pan-analytics-widget-translations"
```

## Usage

### Add the plugin to your PanelProvider
```php
->plugins([
    \Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidgetPlugin::make()
])
```
### Options
```php
->plugins([
    \Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidgetPlugin::make()
        ->searchable() // display a search for the name column       
])
```

### Add the Widget to your PanelProvider
```php
->widgets([
    \Schmeits\FilamentPanAnalyticsWidget\Widgets\PanAnalyticsTableWidget::class, // <-- add this widget
])
```

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
