<?php

declare(strict_types=1);

namespace OpenMeteo\Resources;

use OpenMeteo\Contracts\ResolvesRequestUrl;
use Saloon\Http\Connector;
use Saloon\Http\Request;

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

        return (string) $this->connector->createPendingRequest($request)->createPsrRequest()->getUri();
    }
}
