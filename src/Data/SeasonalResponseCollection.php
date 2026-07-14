<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use Traversable;

final readonly class SeasonalResponseCollection extends CoordinateResponseCollection
{
    /** @param list<SeasonalResponse> $responses */
    public function __construct(array $responses)
    {
        parent::__construct($responses);
    }

    public function first(): ?SeasonalResponse
    {
        $first = parent::first();

        return $first instanceof SeasonalResponse ? $first : null;
    }

    public function getIterator(): Traversable
    {
        foreach ($this->responses as $response) {
            yield $response;
        }
    }
}
