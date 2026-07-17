<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Exceptions;

/**
 * Marker interface implemented by every exception thrown by this package.
 *
 * Consumers can catch {@see OpenMeteoException} to handle any SDK failure
 * without depending on the concrete SPL parent classes.
 */
interface OpenMeteoException {}
