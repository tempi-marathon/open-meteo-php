<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Enums\CountryCode;
use TempiMarathon\OpenMeteo\Enums\DailyVariable;
use TempiMarathon\OpenMeteo\Enums\Geocoding\GeocodingFormat;
use TempiMarathon\OpenMeteo\Enums\Geocoding\GeocodingLanguage;
use TempiMarathon\OpenMeteo\Enums\HourlyVariable;
use TempiMarathon\OpenMeteo\Enums\TimeFormat;
use TempiMarathon\OpenMeteo\Enums\Timezone;
use TempiMarathon\OpenMeteo\Enums\WeatherCode;

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
