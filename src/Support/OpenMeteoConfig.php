<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use function Psl\Filesystem\canonicalize;
use function Psl\Filesystem\exists;
use function Psl\Iter\contains;
use function Psl\Str\ends_with;
use function Psl\Str\lowercase;
use function Psl\Str\starts_with;

final class OpenMeteoConfig
{
    /** @var array<string, string> */
    private const DEFAULT_HOSTS = [ // @pest-mutate-ignore
        'forecast' => 'https://api.open-meteo.com/v1/',
        'historical' => 'https://archive-api.open-meteo.com/v1/',
        'geocoding' => 'https://geocoding-api.open-meteo.com/v1/',
        'air_quality' => 'https://air-quality-api.open-meteo.com/v1/',
        'marine' => 'https://marine-api.open-meteo.com/v1/',
        'climate' => 'https://climate-api.open-meteo.com/v1/',
        'flood' => 'https://flood-api.open-meteo.com/v1/',
        'ensemble' => 'https://ensemble-api.open-meteo.com/v1/',
        'seasonal' => 'https://seasonal-api.open-meteo.com/v1/',
        'elevation' => 'https://api.open-meteo.com/v1/',
    ];

    /** @var array<string, mixed>|null */
    private static ?array $config = null;

    /** @param array<string, mixed> $config */
    public static function configure(array $config): void
    {
        self::$config = $config;
    }

    public static function reset(): void
    {
        self::$config = null;
    }

    public static function host(string $key, string $default): string
    {
        $config = self::resolved();
        /** @var array<string, string> $hosts */
        $hosts = $config['hosts'] ?? [];
        $url = $hosts[$key] ?? $default;

        if (self::$config === null) {
            $url = self::isAllowedHostUrl($url) ? $url : $default;
        }

        return self::resolveCommercialHost($url, $key);
    }

    public static function apiKey(): ?string
    {
        $apiKey = self::resolved()['apikey'] ?? null;

        return is_string($apiKey) && $apiKey !== '' ? $apiKey : null;
    }

    public static function userAgent(): ?string
    {
        $userAgent = self::resolved()['user_agent'] ?? null;

        return is_string($userAgent) && $userAgent !== '' ? $userAgent : null;
    }

    /** @return array<string, mixed> */
    private static function resolved(): array
    {
        if (self::$config !== null) {
            return self::$config;
        }

        $path = self::resolveConfigFilePath();
        if ($path === null) {
            return [];
        }

        /** @var array<string, mixed> $config */
        $config = require $path;

        return $config;
    }

    private static function resolveConfigFilePath(): ?string
    {
        $packageRoot = dirname(__DIR__, 2);
        $defaultPath = $packageRoot.'/config/openmeteo.php';

        $configuredPath = getenv('OPENMETEO_CONFIG_PATH');
        if (! is_string($configuredPath) || $configuredPath === '') { // @pest-mutate-ignore: EmptyStringToNotEmpty
            return exists($defaultPath) ? $defaultPath : null;
        }

        $resolved = canonicalize($configuredPath);
        if ($resolved === null) {
            return null;
        }

        if (! ends_with($resolved, '.php')) {
            return exists($defaultPath) ? $defaultPath : null;
        }

        $packageRootReal = canonicalize($packageRoot);
        if ($packageRootReal === null || ! starts_with($resolved, $packageRootReal.DIRECTORY_SEPARATOR)) { // @pest-mutate-ignore: ConcatRemoveRight
            return exists($defaultPath) ? $defaultPath : null; // @pest-mutate-ignore: TernaryNegated
        }

        return $resolved;
    }

    private static function resolveCommercialHost(string $url, string $key): string
    {
        if (self::apiKey() === null) {
            return $url;
        }

        if (! self::isDefaultFreeTierHost($url, $key)) {
            return $url;
        }

        return self::toCustomerHost($url);
    }

    private static function isDefaultFreeTierHost(string $url, string $key): bool
    {
        $default = self::DEFAULT_HOSTS[$key] ?? null;

        return $default !== null && $url === $default;
    }

    private static function toCustomerHost(string $url): string
    {
        $parts = parse_url($url);
        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) { // @pest-mutate-ignore: FalseToTrue
            return $url;
        }

        if (starts_with(lowercase($parts['host']), 'customer-')) {
            return $url;
        }

        $scheme = $parts['scheme'].'://';
        $host = 'customer-'.$parts['host'];
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';
        $path = $parts['path'] ?? '';
        $query = isset($parts['query']) ? '?'.$parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

        return $scheme.$host.$port.$path.$query.$fragment;
    }

    private static function isAllowedHostUrl(string $url): bool
    {
        $parts = parse_url($url);
        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) { // @pest-mutate-ignore: FalseToTrue
            return false;
        }

        if (lowercase($parts['scheme']) !== 'https') {
            return false;
        }

        $host = lowercase($parts['host']);

        if (contains(['localhost', '127.0.0.1'], $host)) {
            return true;
        }

        return $host === 'open-meteo.com' || ends_with($host, '.open-meteo.com');
    }
}
