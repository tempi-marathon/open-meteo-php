<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

final class ConvertsApiKeys
{
    public static function propertyToApiKey(string $property): string
    {
        $key = preg_replace('/(?<!^)[A-Z]/', '_$0', $property) ?? $property;
        $key = strtolower($key);
        $key = preg_replace('/([a-z])(\d)/', '$1_$2', $key) ?? $key;

        return $key;
    }
}
