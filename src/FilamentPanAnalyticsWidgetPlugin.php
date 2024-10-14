<?php

namespace Schmeits\FilamentPanAnalyticsWidget;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentPanAnalyticsWidgetPlugin implements Plugin
{
    use EvaluatesClosures;

    protected string | Closure | null $pollingInterval = null;

    protected bool $persistsFiltersInSession = false;

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

    public function getPollingInterval(): string
    {
        return $this->evaluate($this->pollingInterval) ?? '60s';
    }

    public function pollingInterval(string | Closure | null $interval = '60s'): static
    {
        $this->pollingInterval = $interval;

        return $this;
    }

    public function getPersistFiltersInSession(): bool
    {
        return $this->evaluate($this->persistsFiltersInSession);
    }

    public function persistFiltersInSession(bool | Closure | null $shouldPersist = true): static
    {
        $this->persistsFiltersInSession = $shouldPersist;

        return $this;
    }
}
