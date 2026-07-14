<?php

declare(strict_types=1);

require __DIR__.'/openapi-enum-helpers.php';

$root = dirname(__DIR__);
$enumDir = $root.'/src/Enums';

$definitions = [
    ['HistoricalHourlyVariable', $root.'/openapi/historical-weather.yml', 'hourly'],
    ['HistoricalDailyVariable', $root.'/openapi/historical-weather.yml', 'daily'],
    ['MarineHourlyVariable', $root.'/openapi/marine.yml', 'hourly'],
    ['MarineDailyVariable', $root.'/openapi/marine.yml', 'daily'],
    ['MarineCurrentVariable', $root.'/openapi/marine.yml', 'current'],
    ['MarineMinutely15Variable', $root.'/openapi/marine.yml', 'minutely_15'],
    ['ClimateDailyVariable', $root.'/openapi/climate.yml', 'daily'],
    ['FloodDailyVariable', $root.'/openapi/flood.yml', 'daily'],
    ['EnsembleHourlyVariable', $root.'/openapi/ensemble.yml', 'hourly'],
    ['EnsembleDailyVariable', $root.'/openapi/ensemble.yml', 'daily'],
    ['SeasonalHourlyVariable', $root.'/openapi/seasonal.yml', 'hourly'],
    ['SeasonalDailyVariable', $root.'/openapi/seasonal.yml', 'daily'],
    ['SeasonalWeeklyVariable', $root.'/openapi/seasonal.yml', 'weekly'],
    ['MonthlyVariable', $root.'/openapi/seasonal.yml', 'monthly'],
];

foreach ($definitions as [$enumName, $specPath, $parameterName]) {
    $yaml = file_get_contents($specPath);
    $values = extractOpenApiQueryEnum($yaml, $parameterName);
    writeBackedEnum($enumName, $values, $enumDir.'/'.$enumName.'.php', true);
    echo "Generated {$enumName} (".count($values)." cases)\n";
}
