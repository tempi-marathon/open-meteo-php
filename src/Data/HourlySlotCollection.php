<?php

declare(strict_types=1);

namespace OpenMeteo\Data;

use Countable;
use DateTimeInterface;
use IteratorAggregate;
use Traversable;

use function Psl\Iter\reduce;
use function Psl\Math\abs;

/** @implements IteratorAggregate<int, HourlyWeatherSlot> */
final readonly class HourlySlotCollection implements Countable, IteratorAggregate
{
    /** @param list<HourlyWeatherSlot> $slots */
    public function __construct(private array $slots) {}

    public function count(): int
    {
        return count($this->slots);
    }

    public function getIterator(): Traversable
    {
        foreach ($this->slots as $slot) {
            yield $slot;
        }
    }

    public function closestTo(DateTimeInterface $target): ?HourlyWeatherSlot
    {
        if ($this->slots === []) {
            return null;
        }

        return reduce(
            $this->slots,
            static function (HourlyWeatherSlot $closest, HourlyWeatherSlot $slot) use ($target): HourlyWeatherSlot {
                $currentDiff = abs($closest->datetime->getTimestamp() - $target->getTimestamp());
                $slotDiff = abs($slot->datetime->getTimestamp() - $target->getTimestamp());

                return $slotDiff < $currentDiff ? $slot : $closest;
            },
            $this->slots[0],
        );
    }
}
