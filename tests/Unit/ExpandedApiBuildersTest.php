<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Data\EnsembleUnits;
use TempiMarathon\OpenMeteo\Data\FloodResponse;
use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Enums\AirQualityDomain;
use TempiMarathon\OpenMeteo\Enums\CellSelection;
use TempiMarathon\OpenMeteo\Enums\ClimateDailyVariable;
use TempiMarathon\OpenMeteo\Enums\ClimateModel;
use TempiMarathon\OpenMeteo\Enums\EnsembleDailyVariable;
use TempiMarathon\OpenMeteo\Enums\EnsembleHourlyVariable;
use TempiMarathon\OpenMeteo\Enums\EnsembleTemporalResolution;
use TempiMarathon\OpenMeteo\Enums\FloodDailyVariable;
use TempiMarathon\OpenMeteo\Enums\FloodModel;
use TempiMarathon\OpenMeteo\Enums\ForecastModel;
use TempiMarathon\OpenMeteo\Enums\HistoricalDailyVariable;
use TempiMarathon\OpenMeteo\Enums\HistoricalModel;
use TempiMarathon\OpenMeteo\Enums\LengthUnit;
use TempiMarathon\OpenMeteo\Enums\MarineDailyVariable;
use TempiMarathon\OpenMeteo\Enums\MarineHourlyVariable;
use TempiMarathon\OpenMeteo\Enums\MarineModel;
use TempiMarathon\OpenMeteo\Enums\PrecipitationUnit;
use TempiMarathon\OpenMeteo\Enums\SeasonalDailyVariable;
use TempiMarathon\OpenMeteo\Enums\SeasonalHourlyVariable;
use TempiMarathon\OpenMeteo\Enums\SeasonalModel;
use TempiMarathon\OpenMeteo\Enums\SeasonalWeeklyVariable;
use TempiMarathon\OpenMeteo\Enums\TemperatureUnit;
use TempiMarathon\OpenMeteo\Enums\TimeFormat;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Enums\WindSpeedUnit;
use TempiMarathon\OpenMeteo\Exceptions\InvalidCoordinateException;
use TempiMarathon\OpenMeteo\Exceptions\InvalidForecastParameterException;
use TempiMarathon\OpenMeteo\Exceptions\MissingDateRangeException;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;
use TempiMarathon\OpenMeteo\Requests\AirQuality\GetAirQualityRequest;
use TempiMarathon\OpenMeteo\Requests\Climate\GetClimateRequest;
use TempiMarathon\OpenMeteo\Requests\Elevation\GetElevationRequest;
use TempiMarathon\OpenMeteo\Requests\Ensemble\GetEnsembleRequest;
use TempiMarathon\OpenMeteo\Requests\Flood\GetFloodRequest;
use TempiMarathon\OpenMeteo\Requests\Forecast\GetForecastRequest;
use TempiMarathon\OpenMeteo\Requests\Historical\GetArchiveRequest;
use TempiMarathon\OpenMeteo\Requests\Marine\GetMarineRequest;
use TempiMarathon\OpenMeteo\Requests\Seasonal\GetSeasonalRequest;
use TempiMarathon\OpenMeteo\Support\ForecastWindowLimits;

covers(
    GetAirQualityRequest::class,
    GetClimateRequest::class,
    GetElevationRequest::class,
    GetEnsembleRequest::class,
    GetFloodRequest::class,
    GetForecastRequest::class,
    GetMarineRequest::class,
    GetSeasonalRequest::class,
    GetArchiveRequest::class,
    AbstractCoordinateGetRequest::class,
    EnsembleUnits::class,
    ForecastWindowLimits::class,
);

