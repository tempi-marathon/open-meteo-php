<?php

declare(strict_types=1);

namespace OpenMeteo\Laravel;

use OpenMeteo\Support\OpenMeteoConfig;

final class OpenMeteoServiceProvider
{
    public function register(): void
    {
        if (! function_exists('config')) {
            return;
        }
        /** @var array<string, mixed>|null $config */
        $config = config('openmeteo');
        if (is_array($config)) {
            OpenMeteoConfig::configure($config);
        }
    }

    public function boot(): void
    {
        if (! function_exists('base_path')) {
            return;
        }
        $target = base_path('config/openmeteo.php');
        if (! is_file($target)) {
            copy(dirname(__DIR__).'/config/openmeteo.php', $target);
        }
    }
}
