<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum TemperatureUnit: string
{
    case Celsius = 'celsius';
    case Fahrenheit = 'fahrenheit';
}
