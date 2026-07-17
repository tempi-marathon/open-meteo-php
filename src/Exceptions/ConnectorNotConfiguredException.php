<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Exceptions;

use LogicException;

final class ConnectorNotConfiguredException extends LogicException implements OpenMeteoException
{
    public function __construct()
    {
        parent::__construct('No connector set. Build the request from a resource, or call ->using($connector) before ->send().');
    }
}
