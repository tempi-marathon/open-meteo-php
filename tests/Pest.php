<?php

declare(strict_types=1);

use OpenMeteo\Support\OpenMeteoConfig;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

uses()->beforeEach(function (): void {
    MockClient::destroyGlobal();
    OpenMeteoConfig::reset();
})->in(__DIR__);

/** @return array<string, mixed> */
function forecastPayload(): array
{
    return [
        'latitude' => 52.37,
        'longitude' => 4.89,
        'timezone' => 'Europe/Amsterdam',
        'hourly' => [
            'time' => ['2026-07-06T12:00'],
            'temperature_2m' => [18.0],
            'apparent_temperature' => [17.0],
            'precipitation' => [0.0],
            'weathercode' => [0],
            'windspeed_10m' => [5.5],
            'winddirection_10m' => [90],
            'is_day' => [1],
        ],
        'daily' => [],
        'hourly_units' => ['time' => 'iso8601', 'temperature_2m' => '°C'],
        'daily_units' => [],
    ];
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

function mockError(string $reason, int $status = 400): MockResponse
{
    return MockResponse::make(['error' => true, 'reason' => $reason], $status);
}
