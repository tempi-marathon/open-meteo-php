<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Exceptions;

use Exception;

final class OpenMeteoRequestException extends Exception implements OpenMeteoException
{
    public function __construct(
        private readonly ?string $reason,
        private readonly ?int $statusCode,
        string $message = '',
    ) {
        parent::__construct($message !== '' ? $message : ($reason ?? 'Open-Meteo request failed'));
    }

    public function reason(): ?string
    {
        return $this->reason;
    }

    public function statusCode(): ?int
    {
        return $this->statusCode;
    }
}
