<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Exceptions;

use UnexpectedValueException;

final class MalformedPayloadException extends UnexpectedValueException implements OpenMeteoException {}
