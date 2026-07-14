<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use Saloon\Http\Connector;
use Saloon\Http\Request;
use TempiMarathon\OpenMeteo\Exceptions\ResolvesRequestUrlMisuseException;

trait ResolvesRequestUrl
{
    public function resolveRequestUrl(Connector $connector): string
    {
        if (! $this instanceof Request) {
            throw new ResolvesRequestUrlMisuseException;
        }

        return rtrim($connector->resolveBaseUrl(), '/').'/'.ltrim($this->resolveEndpoint(), '/');
    }
}
