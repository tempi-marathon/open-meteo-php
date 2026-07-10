<?php

declare(strict_types=1);

namespace OpenMeteo\Contracts;

use Saloon\Http\Connector;

interface ResolvesRequestUrl
{
    public function resolveRequestUrl(Connector $connector): string;
}
