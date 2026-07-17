<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Exceptions;

use InvalidArgumentException;

final class MissingCurrentTimeException extends InvalidArgumentException implements OpenMeteoException
{
    public function __construct()
    {
        parent::__construct('Current data must contain a time value.');
    }
}
