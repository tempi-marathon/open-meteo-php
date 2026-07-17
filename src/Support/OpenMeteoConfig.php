<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

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

    /** @var (callable(): array<string, mixed>)|null */
    private static $resolver = null;

    /** @param array<string, mixed> $config */
    public static function configure(array $config): void
    {
        self::$config = $config;
    }

    /**
     * Register a callback that returns the current configuration on demand.
     *
     * Used by the Laravel integration to read live `config('openmeteo')` on
     * every request, which keeps the SDK correct under Octane and queue workers.
     *
     * @param  (callable(): array<string, mixed>)|null  $resolver
     */
    public static function resolveUsing(?callable $resolver): void
    {
        self::$resolver = $resolver;
    }

    public static function reset(): void
    {
        self::$config = null;
        self::$resolver = null;
    }

    public static function host(string $key, string $default): string
    {
        $config = self::resolved();
        /** @var array<string, string> $hosts */
        $hosts = $config['hosts'] ?? [];
        $url = $hosts[$key] ?? $default;

        $url = self::isTrustedSource()
            ? (self::isAllowedTrustedHostUrl($url) ? $url : $default)
            : (self::isAllowedFileHostUrl($url) ? $url : $default);

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

    /**
     * Whether the active configuration originated from trusted application code
     * (an explicit array or resolver) rather than an untrusted config file.
     */
    private static function isTrustedSource(): bool
    {
        return self::$config !== null || self::$resolver !== null;
    }

    /** @return array<string, mixed> */
    private static function resolved(): array
    {
        if (self::$config !== null) {
            return self::$config;
        }

        if (self::$resolver !== null) {
            $resolved = (self::$resolver)();

            return $resolved;
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
            return is_file($defaultPath) ? $defaultPath : null;
        }

        $resolved = realpath($configuredPath);
        if ($resolved === false) {
            return null;
        }

        if (! str_ends_with($resolved, '.php')) {
            return is_file($defaultPath) ? $defaultPath : null;
        }

        $packageRootReal = realpath($packageRoot);
        if ($packageRootReal === false || ! str_starts_with($resolved, $packageRootReal.DIRECTORY_SEPARATOR)) { // @pest-mutate-ignore: ConcatRemoveRight
            return is_file($defaultPath) ? $defaultPath : null; // @pest-mutate-ignore: TernaryNegated
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

        if (str_starts_with(strtolower($parts['host']), 'customer-')) {
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

    /**
     * Trusted sources may target any host, but non-loopback hosts must use https.
     */
    private static function isAllowedTrustedHostUrl(string $url): bool
    {
        $parts = parse_url($url);
        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) { // @pest-mutate-ignore: FalseToTrue
            return false;
        }

        if (self::isLoopbackHost(strtolower($parts['host']))) {
            return true;
        }

        return strtolower($parts['scheme']) === 'https';
    }

    /**
     * Untrusted config files may only target Open-Meteo hosts (or loopback) over https.
     */
    private static function isAllowedFileHostUrl(string $url): bool
    {
        $parts = parse_url($url);
        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) { // @pest-mutate-ignore: FalseToTrue
            return false;
        }

        if (strtolower($parts['scheme']) !== 'https') {
            return false;
        }

        $host = strtolower($parts['host']);

        if (self::isLoopbackHost($host)) {
            return true;
        }

        return $host === 'open-meteo.com' || str_ends_with($host, '.open-meteo.com');
    }

    private static function isLoopbackHost(string $host): bool
    {
        return in_array($host, ['localhost', '127.0.0.1'], true);
    }
}
