<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

final class ForecastWindowLimits
{
    public const int FORECAST_DAYS_MIN = 0;

    public const int FORECAST_DAYS_MAX = 16;

    public const int PAST_DAYS_MIN = 0;

    public const int PAST_DAYS_MAX = 92;

    public const int FORECAST_HOURS_MIN = 0;

    public const int FORECAST_HOURS_MAX = 384;

    public const int AIR_QUALITY_FORECAST_DAYS_MAX = 7;

    public const int MARINE_FORECAST_DAYS_MAX = 16;

    public const int ENSEMBLE_FORECAST_DAYS_MAX = 36;

    public const int FLOOD_FORECAST_DAYS_MAX = 366;

    public const int SEASONAL_FORECAST_DAYS_MAX = 217;

    public const int ARCHIVE_FORECAST_DAYS_MAX = 0;
}
