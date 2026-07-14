<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use Countable;
use DateTimeInterface;
use IteratorAggregate;
use Traversable;

use function Psl\Iter\is_empty;
use function Psl\Iter\reduce;
use function Psl\Math\abs;

/** @implements IteratorAggregate<int, SeriesReading> */
abstract readonly class SeriesReadingCollection implements Countable, IteratorAggregate
{
    /** @param list<SeriesReading> $readings */
    public function __construct(private array $readings) {}

    public function count(): int
    {
        return count($this->readings);
    }

    public function getIterator(): Traversable
    {
        foreach ($this->readings as $reading) {
            yield $reading;
        }
    }

    public function at(int $index): ?SeriesReading
    {
        return $this->readings[$index] ?? null;
    }

    public function closestTo(DateTimeInterface $target): ?SeriesReading
    {
        if (is_empty($this->readings)) {
            return null; // @pest-mutate-ignore: RemoveEarlyReturn
        }

        return reduce(
            $this->readings,
            static function (SeriesReading $closest, SeriesReading $reading) use ($target): SeriesReading {
                $currentDiff = abs($closest->datetime->getTimestamp() - $target->getTimestamp());
                $readingDiff = abs($reading->datetime->getTimestamp() - $target->getTimestamp());

                return $readingDiff < $currentDiff ? $reading : $closest;
            },
            $this->readings[0],
        );
    }
}
