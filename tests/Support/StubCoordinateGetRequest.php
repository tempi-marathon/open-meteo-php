<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Tests\Support;

use TempiMarathon\OpenMeteo\Data\MarineResponse;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;

final class StubCoordinateGetRequest extends AbstractCoordinateGetRequest
{
    protected function responseClass(): string
    {
        return MarineResponse::class;
    }

    public function resolveEndpoint(): string
    {
        return '/v1/marine';
    }
}
