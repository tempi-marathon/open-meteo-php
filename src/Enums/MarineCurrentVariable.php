<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum MarineCurrentVariable: string
{
    case WaveHeight = 'wave_height';
    case WaveDirection = 'wave_direction';
    case WavePeriod = 'wave_period';
    case WindWaveHeight = 'wind_wave_height';
    case WindWaveDirection = 'wind_wave_direction';
    case WindWavePeriod = 'wind_wave_period';
    case WindWavePeakPeriod = 'wind_wave_peak_period';
    case SwellWaveHeight = 'swell_wave_height';
    case SwellWaveDirection = 'swell_wave_direction';
    case SwellWavePeriod = 'swell_wave_period';
    case SwellWavePeakPeriod = 'swell_wave_peak_period';
    case SeaSurfaceTemperature = 'sea_surface_temperature';
    case OceanCurrentVelocity = 'ocean_current_velocity';
    case OceanCurrentDirection = 'ocean_current_direction';
}
