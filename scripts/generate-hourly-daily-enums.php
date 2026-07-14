<?php

declare(strict_types=1);

require __DIR__.'/openapi-enum-helpers.php';

$forecast = file_get_contents(dirname(__DIR__).'/openapi/forecast.yml');

writeBackedEnum('HourlyVariable', extractOpenApiQueryEnum($forecast, 'hourly'), dirname(__DIR__).'/src/Enums/HourlyVariable.php', true);
writeBackedEnum('DailyVariable', extractOpenApiQueryEnum($forecast, 'daily'), dirname(__DIR__).'/src/Enums/DailyVariable.php', true);
writeBackedEnum('ForecastCurrentVariable', extractOpenApiQueryEnum($forecast, 'current'), dirname(__DIR__).'/src/Enums/ForecastCurrentVariable.php', true);
writeBackedEnum('ForecastMinutely15Variable', extractOpenApiQueryEnum($forecast, 'minutely_15'), dirname(__DIR__).'/src/Enums/ForecastMinutely15Variable.php', true);

echo 'Generated forecast variable enums'.PHP_EOL;
