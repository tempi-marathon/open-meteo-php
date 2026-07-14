<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Resources;

use TempiMarathon\OpenMeteo\Requests\Ensemble\GetEnsembleRequest;

final class EnsembleResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetEnsembleRequest
    {
        return GetEnsembleRequest::forCoordinates($latitude, $longitude)->using($this->connector);
    }

    /**
     * @param  list<array{0: float, 1: float}>  $points
     */
    public function forPoints(array $points): GetEnsembleRequest
    {
        return GetEnsembleRequest::forPoints($points)->using($this->connector);
    }
}
