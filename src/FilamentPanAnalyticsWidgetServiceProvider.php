<?php

namespace Schmeits\FilamentPanAnalyticsWidget;

use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Schmeits\FilamentPanAnalyticsWidget\Testing\TestsFilamentPanAnalyticsWidget;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentPanAnalyticsWidgetServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-pan-analytics-widget';

    public static string $viewNamespace = 'filament-pan-analytics-widget';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->askToStarRepoOnGitHub('schmeits/filament-pan-analytics-widget');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-pan-analytics-widget/{$file->getFilename()}"),
                ], 'filament-pan-analytics-widget-stubs');
            }
        }

        // register livewire component
        Livewire::component('filament-pan-analytics-widget', Widgets\PanAnalyticsTableWidget::class);

        // Testing
        Testable::mixin(new TestsFilamentPanAnalyticsWidget);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'schmeits/filament-pan-analytics-widget';
    }
}
