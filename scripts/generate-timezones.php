<?php

declare(strict_types=1);

$zones = array_merge(
    ['auto', 'GMT', 'UTC'],
    array_filter(
        DateTimeZone::listIdentifiers(),
        static fn (string $zone): bool => str_starts_with($zone, 'Europe/')
            || str_starts_with($zone, 'America/')
            || str_starts_with($zone, 'Asia/')
            || str_starts_with($zone, 'Australia/')
            || str_starts_with($zone, 'Pacific/')
            || str_starts_with($zone, 'Africa/'),
    ),
);

$lines = ['<?php', '', 'declare(strict_types=1);', '', 'namespace OpenMeteo\Enums;', '', 'enum Timezone: string', '{'];
foreach ($zones as $zone) {
    $case = preg_replace('/[^A-Za-z0-9]/', '', $zone) ?? 'Zone';
    if ($case === '' || ctype_digit($case[0])) {
        $case = 'Tz'.$case;
    }
    $lines[] = "    case {$case} = '{$zone}';";
}
$lines[] = '}';
$lines[] = '';
file_put_contents(dirname(__DIR__).'/src/Enums/Timezone.php', implode("\n", $lines));
echo 'Generated Timezone enum with '.count($zones)." cases\n";
