<?php

namespace Schmeits\FilamentPanAnalyticsWidget;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentPanAnalyticsWidgetPlugin implements Plugin
{
    use EvaluatesClosures;

    protected bool $searchable = false;

    public function getSearchable(): bool
    {
        return $this->evaluate($this->searchable);
    }

    public function searchable(bool | Closure | null $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function getId(): string
    {
        return 'filament-pan-analytics-widget';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