it('builds marine weather query options', function (): void {
    $request = GetMarineRequest::forCoordinates(52.37, 4.89)
        ->hourly(MarineHourlyVariable::WaveHeight)
        ->daily(MarineDailyVariable::WaveHeightMax)
        ->timezone(Timezone::EuropeAmsterdam)
        ->between(new DateTimeImmutable('2024-06-01'), new DateTimeImmutable('2024-06-15'))
        ->forecastDays(7)
        ->pastDays(1)
        ->lengthUnit(LengthUnit::Metric)
        ->temperatureUnit(TemperatureUnit::Celsius)
        ->windSpeedUnit(WindSpeedUnit::Ms)
        ->timeFormat(TimeFormat::Unixtime)
        ->cellSelection(CellSelection::Sea)
        ->models(MarineModel::BestMatch)
        ->withQueryParam('custom', 'value');
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['hourly'])->toBe('wave_height')
        ->and($query['daily'])->toBe('wave_height_max')
        ->and($query['timezone'])->toBe('Europe/Amsterdam')
        ->and($query['forecast_days'])->toBe('7')
        ->and($query['past_days'])->toBe('1')
        ->and($query['length_unit'])->toBe('metric')
        ->and($query['temperature_unit'])->toBe('celsius')
        ->and($query['wind_speed_unit'])->toBe('ms')
        ->and($query['timeformat'])->toBe('unixtime')
        ->and($query['cell_selection'])->toBe('sea')
        ->and($query['models'])->toBe('best_match')
        ->and($query['custom'])->toBe('value');
});

it('builds climate and flood daily queries', function (): void {
    $climate = GetClimateRequest::forCoordinates(52.37, 4.89)
        ->daily(ClimateDailyVariable::Temperature2mMax)
        ->precipitationUnit(PrecipitationUnit::Inch)
        ->between(new DateTimeImmutable('2024-06-01'), new DateTimeImmutable('2024-06-15'));
    $flood = GetFloodRequest::forCoordinates(52.37, 4.89)
        ->daily(FloodDailyVariable::RiverDischarge)
        ->forecastDays(92)
        ->ensemble(true);

    $climateQuery = (new ReflectionClass($climate))->getMethod('defaultQuery')->invoke($climate);
    $floodQuery = (new ReflectionClass($flood))->getMethod('defaultQuery')->invoke($flood);

    expect($climateQuery['daily'])->toBe('temperature_2m_max')
        ->and($climateQuery['precipitation_unit'])->toBe('inch')
        ->and($floodQuery['daily'])->toBe('river_discharge')
        ->and($floodQuery['forecast_days'])->toBe('92')
        ->and($floodQuery['ensemble'])->toBe('true');
});

it('builds ensemble and seasonal interval queries', function (): void {
    $ensemble = GetEnsembleRequest::forCoordinates(52.37, 4.89)
        ->hourly(EnsembleHourlyVariable::Temperature2m)
        ->daily(EnsembleDailyVariable::Temperature2mMax)
        ->forecastHours(24);
    $seasonal = GetSeasonalRequest::forCoordinates(52.37, 4.89)
        ->hourly(SeasonalHourlyVariable::Temperature2m)
        ->daily(SeasonalDailyVariable::Temperature2mMax)
        ->forecastDays(183);

    $ensembleQuery = (new ReflectionClass($ensemble))->getMethod('defaultQuery')->invoke($ensemble);
    $seasonalQuery = (new ReflectionClass($seasonal))->getMethod('defaultQuery')->invoke($seasonal);

    expect($ensembleQuery['hourly'])->toBe('temperature_2m')
        ->and($ensembleQuery['daily'])->toBe('temperature_2m_max')
        ->and($ensembleQuery['forecast_hours'])->toBe('24')
        ->and($seasonalQuery['hourly'])->toBe('temperature_2m')
        ->and($seasonalQuery['daily'])->toBe('temperature_2m_max')
        ->and($seasonalQuery['forecast_days'])->toBe('183');
});

it('builds forecast weather query options', function (): void {
    $request = GetForecastRequest::forCoordinates(52.37, 4.89)
        ->elevation(11.0)
        ->timeFormat(TimeFormat::Iso8601)
        ->withQueryParam('tilt', 30);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['elevation'])->toBe('11')
        ->and($query['timeformat'])->toBe('iso8601')
        ->and($query['tilt'])->toBe('30');
});

it('builds air quality forecast window options', function (): void {
    $request = GetAirQualityRequest::forCoordinates(52.37, 4.89)
        ->forecastDays(5)
        ->pastDays(2);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['forecast_days'])->toBe('5')
        ->and($query['past_days'])->toBe('2');
});

