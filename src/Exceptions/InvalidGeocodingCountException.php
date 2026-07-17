<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Exceptions;

use InvalidArgumentException;

final class InvalidGeocodingCountException extends InvalidArgumentException implements OpenMeteoException {}
