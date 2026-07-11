<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Resources;

use Saloon\Http\Connector;
use Saloon\Http\Request;
use TempiMarathon\OpenMeteo\Contracts\ResolvesRequestUrl;
use TempiMarathon\OpenMeteo\Support\RedactsUriSecrets;

abstract class BaseResource
{
    public function __construct(protected readonly Connector $connector) {}

    public function connector(): Connector
    {
        return $this->connector;
    }

    public function debugUrl(Request $request): string
    {
        if (! $request instanceof ResolvesRequestUrl) {
            throw new \LogicException('Request must implement ResolvesRequestUrl to build a debug URL.');
        }

        $uri = (string) $this->connector->createPendingRequest($request)->createPsrRequest()->getUri();

        return RedactsUriSecrets::redact($uri);
    }
}
