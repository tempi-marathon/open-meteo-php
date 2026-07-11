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

/** @implements IteratorAggregate<int, HourlyReading> */
final readonly class HourlyReadingCollection implements Countable, IteratorAggregate
{
    /** @param list<HourlyReading> $readings */
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

    public function closestTo(DateTimeInterface $target): ?HourlyReading
    {
        if (is_empty($this->readings)) {
            return null;
        }

        return reduce(
            $this->readings,
            static function (HourlyReading $closest, HourlyReading $reading) use ($target): HourlyReading {
                $currentDiff = abs($closest->datetime->getTimestamp() - $target->getTimestamp());
                $readingDiff = abs($reading->datetime->getTimestamp() - $target->getTimestamp());

                return $readingDiff < $currentDiff ? $reading : $closest;
            },
            $this->readings[0],
        );
    }
}
