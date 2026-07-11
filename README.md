# tempi-marathon/open-meteo-php

Framework-agnostic Saloon SDK for Open-Meteo APIs.

## Installation

```bash
composer require tempi-marathon/open-meteo-php
```

## Configuration

Set environment variables:

```bash
export OPENMETEO_API_KEY=your-key
export OPENMETEO_USER_AGENT=my-app/1.0
```

Laravel apps can publish `config/openmeteo.php` via the service provider and use `env('OPENMETEO_API_KEY')`.

## Usage

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

## Quality

Run `composer test` for Pint, PHPStan (max), and Pest (100% coverage).

Run `composer test:infection` for mutation testing (Pest mutate).

Run `composer generate` to regenerate enums from pinned OpenAPI specs.
