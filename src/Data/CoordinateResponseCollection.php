<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use Countable;
use IteratorAggregate;
use TempiMarathon\OpenMeteo\Contracts\CoordinateResponse;
use Traversable;

/** @implements IteratorAggregate<int, CoordinateResponse> */
readonly class CoordinateResponseCollection implements Countable, IteratorAggregate
{
    /** @param list<CoordinateResponse> $responses */
    public function __construct(protected array $responses) {}

    public function count(): int
    {
        return count($this->responses);
    }

    public function getIterator(): Traversable
    {
        foreach ($this->responses as $response) {
            yield $response;
        }
    }

    public function first(): ?CoordinateResponse
    {
        return $this->responses[0] ?? null;
    }
}