it('builds batch elevation queries', function (): void {
    $request = GetElevationRequest::forPoints([
        [52.37, 4.89],
        [48.13, 11.58],
    ]);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['latitude'])->toBe('52.37,48.13')
        ->and($query['longitude'])->toBe('4.89,11.58');
});

it('rejects empty elevation point lists', function (): void {
    expect(fn () => GetElevationRequest::forPoints([]))
        ->toThrow(InvalidCoordinateException::class, 'At least one coordinate pair is required.');
});

it('rejects unsupported forecast window options on climate requests', function (): void {
    expect(fn () => GetClimateRequest::forCoordinates(52.37, 4.89)->forecastDays(1))
        ->toThrow(InvalidForecastParameterException::class, 'forecast_days is not supported');
});

it('rejects flood ensemble false query building edge case', function (): void {
    $request = GetFloodRequest::forCoordinates(52.37, 4.89)->ensemble(false);
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['ensemble'])->toBe('false');
});

it('covers abstract coordinate default query', function (): void {
    $request = new class('52.37', '4.89') extends AbstractCoordinateGetRequest
    {
        protected function responseClass(): string
        {
            return FloodResponse::class;
        }

        public function resolveEndpoint(): string
        {
            return 'flood';
        }
    };
    $query = (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);

    expect($query['latitude'])->toBe('52.37')
        ->and($query['longitude'])->toBe('4.89');
});

it('covers remaining forecast window ranges and validation', function (): void {
    $ensemble = GetEnsembleRequest::forCoordinates(52.37, 4.89)
        ->forecastDays(7)
        ->pastDays(1);
    $flood = GetFloodRequest::forCoordinates(52.37, 4.89)->pastDays(1);
    $seasonal = GetSeasonalRequest::forCoordinates(52.37, 4.89)->pastDays(1);
    $archive = GetArchiveRequest::forCoordinates(52.37, 4.89)
        ->between(new DateTimeImmutable('2024-06-01'), new DateTimeImmutable('2024-06-15'))
        ->forecastDays(0)
        ->pastDays(1);

    expect((new ReflectionClass($ensemble))->getMethod('defaultQuery')->invoke($ensemble))
        ->toHaveKeys(['forecast_days', 'past_days'])
        ->and((new ReflectionClass($flood))->getMethod('defaultQuery')->invoke($flood)['past_days'])->toBe('1')
        ->and((new ReflectionClass($seasonal))->getMethod('defaultQuery')->invoke($seasonal)['past_days'])->toBe('1')
        ->and((new ReflectionClass($archive))->getMethod('defaultQuery')->invoke($archive))
        ->toMatchArray(['forecast_days' => '0', 'past_days' => '1']);

    expect(fn () => GetClimateRequest::forCoordinates(52.37, 4.89)->pastDays(1))
        ->toThrow(InvalidForecastParameterException::class, 'past_days is not supported')
        ->and(fn () => GetMarineRequest::forCoordinates(52.37, 4.89)->forecastHours(1))
        ->toThrow(InvalidForecastParameterException::class, 'forecast_hours is not supported')
        ->and(fn () => GetEnsembleRequest::forCoordinates(52.37, 4.89)->forecastDays(ForecastWindowLimits::ENSEMBLE_FORECAST_DAYS_MAX + 1))
        ->toThrow(InvalidForecastParameterException::class, 'forecast_days must be between 0 and 36')
        ->and(fn () => GetFloodRequest::forCoordinates(52.37, 4.89)->pastDays(ForecastWindowLimits::PAST_DAYS_MAX + 1))
        ->toThrow(InvalidForecastParameterException::class, 'past_days must be between 0 and 92');
});

it('rejects malformed elevation point pairs', function (): void {
    expect(fn () => GetElevationRequest::forPoints([[52.37]]))
        ->toThrow(InvalidCoordinateException::class, 'Each coordinate pair must contain latitude and longitude.');
});

it('resolves ensemble units', function (): void {
    $units = new EnsembleUnits(
        hourlyUnits: ['temperature_2m' => '°C'],
        dailyUnits: ['temperature_2m_max' => '°C'],
    );

    expect($units->hourlyUnit('temperature_2m'))->toBe('°C')
        ->and($units->dailyUnit('temperature_2m_max'))->toBe('°C')
        ->and($units->dailyUnit('missing'))->toBeNull();
});

