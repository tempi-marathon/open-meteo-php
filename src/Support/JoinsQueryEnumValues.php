<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use BackedEnum;

use function Psl\Str\join;
use function Psl\Vec\map;

trait JoinsQueryEnumValues
{
    /**
     * @param  list<BackedEnum>  $variables
     */
    protected function joinEnumValues(array $variables): string
    {
        return join(map($variables, static fn (BackedEnum $variable): string => (string) $variable->value), ',');
    }
}
