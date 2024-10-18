<?php

namespace Schmeits\FilamentPanAnalyticsWidget\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;
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
                    ->formatStateUsing(fn ($record) => $this->toHumanReadableNumber($record->impressions))
                    ->label(__('filament-pan-analytics-widget::translations.headers.impressions')),
                Tables\Columns\TextColumn::make('hovers')
                    ->alignCenter()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $this->toHumanReadableNumber($record->hovers) . ' (' . $this->toHumanReadablePercentage($record->impressions, $record->hovers) . ')')
                    ->label(__('filament-pan-analytics-widget::translations.headers.hovers')),
                Tables\Columns\TextColumn::make('clicks')
                    ->alignCenter()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $this->toHumanReadableNumber($record->clicks) . ' (' . $this->toHumanReadablePercentage($record->impressions, $record->clicks) . ')')
                    ->label(__('filament-pan-analytics-widget::translations.headers.clicks')),
            ])
            ->paginated()
            ->paginationPageOptions(['5', '10', '20', '50', 'all'])
            ->defaultSort('impressions', 'desc')
            ->persistFiltersInSession();
    }

    private function getIsSearchable(): bool
    {
        return FilamentPanAnalyticsWidgetPlugin::get()->getSearchable();
    }

    private function toHumanReadableNumber(int $number): string
    {
        return Number::format($number);
    }

    private function toHumanReadablePercentage(int $total, int $part): string
    {
        if ($total === 0) {
            return 'Infinity%';
        }

        return Number::percentage($part / $total * 100, 0, 1);
    }
}
