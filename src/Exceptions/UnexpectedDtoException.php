<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Exceptions;

use LogicException;

final class UnexpectedDtoException extends LogicException
{
    /**
     * @param  class-string  $expectedClass
     */
    public function __construct(public readonly string $expectedClass)
    {
        parent::__construct(sprintf('Expected %s DTO.', $expectedClass));
    }
}
