<?php

declare(strict_types=1);

require dirname(__DIR__).'/vendor/autoload.php';

$spec = file_get_contents(dirname(__DIR__).'/openapi/air-quality.yml');
preg_match('/- name: hourly\n(?:.|\n)*?enum:\n((?:\s+- .+\n)+)/', $spec, $match);

function parseEnumBlock(string $block): array
{
    $values = [];
    foreach (explode("\n", $block) as $line) {
        $line = trim($line);
        if (str_starts_with($line, '- ')) {
            $values[] = substr($line, 2);
        }
    }

    return $values;
}

function toCase(string $value): string
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

$values = parseEnumBlock($match[1]);
$lines = ['<?php', '', '/** @pest-mutate-ignore */', '', 'declare(strict_types=1);', '', 'namespace TempiMarathon\\OpenMeteo\\Enums;', '', 'enum AirQualityHourlyVariable: string', '{'];

foreach ($values as $value) {
    $lines[] = '    case '.toCase($value).' = '.var_export($value, true).';';
}

$lines[] = '}';
$lines[] = '';

file_put_contents(dirname(__DIR__).'/src/Enums/AirQualityHourlyVariable.php', implode("\n", $lines));

echo 'Generated AirQualityHourlyVariable with '.count($values)." cases\n";
