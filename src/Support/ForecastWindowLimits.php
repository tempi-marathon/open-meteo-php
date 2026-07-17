<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

final class ForecastWindowLimits
{
    // @pest-mutate-ignore
    public const int FORECAST_DAYS_MIN = 0;

    // @pest-mutate-ignore
    public const int FORECAST_DAYS_MAX = 16;

    // @pest-mutate-ignore
    public const int PAST_DAYS_MIN = 0;

    // @pest-mutate-ignore
    public const int PAST_DAYS_MAX = 92;

    // @pest-mutate-ignore
    public const int FORECAST_HOURS_MIN = 0;

    // @pest-mutate-ignore
    public const int FORECAST_HOURS_MAX = 384;

    // @pest-mutate-ignore
    public const int AIR_QUALITY_FORECAST_DAYS_MAX = 7;

    // @pest-mutate-ignore
    public const int MARINE_FORECAST_DAYS_MAX = 16;

    // @pest-mutate-ignore
    public const int ENSEMBLE_FORECAST_DAYS_MAX = 36;

    // @pest-mutate-ignore
    public const int FLOOD_FORECAST_DAYS_MAX = 366;

    // @pest-mutate-ignore
    public const int SEASONAL_FORECAST_DAYS_MAX = 217;

    // @pest-mutate-ignore
    public const int ARCHIVE_FORECAST_DAYS_MAX = 0;
}
