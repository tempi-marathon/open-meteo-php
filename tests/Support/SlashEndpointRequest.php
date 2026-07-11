<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Tests\Support;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use TempiMarathon\OpenMeteo\Contracts\ResolvesRequestUrl as ResolvesRequestUrlContract;
use TempiMarathon\OpenMeteo\Support\ResolvesRequestUrl;

final class SlashEndpointRequest extends Request implements ResolvesRequestUrlContract
{
    use ResolvesRequestUrl;

    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/forecast';
    }
}
