# tempi-marathon/open-meteo-php

Framework-agnostic Saloon SDK for Open-Meteo APIs.

## Installation

```bash
composer require tempi-marathon/open-meteo-php
```

## Usage

```php
use OpenMeteo\Connectors\ForecastConnector;
use OpenMeteo\Connectors\GeocodingConnector;
use OpenMeteo\Enums\Geocoding\GeocodingLanguage;
use OpenMeteo\Enums\HourlyVariable;
use OpenMeteo\Enums\Timezone;

$geocoding = new GeocodingConnector();

$locations = $geocoding->locations()->search('Berlin')
    ->count(5)
    ->language(GeocodingLanguage::English)
    ->send()
    ->dto();

$location = $geocoding->locations()->get(id: 2950159)->send()->dto();

$forecast = new ForecastConnector();
$data = $forecast->weather()->get(
    latitude: 52.52,
    longitude: 13.41,
)->timezone(Timezone::EuropeAmsterdam)
    ->hourly(HourlyVariable::Temperature2m, HourlyVariable::WeatherCode)
    ->forecastDays(7)
    ->send()
    ->dto();
```

## Quality

Run `composer test` for Pint, PHPStan (max), and Pest (100% coverage).

Run `composer test:infection` for mutation testing (Pest mutate).

Run `composer generate` to regenerate enums from pinned OpenAPI specs.
