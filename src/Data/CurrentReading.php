<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Data;

use BackedEnum;
use DateTimeImmutable;
use TempiMarathon\OpenMeteo\Enums\WeatherCode;
use TempiMarathon\OpenMeteo\Support\ConvertsApiKeys;
use TempiMarathon\OpenMeteo\WindDirection;

/**
 * @property-read ?float $temperature2m
 * @property-read ?WindDirection $windDirection10m
 * @property-read ?WeatherCode $weatherCode
 */
final readonly class CurrentReading
{
    /**
     * @param  array<string, float|int|bool|string|WindDirection|WeatherCode|DateTimeImmutable|null>  $values
     */
    public function __construct(
        public DateTimeImmutable $time,
        public ?int $interval,
        private array $values,
    ) {}

    public function get(BackedEnum|string $variable): mixed
    {
        $key = is_string($variable) ? $variable : $variable->value;

        return $this->values[$key] ?? null;
    }

    public function __get(string $name): mixed
    {
        if ($name === 'weatherCode') {
            if (array_key_exists('weathercode', $this->values)) {
                return $this->values['weathercode'];
            }

            if (array_key_exists('weather_code', $this->values)) {
                return $this->values['weather_code'];
            }

            return null;
        }

        $key = ConvertsApiKeys::propertyToApiKey($name);

        if (array_key_exists($key, $this->values)) {
            return $this->values[$key];
        }

        $aliases = [
            'weathercode' => 'weather_code',
            'windspeed_10m' => 'wind_speed_10m',
            'winddirection_10m' => 'wind_direction_10m',
        ];

        foreach ($aliases as $from => $to) {
            if ($key === $to && array_key_exists($from, $this->values)) {
                return $this->values[$from];
            }

            if ($key === $from && array_key_exists($to, $this->values)) {
                return $this->values[$to];
            }
        }

        return null;
    }

    public function __isset(string $name): bool
    {
        return $this->__get($name) !== null;
    }
}
