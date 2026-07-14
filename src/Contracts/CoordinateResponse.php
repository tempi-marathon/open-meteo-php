<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Contracts;

interface CoordinateResponse
{
    public float $latitude { get; }

    public float $longitude { get; }

    public string $timezone { get; }
}
