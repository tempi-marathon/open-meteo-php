<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use Traversable;

final readonly class ClimateResponseCollection extends CoordinateResponseCollection
{
    /** @param list<ClimateResponse> $responses */
    public function __construct(array $responses)
    {
        parent::__construct($responses);
    }

    public function first(): ?ClimateResponse
    {
        $first = parent::first();

        return $first instanceof ClimateResponse ? $first : null;
    }

    public function getIterator(): Traversable
    {
        foreach ($this->responses as $response) {
            yield $response;
        }
    }
}
