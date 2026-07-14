<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use Traversable;

final readonly class EnsembleResponseCollection extends CoordinateResponseCollection
{
    /** @param list<EnsembleResponse> $responses */
    public function __construct(array $responses)
    {
        parent::__construct($responses);
    }

    public function first(): ?EnsembleResponse
    {
        $first = parent::first();

        return $first instanceof EnsembleResponse ? $first : null;
    }

    public function getIterator(): Traversable
    {
        foreach ($this->responses as $response) {
            yield $response;
        }
    }
}
