<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Tests\Support;

use TempiMarathon\OpenMeteo\Contracts\HasMinutely15;
use TempiMarathon\OpenMeteo\Data\Minutely15Series;
use TempiMarathon\OpenMeteo\Support\ProvidesMinutely15Series;

final readonly class Minutely15SeriesStub implements HasMinutely15
{
    use ProvidesMinutely15Series;

    public function __construct(private Minutely15Series $minutely15) {}
}
