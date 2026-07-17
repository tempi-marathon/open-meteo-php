<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Connectors;

use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Exceptions\OpenMeteoRequestException;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;
use Throwable;

/** @pest-mutate-ignore */
abstract class BaseConnector extends Connector
{
    public ?int $tries = 3;

    public ?int $retryInterval = 500;

    public ?bool $useExponentialBackoff = true;

    /** @return array<string, string> */
    protected function defaultHeaders(): array
    {
        $headers = [
            'Accept' => 'application/json',
        ];

        $userAgent = OpenMeteoConfig::userAgent();
        if ($userAgent !== null) {
            $headers['User-Agent'] = $userAgent;
        }

        return $headers;
    }

    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        if (! $exception instanceof RequestException) {
            return false;
        }

        return in_array($exception->getResponse()->status(), [408, 425, 429, 500, 502, 503, 504], true);
    }

    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
    {
        if ($response->status() === 400) {
            /** @var array<string, mixed> $data */
            $data = $response->json();
            if (($data['error'] ?? false) === true) {
                $reason = is_string($data['reason'] ?? null) ? $data['reason'] : null;

                return new OpenMeteoRequestException($reason, 400);
            }
        }

        return $senderException;
    }
}
