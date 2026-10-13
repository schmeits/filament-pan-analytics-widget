<?php

namespace Schmeits\FilamentPanAnalyticsWidget\Widgets;

use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Number;
use Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidgetPlugin;
use Schmeits\FilamentPanAnalyticsWidget\Models\PanAnalytics;

class PanAnalyticsTableWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -9;

    public static function getHeading(): ?string
    {
        return trans('filament-pan-analytics-widget::translations.heading');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PanAnalytics::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament-pan-analytics-widget::translations.headers.name'))
                    ->sortable()
                    ->searchable($this->getIsSearchable())
                    ->grow(),
                Tables\Columns\TextColumn::make('impressions')
                    ->alignCenter()
                    ->sortable()
                    ->formatStateUsing(fn (PanAnalytics $record): string => $this->toHumanReadableNumber($record->impressions))
                    ->label(__('filament-pan-analytics-widget::translations.headers.impressions')),
                Tables\Columns\TextColumn::make('hovers')
                    ->alignCenter()
                    ->sortable()
                    ->formatStateUsing(fn (PanAnalytics $record): string => $this->toHumanReadableNumber($record->hovers) . ' (' . $this->toHumanReadablePercentage($record->impressions, $record->hovers) . ')')
                    ->label(__('filament-pan-analytics-widget::translations.headers.hovers')),
                Tables\Columns\TextColumn::make('clicks')
                    ->alignCenter()
                    ->sortable()
                    ->formatStateUsing(fn (PanAnalytics $record): string => $this->toHumanReadableNumber($record->clicks) . ' (' . $this->toHumanReadablePercentage($record->impressions, $record->clicks) . ')')
                    ->label(__('filament-pan-analytics-widget::translations.headers.clicks')),
            ])
            ->paginated()
            ->paginationPageOptions(['5', '10', '20', '50', 'all'])
            ->defaultSort('impressions', 'desc')
            ->persistFiltersInSession();
    }

    /**
     * Read the search setting from the plugin on the current panel. When the
     * widget is used on a panel without the plugin (or outside a panel), fall
     * back to "not searchable" instead of throwing and taking the whole
     * dashboard down with it.
     */
    private function getIsSearchable(): bool
    {
        $panel = Filament::getCurrentPanel();
        $pluginId = FilamentPanAnalyticsWidgetPlugin::make()->getId();

        if ($panel === null || ! $panel->hasPlugin($pluginId)) {
            return false;
        }

        /** @var FilamentPanAnalyticsWidgetPlugin $plugin */
        $plugin = $panel->getPlugin($pluginId);

        return $plugin->getSearchable();
    }

    /**
     * Format in the app locale (Number defaults to "en" otherwise). Number::format()
     * returns false when the intl extension is missing, so fall back to the
     * plain number instead of breaking the string contract.
     */
    private function toHumanReadableNumber(int $number): string
    {
        $formatted = Number::format($number, locale: app()->getLocale());

        return $formatted === false ? (string) $number : $formatted;
    }

    private function toHumanReadablePercentage(int $total, int $part): string
    {
        if ($total === 0) {
            return trans('filament-pan-analytics-widget::translations.not_available');
        }

        $percentage = $part / $total * 100;
        $formatted = Number::percentage($percentage, 0, 1, locale: app()->getLocale());

        return $formatted === false ? round($percentage, 1) . '%' : $formatted;
    }
}
