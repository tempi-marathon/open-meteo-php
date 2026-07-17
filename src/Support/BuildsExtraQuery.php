<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

trait BuildsExtraQuery
{
    /** @var array<string, string> */
    private array $extraQuery = [];

    public function withQueryParam(string $key, string|int|float|bool $value): static
    {
        $clone = clone $this;
        $clone->extraQuery = [
            ...$this->extraQuery,
            $key => (string) $value,
        ];

        return $clone;
    }

    /**
     * @param  array<string, string>  $query
     * @return array<string, string>
     */
    protected function withExtraQuery(array $query): array
    {
        if ($this->extraQuery === []) {
            return $query;
        }

        return [
            ...$query,
            ...$this->extraQuery,
        ];
    }
}
