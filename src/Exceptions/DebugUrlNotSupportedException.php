<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Exceptions;

use LogicException;

final class DebugUrlNotSupportedException extends LogicException
{
    public function __construct()
    {
        parent::__construct('Request must implement ResolvesRequestUrl to build a debug URL.');
    }
}
