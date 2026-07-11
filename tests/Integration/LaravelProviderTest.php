<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use TempiMarathon\OpenMeteo\Laravel\OpenMeteoServiceProvider;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

covers(
    OpenMeteoConfig::class,
);

it('boots laravel service provider', function (): void {
    $root = dirname(__DIR__, 2);
    $configPath = sys_get_temp_dir().'/open-meteo-provider-test-'.uniqid('', true);
    mkdir($configPath);

    $app = new Application($root);
    $app->useConfigPath($configPath);
    $app->instance('config', new Repository([
        'openmeteo' => require $root.'/config/openmeteo.php',
    ]));

    $provider = new OpenMeteoServiceProvider($app);
    $app->register($provider);
    $provider->boot();

    expect(OpenMeteoConfig::host('forecast', 'missing'))->toContain('api.open-meteo.com')
        ->and(is_file($configPath.'/openmeteo.php'))->toBeTrue();
});
