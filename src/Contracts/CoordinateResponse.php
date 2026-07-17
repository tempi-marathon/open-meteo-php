<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Contracts;

use TempiMarathon\OpenMeteo\Data\CoordinateMetadata;

/**
 * Contract for coordinate-anchored API responses.
 *
 * Implementations expose these as real (readonly) properties; they are declared
 * here as read-only virtual properties so the floor stays at PHP 8.3, which does
 * not support interface property hooks.
 *
 * @property-read float $latitude
 * @property-read float $longitude
 * @property-read string $timezone
 * @property-read CoordinateMetadata $metadata
 */
interface CoordinateResponse {}
