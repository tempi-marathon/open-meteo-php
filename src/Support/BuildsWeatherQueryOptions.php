<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

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

    public function models(string ...$models): static
    {
        return clone ($this, [
            'models' => array_values($models),
        ]);
    }

    /**
     * @param  array<string, string>  $query
     * @return array<string, string>
     */
    protected function withWeatherQueryOptions(array $query): array
    {
        if ($this->temperatureUnit !== null) {
            $query['temperature_unit'] = $this->temperatureUnit->value;
        }

        if ($this->windSpeedUnit !== null) {
            $query['wind_speed_unit'] = $this->windSpeedUnit->value;
        }

        if ($this->precipitationUnit !== null) {
            $query['precipitation_unit'] = $this->precipitationUnit->value;
        }

        if ($this->timeFormat !== null) {
            $query['timeformat'] = $this->timeFormat->value;
        }

        if ($this->cellSelection !== null) {
            $query['cell_selection'] = $this->cellSelection->value;
        }

        if ($this->elevation !== null) {
            $query['elevation'] = (string) $this->elevation;
        }

        if ($this->models !== []) {
            $query['models'] = join($this->models, ',');
        }

        return $query;
    }
}
