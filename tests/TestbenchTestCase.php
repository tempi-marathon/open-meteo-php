<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase;
use TempiMarathon\OpenMeteo\Laravel\OpenMeteoServiceProvider;

abstract class TestbenchTestCase extends TestCase
{
    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [OpenMeteoServiceProvider::class];
    }
}
