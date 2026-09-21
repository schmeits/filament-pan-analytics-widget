<?php

namespace Schmeits\FilamentPanAnalyticsWidget\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RuntimeException;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;
use Schmeits\FilamentPanAnalyticsWidget\FilamentPanAnalyticsWidgetServiceProvider;
use Schmeits\FilamentPanAnalyticsWidget\Tests\Fixtures\TestPanelProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Schmeits\\FilamentPanAnalyticsWidget\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        // This package only reads the local pan_analytics table and should never
        // reach out over the network. The moment someone does add an outgoing
        // call, the affected test fails right here instead of silently calling an
        // external service in CI.
        Http::preventStrayRequests();
    }

    protected function getPackageProviders($app)
    {
        return [
            ActionsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            FilamentPanAnalyticsWidgetServiceProvider::class,
            TestPanelProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        // Filament encrypts table state into the rendered blade output, so
        // without an app.key every widget render throws a MissingAppKeyException.
        config()->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    /**
     * Create the pan_analytics table that panphp/pan normally provides via its migration.
     *
     * Deliberately NOT using artisan migrate: that could in theory hit a real
     * database. This helper refuses to run until it has proven we are on an
     * in-memory sqlite database, so a misconfigured environment fails hard
     * instead of silently creating tables somewhere.
     */
    protected function createPanAnalyticsTable(): void
    {
        $this->assertRunningOnInMemorySqlite();

        Schema::create('pan_analytics', function ($table): void {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('hovers')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
        });
    }

    /**
     * Hard safety check on the test connection.
     *
     * @throws RuntimeException as soon as the tests run on anything other than sqlite :memory:
     */
    protected function assertRunningOnInMemorySqlite(): void
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        $database = config("database.connections.{$connection}.database");

        if ($driver !== 'sqlite' || $database !== ':memory:') {
            throw new RuntimeException(
                "Refusing to run tests: connection [{$connection}] is not sqlite :memory: " .
                "but [{$driver}] on [{$database}]."
            );
        }
    }
}
