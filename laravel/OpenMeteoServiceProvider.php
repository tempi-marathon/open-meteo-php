<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Laravel;

use Illuminate\Support\ServiceProvider;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

final class OpenMeteoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/config/openmeteo.php', 'openmeteo');

        /** @var array<string, mixed> $config */
        $config = config('openmeteo', []);
        OpenMeteoConfig::configure($config);
    }

    public function boot(): void
    {
        $target = $this->app->configPath('openmeteo.php');

        if (! is_file($target)) {
            copy(dirname(__DIR__).'/config/openmeteo.php', $target);
        }
    }
}
