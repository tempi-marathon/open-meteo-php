<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use BackedEnum;

trait JoinsQueryEnumValues
{
    /**
     * @param  list<BackedEnum>  $variables
     */
    protected function joinEnumValues(array $variables): string
    {
        return implode(',', array_map(
            static fn (BackedEnum $variable): string => (string) $variable->value,
            $variables,
        ));
    }
}
