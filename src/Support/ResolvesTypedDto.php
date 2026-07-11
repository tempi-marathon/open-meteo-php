<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Support;

trait ResolvesTypedDto
{
    /**
     * @template T of object
     *
     * @param  class-string<T>  $responseClass
     * @return T
     */
    protected function resolveDto(string $responseClass): object
    {
        $dto = $this->send()->dtoOrFail();

        if (! $dto instanceof $responseClass) {
            throw new \LogicException(sprintf('Expected %s DTO.', $responseClass));
        }

        return $dto;
    }
}
