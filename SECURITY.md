# Security Policy

If you discover any security related issues, please use [GitHub Security Advisories](https://github.com/tempi-marathon/open-meteo-php/security/advisories/new) instead of the public issue tracker.

## Handling API keys

- Store `OPENMETEO_API_KEY` in server-side environment variables or a secrets manager.
- Do not expose `debugUrl()` output to end users; it redacts `apikey` query parameters, but treat debug URLs as sensitive.
- Prefer `OpenMeteoConfig::configure()` in Laravel apps over untrusted `OPENMETEO_CONFIG_PATH` values.
