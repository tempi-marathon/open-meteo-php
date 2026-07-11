<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Connectors\ForecastConnector;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Resources\BaseResource;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;
use TempiMarathon\OpenMeteo\Support\RedactsUriSecrets;

covers(
    RedactsUriSecrets::class,
    BaseResource::class,
    OpenMeteoConfig::class,
);

it('redacts api keys from uris', function (): void {
    $uri = 'https://api.open-meteo.com/v1/forecast?latitude=52.37&apikey=secret-key&timezone=GMT';

    expect(RedactsUriSecrets::redact($uri))
        ->toContain('latitude=52.37')
        ->toContain('apikey=%5BREDACTED%5D')
        ->not->toContain('secret-key');
});

it('redacts api keys from debug urls', function (): void {
    OpenMeteoConfig::configure(['apikey' => 'secret-key']);

    $connector = new ForecastConnector;
    $request = $connector->weather()->get(52.37, 4.89)->timezone(Timezone::GMT);

    expect($connector->weather()->debugUrl($request))
        ->toContain('forecast?')
        ->toContain('latitude=52.37')
        ->toContain('apikey=%5BREDACTED%5D')
        ->not->toContain('secret-key');

    OpenMeteoConfig::reset();
});

it('rejects untrusted host overrides from config files', function (): void {
    OpenMeteoConfig::reset();
    putenv('OPENMETEO_CONFIG_PATH='.dirname(__DIR__).'/Fixtures/insecure-openmeteo-config.php');

    expect(OpenMeteoConfig::host('forecast', 'https://api.open-meteo.com/v1/'))
        ->toBe('https://api.open-meteo.com/v1/');

    putenv('OPENMETEO_CONFIG_PATH');
});

it('ignores config paths outside the package root', function (): void {
    OpenMeteoConfig::reset();
    $outside = sys_get_temp_dir().'/open-meteo-outside-config.php';
    file_put_contents($outside, "<?php return ['apikey' => 'outside-key'];");
    putenv('OPENMETEO_CONFIG_PATH='.$outside);

    expect(OpenMeteoConfig::apiKey())->not->toBe('outside-key');

    unlink($outside);
    putenv('OPENMETEO_CONFIG_PATH');
});

it('allows trusted host overrides from configure', function (): void {
    OpenMeteoConfig::configure([
        'hosts' => ['forecast' => 'https://custom.example/v1/'],
    ]);

    expect(OpenMeteoConfig::host('forecast', 'https://api.open-meteo.com/v1/'))
        ->toBe('https://custom.example/v1/');

    OpenMeteoConfig::reset();
});

it('rejects non-https hosts from config files', function (): void {
    OpenMeteoConfig::reset();
    putenv('OPENMETEO_CONFIG_PATH='.dirname(__DIR__).'/Fixtures/http-openmeteo-config.php');

    expect(OpenMeteoConfig::host('forecast', 'https://api.open-meteo.com/v1/'))
        ->toBe('https://api.open-meteo.com/v1/');

    putenv('OPENMETEO_CONFIG_PATH');
});

it('allows localhost hosts from config files', function (): void {
    OpenMeteoConfig::reset();
    putenv('OPENMETEO_CONFIG_PATH='.dirname(__DIR__).'/Fixtures/localhost-openmeteo-config.php');

    expect(OpenMeteoConfig::host('forecast', 'https://api.open-meteo.com/v1/'))
        ->toBe('https://localhost/v1/');

    putenv('OPENMETEO_CONFIG_PATH');
});

it('falls back when config path is not a php file', function (): void {
    OpenMeteoConfig::reset();
    putenv('OPENMETEO_CONFIG_PATH='.dirname(__DIR__).'/Fixtures/openmeteo-config');

    expect(OpenMeteoConfig::host('forecast', 'fallback'))->toContain('api.open-meteo.com');

    putenv('OPENMETEO_CONFIG_PATH');
});

it('returns uris without query strings unchanged', function (): void {
    expect(RedactsUriSecrets::redact('https://api.open-meteo.com/v1/forecast'))
        ->toBe('https://api.open-meteo.com/v1/forecast');
});

it('rebuilds uris with auth port and fragment components', function (): void {
    $uri = 'https://user:secret@api.open-meteo.com:8443/v1/forecast?apikey=secret-key#section';

    expect(RedactsUriSecrets::redact($uri))
        ->toBe('https://user:[REDACTED]@api.open-meteo.com:8443/v1/forecast?apikey=%5BREDACTED%5D#section');
});
