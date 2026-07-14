<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use Traversable;

final readonly class ForecastResponseCollection extends CoordinateResponseCollection
{
    /** @param list<ForecastResponse> $responses */
    public function __construct(array $responses)
    {
        parent::__construct($responses);
    }

    public function first(): ?ForecastResponse
    {
        $first = parent::first();

        return $first instanceof ForecastResponse ? $first : null;
    }

    public function getIterator(): Traversable
    {
        foreach ($this->responses as $response) {
            yield $response;
        }
    }
}
