# A widget for pan (lightweight and privacy-focused PHP product analytics library)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/schmeits/filament-pan-analytics-widget.svg?style=flat-square)](https://packagist.org/packages/schmeits/filament-pan-analytics-widget)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/schmeits/filament-pan-analytics-widget/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/schmeits/filament-pan-analytics-widget/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/schmeits/filament-pan-analytics-widget/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/schmeits/filament-pan-analytics-widget/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/schmeits/filament-pan-analytics-widget.svg?style=flat-square)](https://packagist.org/packages/schmeits/filament-pan-analytics-widget)

A Filament Widget for the [PanPHP plugin](https://github.com/panphp/pan) provides a user-friendly interface to monitor and interact with PAN instances directly from your Filament admin dashboard.

![example-screenshot.png](https://github.com/schmeits/filament-pan-analytics-widget/raw/main/docs-assets/screenshots/pan-analytics-widget.png)

## Installation

> **Requires [PHP 8.3+](https://php.net/releases/), and [Laravel 11.0+](https://laravel.com)**.

You can install the package via composer:

```bash
composer require schmeits/filament-pan-analytics-widget
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
