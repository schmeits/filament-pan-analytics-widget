<?php

namespace Schmeits\FilamentPanAnalyticsWidget\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Support\Htmlable;
use Pan\Adapters\Laravel\Repositories\DatabaseAnalyticsRepository;
use Pan\Presentors\AnalyticPresentor;
use Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidgetPlugin;

class PanAnalyticsTableWidget extends Widget
{
    use InteractsWithPageFilters;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -9;

    protected ?string $heading = '';

    protected string $id = 'pan-analytics-table';

    protected bool $limitResults = true;

    public ?int $limit = null;

    protected static string $view = 'filament-pan-analytics-widget::table-widget';

    public ?string $option = null;

    public function updatedLimit(): void
    {
        if ($this->getPersistFiltersInSession()) {
            session()->put($this->id . '-limit', $this->limit);
        }
    }

    public function mount(): void
    {
        $this->heading = trans("filament-pan-analytics-widget::translations.widget.$this->id.heading");
        $this->option = collect($this->getOptions())->keys()->first();

        if ($this->limitResults) {
            if ($this->limit === null &&
                $this->getPersistFiltersInSession() &&
                session()->has($this->id . '-limit')
            ) {
                $this->limit = session()->get($this->id . '-limit');
            } else {
                $this->limit = 5;
            }
        }
    }

    public function getPollingInterval(): ?string
    {
        return FilamentPanAnalyticsWidgetPlugin::get()->getPollingInterval();
    }

    public function getPersistFiltersInSession(): bool
    {
        return FilamentPanAnalyticsWidgetPlugin::get()->getPersistFiltersInSession();
    }

    public function getTableHeading(): string | Htmlable | null
    {
        return $this->heading;
    }

    public function getHeaders(): array
    {
        return [
            [
                'name' => 'id',
                'label' => trans("filament-pan-analytics-widget::translations.widget.$this->id.headers.id"),
                'width' => '5%',
            ],
            [
                'name' => 'name',
                'label' => trans("filament-pan-analytics-widget::translations.widget.$this->id.headers.name"),
                'width' => '65%',
            ],
            [
                'name' => 'impressions',
                'label' => trans("filament-pan-analytics-widget::translations.widget.$this->id.headers.impressions"),
                'width' => '10%',
            ],
            [
                'name' => 'hovers',
                'label' => trans("filament-pan-analytics-widget::translations.widget.$this->id.headers.hovers"),
                'width' => '10%',
            ],
            [
                'name' => 'clicks',
                'label' => trans("filament-pan-analytics-widget::translations.widget.$this->id.headers.clicks"),
                'width' => '10%',
            ],
        ];
    }

    public function getOptions(): array
    {
        return [];
    }

    public function hasLimitedResults(): bool
    {
        return $this->limitResults;
    }

    public function getData(): array
    {
        $analytic = new DatabaseAnalyticsRepository;
        $presenter = new AnalyticPresentor;

        $data = collect($analytic->all())->map(fn ($item) => $presenter->present($item));

        if ($this->limitResults) {
            $data->take($this->limit);
        }

        return $data->toArray();
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }
}
