# 🌤 Open-Meteo PHP

[![Tests](https://github.com/tempi-marathon/open-meteo-php/actions/workflows/test.yml/badge.svg)](https://github.com/tempi-marathon/open-meteo-php/actions/workflows/test.yml)
[![PHP](https://img.shields.io/static/v1?label=PHP&message=%5E8.5&color=777BB4&logo=php&logoColor=white)](https://www.php.net/)
[![License: MIT](https://img.shields.io/static/v1?label=License&message=MIT&color=blue)](LICENSE)

Framework-agnostic [Saloon](https://docs.saloon.dev/) SDK for the [Open-Meteo](https://open-meteo.com/) APIs.

[Open-Meteo](https://open-meteo.com/) · [Open-Meteo GitHub](https://github.com/open-meteo/open-meteo)

**No API key required.** The free, non-commercial Open-Meteo API works out of the box — install the package and start making requests.

Requires PHP `^8.5`.

## Installation

```bash
composer require tempi-marathon/open-meteo-php
```

## Quick start

```php
use TempiMarathon\OpenMeteo\Enums\HourlyVariable;
use TempiMarathon\OpenMeteo\OpenMeteo;

$forecast = (new OpenMeteo())
    ->forecast()->weather()->get(52.52, 13.41)
    ->hourly(HourlyVariable::Temperature2m)
    ->dto();
```

No environment variables, configuration files, or Laravel setup needed.

### Human-readable values

Time-series data is exposed as typed `SeriesPoint` objects inside interval series — not raw arrays:

```php
$point = $forecast->hourly()->at(0);
// or: foreach ($forecast->hourly() as $point) { ... }
// or: $forecast->hourly()->closestTo(new DateTimeImmutable('2026-07-11T12:00'));

$point?->get('temperature_2m');                // 21.2
$point?->get('weathercode')?->label();        // "Partly cloudy"
echo $point?->get('wind_direction_80m');       // "SW" — Stringable compass label
$point?->get('wind_direction_80m')?->getRaw();  // 225 — degrees when you need the number

$forecast->daily()->at(0)?->get('temperature_2m_max');
$forecast->minutely15()->at(0)?->get('temperature_2m');
$forecast->current()->first()?->get('wind_direction_10m')?->label(); // current snapshot
```

Each response type exposes only the intervals its API supports — for example `AirQualityResponse` implements `HasHourly` and `HasCurrent`, while `ForecastResponse` adds `HasDaily` and `HasMinutely15`. Use `instanceof HasDaily` when writing generic code.

Absolute direction fields (wind, wave, swell, ocean current, and similar) are parsed into `WindDirection` automatically. Anomaly fields such as `wind_direction_10m_anomaly` stay as numeric values.

## Usage

Chain request options on the fluent builder, then call `->dto()` for a typed response or `->send()` for the raw Saloon response.

### Facade (recommended entry point)

```php
use TempiMarathon\OpenMeteo\Enums\HourlyVariable;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\OpenMeteo;

$openMeteo = new OpenMeteo();

$locations = $openMeteo->geocoding()->locations()->search('Berlin')->dto();

$forecast = $openMeteo->forecast()->weather()->get(52.52, 13.41)
    ->timezone(Timezone::EuropeAmsterdam)
    ->hourly(HourlyVariable::Temperature2m, HourlyVariable::WeatherCode)
    ->forecastDays(7)
    ->dto();
```

### Connectors (direct Saloon access)

```php
use TempiMarathon\OpenMeteo\Connectors\ForecastConnector;
use TempiMarathon\OpenMeteo\Connectors\GeocodingConnector;
use TempiMarathon\OpenMeteo\Enums\Geocoding\GeocodingLanguage;
use TempiMarathon\OpenMeteo\Enums\HourlyVariable;
use TempiMarathon\OpenMeteo\Enums\Timezone;

$geocoding = new GeocodingConnector();

$locations = $geocoding->locations()->search('Berlin')
    ->count(5)
    ->language(GeocodingLanguage::English)
    ->dto();

$forecast = new ForecastConnector();

$data = $forecast->weather()->get(52.52, 13.41)
    ->timezone(Timezone::EuropeAmsterdam)
    ->hourly(HourlyVariable::Temperature2m, HourlyVariable::WeatherCode)
    ->forecastDays(7)
    ->dto();
```

Use `debugUrl()` on a resource to inspect the request URL during development. It redacts secrets such as API keys — see [SECURITY.md](SECURITY.md).

## Supported APIs

| Facade method | API | Open-Meteo docs |
|---|---|---|
| `forecast()` | Weather forecast | [Forecast API](https://open-meteo.com/en/docs) |
| `historical()` | Historical weather archive | [Historical Weather API](https://open-meteo.com/en/docs/historical-weather-api) |
| `geocoding()` | Location search | [Geocoding API](https://open-meteo.com/en/docs/geocoding-api) |
| `airQuality()` | Air quality | [Air Quality API](https://open-meteo.com/en/docs/air-quality-api) |
| `marine()` | Marine weather | [Marine Weather API](https://open-meteo.com/en/docs/marine-weather-api) |
| `climate()` | Climate | [Climate API](https://open-meteo.com/en/docs/climate-api) |
| `flood()` | Flood | [Flood API](https://open-meteo.com/en/docs/flood-api) |
| `ensemble()` | Ensemble forecast | [Ensemble API](https://open-meteo.com/en/docs/ensemble-api) |
| `seasonal()` | Seasonal forecast | [Seasonal Forecast API](https://open-meteo.com/en/docs/seasonal-forecast-api) |
| `elevation()` | Elevation | [Elevation API](https://open-meteo.com/en/docs/elevation-api) |

All coordinate-based APIs share fluent builders for `timezone()`, `between()`, unit options (`temperatureUnit()`, `windSpeedUnit()`, `precipitationUnit()`, `timeFormat()`, `cellSelection()`, `models()`, `elevation()`), and `withQueryParam()` for escape-hatch parameters. Forecast-window options (`forecastDays()`, `pastDays()`, `forecastHours()`, `pastHours()`) are validated per endpoint.

Climate and historical requests require `between($start, $end)` before sending. Seasonal supports `weekly()` in addition to hourly, daily, and monthly intervals. Air quality accepts `domains()`. Climate accepts `disableBiasCorrection()`. Ensemble accepts `temporalResolution()`. Forecast, historical, and ensemble support solar irradiance options via `tilt()` and `azimuth()`.

Each API uses endpoint-specific variable enums generated from the OpenAPI specs (for example `MarineHourlyVariable`, `HistoricalDailyVariable`, `EnsembleHourlyVariable`, `ForecastCurrentVariable`, `AirQualityCurrentVariable`). Weather model IDs are typed per endpoint (`ForecastModel`, `HistoricalModel`, etc.) and passed to `models()`. Responses expose root metadata (`elevation`, `generationtime_ms`, `utc_offset_seconds`, `timezone_abbreviation`) on `$response->metadata`.

All coordinate resources support batch lookups via `forPoints()`. Single-coordinate responses use `->dto()`; multi-coordinate responses use `->dtoCollection()`, which returns a typed collection per endpoint (for example `ForecastResponseCollection`, `HistoricalResponseCollection`).

## Optional configuration

### User-Agent

Optional, but recommended in production so Open-Meteo can identify your application:

```bash
export OPENMETEO_USER_AGENT=my-app/1.0
```

Sent as the `User-Agent` header on every request.

### Commercial subscriptions

Open-Meteo API keys are only for [paid commercial subscriptions](https://open-meteo.com/en/pricing). The free API does not require a key.

When `OPENMETEO_API_KEY` is set, the SDK automatically:

1. Appends the key as an `apikey` query parameter on every request
2. Rewrites default free-tier `*.open-meteo.com` hosts to `customer-*.open-meteo.com`

```dotenv
# .env (Laravel) — all you need for commercial use
OPENMETEO_API_KEY=your-subscription-key
OPENMETEO_USER_AGENT=my-app/1.0
```

No manual host remapping required.

Non-Laravel apps can set the `OPENMETEO_API_KEY` environment variable (read by the package config) or bootstrap with:

```php
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

OpenMeteoConfig::configure(['apikey' => 'your-subscription-key']);
```

Not every commercial plan includes every API — see the [pricing table](https://open-meteo.com/en/pricing). Standard covers forecast, marine, air quality, geocoding, elevation, and flood; Professional adds historical, climate, ensemble, seasonal, and more.

#### Advanced: explicit host overrides

Explicit `hosts` in config always take precedence over auto-switching. Use this for self-hosted instances, debugging, or when you need a specific endpoint.

| Config key | Free (default) | Commercial (auto or manual) |
|---|---|---|
| `forecast` | `https://api.open-meteo.com/v1/` | `https://customer-api.open-meteo.com/v1/` |
| `historical` | `https://archive-api.open-meteo.com/v1/` | `https://customer-archive-api.open-meteo.com/v1/` |
| `geocoding` | `https://geocoding-api.open-meteo.com/v1/` | `https://customer-geocoding-api.open-meteo.com/v1/` |
| `air_quality` | `https://air-quality-api.open-meteo.com/v1/` | `https://customer-air-quality-api.open-meteo.com/v1/` |
| `marine` | `https://marine-api.open-meteo.com/v1/` | `https://customer-marine-api.open-meteo.com/v1/` |
| `climate` | `https://climate-api.open-meteo.com/v1/` | `https://customer-climate-api.open-meteo.com/v1/` |
| `flood` | `https://flood-api.open-meteo.com/v1/` | `https://customer-flood-api.open-meteo.com/v1/` |
| `ensemble` | `https://ensemble-api.open-meteo.com/v1/` | `https://customer-ensemble-api.open-meteo.com/v1/` |
| `seasonal` | `https://seasonal-api.open-meteo.com/v1/` | `https://customer-seasonal-api.open-meteo.com/v1/` |
| `elevation` | `https://api.open-meteo.com/v1/` | `https://customer-api.open-meteo.com/v1/` |

Self-hosted deployments should set custom `hosts` and leave `apikey` unset.

## Laravel

The package auto-registers via Laravel package discovery. On first boot, `config/openmeteo.php` is copied to your application if it does not already exist.

All environment variables are optional:

```dotenv
# OPENMETEO_API_KEY=your-subscription-key
# OPENMETEO_USER_AGENT=my-app/1.0
```

## Attribution

Open-Meteo data is licensed under [CC BY 4.0](https://creativecommons.org/licenses/by/4.0/). Geocoding data includes information from GeoNames — see [ATTRIBUTIONS.md](ATTRIBUTIONS.md).

For commercial use boundaries and licensing, see [Open-Meteo pricing](https://open-meteo.com/en/pricing).

## Security

See [SECURITY.md](SECURITY.md) for API key handling and `debugUrl()` guidance.

## Quality

Run `composer test` for Pint, PHPStan (max), and Pest (100% coverage).

Run `composer test:infection` for mutation testing (Pest mutate).

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for development setup and enum regeneration (`composer generate`).
