<?php

declare(strict_types=1);

/*
 * src/FilamentPanAnalyticsWidgetPlugin.php
 *
 * Filament plugin that holds the widget settings (search, authorization) per panel.
 */

namespace Schmeits\FilamentPanAnalyticsWidget;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use LogicException;

class FilamentPanAnalyticsWidgetPlugin implements Plugin
{
    use EvaluatesClosures;

    public const string ID = 'filament-pan-analytics-widget';

    protected bool | Closure | null $searchable = false;

    protected bool | Closure $authorized = true;

    public static function make(): static
    {
        return static::ensure(app(static::class));
    }

    /**
     * The plugin on the current panel. Throws when the panel does not have it;
     * use current() when that is a normal situation.
     */
    public static function get(): static
    {
        return static::ensure(filament(static::ID));
    }

    /**
     * The plugin on the current panel, or null when the widget is used on a
     * panel without the plugin (or outside a panel).
     */
    public static function current(): ?static
    {
        $panel = Filament::getCurrentOrDefaultPanel();

        if ($panel === null || ! $panel->hasPlugin(static::ID)) {
            return null;
        }

        return static::ensure($panel->getPlugin(static::ID));
    }

    public function getId(): string
    {
        return static::ID;
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}

    /**
     * Show a search field on the name column.
     */
    public function searchable(bool | Closure | null $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->searchable);
    }

    /**
     * Decide who may see the widget, for example
     * ->authorize(fn (): bool => auth()->user()?->isAdmin() ?? false).
     */
    public function authorize(bool | Closure $authorized): static
    {
        $this->authorized = $authorized;

        return $this;
    }

    public function isAuthorized(): bool
    {
        return (bool) $this->evaluate($this->authorized);
    }

    protected static function ensure(mixed $plugin): static
    {
        if (! $plugin instanceof static) {
            throw new LogicException('Expected an instance of ' . static::class . ', got ' . get_debug_type($plugin) . '.');
        }

        return $plugin;
    }
}
