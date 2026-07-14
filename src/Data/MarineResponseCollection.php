<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use Traversable;

final readonly class MarineResponseCollection extends CoordinateResponseCollection
{
    /** @param list<MarineResponse> $responses */
    public function __construct(array $responses)
    {
        parent::__construct($responses);
    }

    public function first(): ?MarineResponse
    {
        $first = parent::first();

        return $first instanceof MarineResponse ? $first : null;
    }

    public function getIterator(): Traversable
    {
        foreach ($this->responses as $response) {
            yield $response;
        }
    }
}
