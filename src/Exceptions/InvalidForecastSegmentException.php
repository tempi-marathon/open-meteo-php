<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Exceptions;

use UnexpectedValueException;

final class InvalidForecastSegmentException extends UnexpectedValueException
{
    public function __construct()
    {
        parent::__construct('Expected forecast segment to be an array.');
    }
}
