<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum TimeFormat: string
{
    case Iso8601 = 'iso8601';
    case Unixtime = 'unixtime';
}
