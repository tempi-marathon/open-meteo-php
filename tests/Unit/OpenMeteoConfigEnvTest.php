<?php

declare(strict_types=1);

use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

covers(
    OpenMeteoConfig::class,
);

it('reads api key from environment variables', function (): void {
    OpenMeteoConfig::reset();
    putenv('OPENMETEO_API_KEY=test-api-key');
    putenv('OPENMETEO_USER_AGENT=test-agent');

    expect(OpenMeteoConfig::apiKey())->toBe('test-api-key')
        ->and(OpenMeteoConfig::userAgent())->toBe('test-agent');

    putenv('OPENMETEO_API_KEY');
    putenv('OPENMETEO_USER_AGENT');
});
