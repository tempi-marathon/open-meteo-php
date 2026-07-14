<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Data\AirQualityUnits;
use TempiMarathon\OpenMeteo\Data\DailyUnits;
use TempiMarathon\OpenMeteo\Data\ForecastUnits;
use TempiMarathon\OpenMeteo\Data\HistoricalUnits;
use TempiMarathon\OpenMeteo\Data\HourlyUnits;
use TempiMarathon\OpenMeteo\Data\MarineUnits;
use TempiMarathon\OpenMeteo\Data\SeasonalUnits;
use TempiMarathon\OpenMeteo\Enums\HourlyVariable;

covers(
    AirQualityUnits::class,
    DailyUnits::class,
    ForecastUnits::class,
    HistoricalUnits::class,
    HourlyUnits::class,
    MarineUnits::class,
    SeasonalUnits::class,
);

it('resolves forecast units for each interval', function (): void {
    $units = new ForecastUnits(
        hourlyUnits: ['temperature_2m' => '°C'],
        dailyUnits: ['temperature_2m_max' => '°C'],
        currentUnits: ['temperature_2m' => '°C'],
        minutely15Units: ['temperature_2m' => '°C'],
    );

    expect($units->hourlyUnit('temperature_2m'))->toBe('°C')
        ->and($units->hourlyUnit(HourlyVariable::Temperature2m))->toBe('°C')
        ->and($units->dailyUnit('temperature_2m_max'))->toBe('°C')
        ->and($units->currentUnit('temperature_2m'))->toBe('°C')
        ->and($units->minutely15Unit('temperature_2m'))->toBe('°C')
        ->and($units->hourlyUnit('missing'))->toBeNull();
});

it('resolves historical units', function (): void {
    $units = new HistoricalUnits(
        hourlyUnits: ['temperature_2m' => '°C'],
        dailyUnits: ['temperature_2m_max' => '°C'],
    );

    expect($units->hourlyUnit('temperature_2m'))->toBe('°C')
        ->and($units->dailyUnit('temperature_2m_max'))->toBe('°C')
        ->and($units->dailyUnit('missing'))->toBeNull();
});

it('resolves air quality units', function (): void {
    $units = new AirQualityUnits(
        hourlyUnits: ['european_aqi' => 'EAQI'],
        currentUnits: ['european_aqi' => 'EAQI'],
    );

    expect($units->hourlyUnit('european_aqi'))->toBe('EAQI')
        ->and($units->currentUnit('european_aqi'))->toBe('EAQI')
        ->and($units->currentUnit('missing'))->toBeNull();
});

it('resolves marine units', function (): void {
    $units = new MarineUnits(
        hourlyUnits: ['wave_height' => 'm'],
        currentUnits: ['wave_height' => 'm'],
        minutely15Units: ['wave_height' => 'm'],
    );

    expect($units->hourlyUnit('wave_height'))->toBe('m')
        ->and($units->currentUnit('wave_height'))->toBe('m')
        ->and($units->minutely15Unit('wave_height'))->toBe('m')
        ->and($units->minutely15Unit('missing'))->toBeNull();
});

it('resolves daily-only units', function (): void {
    $units = new DailyUnits(['river_discharge' => 'm³/s']);

    expect($units->dailyUnit('river_discharge'))->toBe('m³/s')
        ->and($units->dailyUnit('missing'))->toBeNull();
});

it('resolves hourly-only units', function (): void {
    $units = new HourlyUnits(['temperature_2m' => '°C']);

    expect($units->hourlyUnit('temperature_2m'))->toBe('°C')
        ->and($units->hourlyUnit('missing'))->toBeNull();
});

it('resolves seasonal units', function (): void {
    $units = new SeasonalUnits(
        dailyUnits: ['temperature_2m_max' => '°C'],
        monthlyUnits: ['temperature_2m_mean' => '°C'],
    );

    expect($units->dailyUnit('temperature_2m_max'))->toBe('°C')
        ->and($units->monthlyUnit('temperature_2m_mean'))->toBe('°C')
        ->and($units->monthlyUnit('missing'))->toBeNull();
});
