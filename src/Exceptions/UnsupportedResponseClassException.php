<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Exceptions;

use InvalidArgumentException;

final class UnsupportedResponseClassException extends InvalidArgumentException implements OpenMeteoException
{
    public function __construct(string $responseClass)
    {
        parent::__construct(sprintf('Unsupported response class: %s', $responseClass));
    }
}
