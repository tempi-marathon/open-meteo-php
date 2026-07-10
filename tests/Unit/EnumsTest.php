<?php

declare(strict_types=1);

use OpenMeteo\Enums\CountryCode;
use OpenMeteo\Enums\DailyVariable;
use OpenMeteo\Enums\Geocoding\GeocodingFormat;
use OpenMeteo\Enums\Geocoding\GeocodingLanguage;
use OpenMeteo\Enums\HourlyVariable;
use OpenMeteo\Enums\TimeFormat;
use OpenMeteo\Enums\Timezone;
use OpenMeteo\Enums\WeatherCode;

covers(
    GeocodingLanguage::class,
    GeocodingFormat::class,
    TimeFormat::class,
    WeatherCode::class,
);

it('covers geocoding language cases', function (): void {
    expect(GeocodingLanguage::English->value)->toBe('en')
        ->and(GeocodingLanguage::cases())->toHaveCount(9);
});

it('covers geocoding format cases', function (): void {
    expect(GeocodingFormat::Json->value)->toBe('json');
});

it('covers time format cases', function (): void {
    expect(TimeFormat::cases())->not->toBeEmpty();
});

it('describes every weather code', function (WeatherCode $code): void {
    expect($code->label())->not->toBe('')
        ->and($code->description())->toBe($code->label());
})->with(WeatherCode::cases());

it('covers generated enum values', function (): void {
    foreach (HourlyVariable::cases() as $case) {
        expect(HourlyVariable::from($case->value))->toBe($case);
    }

    foreach (DailyVariable::cases() as $case) {
        expect(DailyVariable::from($case->value))->toBe($case);
    }

    foreach (CountryCode::cases() as $case) {
        expect(CountryCode::from($case->value))->toBe($case);
    }

    foreach (Timezone::cases() as $case) {
        expect(Timezone::from($case->value))->toBe($case);
    }
});
