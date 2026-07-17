<?php

declare(strict_types=1);

use Illuminate\Support\ServiceProvider;
use TempiMarathon\OpenMeteo\Laravel\OpenMeteoServiceProvider;
use TempiMarathon\OpenMeteo\OpenMeteo;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;
use TempiMarathon\OpenMeteo\Tests\TestbenchTestCase;

uses(TestbenchTestCase::class);

covers(OpenMeteoConfig::class);

beforeEach(function (): void {
    // Pest's global beforeEach resets the static config (clearing any resolver),
    // so force-register the provider bindings again against the freshly booted app.
    $this->app->register(new OpenMeteoServiceProvider($this->app), true);
});

it('merges package config defaults with zero setup', function (): void {
    expect(config('openmeteo.hosts.forecast'))->toBe('https://api.open-meteo.com/v1/')
        ->and(OpenMeteoConfig::host('forecast', 'missing'))->toContain('api.open-meteo.com');
});

it('reads live container config through the resolver', function (): void {
    config()->set('openmeteo.hosts.forecast', 'https://custom.example/v1/');

    expect(OpenMeteoConfig::host('forecast', 'https://api.open-meteo.com/v1/'))
        ->toBe('https://custom.example/v1/');
});

it('binds the OpenMeteo entry point as a singleton', function (): void {
    $first = $this->app->make(OpenMeteo::class);
    $second = $this->app->make(OpenMeteo::class);

    expect($first)->toBeInstanceOf(OpenMeteo::class)
        ->and($first)->toBe($second);
});

it('registers the config publish group instead of copying files on boot', function (): void {
    $paths = ServiceProvider::pathsToPublish(OpenMeteoServiceProvider::class, 'openmeteo-config');

    expect($paths)->not->toBeEmpty()
        ->and(array_keys($paths)[0])->toEndWith('config/openmeteo.php');
});
