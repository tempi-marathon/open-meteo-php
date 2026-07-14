<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Tests\Support;

use TempiMarathon\OpenMeteo\Contracts\HasCurrent;
use TempiMarathon\OpenMeteo\Data\CurrentSeries;
use TempiMarathon\OpenMeteo\Support\ProvidesCurrentSeries;

final readonly class CurrentSeriesStub implements HasCurrent
{
    use ProvidesCurrentSeries;

    public function __construct(private CurrentSeries $current) {}
}
