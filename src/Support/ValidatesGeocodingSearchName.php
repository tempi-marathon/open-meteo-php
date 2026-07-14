<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Exceptions\InvalidGeocodingSearchException;

use function Psl\Str\length;
use function Psl\Str\trim;

/** @pest-mutate-ignore */
final class ValidatesGeocodingSearchName
{
    private const int MAX_LENGTH = 256;

    public static function normalize(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            throw new InvalidGeocodingSearchException('name must not be empty.');
        }

        if (length($name) > self::MAX_LENGTH) {
            throw new InvalidGeocodingSearchException(
                sprintf('name must not exceed %d characters, %d given.', self::MAX_LENGTH, length($name)),
            );
        }

        return $name;
    }
}
