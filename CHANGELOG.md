# Changelog

All notable changes to `filament-pan-analytics-widget` will be documented in this file.

## 5.0.0 - 2026-10-13

For Filament 4 and 5. Filament 3 stays on 4.x (branch `4.x`).

### Breaking

- Requires Filament 4 or 5, tested on Laravel 12 and 13 with PHP 8.3 to 8.5.
- `FilamentPanAnalyticsWidgetPlugin::getSearchable()` is renamed to `isSearchable()`.
- Page size options are 5, 10, 25 and 50; the "all" option is removed.
- Removed the unused facade, `FilamentPanAnalyticsWidget` class, testing mixin, stubs, view namespace and the install command.
- Translation key `headers.id` removed; `not_available`, `empty.heading` and `empty.description` added.

### Added

- `->authorize(bool|Closure)` on the plugin to decide who may see the widget.
- `FilamentPanAnalyticsWidgetPlugin::current()`, which returns null on a panel without the plugin.
- An empty state that explains where the data comes from.

### Changed

- Number columns are right-aligned.
- Strict types everywhere, PHPStan (Larastan 3) on level max for source and tests, architecture tests.
- No more JavaScript build tooling: the package ships no assets.

Also includes every fix from 4.1.0.

## 4.1.0 - 2026-10-13

For Filament 3. The Filament 4/5 version continues as 5.x.

### Fixed

- The widget no longer crashes the dashboard when it is used on a panel without the plugin, or outside a panel. Search is then simply off.
- Elements with 0 impressions show "-" instead of "Infinity%" for the hover and click percentages (translatable via `not_available`).
- Numbers and percentages are formatted in the app locale. They were always formatted as English (1,234 instead of 1.234 for Dutch).
- No TypeError when the intl extension is missing: the plain number is shown instead.
- `searchable()` accepts a closure, as its signature promised (this fix was on main but never released).
- The PanAnalytics model casts the counters to integers and no longer expects timestamp columns.

### Added

- A real test suite: plugin, service provider and widget tests through the Filament table stack, including search, the panel-without-plugin case and the Dutch locale.
- CI on PHP 8.3, 8.4 and 8.5 with Laravel 11 and 12.

### Changed

- GitHub Actions pinned to commit SHAs, Dependabot with a 3-day cooldown and grouped updates, and a security policy.

## 4.0.0 - 2025-06-18

Requires Filament 3 (`filament/filament: ^3.2`), despite the version number.

## 0.4 - 2024-10-18

Used a proper Laravel model

## 0.3 - 2024-10-16

Changed: huge refactor of internals, now uses Filament tables

## 0.2 - 2024-10-14

Added: empty state to the table

## 0.1 - 2024-10-14

First release of the Pan Analytics Widget plugin
