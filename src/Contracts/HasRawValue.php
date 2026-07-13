<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Contracts;

interface HasRawValue
{
    public function getRaw(): int|float;
}
