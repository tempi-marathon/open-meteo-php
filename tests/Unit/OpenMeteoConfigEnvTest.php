<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

covers(
    OpenMeteoConfig::class,
);

it('reads api key from environment variables', function (): void {
    OpenMeteoConfig::reset();
    putenv('OPENMETEO_API_KEY=test-api-key');
    putenv('OPENMETEO_USER_AGENT=test-agent');

    expect(OpenMeteoConfig::apiKey())->toBe('test-api-key')
        ->and(OpenMeteoConfig::userAgent())->toBe('test-agent');

    putenv('OPENMETEO_API_KEY');
    putenv('OPENMETEO_USER_AGENT');
});

it('auto switches default hosts to customer endpoints when api key is set', function (): void {
    OpenMeteoConfig::configure(['apikey' => 'commercial-key']);

    expect(OpenMeteoConfig::host('forecast', 'https://api.open-meteo.com/v1/'))
        ->toBe('https://customer-api.open-meteo.com/v1/')
        ->and(OpenMeteoConfig::host('historical', 'https://archive-api.open-meteo.com/v1/'))
        ->toBe('https://customer-archive-api.open-meteo.com/v1/')
        ->and(OpenMeteoConfig::host('geocoding', 'https://geocoding-api.open-meteo.com/v1/'))
        ->toBe('https://customer-geocoding-api.open-meteo.com/v1/')
        ->and(OpenMeteoConfig::host('air_quality', 'https://air-quality-api.open-meteo.com/v1/'))
        ->toBe('https://customer-air-quality-api.open-meteo.com/v1/')
        ->and(OpenMeteoConfig::host('marine', 'https://marine-api.open-meteo.com/v1/'))
        ->toBe('https://customer-marine-api.open-meteo.com/v1/')
        ->and(OpenMeteoConfig::host('climate', 'https://climate-api.open-meteo.com/v1/'))
        ->toBe('https://customer-climate-api.open-meteo.com/v1/')
        ->and(OpenMeteoConfig::host('flood', 'https://flood-api.open-meteo.com/v1/'))
        ->toBe('https://customer-flood-api.open-meteo.com/v1/')
        ->and(OpenMeteoConfig::host('ensemble', 'https://ensemble-api.open-meteo.com/v1/'))
        ->toBe('https://customer-ensemble-api.open-meteo.com/v1/')
        ->and(OpenMeteoConfig::host('seasonal', 'https://seasonal-api.open-meteo.com/v1/'))
        ->toBe('https://customer-seasonal-api.open-meteo.com/v1/')
        ->and(OpenMeteoConfig::host('elevation', 'https://api.open-meteo.com/v1/'))
        ->toBe('https://customer-api.open-meteo.com/v1/');

    OpenMeteoConfig::reset();
});

it('keeps free tier hosts when no api key is configured', function (): void {
    OpenMeteoConfig::reset();

    expect(OpenMeteoConfig::host('forecast', 'https://api.open-meteo.com/v1/'))
        ->toBe('https://api.open-meteo.com/v1/');

    OpenMeteoConfig::reset();
});

it('does not double prefix customer hosts', function (): void {
    OpenMeteoConfig::configure([
        'apikey' => 'commercial-key',
        'hosts' => ['forecast' => 'https://customer-api.open-meteo.com/v1/'],
    ]);

    expect(OpenMeteoConfig::host('forecast', 'https://api.open-meteo.com/v1/'))
        ->toBe('https://customer-api.open-meteo.com/v1/');

    OpenMeteoConfig::reset();
});

it('does not transform custom or localhost hosts when api key is set', function (): void {
    OpenMeteoConfig::configure([
        'apikey' => 'commercial-key',
        'hosts' => [
            'forecast' => 'https://custom.example/v1/',
            'geocoding' => 'https://localhost/v1/',
        ],
    ]);

    expect(OpenMeteoConfig::host('forecast', 'https://api.open-meteo.com/v1/'))
        ->toBe('https://custom.example/v1/')
        ->and(OpenMeteoConfig::host('geocoding', 'https://geocoding-api.open-meteo.com/v1/'))
        ->toBe('https://localhost/v1/');

    OpenMeteoConfig::reset();
});

it('auto switches hosts from environment api key', function (): void {
    OpenMeteoConfig::reset();
    putenv('OPENMETEO_API_KEY=test-api-key');

    expect(OpenMeteoConfig::host('forecast', 'https://api.open-meteo.com/v1/'))
        ->toBe('https://customer-api.open-meteo.com/v1/');

    putenv('OPENMETEO_API_KEY');
});

it('leaves invalid and already customer urls unchanged when transforming', function (): void {
    $toCustomerHost = (new ReflectionMethod(OpenMeteoConfig::class, 'toCustomerHost'))->getClosure();

    expect($toCustomerHost('not-a-valid-url'))->toBe('not-a-valid-url')
        ->and($toCustomerHost('https://customer-api.open-meteo.com/v1/'))
        ->toBe('https://customer-api.open-meteo.com/v1/');
});
