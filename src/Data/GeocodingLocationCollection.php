<?php

declare(strict_types=1);

namespace OpenMeteo\Data;

use Countable;
use IteratorAggregate;
use Traversable;

/** @implements IteratorAggregate<int, GeocodingLocation> */
final readonly class GeocodingLocationCollection implements Countable, IteratorAggregate
{
    /** @param list<GeocodingLocation> $locations */
    public function __construct(private array $locations) {}

    public function count(): int
    {
        return count($this->locations);
    }

    public function getIterator(): Traversable
    {
        foreach ($this->locations as $location) {
            yield $location;
        }
    }

    public function first(): ?GeocodingLocation
    {
        return $this->locations[0] ?? null;
    }
}
