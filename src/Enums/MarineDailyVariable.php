<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum MarineDailyVariable: string
{
    case WaveHeightMax = 'wave_height_max';
    case WaveDirectionDominant = 'wave_direction_dominant';
    case WavePeriodMax = 'wave_period_max';
    case WindWaveHeightMax = 'wind_wave_height_max';
    case WindWaveDirectionDominant = 'wind_wave_direction_dominant';
    case WindWavePeriodMax = 'wind_wave_period_max';
    case WindWavePeakPeriodMax = 'wind_wave_peak_period_max';
    case SwellWaveHeightMax = 'swell_wave_height_max';
    case SwellWaveDirectionDominant = 'swell_wave_direction_dominant';
    case SwellWavePeriodMax = 'swell_wave_period_max';
    case SwellWavePeakPeriodMax = 'swell_wave_peak_period_max';
}