it('defaults flood ensemble to true', function (): void {
    $query = invokeDefaultQuery(GetFloodRequest::forCoordinates(52.37, 4.89)->ensemble());

    expect($query['ensemble'])->toBe('true');
});

it('stringifies elevation coordinates for batch lookups', function (): void {
    $query = invokeDefaultQuery(GetElevationRequest::forPoints([[52.5, 13.4]]));

    expect($query['latitude'])->toBe('52.5')
        ->and($query['longitude'])->toBe('13.4');
});

it('omits unset interval parameters from thin endpoint queries', function (): void {
    $marine = invokeDefaultQuery(GetMarineRequest::forCoordinates(52.37, 4.89));
    $climate = invokeDefaultQuery(GetClimateRequest::forCoordinates(52.37, 4.89)
        ->between(new DateTimeImmutable('2024-06-01'), new DateTimeImmutable('2024-06-15')));
    $flood = invokeDefaultQuery(GetFloodRequest::forCoordinates(52.37, 4.89));
    $ensemble = invokeDefaultQuery(GetEnsembleRequest::forCoordinates(52.37, 4.89));
    $seasonal = invokeDefaultQuery(GetSeasonalRequest::forCoordinates(52.37, 4.89));

    expect($marine)->not->toHaveKeys(['hourly', 'daily', 'current', 'minutely_15'])
        ->and($climate)->not->toHaveKey('daily')
        ->and($flood)->not->toHaveKeys(['daily', 'ensemble'])
        ->and($ensemble)->not->toHaveKeys(['hourly', 'daily'])
        ->and($seasonal)->not->toHaveKeys(['hourly', 'daily', 'monthly', 'weekly'])
        ->and($marine['latitude'])->toBe('52.37');
});

it('builds newly supported query parameters', function (): void {
    $airQuality = invokeDefaultQuery(
        GetAirQualityRequest::forCoordinates(52.37, 4.89)->domains(AirQualityDomain::CamsEurope),
    );
    $climate = invokeDefaultQuery(
        GetClimateRequest::forCoordinates(52.37, 4.89)
            ->disableBiasCorrection()
            ->between(new DateTimeImmutable('2024-06-01'), new DateTimeImmutable('2024-06-15')),
    );
    $ensemble = invokeDefaultQuery(
        GetEnsembleRequest::forCoordinates(52.37, 4.89)
            ->temporalResolution(EnsembleTemporalResolution::Hourly3)
            ->pastHours(24)
            ->tilt(30.0)
            ->azimuth(180.0),
    );
    $seasonal = invokeDefaultQuery(
        GetSeasonalRequest::forCoordinates(52.37, 4.89)
            ->weekly(SeasonalWeeklyVariable::WindSpeed10mMean),
    );
    $forecast = invokeDefaultQuery(
        GetForecastRequest::forCoordinates(52.37, 4.89)->pastHours(12),
    );
    $archive = invokeDefaultQuery(
        GetArchiveRequest::forCoordinates(52.37, 4.89)
            ->between(new DateTimeImmutable('2024-06-01'), new DateTimeImmutable('2024-06-15'))
            ->pastHours(6)
            ->forecastHours(12),
    );

    expect($airQuality['domains'])->toBe('cams_europe')
        ->and($climate['disable_bias_correction'])->toBe('true')
        ->and($ensemble['temporal_resolution'])->toBe('hourly_3')
        ->and($ensemble['past_hours'])->toBe('24')
        ->and($ensemble['tilt'])->toBe('30')
        ->and($ensemble['azimuth'])->toBe('180')
        ->and($seasonal['weekly'])->toBe('wind_speed_10m_mean')
        ->and($forecast['past_hours'])->toBe('12')
        ->and($archive['past_hours'])->toBe('6')
        ->and($archive['forecast_hours'])->toBe('12');
});

it('requires date ranges for climate and historical endpoints', function (): void {
    expect(fn () => invokeDefaultQuery(GetClimateRequest::forCoordinates(52.37, 4.89)))
        ->toThrow(MissingDateRangeException::class, 'start_date and end_date are required')
        ->and(fn () => invokeDefaultQuery(
            GetArchiveRequest::forCoordinates(52.37, 4.89)->daily(HistoricalDailyVariable::Temperature2mMax),
        ))->toThrow(MissingDateRangeException::class, 'start_date and end_date are required');
});

