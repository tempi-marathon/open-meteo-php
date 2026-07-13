<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo;

use TempiMarathon\OpenMeteo\Contracts\HasRawValue;

/**
 * Converts absolute wind or wave direction in degrees (0–360) to a 16-point compass label.
 *
 * Do not use for anomaly fields such as wind_direction_10m_anomaly — those are deviations
 * from climatology, not compass bearings.
 */
final readonly class WindDirection implements \Stringable, HasRawValue
{
    /** @var array<string> */
    private const DIRECTIONS = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW', 'N']; // @pest-mutate-ignore

    public function __construct(private int|float $degrees) {}

    public static function fromDegrees(int|float $degrees): self
    {
        return new self($degrees);
    }

    public static function tryFrom(int|float|null $degrees): ?self
    {
        return $degrees === null ? null : self::fromDegrees($degrees);
    }

    public function getRaw(): int|float
    {
        return $this->degrees;
    }

    public function label(): string
    {
        $normalized = fmod(fmod($this->degrees, 360.0) + 360.0, 360.0); // @pest-mutate-ignore: DecrementFloat,IncrementFloat

        return self::DIRECTIONS[(int) round($normalized / 22.5)]; // @pest-mutate-ignore: RemoveIntegerCast
    }

    public function __toString(): string
    {
        return $this->label();
    }
}
