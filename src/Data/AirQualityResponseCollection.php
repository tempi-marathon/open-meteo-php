<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use Traversable;

final readonly class AirQualityResponseCollection extends CoordinateResponseCollection
{
    /** @param list<AirQualityResponse> $responses */
    public function __construct(array $responses)
    {
        parent::__construct($responses);
    }

    public function first(): ?AirQualityResponse
    {
        $first = parent::first();

        return $first instanceof AirQualityResponse ? $first : null;
    }

    public function getIterator(): Traversable
    {
        foreach ($this->responses as $response) {
            yield $response;
        }
    }
}
