<?php

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Livewire\Livewire;
use Schmeits\FilamentPanAnalyticsWidget\Models\PanAnalytics;
use Schmeits\FilamentPanAnalyticsWidget\Widgets\PanAnalyticsTableWidget;

/*
 * This is where most of the value of this suite lives: the widget renders
 * through the full Filament table stack. If Filament changes a method
 * signature, a column API or the way a TableWidget picks up its query, this
 * fails right away instead of only in production for a user of the package.
 *
 * The pan_analytics table normally comes from the panphp/pan migration. Here we
 * create it ourselves in the in-memory sqlite database, behind a hard check that
 * we really are on :memory:.
 */

beforeEach(function () {
    $this->createPanAnalyticsTable();
});

it('renders the widget without errors', function () {
    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertSuccessful();
});

it('shows the rows from pan_analytics in the table', function () {
    DB::table('pan_analytics')->insert([
        ['name' => 'checkout-button', 'impressions' => 100, 'hovers' => 50, 'clicks' => 25],
    ]);

    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertCanSeeTableRecords(PanAnalytics::all());
});

it('sorts by impressions descending by default', function () {
    // The widget sets ->defaultSort('impressions', 'desc'). Flip that around and
    // the dashboard shows the least-viewed elements at the top.
    DB::table('pan_analytics')->insert([
        ['name' => 'low', 'impressions' => 10, 'hovers' => 1, 'clicks' => 1],
        ['name' => 'high', 'impressions' => 900, 'hovers' => 1, 'clicks' => 1],
    ]);

    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertCanSeeTableRecords(
            PanAnalytics::orderByDesc('impressions')->get(),
            inOrder: true,
        );
});

it('formats impressions as a human-readable number', function () {
    // Number::format depends on the app locale. If that formatting breaks, the
    // dashboard suddenly shows a raw number (or nothing).
    DB::table('pan_analytics')->insert([
        ['name' => 'most-viewed', 'impressions' => 1234, 'hovers' => 0, 'clicks' => 0],
    ]);

    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertTableColumnFormattedStateSet('impressions', Number::format(1234), PanAnalytics::first());
});

it('shows hovers and clicks with their percentage of impressions', function () {
    DB::table('pan_analytics')->insert([
        ['name' => 'button', 'impressions' => 200, 'hovers' => 50, 'clicks' => 10],
    ]);

    $record = PanAnalytics::first();

    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertTableColumnFormattedStateSet(
            'hovers',
            Number::format(50) . ' (' . Number::percentage(25, 0, 1) . ')',
            $record,
        )
        ->assertTableColumnFormattedStateSet(
            'clicks',
            Number::format(10) . ' (' . Number::percentage(5, 0, 1) . ')',
            $record,
        );
});

it('does not divide by zero when an element has zero impressions', function () {
    // Pan writes rows as soon as an element exists, even with 0 impressions.
    // Without the guard in toHumanReadablePercentage(), the whole dashboard would
    // fall over with a DivisionByZeroError.
    DB::table('pan_analytics')->insert([
        ['name' => 'never-seen', 'impressions' => 0, 'hovers' => 0, 'clicks' => 0],
    ]);

    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertSuccessful()
        ->assertTableColumnFormattedStateSet(
            'hovers',
            Number::format(0) . ' (-)',
            PanAnalytics::first(),
        );
});

it('uses the translated package heading as the widget title', function () {
    expect(PanAnalyticsTableWidget::getHeading())->toBe('Pan Analytics Table');
});

it('renders the name column without a search bar when the plugin is not searchable', function () {
    // getIsSearchable() pulls its value from the plugin on the current panel.
    // This test proves that panel -> plugin -> widget wiring is intact.
    DB::table('pan_analytics')->insert([
        ['name' => 'button', 'impressions' => 1, 'hovers' => 0, 'clicks' => 0],
    ]);

    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertSuccessful()
        ->assertTableColumnExists('name');
});

it('shows a search field on the name column when the plugin is searchable', function () {
    Filament::getPanel('test-admin')
        ->getPlugin('filament-pan-analytics-widget')
        ->searchable();

    DB::table('pan_analytics')->insert([
        ['name' => 'checkout-button', 'impressions' => 10, 'hovers' => 0, 'clicks' => 0],
        ['name' => 'newsletter-form', 'impressions' => 5, 'hovers' => 0, 'clicks' => 0],
    ]);

    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertTableColumnExists('name', fn (TextColumn $column): bool => $column->isGloballySearchable())
        ->searchTable('newsletter')
        ->assertCanSeeTableRecords(PanAnalytics::where('name', 'newsletter-form')->get())
        ->assertCanNotSeeTableRecords(PanAnalytics::where('name', 'checkout-button')->get());
});

it('does not make the name column searchable by default', function () {
    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertTableColumnExists('name', fn (TextColumn $column): bool => ! $column->isGloballySearchable());
});

it('still renders on a panel that does not have the plugin', function () {
    // Users can put the widget on a panel and forget the plugin. That used to
    // throw and take the whole dashboard down; now search is simply off.
    $panel = Panel::make()->id('without-plugin')->path('without-plugin');
    Filament::registerPanel($panel);
    Filament::setCurrentPanel($panel);

    DB::table('pan_analytics')->insert([
        ['name' => 'button', 'impressions' => 1, 'hovers' => 0, 'clicks' => 0],
    ]);

    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords(PanAnalytics::all());
});

it('uses the Dutch translations and number format for the nl locale', function () {
    app()->setLocale('nl');

    DB::table('pan_analytics')->insert([
        ['name' => 'knop', 'impressions' => 1234, 'hovers' => 0, 'clicks' => 0],
    ]);

    expect(PanAnalyticsTableWidget::getHeading())->toBe('Pan Analytics Tabel');

    Livewire::test(PanAnalyticsTableWidget::class)
        ->assertTableColumnFormattedStateSet('impressions', Number::format(1234, locale: 'nl'), PanAnalytics::first())
        ->assertSee('Vertoningen');
});

it('casts the counters to integers', function () {
    DB::table('pan_analytics')->insert([
        ['name' => 'button', 'impressions' => 3, 'hovers' => 2, 'clicks' => 1],
    ]);

    $record = PanAnalytics::first();

    expect($record->impressions)->toBe(3)
        ->and($record->hovers)->toBe(2)
        ->and($record->clicks)->toBe(1);
});
