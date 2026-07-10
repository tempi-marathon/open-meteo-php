<?php

declare(strict_types=1);

namespace OpenMeteo\Resources;

use OpenMeteo\Requests\Ensemble\GetEnsembleRequest;

final class EnsembleResource extends BaseResource
{
    public function get(float $latitude, float $longitude): GetEnsembleRequest
    {
        return GetEnsembleRequest::forCoordinates($latitude, $longitude);
    }
}
