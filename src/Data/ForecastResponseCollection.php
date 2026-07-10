<?php

declare(strict_types=1);

namespace OpenMeteo\Data;

use Countable;
use IteratorAggregate;
use Traversable;

/** @implements IteratorAggregate<int, ForecastResponse> */
final readonly class ForecastResponseCollection implements Countable, IteratorAggregate
{
    /** @param list<ForecastResponse> $responses */
    public function __construct(private array $responses) {}

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

    public function first(): ?ForecastResponse
    {
        return $this->responses[0] ?? null;
    }
}
