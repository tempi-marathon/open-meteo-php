<?php

declare(strict_types=1);

require __DIR__.'/openapi-enum-helpers.php';

$root = dirname(__DIR__);
$enumDir = $root.'/src/Enums';

$definitions = [
    ['ForecastModel', $root.'/openapi/forecast.yml'],
    ['HistoricalModel', $root.'/openapi/historical-weather.yml'],
    ['MarineModel', $root.'/openapi/marine.yml'],
    ['ClimateModel', $root.'/openapi/climate.yml'],
    ['FloodModel', $root.'/openapi/flood.yml'],
    ['EnsembleModel', $root.'/openapi/ensemble.yml'],
    ['SeasonalModel', $root.'/openapi/seasonal.yml'],
];

foreach ($definitions as [$enumName, $specPath]) {
    $yaml = file_get_contents($specPath);
    $values = extractOpenApiQueryEnum($yaml, 'models');
    writeBackedEnum($enumName, $values, $enumDir.'/'.$enumName.'.php', true);
    echo "Generated {$enumName} (".count($values)." cases)\n";
}
