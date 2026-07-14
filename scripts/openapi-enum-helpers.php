<?php

declare(strict_types=1);

/**
 * @return list<string>
 */
function extractOpenApiQueryEnum(string $yaml, string $parameterName): array
{
    $pattern = '/- name: '.preg_quote($parameterName, '/').'\n(?:.*?\n)*?              enum:\n((?:                - .+\n)+)/';

    if (! preg_match($pattern, $yaml, $match)) {
        return [];
    }

    return parseOpenApiEnumBlock($match[1]);
}

/**
 * @return list<string>
 */
function parseOpenApiEnumBlock(string $block): array
{
    $values = [];

    foreach (explode("\n", $block) as $line) {
        if (! preg_match('/^\s+- (.+)$/', $line, $matches)) {
            continue;
        }

        $value = trim($matches[1]);

        if ($value === '' || str_starts_with($value, 'name:')) {
            continue;
        }

        if (! preg_match('/^[a-z0-9_]+$/i', $value)) {
            continue;
        }

        $values[] = $value;
    }

    return $values;
}

function toEnumCase(string $value): string
{
    $parts = preg_split('/[^a-zA-Z0-9]+/', $value) ?: [];
    $case = '';

    foreach ($parts as $part) {
        if ($part === '') {
            continue;
        }

        $case .= ucfirst($part);
    }

    if ($case === '' || ctype_digit($case[0])) {
        $case = 'V'.$case;
    }

    return $case;
}

/**
 * @param  list<string>  $values
 */
function writeBackedEnum(string $name, array $values, string $target, bool $mutateIgnore = false): void
{
    $lines = ['<?php', ''];

    if ($mutateIgnore) {
        $lines[] = '/** @pest-mutate-ignore */';
        $lines[] = '';
    }

    $lines[] = 'declare(strict_types=1);';
    $lines[] = '';
    $lines[] = 'namespace TempiMarathon\OpenMeteo\Enums;';
    $lines[] = '';
    $lines[] = "enum {$name}: string";
    $lines[] = '{';

    foreach ($values as $value) {
        $lines[] = '    case '.toEnumCase($value).' = '.var_export($value, true).';';
    }

    $lines[] = '}';
    $lines[] = '';

    file_put_contents($target, implode("\n", $lines));
}
