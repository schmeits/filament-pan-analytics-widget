<?php

declare(strict_types=1);

/*
 * tests/WidgetTest.php
 *
 * The widget through the full Filament table stack.
 */

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Livewire\Livewire;
use Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidgetPlugin;
use Schmeits\FilamentPanAnalyticsWidget\Models\PanAnalytics;
use Schmeits\FilamentPanAnalyticsWidget\Tests\TestCase;
use Schmeits\FilamentPanAnalyticsWidget\Widgets\PanAnalyticsTableWidget;

beforeEach(function () {
    TestCase::createPanAnalyticsTable();
});

/**
 * @param  array<int, array{name: string, impressions: int, hovers: int, clicks: int}>  $rows
 */
function insertPanRows(array $rows): void
{
    DB::table('pan_analytics')->insert($rows);
}

it('renders with the translated heading', function () {
    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertSuccessful()
        ->assertSee('Pan Analytics Table');
});

it('shows an empty state when pan has not recorded anything yet', function () {
    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertSee('No analytics yet');
});

it('shows the rows sorted by impressions, highest first', function () {
    insertPanRows([
        ['name' => 'low', 'impressions' => 10, 'hovers' => 1, 'clicks' => 1],
        ['name' => 'high', 'impressions' => 900, 'hovers' => 1, 'clicks' => 1],
    ]);

    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertCanSeeTableRecords(PanAnalytics::orderByDesc('impressions')->get(), inOrder: true);
});

it('shows hovers and clicks with their share of the impressions', function () {
    insertPanRows([['name' => 'button', 'impressions' => 1234, 'hovers' => 50, 'clicks' => 10]]);

    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertTableColumnFormattedStateSet('impressions', '1,234', PanAnalytics::first())
        ->assertTableColumnFormattedStateSet('hovers', '50 (4.1%)', PanAnalytics::first())
        ->assertTableColumnFormattedStateSet('clicks', '10 (0.8%)', PanAnalytics::first());
});

it('shows a dash instead of a percentage when there are no impressions', function () {
    insertPanRows([['name' => 'never-seen', 'impressions' => 0, 'hovers' => 0, 'clicks' => 0]]);

    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertSuccessful()
        ->assertTableColumnFormattedStateSet('hovers', '0 (-)', PanAnalytics::first())
        ->assertDontSee('Infinity');
});

it('formats numbers and labels in the Dutch locale', function () {
    app()->setLocale('nl');
    insertPanRows([['name' => 'knop', 'impressions' => 1234, 'hovers' => 617, 'clicks' => 0]]);

    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertSee('Pan Analytics Tabel')
        ->assertSee('Vertoningen')
        ->assertTableColumnFormattedStateSet('impressions', Number::format(1234, locale: 'nl'), PanAnalytics::first())
        ->assertTableColumnFormattedStateSet('hovers', '617 (' . Number::percentage(50, 0, 1, locale: 'nl') . ')', PanAnalytics::first());

    expect(Number::format(1234, locale: 'nl'))->toBe('1.234');
});

it('right-aligns the numeric columns', function () {
    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertTableColumnExists('impressions', fn (TextColumn $column): bool => $column->getAlignment() === Alignment::End)
        ->assertTableColumnExists('clicks', fn (TextColumn $column): bool => $column->getAlignment() === Alignment::End);
});

it('offers fixed page sizes without an "all" option', function () {
    $widget = Livewire::test(PanAnalyticsTableWidget::class)->instance();

    expect($widget)->toBeInstanceOf(PanAnalyticsTableWidget::class)
        ->and($widget instanceof PanAnalyticsTableWidget ? $widget->getTable()->getPaginationPageOptions() : null)->toBe([5, 10, 25, 50]);
});

it('does not make the name column searchable by default', function () {
    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertTableColumnExists('name', fn (TextColumn $column): bool => ! $column->isGloballySearchable());
});

it('searches on name when the plugin is searchable', function () {
    FilamentPanAnalyticsWidgetPlugin::get()->searchable();

    insertPanRows([
        ['name' => 'checkout-button', 'impressions' => 10, 'hovers' => 0, 'clicks' => 0],
        ['name' => 'newsletter-form', 'impressions' => 5, 'hovers' => 0, 'clicks' => 0],
    ]);

    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertTableColumnExists('name', fn (TextColumn $column): bool => $column->isGloballySearchable())
        ->searchTable('newsletter')
        ->assertCanSeeTableRecords(PanAnalytics::where('name', 'newsletter-form')->get())
        ->assertCanNotSeeTableRecords(PanAnalytics::where('name', 'checkout-button')->get());
});

it('still renders on a panel without the plugin, without search', function () {
    $panel = Panel::make()->id('without-plugin')->path('without-plugin');
    Filament::registerPanel($panel);
    Filament::setCurrentPanel($panel);

    insertPanRows([['name' => 'button', 'impressions' => 1, 'hovers' => 0, 'clicks' => 0]]);

    expect(PanAnalyticsTableWidget::canView())->toBeTrue();

    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords(PanAnalytics::all())
        ->assertTableColumnExists('name', fn (TextColumn $column): bool => ! $column->isGloballySearchable());
});

it('is visible by default and hidden when the plugin does not authorize it', function () {
    expect(PanAnalyticsTableWidget::canView())->toBeTrue();

    FilamentPanAnalyticsWidgetPlugin::get()->authorize(fn (): bool => false);

    expect(PanAnalyticsTableWidget::canView())->toBeFalse();
});

it('casts the counters to integers', function () {
    insertPanRows([['name' => 'button', 'impressions' => 3, 'hovers' => 2, 'clicks' => 1]]);

    $record = PanAnalytics::query()->firstOrFail();

    expect($record->impressions)->toBe(3)
        ->and($record->hovers)->toBe(2)
        ->and($record->clicks)->toBe(1);
});
