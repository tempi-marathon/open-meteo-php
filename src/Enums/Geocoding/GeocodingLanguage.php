<?php

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums\Geocoding;

enum GeocodingLanguage: string
{
    case English = 'en';
    case German = 'de';
    case French = 'fr';
    case Spanish = 'es';
    case Italian = 'it';
    case Portuguese = 'pt';
    case Russian = 'ru';
    case Turkish = 'tr';
    case Hindi = 'hi';
}