it('rejects past hours on unsupported endpoints', function (): void {
    expect(fn () => GetMarineRequest::forCoordinates(52.37, 4.89)->pastHours(1))
        ->toThrow(InvalidForecastParameterException::class, 'past_hours is not supported');
});

it('rejects empty multi-point coordinate lists on abstract requests', function (): void {
    expect(fn () => GetArchiveRequest::forPoints([]))
        ->toThrow(InvalidCoordinateException::class, 'At least one coordinate pair is required.')
        ->and(fn () => GetEnsembleRequest::forPoints([[52.37]]))
        ->toThrow(InvalidCoordinateException::class, 'Each coordinate pair must contain latitude and longitude.');
});

it('validates past hours when an endpoint defines a range', function (): void {
    $request = new class('52.37', '4.89') extends AbstractCoordinateGetRequest
    {
        protected function responseClass(): string
        {
            return ForecastResponse::class;
        }

        public function resolveEndpoint(): string
        {
            return 'forecast';
        }

        protected function supportsPastHours(): bool
        {
            return true;
        }

        protected function supportedPastHoursRange(): array
        {
            return [0, 24];
        }
    };

    expect(fn () => $request->pastHours(25))
        ->toThrow(InvalidForecastParameterException::class, 'past_hours must be between 0 and 24, 25 given.');
});

it('exposes forecast response class from request', function (): void {
    $method = new ReflectionMethod(GetForecastRequest::class, 'responseClass');
    $method->setAccessible(true);

    expect($method->invoke(GetForecastRequest::forCoordinates(52.37, 4.89)))->toBe(ForecastResponse::class);
});

it('accepts typed weather model enums', function (): void {
    $query = invokeDefaultQuery(
        GetForecastRequest::forCoordinates(52.37, 4.89)
            ->models(ForecastModel::BestMatch, ForecastModel::EcmwfIfs),
    );

    expect($query['models'])->toBe('best_match,ecmwf_ifs');
});

