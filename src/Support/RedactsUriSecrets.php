<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

final class RedactsUriSecrets
{
    /** @var list<string> */
    private const array SENSITIVE_QUERY_KEYS = [
        'apikey',
    ];

    public static function redact(string $uri): string
    {
        $parts = parse_url($uri);
        if ($parts === false || ! isset($parts['query'])) {
            return $uri;
        }

        parse_str($parts['query'], $query);
        foreach (self::SENSITIVE_QUERY_KEYS as $key) {
            if (array_key_exists($key, $query)) {
                $query[$key] = '[REDACTED]';
            }
        }

        $parts['query'] = http_build_query($query);

        return self::buildUri($parts);
    }

    /**
     * @param  array<string, int|string|null>  $parts
     */
    private static function buildUri(array $parts): string
    {
        $uri = '';

        if (isset($parts['scheme'])) {
            $uri .= $parts['scheme'].'://';
        }

        if (isset($parts['user'])) {
            $uri .= $parts['user'];
            if (isset($parts['pass'])) {
                $uri .= ':[REDACTED]';
            }
            $uri .= '@';
        }

        if (isset($parts['host'])) {
            $uri .= $parts['host'];
        }

        if (isset($parts['port'])) {
            $uri .= ':'.$parts['port'];
        }

        $uri .= $parts['path'] ?? '';

        if (isset($parts['query']) && $parts['query'] !== '') {
            $uri .= '?'.$parts['query'];
        }

        if (isset($parts['fragment'])) {
            $uri .= '#'.$parts['fragment'];
        }

        return $uri;
    }
}
