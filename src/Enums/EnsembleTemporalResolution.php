<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum EnsembleTemporalResolution: string
{
    case Native = 'native';
    case Hourly = 'hourly';
    case Hourly3 = 'hourly_3';
    case Hourly6 = 'hourly_6';
}
