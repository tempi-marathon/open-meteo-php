<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use TempiMarathon\OpenMeteo\Exceptions\InvalidForecastParameterException;

trait BuildsForecastWindowQuery
{
    private ?int $forecastDays = null;

    private ?int $pastDays = null;

    private ?int $forecastHours = null;

    private ?int $pastHours = null;

    /**
     * @return array{0: int, 1: int}|null
     */
    protected function supportedForecastDaysRange(): ?array
    {
        return null;
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    protected function supportedPastDaysRange(): ?array
    {
        return null;
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    protected function supportedForecastHoursRange(): ?array
    {
        return null;
    }

    protected function supportsPastHours(): bool
    {
        return false;
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    protected function supportedPastHoursRange(): ?array
    {
        return null;
    }

    public function forecastDays(int $forecastDays): static
    {
        $range = $this->supportedForecastDaysRange();

        if ($range === null) {
            throw new InvalidForecastParameterException('forecast_days is not supported for this endpoint.');
        }

        if ($forecastDays < $range[0] || $forecastDays > $range[1]) {
            throw new InvalidForecastParameterException(
                sprintf('forecast_days must be between %d and %d, %d given.', $range[0], $range[1], $forecastDays),
            );
        }

        $clone = clone $this;
        $clone->forecastDays = $forecastDays;

        return $clone;
    }

    public function pastDays(int $pastDays): static
    {
        $range = $this->supportedPastDaysRange();

        if ($range === null) {
            throw new InvalidForecastParameterException('past_days is not supported for this endpoint.');
        }

        if ($pastDays < $range[0] || $pastDays > $range[1]) {
            throw new InvalidForecastParameterException(
                sprintf('past_days must be between %d and %d, %d given.', $range[0], $range[1], $pastDays),
            );
        }

        $clone = clone $this;
        $clone->pastDays = $pastDays;

        return $clone;
    }

    public function forecastHours(int $forecastHours): static
    {
        $range = $this->supportedForecastHoursRange();

        if ($range === null) {
            throw new InvalidForecastParameterException('forecast_hours is not supported for this endpoint.');
        }

        if ($forecastHours < $range[0] || $forecastHours > $range[1]) {
            throw new InvalidForecastParameterException(
                sprintf('forecast_hours must be between %d and %d, %d given.', $range[0], $range[1], $forecastHours),
            );
        }

        $clone = clone $this;
        $clone->forecastHours = $forecastHours;

        return $clone;
    }

    public function pastHours(int $pastHours): static
    {
        if (! $this->supportsPastHours()) {
            throw new InvalidForecastParameterException('past_hours is not supported for this endpoint.');
        }

        $range = $this->supportedPastHoursRange();

        if ($range !== null && ($pastHours < $range[0] || $pastHours > $range[1])) {
            throw new InvalidForecastParameterException(
                sprintf('past_hours must be between %d and %d, %d given.', $range[0], $range[1], $pastHours),
            );
        }

        $clone = clone $this;
        $clone->pastHours = $pastHours;

        return $clone;
    }

    /**
     * @param  array<string, string>  $query
     * @return array<string, string>
     */
    protected function withForecastWindowQuery(array $query): array
    {
        if ($this->forecastDays !== null) {
            $query['forecast_days'] = (string) $this->forecastDays;
        }

        if ($this->pastDays !== null) {
            $query['past_days'] = (string) $this->pastDays;
        }

        if ($this->forecastHours !== null) {
            $query['forecast_hours'] = (string) $this->forecastHours;
        }

        if ($this->pastHours !== null) {
            $query['past_hours'] = (string) $this->pastHours;
        }

        return $query;
    }
}
