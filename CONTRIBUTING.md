# Contributing

## Development setup

```bash
composer install
composer test
```

`composer test` runs Pint (style), PHPStan (max level), and Pest with 100% coverage.

For mutation testing:

```bash
composer test:mutation
```

## Why not Saloon SDK Generator?

A Saloon SDK Generator dry-run against the pinned OpenAPI YAML files under `openapi/` would produce an oversized surface area — hundreds of hourly variables with little curation. This package instead generates focused enums from the specs and hand-writes requests where a smaller, typed API is more useful (such as geocoding).

## Enum generation

Enums are regenerated from pinned OpenAPI specs:

```bash
composer generate
```

This runs five scripts:

- `scripts/generate-hourly-daily-enums.php` — forecast hourly, daily, current, and minutely 15 variables
- `scripts/generate-timezones.php` — timezone enum
- `scripts/generate-country-codes.php` — country code enum
- `scripts/generate-air-quality-enum.php` — air quality hourly and current variables
- `scripts/generate-endpoint-enums.php` — endpoint-specific variable enums (marine, historical, climate, flood, ensemble, seasonal including weekly and monthly)
- `scripts/generate-models-enums.php` — per-endpoint weather model enums (forecast, historical, marine, climate, flood, ensemble, seasonal)

### Workflow

1. Update the relevant YAML file(s) under `openapi/`
2. Run `composer generate`
3. Review the diff
4. Run `composer test`

## Hand-maintained code

Geocoding requests, connectors, and other curated types stay hand-written. Not every OpenAPI parameter needs an enum — only the ones that benefit from type safety in PHP.