it('scopes weather query options per endpoint', function (): void {
    $start = new DateTimeImmutable('2024-06-01');
    $end = new DateTimeImmutable('2024-06-15');

    $climate = invokeDefaultQuery(GetClimateRequest::forCoordinates(52.37, 4.89)
        ->between($start, $end)
        ->temperatureUnit(TemperatureUnit::Fahrenheit)
        ->windSpeedUnit(WindSpeedUnit::Mph)
        ->precipitationUnit(PrecipitationUnit::Inch)
        ->timeFormat(TimeFormat::Iso8601)
        ->cellSelection(CellSelection::Sea)
        ->models(ClimateModel::ECEarth3PHR)
        ->elevation(10.0));

    expect($climate)->toMatchArray([
        'temperature_unit' => 'fahrenheit',
        'wind_speed_unit' => 'mph',
        'precipitation_unit' => 'inch',
        'timeformat' => 'iso8601',
        'cell_selection' => 'sea',
        'models' => 'EC_Earth3P_HR',
    ])->and($climate)->not->toHaveKey('elevation');

    $marine = invokeDefaultQuery(GetMarineRequest::forCoordinates(52.37, 4.89)
        ->temperatureUnit(TemperatureUnit::Celsius)
        ->windSpeedUnit(WindSpeedUnit::Kmh)
        ->timeFormat(TimeFormat::Iso8601)
        ->cellSelection(CellSelection::Land)
        ->models(MarineModel::EcmwfWam)
        ->precipitationUnit(PrecipitationUnit::Mm)
        ->elevation(10.0));

    expect($marine)->toMatchArray([
        'temperature_unit' => 'celsius',
        'wind_speed_unit' => 'kmh',
        'timeformat' => 'iso8601',
        'cell_selection' => 'land',
        'models' => 'ecmwf_wam',
    ])->and($marine)->not->toHaveKeys(['precipitation_unit', 'elevation']);

    $airQuality = invokeDefaultQuery(GetAirQualityRequest::forCoordinates(52.37, 4.89)
        ->timeFormat(TimeFormat::Iso8601)
        ->temperatureUnit(TemperatureUnit::Celsius));

    expect($airQuality)->toMatchArray(['timeformat' => 'iso8601'])
        ->and($airQuality)->not->toHaveKey('temperature_unit');

    $flood = invokeDefaultQuery(GetFloodRequest::forCoordinates(52.37, 4.89)
        ->timeFormat(TimeFormat::Iso8601)
        ->cellSelection(CellSelection::Sea)
        ->models(FloodModel::SeamlessV4)
        ->temperatureUnit(TemperatureUnit::Celsius));

    expect($flood)->toMatchArray([
        'timeformat' => 'iso8601',
        'cell_selection' => 'sea',
        'models' => 'seamless_v4',
    ])->and($flood)->not->toHaveKey('temperature_unit');

    $seasonal = invokeDefaultQuery(GetSeasonalRequest::forCoordinates(52.37, 4.89)
        ->temperatureUnit(TemperatureUnit::Celsius)
        ->windSpeedUnit(WindSpeedUnit::Kmh)
        ->precipitationUnit(PrecipitationUnit::Mm)
        ->timeFormat(TimeFormat::Iso8601)
        ->cellSelection(CellSelection::Sea)
        ->models(SeasonalModel::EcmwfSeas5)
        ->elevation(10.0));

    expect($seasonal)->toMatchArray([
        'temperature_unit' => 'celsius',
        'wind_speed_unit' => 'kmh',
        'precipitation_unit' => 'mm',
        'timeformat' => 'iso8601',
        'cell_selection' => 'sea',
        'models' => 'ecmwf_seas5',
    ])->and($seasonal)->not->toHaveKey('elevation');

    $archive = invokeDefaultQuery(GetArchiveRequest::forCoordinates(52.37, 4.89)
        ->between($start, $end)
        ->temperatureUnit(TemperatureUnit::Celsius)
        ->windSpeedUnit(WindSpeedUnit::Kmh)
        ->precipitationUnit(PrecipitationUnit::Mm)
        ->timeFormat(TimeFormat::Iso8601)
        ->cellSelection(CellSelection::Land)
        ->elevation(12.0)
        ->models(HistoricalModel::Era5));

    expect($archive)->toMatchArray([
        'temperature_unit' => 'celsius',
        'wind_speed_unit' => 'kmh',
        'precipitation_unit' => 'mm',
        'timeformat' => 'iso8601',
        'cell_selection' => 'land',
        'elevation' => '12',
        'models' => 'era5',
    ]);
});

it('requires both start and end dates when a date range is mandatory', function (): void {
    $request = GetClimateRequest::forCoordinates(52.37, 4.89);
    $startDate = (new ReflectionClass(AbstractCoordinateGetRequest::class))->getProperty('startDate');
    $startDate->setAccessible(true);
    $startDate->setValue($request, new DateTimeImmutable('2024-06-01'));

    expect(fn () => invokeDefaultQuery($request))
        ->toThrow(MissingDateRangeException::class, 'start_date and end_date are required');
});

it('validates coordinates for multi-point abstract requests', function (): void {
    expect(fn () => GetArchiveRequest::forPoints([[91.0, 4.89]]))
        ->toThrow(InvalidCoordinateException::class, 'latitude must be between');
});

it('stringifies multi-point coordinates for historical and ensemble requests', function (): void {
    $historical = invokeDefaultQuery(GetArchiveRequest::forPoints([[52.5, 13.4], [48.1, 11.6]])
        ->between(new DateTimeImmutable('2024-06-01'), new DateTimeImmutable('2024-06-15')));
    $ensemble = invokeDefaultQuery(GetEnsembleRequest::forPoints([[52.5, 13.4], [48.1, 11.6]]));

    expect($historical['latitude'])->toBe('52.5,48.1')
        ->and($historical['longitude'])->toBe('13.4,11.6')
        ->and($ensemble['latitude'])->toBe('52.5,48.1')
        ->and($ensemble['longitude'])->toBe('13.4,11.6');
});

function invokeDefaultQuery(object $request): array
{
    return (new ReflectionClass($request))->getMethod('defaultQuery')->invoke($request);
}
