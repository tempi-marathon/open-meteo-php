<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use TempiMarathon\OpenMeteo\Contracts\CoordinateResponse;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Support\CreatesTimeSeriesResponse;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

uses()->beforeEach(function (): void {
    MockClient::destroyGlobal();
    OpenMeteoConfig::reset();
})->in(__DIR__);

/** @return array<string, mixed> */
function fixturePayload(string $name): array
{
    $path = __DIR__.'/Fixtures/responses/'.$name.'.json';
    if (! is_file($path)) {
        throw new RuntimeException("Missing fixture: {$path}");
    }

    /** @var array<string, mixed> $payload */
    $payload = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

    return $payload;
}

/** @return array<string, mixed> */
function forecastPayload(): array
{
    return fixturePayload('forecast');
}

/** @return array<string, mixed> */
function airQualityPayload(): array
{
    return fixturePayload('air_quality');
}

/** @return array<string, mixed> */
function marinePayload(): array
{
    return fixturePayload('marine');
}

/** @return array<string, mixed> */
function climatePayload(): array
{
    return fixturePayload('climate');
}

/** @return array<string, mixed> */
function floodPayload(): array
{
    return fixturePayload('flood');
}

/** @return array<string, mixed> */
function ensemblePayload(): array
{
    return fixturePayload('ensemble');
}

/** @return array<string, mixed> */
function seasonalPayload(): array
{
    return fixturePayload('seasonal');
}

/** @return array<string, mixed> */
function historicalPayload(): array
{
    return fixturePayload('historical');
}

/** @return array<string, mixed> */
function elevationPayload(): array
{
    return fixturePayload('elevation');
}

/** @return array<string, mixed> */
function geocodingSearchPayload(): array
{
    return [
        'results' => [
            [
                'id' => 2759794,
                'name' => 'Amsterdam',
                'latitude' => 52.37403,
                'longitude' => 4.88969,
                'timezone' => 'Europe/Amsterdam',
                'country' => 'Netherlands',
                'country_code' => 'NL',
                'admin1' => 'North Holland',
                'feature_code' => 'PPLA',
                'elevation' => 4.0,
                'population' => 741636,
                'postcodes' => ['1011'],
                'admin1_id' => 1,
            ],
        ],
    ];
}

/** @return array<string, mixed> */
function geocodingGetPayload(): array
{
    return [
        'id' => 2759794,
        'name' => 'Amsterdam',
        'latitude' => 52.37403,
        'longitude' => 4.88969,
        'timezone' => 'Europe/Amsterdam',
        'country' => 'Netherlands',
        'country_code' => 'NL',
        'admin1' => 'North Holland',
    ];
}

/** @param array<string, mixed> $body */
function mockOk(array $body): MockResponse
{
    return MockResponse::make($body, 200);
}

/**
 * @template T of \TempiMarathon\OpenMeteo\Contracts\CoordinateResponse
 *
 * @param  array<string, mixed>  $payload
 * @param  class-string<T>  $responseClass
 * @return T
 */
function timeSeriesResponseFromPayload(array $payload, string $responseClass = ForecastResponse::class): CoordinateResponse
{
    return (new class
    {
        use CreatesTimeSeriesResponse;

        /** @param class-string<CoordinateResponse> $responseClass */
        public function make(array $payload, string $responseClass): CoordinateResponse
        {
            return $this->createTimeSeriesResponseFromPayload($payload, $responseClass);
        }
    })->make($payload, $responseClass);
}

function mockError(string $reason, int $status = 400): MockResponse
{
    return MockResponse::make(['error' => true, 'reason' => $reason], $status);
}
