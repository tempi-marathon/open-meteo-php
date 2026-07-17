<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Exceptions;

use RuntimeException;

final class MultiCoordinateResponseException extends RuntimeException implements OpenMeteoException
{
    public function __construct(
        string $message = 'Multi-coordinate response received. Use createDtoCollectionFromResponse() or dtoCollection() instead.',
    ) {
        parent::__construct($message);
    }
}
