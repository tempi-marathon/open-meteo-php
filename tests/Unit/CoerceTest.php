<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Exceptions\MalformedPayloadException;
use TempiMarathon\OpenMeteo\Support\Coerce;

covers(Coerce::class);

it('coerces values to float', function (): void {
    expect(Coerce::toFloat(3))->toBe(3.0)
        ->and(Coerce::toFloat(3.5))->toBe(3.5)
        ->and(Coerce::toFloat('4.25'))->toBe(4.25);
});

it('throws when a value cannot become a float', function (mixed $value): void {
    expect(fn () => Coerce::toFloat($value))->toThrow(MalformedPayloadException::class);
})->with([
    'non-numeric string' => ['abc'],
    'null' => [null],
    'bool' => [true],
    'array' => [[1.0]],
]);

it('coerces values to int', function (): void {
    expect(Coerce::toInt(7))->toBe(7)
        ->and(Coerce::toInt(7.0))->toBe(7)
        ->and(Coerce::toInt('9'))->toBe(9);
});

it('throws when a value cannot become an int', function (mixed $value): void {
    expect(fn () => Coerce::toInt($value))->toThrow(MalformedPayloadException::class);
})->with([
    'fractional float' => [7.5],
    'fractional string' => ['9.5'],
    'non-numeric string' => ['abc'],
    'null' => [null],
    'bool' => [false],
]);

it('coerces values to string', function (): void {
    expect(Coerce::toString('hi'))->toBe('hi')
        ->and(Coerce::toString(5))->toBe('5')
        ->and(Coerce::toString(5.5))->toBe('5.5');
});

it('throws when a value cannot become a string', function (mixed $value): void {
    expect(fn () => Coerce::toString($value))->toThrow(MalformedPayloadException::class);
})->with([
    'null' => [null],
    'bool' => [true],
    'array' => [['x']],
]);

it('passes through scalar series values', function (): void {
    expect(Coerce::toSeriesValue(null))->toBeNull()
        ->and(Coerce::toSeriesValue(1))->toBe(1)
        ->and(Coerce::toSeriesValue(1.5))->toBe(1.5)
        ->and(Coerce::toSeriesValue('x'))->toBe('x');
});

it('throws for non-scalar series values', function (): void {
    expect(fn () => Coerce::toSeriesValue(['x']))->toThrow(MalformedPayloadException::class)
        ->and(fn () => Coerce::toSeriesValue(true))->toThrow(MalformedPayloadException::class);
});

it('coerces series columns into scalar lists', function (): void {
    expect(Coerce::toSeriesColumn([1, 2.5, 'x', null]))->toBe([1, 2.5, 'x', null])
        ->and(Coerce::toSeriesColumn([]))->toBe([]);
});

it('throws when a series column is not an array', function (): void {
    expect(fn () => Coerce::toSeriesColumn('nope'))->toThrow(MalformedPayloadException::class);
});

it('coerces float lists', function (): void {
    expect(Coerce::toFloatList([1, 2.5, '3']))->toBe([1.0, 2.5, 3.0]);
});

it('throws when a float list is not an array', function (): void {
    expect(fn () => Coerce::toFloatList('nope'))->toThrow(MalformedPayloadException::class);
});

it('coerces string lists', function (): void {
    expect(Coerce::toStringList(['a', 1, 2.5]))->toBe(['a', '1', '2.5']);
});

it('throws when a string list is not an array', function (): void {
    expect(fn () => Coerce::toStringList('nope'))->toThrow(MalformedPayloadException::class);
});
