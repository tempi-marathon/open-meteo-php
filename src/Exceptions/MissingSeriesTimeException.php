<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Exceptions;

use InvalidArgumentException;

final class MissingSeriesTimeException extends InvalidArgumentException implements OpenMeteoException
{
    public function __construct()
    {
        parent::__construct('Series data must contain a time array.');
    }
}
