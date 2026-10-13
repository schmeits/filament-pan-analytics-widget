<?php

declare(strict_types=1);

/*
 * src/Widgets/PanAnalyticsTableWidget.php
 *
 * Dashboard table with the impressions, hovers and clicks that panphp/pan collected.
 */

namespace Schmeits\FilamentPanAnalyticsWidget\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Number;
use Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidgetPlugin;
use Schmeits\FilamentPanAnalyticsWidget\Models\PanAnalytics;

class PanAnalyticsTableWidget extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -9;

    /**
     * Visible unless the plugin on the current panel says otherwise. Without the
     * plugin the widget stays visible, as before.
     */
    public static function canView(): bool
    {
        return FilamentPanAnalyticsWidgetPlugin::current()?->isAuthorized() ?? true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('filament-pan-analytics-widget::translations.heading'))
            ->query(PanAnalytics::query())
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament-pan-analytics-widget::translations.headers.name'))
                    ->sortable()
                    ->searchable($this->isSearchable())
                    ->grow(),
                TextColumn::make('impressions')
                    ->label(__('filament-pan-analytics-widget::translations.headers.impressions'))
                    ->alignEnd()
                    ->sortable()
                    ->formatStateUsing(fn (PanAnalytics $record): string => $this->formatNumber($record->impressions)),
                TextColumn::make('hovers')
                    ->label(__('filament-pan-analytics-widget::translations.headers.hovers'))
                    ->alignEnd()
                    ->sortable()
                    ->formatStateUsing(fn (PanAnalytics $record): string => $this->formatWithShare($record->hovers, $record->impressions)),
                TextColumn::make('clicks')
                    ->label(__('filament-pan-analytics-widget::translations.headers.clicks'))
                    ->alignEnd()
                    ->sortable()
                    ->formatStateUsing(fn (PanAnalytics $record): string => $this->formatWithShare($record->clicks, $record->impressions)),
            ])
            ->paginated([5, 10, 25, 50])
            ->defaultSort('impressions', 'desc')
            ->emptyStateHeading(__('filament-pan-analytics-widget::translations.empty.heading'))
            ->emptyStateDescription(__('filament-pan-analytics-widget::translations.empty.description'));
    }

    private function isSearchable(): bool
    {
        return FilamentPanAnalyticsWidgetPlugin::current()?->isSearchable() ?? false;
    }

    /**
     * Format in the app locale (Number defaults to "en" otherwise). Number::format()
     * returns false when the intl extension is missing, so fall back to the
     * plain number.
     */
    private function formatNumber(int $number): string
    {
        $formatted = Number::format($number, locale: app()->getLocale());

        return $formatted === false ? (string) $number : $formatted;
    }

    /**
     * "50 (25%)": the count plus its share of the impressions. Without
     * impressions there is no meaningful share, so show "-" instead.
     */
    private function formatWithShare(int $part, int $total): string
    {
        return $this->formatNumber($part) . ' (' . $this->formatPercentage($part, $total) . ')';
    }

    private function formatPercentage(int $part, int $total): string
    {
        if ($total === 0) {
            return __('filament-pan-analytics-widget::translations.not_available');
        }

        $percentage = $part / $total * 100;
        $formatted = Number::percentage($percentage, 0, 1, locale: app()->getLocale());

        return $formatted === false ? round($percentage, 1) . '%' : $formatted;
    }
}
