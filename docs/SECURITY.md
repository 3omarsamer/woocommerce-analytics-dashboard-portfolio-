# Public-release security review

## Sanitization performed

- Removed the original production hostname from the plugin's bootstrap and checkout-compatibility guard. The public edition requires `AZULA_ANALYTICS_ALLOWED_HOST` in private configuration.
- Excluded operational production-release documents and local diagnostics, logs, credential JSON and customer/order exports from the public repository.
- Retained **configuration variable names** for GA4 credentials/property ID, but no real credentials or private key. The source reads service-account JSON from an externally configured file.
- No raw order records, emails, phone numbers, access tokens or SSH information are bundled.

## Configuration and business-specific identifiers

The source retains the original numeric product-category term mappings, product-ID overrides and brand names because they are part of the real implementation. These are **store-specific configuration**, not portable sample IDs. They may reveal catalog taxonomy design; review them before public release if that is sensitive. The original attribution activation timestamp is also historical implementation context and should be reviewed.

## Operational recommendations

Use least-privilege GA4 Viewer service account, JSON file outside the webroot, WordPress admin authorization/nonces, HTTPS, private secrets management and isolated staging tests. Do not commit `wp-config.php`, JSON keys, `.env`, backups, logs, real order exports or screenshots containing customer data. Review all external plugin dependencies and WooCommerce/WordPress compatibility before installation.

## Review limitations

This is a static source-level sanitization and pattern scan, **not** a formal security audit, vulnerability scan or production authorization. No customer database or running server was accessed to build the package. No claim is made that every third-party deployment context is safe.