<?php

namespace Schmeits\FilamentPanAnalyticsWidget;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;

class FilamentPanAnalyticsWidgetPlugin implements Plugin
{
    use EvaluatesClosures;

    /**
     * The setter promises bool|Closure|null, and the class relies on
     * EvaluatesClosures precisely so a closure can be evaluated lazily. The
     * property type must therefore allow the same, otherwise every closure call
     * would throw a TypeError.
     */
    protected bool | Closure | null $searchable = false;

    public function getSearchable(): bool
    {
        // evaluate() can return null when searchable was never set (or set to
        // null). The cast keeps the public bool contract intact: not configured
        // means not searchable.
        return (bool) $this->evaluate($this->searchable);
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
