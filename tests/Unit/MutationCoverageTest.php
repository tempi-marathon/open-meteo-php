<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use TempiMarathon\OpenMeteo\Connectors\ForecastConnector;
use TempiMarathon\OpenMeteo\Data\HourlySeries;
use TempiMarathon\OpenMeteo\Enums\WeatherCode;
use TempiMarathon\OpenMeteo\Requests\Forecast\GetForecastRequest;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;
use TempiMarathon\OpenMeteo\Support\RedactsUriSecrets;
use TempiMarathon\OpenMeteo\WindDirection;

covers(
    HourlySeries::class,
    OpenMeteoConfig::class,
    RedactsUriSecrets::class,
    WindDirection::class,
);

it('creates wind direction from non-null degrees', function (): void {
    $direction = WindDirection::tryFrom(225);

    expect($direction)->toBeInstanceOf(WindDirection::class)
        ->and($direction?->getRaw())->toBe(225)
        ->and($direction?->label())->toBe('SW');
});

it('labels all sixteen compass points', function (int|float $degrees, string $label): void {
    expect(WindDirection::fromDegrees($degrees)->label())->toBe($label);
})->with([
    'n' => [0, 'N'],
    'nne' => [22.5, 'NNE'],
    'ne' => [45, 'NE'],
    'ene' => [67.5, 'ENE'],
    'e' => [90, 'E'],
    'ese' => [112.5, 'ESE'],
    'se' => [135, 'SE'],
    'sse' => [157.5, 'SSE'],
    's' => [180, 'S'],
    'ssw' => [202.5, 'SSW'],
    'sw' => [225, 'SW'],
    'wsw' => [247.5, 'WSW'],
    'w' => [270, 'W'],
    'wnw' => [292.5, 'WNW'],
    'nw' => [315, 'NW'],
    'nnw' => [337.5, 'NNW'],
]);

it('uses empty config path environment variable as unset', function (): void {
    OpenMeteoConfig::reset();
    putenv('OPENMETEO_CONFIG_PATH=');

    expect(OpenMeteoConfig::host('forecast', 'https://wrong.example/v1/'))
        ->toBe('https://api.open-meteo.com/v1/');

    putenv('OPENMETEO_CONFIG_PATH');
});

it('falls back to the package config for paths outside the package root', function (): void {
    OpenMeteoConfig::reset();

    $outside = sys_get_temp_dir().'/open-meteo-outside-config.php';
    file_put_contents($outside, "<?php return ['apikey' => 'outside-key'];");
    putenv('OPENMETEO_CONFIG_PATH='.$outside);

    expect(OpenMeteoConfig::apiKey())->not->toBe('outside-key')
        ->and(OpenMeteoConfig::host('forecast', 'https://wrong.example/v1/'))
        ->toBe('https://api.open-meteo.com/v1/');

    unlink($outside);
    putenv('OPENMETEO_CONFIG_PATH');
});

it('loads trusted config files from inside the package root', function (): void {
    OpenMeteoConfig::reset();
    putenv('OPENMETEO_CONFIG_PATH='.dirname(__DIR__).'/Fixtures/localhost-openmeteo-config.php');

    expect(OpenMeteoConfig::host('forecast', 'https://api.open-meteo.com/v1/'))
        ->toBe('https://localhost/v1/');

    putenv('OPENMETEO_CONFIG_PATH');
});

it('exposes default hosts for every api surface', function (string $surface, string $host): void {
    OpenMeteoConfig::reset();

    expect(OpenMeteoConfig::host($surface, 'https://wrong.example/v1/'))->toBe($host);
})->with([
    'forecast' => ['forecast', 'https://api.open-meteo.com/v1/'],
    'historical' => ['historical', 'https://archive-api.open-meteo.com/v1/'],
    'geocoding' => ['geocoding', 'https://geocoding-api.open-meteo.com/v1/'],
    'air quality' => ['air_quality', 'https://air-quality-api.open-meteo.com/v1/'],
    'marine' => ['marine', 'https://marine-api.open-meteo.com/v1/'],
    'climate' => ['climate', 'https://climate-api.open-meteo.com/v1/'],
    'flood' => ['flood', 'https://flood-api.open-meteo.com/v1/'],
    'ensemble' => ['ensemble', 'https://ensemble-api.open-meteo.com/v1/'],
    'seasonal' => ['seasonal', 'https://seasonal-api.open-meteo.com/v1/'],
    'elevation' => ['elevation', 'https://api.open-meteo.com/v1/'],
]);

