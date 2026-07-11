<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use Illuminate\Support\ServiceProvider;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\PendingRequest;
use TempiMarathon\OpenMeteo\Connectors\BaseConnector;
use TempiMarathon\OpenMeteo\Connectors\ForecastConnector;
use TempiMarathon\OpenMeteo\Exceptions\OpenMeteoRequestException;
use TempiMarathon\OpenMeteo\Laravel\OpenMeteoServiceProvider;
use TempiMarathon\OpenMeteo\Requests\Forecast\GetForecastRequest;
use TempiMarathon\OpenMeteo\Support\OpenMeteoConfig;

covers(
    ForecastConnector::class,
    BaseConnector::class,
    OpenMeteoRequestException::class,
    GetForecastRequest::class,
    OpenMeteoConfig::class,
);

it('retries transient failures', function (): void {
    $attempts = 0;

    MockClient::global([
        GetForecastRequest::class => function () use (&$attempts) {
            $attempts++;

            if ($attempts < 2) {
                return mockError('Rate limited', 429);
            }

            return mockOk(forecastPayload());
        },
    ]);

    $connector = new ForecastConnector;
    $response = $connector->weather()->get(52.37, 4.89)->send();

    expect($attempts)->toBe(2)
        ->and($response->successful())->toBeTrue();
});

it('does not retry client errors', function (): void {
    MockClient::global([
        GetForecastRequest::class => mockError('Bad request', 400),
    ]);

    $connector = new ForecastConnector;

    try {
        $connector->weather()->get(52.37, 4.89)->send();
        expect(false)->toBeTrue('Expected exception');
    } catch (OpenMeteoRequestException $exception) {
        expect($exception->statusCode())->toBe(400);
    }
});

it('exposes a laravel service provider', function (): void {
    expect(is_subclass_of(OpenMeteoServiceProvider::class, ServiceProvider::class))->toBeTrue();
});

it('handles retry callback for fatal errors', function (): void {
    $connector = new ForecastConnector;
    $request = $connector->weather()->get(52.37, 4.89);
    $fatal = new FatalRequestException(
        new Exception('network'),
        new PendingRequest($connector, $request),
    );

    expect($connector->handleRetry($fatal, $request))->toBeFalse();
});

it('handles retry callback for non-transient errors', function (): void {
    $connector = new ForecastConnector;
    $request = $connector->weather()->get(52.37, 4.89);
    $pendingRequest = $connector->createPendingRequest($request);
    $psrResponse = new Response(404);
    $response = new Saloon\Http\Response(
        $psrResponse,
        $pendingRequest,
        $pendingRequest->createPsrRequest(),
    );
    $exception = new RequestException($response, 'Not found');

    expect($connector->handleRetry($exception, $request))->toBeFalse();
});

it('uses user agent header when configured', function (): void {
    OpenMeteoConfig::configure(['user_agent' => 'open-meteo-php']);

    $connector = new ForecastConnector;
    $headers = (new ReflectionClass($connector))->getMethod('defaultHeaders')->invoke($connector);

    expect($headers)->toHaveKey('Accept')
        ->and($headers['Accept'])->toBe('application/json')
        ->and($headers['User-Agent'])->toBe('open-meteo-php');
});
