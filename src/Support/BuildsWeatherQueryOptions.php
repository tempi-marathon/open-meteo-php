<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

use BackedEnum;
use TempiMarathon\OpenMeteo\Enums\CellSelection;
use TempiMarathon\OpenMeteo\Enums\PrecipitationUnit;
use TempiMarathon\OpenMeteo\Enums\TemperatureUnit;
use TempiMarathon\OpenMeteo\Enums\TimeFormat;
use TempiMarathon\OpenMeteo\Enums\WindSpeedUnit;

use function Psl\Str\join;

trait BuildsWeatherQueryOptions
{
    private ?TemperatureUnit $temperatureUnit = null;

    private ?WindSpeedUnit $windSpeedUnit = null;

    private ?PrecipitationUnit $precipitationUnit = null;

    private ?TimeFormat $timeFormat = null;

    private ?CellSelection $cellSelection = null;

    private ?float $elevation = null;

    /** @var list<string> */
    private array $models = [];

    public function temperatureUnit(TemperatureUnit $unit): static
    {
        return clone ($this, [
            'temperatureUnit' => $unit,
        ]);
    }

    public function windSpeedUnit(WindSpeedUnit $unit): static
    {
        return clone ($this, [
            'windSpeedUnit' => $unit,
        ]);
    }

    public function precipitationUnit(PrecipitationUnit $unit): static
    {
        return clone ($this, [
            'precipitationUnit' => $unit,
        ]);
    }

    public function timeFormat(TimeFormat $format): static
    {
        return clone ($this, [
            'timeFormat' => $format,
        ]);
    }

    public function cellSelection(CellSelection $selection): static
    {
        return clone ($this, [
            'cellSelection' => $selection,
        ]);
    }

    public function elevation(float $elevation): static
    {
        return clone ($this, [
            'elevation' => $elevation,
        ]);
    }

    public function models(BackedEnum|string ...$models): static
    {
        return clone ($this, [
            'models' => array_map(
                static fn (BackedEnum|string $model): string => $model instanceof BackedEnum ? (string) $model->value : $model,
                array_values($models),
            ),
        ]);
    }

    /**
     * @return list<string>
     */
    protected function weatherQueryOptionKeys(): array
    {
        return [
            'temperature_unit',
            'wind_speed_unit',
            'precipitation_unit',
            'timeformat',
            'cell_selection',
            'elevation',
            'models',
        ];
    }

    /**
     * @param  array<string, string>  $query
     * @return array<string, string>
     */
    protected function withWeatherQueryOptions(array $query): array
    {
        $allowed = array_flip($this->weatherQueryOptionKeys());

        if (isset($allowed['temperature_unit']) && $this->temperatureUnit !== null) {
            $query['temperature_unit'] = $this->temperatureUnit->value;
        }

        if (isset($allowed['wind_speed_unit']) && $this->windSpeedUnit !== null) {
            $query['wind_speed_unit'] = $this->windSpeedUnit->value;
        }

        if (isset($allowed['precipitation_unit']) && $this->precipitationUnit !== null) {
            $query['precipitation_unit'] = $this->precipitationUnit->value;
        }

        if (isset($allowed['timeformat']) && $this->timeFormat !== null) {
            $query['timeformat'] = $this->timeFormat->value;
        }

        if (isset($allowed['cell_selection']) && $this->cellSelection !== null) {
            $query['cell_selection'] = $this->cellSelection->value;
        }

        if (isset($allowed['elevation']) && $this->elevation !== null) {
            $query['elevation'] = (string) $this->elevation;
        }

        if (isset($allowed['models']) && $this->models !== []) {
            $query['models'] = join($this->models, ',');
        }

        return $query;
    }
}
