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

        if (self::$config !== null) {
            return $url;
        }

        return self::isAllowedHostUrl($url) ? $url : $default;
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
        if (! is_string($configuredPath) || $configuredPath === '') {
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
        if ($packageRootReal === null || ! starts_with($resolved, $packageRootReal.DIRECTORY_SEPARATOR)) {
            return exists($defaultPath) ? $defaultPath : null;
        }

        return $resolved;
    }

    private static function isAllowedHostUrl(string $url): bool
    {
        $parts = parse_url($url);
        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
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