it('rejects config files outside the package root even with a matching prefix', function (): void {
    OpenMeteoConfig::reset();

    $packageRoot = dirname(__DIR__, 2);
    $evilDirectory = $packageRoot.'-evil';
    $evilConfig = $evilDirectory.'/evil.php';

    if (is_dir($evilDirectory)) {
        array_map(unlink(...), glob($evilDirectory.'/*') ?: []);
        rmdir($evilDirectory);
    }

    mkdir($evilDirectory);
    file_put_contents($evilConfig, "<?php return ['apikey' => 'evil-key'];");
    putenv('OPENMETEO_CONFIG_PATH='.$evilConfig);

    expect(OpenMeteoConfig::apiKey())->not->toBe('evil-key');

    unlink($evilConfig);
    rmdir($evilDirectory);
    putenv('OPENMETEO_CONFIG_PATH');
});

it('transforms valid urls through customer host conversion', function (): void {
    $toCustomerHost = (new ReflectionMethod(OpenMeteoConfig::class, 'toCustomerHost'))->getClosure();

    expect($toCustomerHost('https://api.open-meteo.com/v1/forecast'))
        ->toBe('https://customer-api.open-meteo.com/v1/forecast');
});

it('allows https urls with a host and no path', function (): void {
    $isAllowedHostUrl = (new ReflectionMethod(OpenMeteoConfig::class, 'isAllowedFileHostUrl'))->getClosure();

    expect($isAllowedHostUrl('https://api.open-meteo.com'))->toBeTrue();
});

it('redacts api keys from uris without a path segment', function (): void {
    expect(RedactsUriSecrets::redact('https://api.open-meteo.com?apikey=secret-key'))
        ->toBe('https://api.open-meteo.com?apikey=%5BREDACTED%5D')
        ->not->toContain('PEST Mutator was here!');
});

it('rebuilds redacted uris with scheme host and credentials', function (): void {
    expect(RedactsUriSecrets::redact('https://user:secret@api.open-meteo.com?apikey=secret-key'))
        ->toBe('https://user:[REDACTED]@api.open-meteo.com?apikey=%5BREDACTED%5D');
});

it('returns null for missing hourly indexes without throwing', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk([
            'latitude' => 52.37,
            'longitude' => 4.89,
            'timezone' => 'Europe/Amsterdam',
            'hourly' => [
                'time' => ['2026-07-06T10:00', '2026-07-06T11:00'],
            ],
            'hourly_units' => [],
            'daily_units' => [],
            'daily' => [],
        ]),
    ]);

    $readings = (new ForecastConnector)
        ->weather()
        ->get(52.37, 4.89)
        ->dto()
        ->hourly();

    expect($readings->count())->toBe(2)
        ->and($readings->closestTo(new DateTimeImmutable('2026-07-06T11:00'))?->get('temperature_2m'))->toBeNull()
        ->and($readings->closestTo(new DateTimeImmutable('2026-07-06T11:00'))?->get('weathercode'))->toBeNull()
        ->and($readings->closestTo(new DateTimeImmutable('2026-07-06T11:00'))?->get('wind_speed_10m'))->toBeNull()
        ->and($readings->closestTo(new DateTimeImmutable('2026-07-06T11:00'))?->get('wind_direction_10m'))->toBeNull()
        ->and($readings->closestTo(new DateTimeImmutable('2026-07-06T11:00'))?->get('is_day'))->toBeNull();
});

it('returns null for absent weather code indexes', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockOk([
            'latitude' => 52.37,
            'longitude' => 4.89,
            'timezone' => 'Europe/Amsterdam',
            'hourly' => [
                'time' => ['2026-07-06T10:00', '2026-07-06T11:00'],
                'weathercode' => [WeatherCode::CLEAR->value],
            ],
            'hourly_units' => [],
            'daily_units' => [],
            'daily' => [],
        ]),
    ]);

    $reading = (new ForecastConnector)
        ->weather()
        ->get(52.37, 4.89)
        ->dto()
        ->hourly()
        ->closestTo(new DateTimeImmutable('2026-07-06T11:00'));

    expect($reading?->get('weathercode'))->toBeNull();
});

it('returns null when closest reading is requested on an empty collection', function (): void {
    expect((new HourlySeries([]))->closestTo(new DateTimeImmutable('2026-07-06T12:00')))->toBeNull();
});
