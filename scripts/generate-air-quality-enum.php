<?php

declare(strict_types=1);

require __DIR__.'/openapi-enum-helpers.php';

$spec = file_get_contents(dirname(__DIR__).'/openapi/air-quality.yml');

writeBackedEnum('AirQualityHourlyVariable', extractOpenApiQueryEnum($spec, 'hourly'), dirname(__DIR__).'/src/Enums/AirQualityHourlyVariable.php', true);
writeBackedEnum('AirQualityCurrentVariable', extractOpenApiQueryEnum($spec, 'current'), dirname(__DIR__).'/src/Enums/AirQualityCurrentVariable.php', true);

echo 'Generated AirQualityHourlyVariable and AirQualityCurrentVariable'.PHP_EOL;
