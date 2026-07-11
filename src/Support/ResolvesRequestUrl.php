<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use Saloon\Http\Connector;
use Saloon\Http\Request;

trait ResolvesRequestUrl
{
    public function resolveRequestUrl(Connector $connector): string
    {
        if (! $this instanceof Request) {
            throw new \LogicException('ResolvesRequestUrl can only be used on Saloon requests.');
        }

        return rtrim($connector->resolveBaseUrl(), '/').'/'.ltrim($this->resolveEndpoint(), '/');
    }
}
