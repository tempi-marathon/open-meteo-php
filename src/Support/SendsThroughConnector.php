<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use Saloon\Http\Connector;
use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Exceptions\ConnectorNotConfiguredException;

trait SendsThroughConnector
{
    use ResolvesTypedDto;

    private ?Connector $connector = null;

    public function using(Connector $connector): static
    {
        return clone ($this, [
            'connector' => $connector,
        ]);
    }

    public function send(): Response
    {
        if ($this->connector === null) {
            throw new ConnectorNotConfiguredException;
        }

        return $this->connector->send($this);
    }
}
