<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use Countable;
use DateTimeInterface;
use IteratorAggregate;
use Traversable;

/** @implements IteratorAggregate<int, SeriesPoint> */
abstract readonly class SeriesCollection implements Countable, IteratorAggregate
{
    /** @param list<SeriesPoint> $points */
    public function __construct(private array $points) {}

    public function count(): int
    {
        return count($this->points);
    }

    public function getIterator(): Traversable
    {
        foreach ($this->points as $point) {
            yield $point;
        }
    }

    public function at(int $index): ?SeriesPoint
    {
        return $this->points[$index] ?? null;
    }

    public function first(): ?SeriesPoint
    {
        return $this->points[0] ?? null;
    }

    public function closestTo(DateTimeInterface $target): ?SeriesPoint
    {
        if ($this->points === []) {
            return null; // @pest-mutate-ignore: RemoveEarlyReturn
        }

        $closest = $this->points[0];
        foreach ($this->points as $point) {
            $currentDiff = abs($closest->datetime->getTimestamp() - $target->getTimestamp());
            $pointDiff = abs($point->datetime->getTimestamp() - $target->getTimestamp());

            if ($pointDiff < $currentDiff) {
                $closest = $point;
            }
        }

        return $closest;
    }
}
