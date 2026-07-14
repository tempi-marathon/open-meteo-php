<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Requests\Climate;

use Saloon\Http\Response;
use TempiMarathon\OpenMeteo\Data\ClimateResponse;
use TempiMarathon\OpenMeteo\Data\ClimateResponseCollection;
use TempiMarathon\OpenMeteo\Enums\ClimateDailyVariable;
use TempiMarathon\OpenMeteo\Requests\AbstractCoordinateGetRequest;
use TempiMarathon\OpenMeteo\Support\JoinsQueryEnumValues;

use function Psl\Vec\values;

final class GetClimateRequest extends AbstractCoordinateGetRequest
{
    use JoinsQueryEnumValues;

    /** @var list<ClimateDailyVariable> */
    private array $daily = [];

    private ?bool $disableBiasCorrection = null;

    public function daily(ClimateDailyVariable ...$variables): static
    {
        return clone ($this, [
            'daily' => values($variables),
        ]);
    }

    public function disableBiasCorrection(bool $disableBiasCorrection = true): static
    {
        return clone ($this, [
            'disableBiasCorrection' => $disableBiasCorrection,
        ]);
    }

    protected function requiresDateRange(): bool
    {
        return true;
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
            'models',
        ];
    }

    public function resolveEndpoint(): string
    {
        return 'climate';
    }

    protected function responseClass(): string
    {
        return ClimateResponse::class;
    }

    protected function defaultQuery(): array
    {
        $query = $this->coordinateWeatherQuery();

        if ($this->daily !== []) {
            $query['daily'] = $this->joinEnumValues($this->daily);
        }

        if ($this->disableBiasCorrection !== null) {
            $query['disable_bias_correction'] = $this->disableBiasCorrection ? 'true' : 'false';
        }

        return $this->withApiKey($query);
    }

    public function createDtoCollectionFromResponse(Response $response): ClimateResponseCollection
    {
        /** @var array<string, mixed> $data */
        $data = $response->json();

        /** @var ClimateResponseCollection */
        return $this->createResponseCollectionFromPayload($data, ClimateResponse::class);
    }

    public function dtoCollection(): ClimateResponseCollection
    {
        return $this->createDtoCollectionFromResponse($this->send());
    }

    public function dto(): ClimateResponse
    {
        return $this->resolveDto(ClimateResponse::class);
    }
}
