<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Exceptions\InvalidCoordinateException;

final class ValidatesCoordinates
{
    // @pest-mutate-ignore
    private const float MIN_LATITUDE = -90.0;

    // @pest-mutate-ignore
    private const float MAX_LATITUDE = 90.0;

    // @pest-mutate-ignore
    private const float MIN_LONGITUDE = -180.0;

    // @pest-mutate-ignore
    private const float MAX_LONGITUDE = 180.0;

    public static function assert(float $latitude, float $longitude): void
    {
        if ($latitude < self::MIN_LATITUDE || $latitude > self::MAX_LATITUDE) {
            throw new InvalidCoordinateException(
                sprintf('latitude must be between %s and %s, %s given.', self::MIN_LATITUDE, self::MAX_LATITUDE, $latitude),
            );
        }

        if ($longitude < self::MIN_LONGITUDE || $longitude > self::MAX_LONGITUDE) {
            throw new InvalidCoordinateException(
                sprintf('longitude must be between %s and %s, %s given.', self::MIN_LONGITUDE, self::MAX_LONGITUDE, $longitude),
            );
        }
    }
}
