<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use BackedEnum;

final class ConvertsApiKeys
{
    /** @var array<string, string> */
    private const ALIASES = [
        'weathercode' => 'weather_code', // @pest-mutate-ignore
        'windspeed_10m' => 'wind_speed_10m', // @pest-mutate-ignore
        'winddirection_10m' => 'wind_direction_10m', // @pest-mutate-ignore
    ];

    public static function propertyToApiKey(string $property): string
    {
        $key = preg_replace('/(?<!^)[A-Z]/', '_$0', $property) ?? $property;
        $key = strtolower($key);

        return preg_replace('/([a-z])(\d)/', '$1_$2', $key) ?? $key;
    }

    /**
     * @return list<string>
     */
    public static function candidateKeys(BackedEnum|string $variable): array
    {
        if ($variable instanceof BackedEnum) {
            return self::expandAliases([(string) $variable->value]);
        }

        return self::expandAliases([self::propertyToApiKey($variable)]);
    }

    /**
     * @param  list<string>  $keys
     * @return list<string>
     */
    private static function expandAliases(array $keys): array
    {
        $expanded = [];

        foreach ($keys as $key) {
            $expanded[] = $key;

            if (isset(self::ALIASES[$key])) {
                $expanded[] = self::ALIASES[$key];
            }

            foreach (self::ALIASES as $from => $to) {
                if ($key === $to) {
                    $expanded[] = $from;
                }
            }
        }

        return array_values(array_unique($expanded)); // @pest-mutate-ignore: UnwrapArrayUnique, UnwrapArrayValues
    }
}
