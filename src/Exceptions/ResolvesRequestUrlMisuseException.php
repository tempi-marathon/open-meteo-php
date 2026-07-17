<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Exceptions;

use LogicException;

final class ResolvesRequestUrlMisuseException extends LogicException implements OpenMeteoException
{
    public function __construct()
    {
        parent::__construct('ResolvesRequestUrl can only be used on Saloon requests.');
    }
}
