<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

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

        return $hosts[$key] ?? $default;
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

        $configuredPath = getenv('OPENMETEO_CONFIG_PATH');
        $path = is_string($configuredPath) && $configuredPath !== ''
            ? $configuredPath
            : dirname(__DIR__, 2).'/config/openmeteo.php';
        if (! is_file($path)) {
            return [];
        }

        /** @var array<string, mixed> $config */
        $config = require $path;

        return $config;
    }
}
