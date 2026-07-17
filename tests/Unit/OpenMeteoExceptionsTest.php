<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Data\ForecastResponse;
use TempiMarathon\OpenMeteo\Exceptions\ConnectorNotConfiguredException;
use TempiMarathon\OpenMeteo\Exceptions\DebugUrlNotSupportedException;
use TempiMarathon\OpenMeteo\Exceptions\InvalidCoordinateException;
use TempiMarathon\OpenMeteo\Exceptions\InvalidForecastParameterException;
use TempiMarathon\OpenMeteo\Exceptions\InvalidForecastSegmentException;
use TempiMarathon\OpenMeteo\Exceptions\InvalidGeocodingCountException;
use TempiMarathon\OpenMeteo\Exceptions\InvalidGeocodingSearchException;
use TempiMarathon\OpenMeteo\Exceptions\MalformedPayloadException;
use TempiMarathon\OpenMeteo\Exceptions\MissingCurrentTimeException;
use TempiMarathon\OpenMeteo\Exceptions\MissingDateRangeException;
use TempiMarathon\OpenMeteo\Exceptions\MissingSeriesTimeException;
use TempiMarathon\OpenMeteo\Exceptions\MultiCoordinateResponseException;
use TempiMarathon\OpenMeteo\Exceptions\OpenMeteoException;
use TempiMarathon\OpenMeteo\Exceptions\OpenMeteoRequestException;
use TempiMarathon\OpenMeteo\Exceptions\ResolvesRequestUrlMisuseException;
use TempiMarathon\OpenMeteo\Exceptions\UnexpectedDtoException;
use TempiMarathon\OpenMeteo\Exceptions\UnsupportedResponseClassException;

covers(
    ConnectorNotConfiguredException::class,
    DebugUrlNotSupportedException::class,
    InvalidCoordinateException::class,
    InvalidForecastParameterException::class,
    InvalidForecastSegmentException::class,
    InvalidGeocodingCountException::class,
    InvalidGeocodingSearchException::class,
    MalformedPayloadException::class,
    MissingDateRangeException::class,
    MultiCoordinateResponseException::class,
    MissingCurrentTimeException::class,
    MissingSeriesTimeException::class,
    OpenMeteoRequestException::class,
    ResolvesRequestUrlMisuseException::class,
    UnsupportedResponseClassException::class,
    UnexpectedDtoException::class,
);

it('marks every sdk exception with the shared OpenMeteoException contract', function (string $exceptionClass): void {
    expect(is_subclass_of($exceptionClass, OpenMeteoException::class))->toBeTrue()
        ->and(is_subclass_of($exceptionClass, Throwable::class))->toBeTrue();
})->with([
    ConnectorNotConfiguredException::class,
    DebugUrlNotSupportedException::class,
    InvalidCoordinateException::class,
    InvalidForecastParameterException::class,
    InvalidForecastSegmentException::class,
    InvalidGeocodingCountException::class,
    InvalidGeocodingSearchException::class,
    MalformedPayloadException::class,
    MissingCurrentTimeException::class,
    MissingDateRangeException::class,
    MissingSeriesTimeException::class,
    MultiCoordinateResponseException::class,
    OpenMeteoRequestException::class,
    ResolvesRequestUrlMisuseException::class,
    UnexpectedDtoException::class,
    UnsupportedResponseClassException::class,
]);

it('can be caught through the shared OpenMeteoException contract', function (): void {
    $caught = null;

    try {
        throw new InvalidCoordinateException('boom');
    } catch (OpenMeteoException $exception) {
        $caught = $exception;
    }

    expect($caught)->toBeInstanceOf(InvalidCoordinateException::class);
});

it('exposes stable messages for parameterless sdk exceptions', function (string $exceptionClass, string $message): void {
    $exception = new $exceptionClass;

    expect($exception)->toBeInstanceOf(Throwable::class)
        ->and($exception->getMessage())->toBe($message);
})->with([
    'connector not configured' => [ConnectorNotConfiguredException::class, 'No connector set. Build the request from a resource, or call ->using($connector) before ->send().'],
    'debug url not supported' => [DebugUrlNotSupportedException::class, 'Request must implement ResolvesRequestUrl to build a debug URL.'],
    'resolves request url misuse' => [ResolvesRequestUrlMisuseException::class, 'ResolvesRequestUrl can only be used on Saloon requests.'],
    'missing series time' => [MissingSeriesTimeException::class, 'Series data must contain a time array.'],
    'missing current time' => [MissingCurrentTimeException::class, 'Current data must contain a time value.'],
    'invalid forecast segment' => [InvalidForecastSegmentException::class, 'Expected forecast segment to be an array.'],
    'multi coordinate response' => [MultiCoordinateResponseException::class, 'Multi-coordinate response received. Use createDtoCollectionFromResponse() or dtoCollection() instead.'],
]);

it('formats unsupported response class exceptions', function (): void {
    $exception = new UnsupportedResponseClassException(stdClass::class);

    expect($exception->getMessage())->toBe('Unsupported response class: stdClass');
});

it('formats unexpected dto exceptions', function (): void {
    $exception = new UnexpectedDtoException(ForecastResponse::class);

    expect($exception->getMessage())->toBe('Expected TempiMarathon\OpenMeteo\Data\ForecastResponse DTO.')
        ->and($exception->expectedClass)->toBe(ForecastResponse::class);
});

it('accepts custom invalid argument messages', function (): void {
    expect((new InvalidCoordinateException('latitude must be between -90 and 90, 100 given.'))->getMessage())
        ->toBe('latitude must be between -90 and 90, 100 given.')
        ->and((new InvalidGeocodingSearchException('name must not be empty.'))->getMessage())
        ->toBe('name must not be empty.')
        ->and((new InvalidGeocodingCountException('count must be between 1 and 100, 0 given.'))->getMessage())
        ->toBe('count must be between 1 and 100, 0 given.')
        ->and((new InvalidForecastParameterException('forecast_days must be between 0 and 16, 17 given.'))->getMessage())
        ->toBe('forecast_days must be between 0 and 16, 17 given.')
        ->and((new MissingDateRangeException('start_date and end_date are required for this endpoint.'))->getMessage())
        ->toBe('start_date and end_date are required for this endpoint.');
});
