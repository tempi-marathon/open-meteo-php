<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Laravel;

use Illuminate\Support\ServiceProvider;
use TempiMarathon\OpenMeteo\OpenMeteo;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

final class OpenMeteoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/config/openmeteo.php', 'openmeteo');

        // Resolve configuration lazily on every access so long-running runtimes
        // (Octane, queue workers) always see the current container config.
        OpenMeteoConfig::resolveUsing(static function (): array {
            /** @var array<string, mixed> $config */
            $config = config('openmeteo', []);

            return $config;
        });

        $this->app->singleton(OpenMeteo::class, static fn (): OpenMeteo => new OpenMeteo);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                dirname(__DIR__).'/config/openmeteo.php' => $this->app->configPath('openmeteo.php'),
            ], 'openmeteo-config');
        }
    }
}
