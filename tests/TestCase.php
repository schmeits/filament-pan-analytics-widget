<?php

declare(strict_types=1);

/*
 * tests/TestCase.php
 *
 * Testbench base: boots Filament with a test panel on an in-memory sqlite database.
 */

namespace Schmeits\FilamentPanAnalyticsWidget\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Schema\Blueprint;
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

        // This package only reads the local pan_analytics table and must never
        // reach out over the network.
        Http::preventStrayRequests();
    }

    /**
     * The order is functional: Filament\Support rebinds Livewire's DataStore, so
     * it has to register before Livewire, as package discovery does in a real app.
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            SupportServiceProvider::class,
            ActionsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            NotificationsServiceProvider::class,
            SchemasServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            LivewireServiceProvider::class,
            FilamentPanAnalyticsWidgetServiceProvider::class,
            TestPanelProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        // Filament encrypts table state into the rendered output, so without an
        // app key every widget render throws a MissingAppKeyException.
        config()->set('app.key', 'base64:' . base64_encode(str_repeat('a', 32)));
    }

    /**
     * Create the pan_analytics table that panphp/pan normally provides via its
     * migration. Refuses to run on anything other than sqlite :memory:.
     */
    public static function createPanAnalyticsTable(): void
    {
        static::assertRunningOnInMemorySqlite();

        Schema::create('pan_analytics', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('hovers')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
        });
    }

    /**
     * @throws RuntimeException as soon as the tests run on anything other than sqlite :memory:
     */
    protected static function assertRunningOnInMemorySqlite(): void
    {
        $connection = config()->string('database.default');
        $driver = config()->string("database.connections.{$connection}.driver", '');
        $database = config()->string("database.connections.{$connection}.database", '');

        if ($driver !== 'sqlite' || $database !== ':memory:') {
            throw new RuntimeException(
                "Refusing to run tests: connection [{$connection}] is not sqlite :memory: " .
                "but [{$driver}] on [{$database}]."
            );
        }
    }
}
