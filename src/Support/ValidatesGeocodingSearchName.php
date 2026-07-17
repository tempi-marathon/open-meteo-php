<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Exceptions\InvalidGeocodingSearchException;

final class ValidatesGeocodingSearchName
{
    // @pest-mutate-ignore
    private const int MAX_LENGTH = 256;

    public static function normalize(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            throw new InvalidGeocodingSearchException('name must not be empty.');
        }

        $length = mb_strlen($name);
        if ($length > self::MAX_LENGTH) {
            throw new InvalidGeocodingSearchException(
                sprintf('name must not exceed %d characters, %d given.', self::MAX_LENGTH, $length),
            );
        }

        return $name;
    }
}
