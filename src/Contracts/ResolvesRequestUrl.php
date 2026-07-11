<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Contracts;

use Saloon\Http\Connector;

interface ResolvesRequestUrl
{
    public function resolveRequestUrl(Connector $connector): string;
}
