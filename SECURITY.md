# Security Policy

If you discover any security related issues, please use [GitHub Security Advisories](https://github.com/tempi-marathon/open-meteo-php/security/advisories/new) instead of the public issue tracker.

## Handling API keys

API keys are only used for commercial Open-Meteo subscriptions; the free API does not require a key.

When a global API key is configured, the SDK automatically uses `customer-*.open-meteo.com` endpoints. Explicit `hosts` overrides take precedence.

- Store `OPENMETEO_API_KEY` in server-side environment variables or a secrets manager.
- **The API key is sent as an `apikey` query parameter** (this is how the Open-Meteo commercial API authenticates). Query strings are more likely than headers to be captured by proxy logs, browser history, or referrer headers. Ensure any logging or error-reporting layer that records outbound URLs redacts the `apikey` parameter, and never log raw request URLs at rest.
- Do not expose `debugUrl()` output to end users; it redacts `apikey` query parameters, but treat debug URLs as sensitive.
- Prefer `OpenMeteoConfig::configure()` (or, in Laravel, `config/openmeteo.php`) over untrusted `OPENMETEO_CONFIG_PATH` values.

## Host validation

To prevent requests (and any attached API key) from being redirected to an untrusted origin, host overrides are validated by trust level:

- **Trusted sources** (`OpenMeteoConfig::configure()`, a registered resolver, or Laravel's container config) may target any HTTPS host; loopback hosts may use plain HTTP.
- **Untrusted sources** (a config file loaded directly from disk via `OPENMETEO_CONFIG_PATH`) may only target `open-meteo.com` and its subdomains over HTTPS, or a loopback host. Any other value is ignored and the built-in default host is used instead.

`OPENMETEO_CONFIG_PATH` is additionally constrained to `.php` files inside the package root, and is resolved with `realpath()` to defeat traversal.
