<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

trait HasApiKeyQuery
{
    private ?string $apiKey = null;

    public function apiKey(#[\SensitiveParameter] string $apiKey): static
    {
        $clone = clone $this;
        $clone->apiKey = $apiKey;

        return $clone;
    }

    /**
     * @param  array<string, string>  $query
     * @return array<string, string>
     */
    protected function withApiKey(array $query): array
    {
        $apiKey = $this->apiKey ?? OpenMeteoConfig::apiKey();
        if ($apiKey !== null) {
            $query['apikey'] = $apiKey;
        }

        return $query;
    }
}
