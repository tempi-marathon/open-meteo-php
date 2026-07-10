<?php

declare(strict_types=1);

namespace OpenMeteo\Resources;

use OpenMeteo\Requests\Geocoding\GetRequest;
use OpenMeteo\Requests\Geocoding\SearchRequest;

final class GeocodingResource extends BaseResource
{
    public function search(string $name): SearchRequest
    {
        return new SearchRequest($name);
    }

    public function get(int $id): GetRequest
    {
        return new GetRequest($id);
    }
}
