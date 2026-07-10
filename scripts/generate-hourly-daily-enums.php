<?php

declare(strict_types=1);

require dirname(__DIR__).'/vendor/autoload.php';

$forecast = file_get_contents(dirname(__DIR__).'/openapi/forecast.yml');
preg_match('/- name: hourly\n(?:.|\n)*?enum:\n((?:\s+- .+\n)+)/', $forecast, $hourlyMatch);
preg_match('/- name: daily\n(?:.|\n)*?enum:\n((?:\s+- .+\n)+)/', $forecast, $dailyMatch);

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

function writeEnum(string $name, array $values, string $target): void
{
    $lines = ['<?php', '', 'declare(strict_types=1);', '', 'namespace OpenMeteo\Enums;', '', "enum {$name}: string", '{'];
    foreach ($values as $value) {
        $lines[] = '    case '.toCase($value)." = '{$value}';";
    }
    $lines[] = '}';
    $lines[] = '';
    file_put_contents($target, implode("\n", $lines));
}

$hourly = parseEnumBlock($hourlyMatch[1] ?? '');
$daily = parseEnumBlock($dailyMatch[1] ?? '');
writeEnum('HourlyVariable', $hourly, dirname(__DIR__).'/src/Enums/HourlyVariable.php');
writeEnum('DailyVariable', $daily, dirname(__DIR__).'/src/Enums/DailyVariable.php');
echo 'Generated HourlyVariable ('.count($hourly).') and DailyVariable ('.count($daily).")\n";
