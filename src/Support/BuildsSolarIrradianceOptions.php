<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

trait BuildsSolarIrradianceOptions
{
    private ?float $tilt = null;

    private ?float $azimuth = null;

    public function tilt(float $tilt): static
    {
        $clone = clone $this;
        $clone->tilt = $tilt;

        return $clone;
    }

    public function azimuth(float $azimuth): static
    {
        $clone = clone $this;
        $clone->azimuth = $azimuth;

        return $clone;
    }

    /**
     * @param  array<string, string>  $query
     * @return array<string, string>
     */
    protected function withSolarIrradianceQuery(array $query): array
    {
        if ($this->tilt !== null) {
            $query['tilt'] = (string) $this->tilt;
        }

        if ($this->azimuth !== null) {
            $query['azimuth'] = (string) $this->azimuth;
        }

        return $query;
    }
}
