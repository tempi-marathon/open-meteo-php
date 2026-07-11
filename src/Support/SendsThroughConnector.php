<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use Saloon\Http\Connector;
use Saloon\Http\Response;

trait SendsThroughConnector
{
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
            throw new \LogicException('No connector set. Build the request from a resource, or call ->using($connector) before ->send().');
        }

        return $this->connector->send($this);
    }

    public function dto(): mixed
    {
        return $this->send()->dtoOrFail();
    }
}
