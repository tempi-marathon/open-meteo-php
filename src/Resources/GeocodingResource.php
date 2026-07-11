<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Resources;

use TempiMarathon\OpenMeteo\Requests\Geocoding\GetRequest;
use TempiMarathon\OpenMeteo\Requests\Geocoding\SearchRequest;

final class GeocodingResource extends BaseResource
{
    public function search(string $name): SearchRequest
    {
        return (new SearchRequest($name))->using($this->connector);
    }

    public function get(int $id): GetRequest
    {
        return (new GetRequest($id))->using($this->connector);
    }
}
