# Installation and configuration — portfolio edition

1. Use an isolated WordPress + WooCommerce environment with WooCommerce Analytics lookup tables populated; do not deploy to a real store without regression testing.
2. Copy `plugin/azula-analytics/` into `wp-content/plugins/` (or zip only that directory and upload it).
3. In the environment's private `wp-config.php`, set `define('AZULA_ANALYTICS_ALLOWED_HOST', 'your-staging-host.example');`. The sanitized source deliberately does **not** auto-enable on arbitrary hosts. A `www` alias needs its own handling if applicable.
4. For GA4, create a Google Cloud service account with the minimum GA4 property read permission and keep its JSON **outside the webroot**. Set `AZULA_ANALYTICS_GA4_PROPERTY_ID` and `AZULA_ANALYTICS_GA4_CREDENTIALS` to your own values in private configuration. Never commit JSON keys.
5. Review `class-product-classifier.php`: category/brand term IDs, product overrides and gender tag IDs are store-specific; replace with your test store's mapping. Review `attribution_activation_ts()` in `class-woo-data.php`: it is a historical business boundary, not a universal default.
6. Note the Stage A and Stage B acquisition collectors were **separate plugins**, not included here. Channel reports can show Legacy/Unattributed for orders without Stage B snapshots.
7. Activate only in staging, confirm the admin dashboard and permissions, compare every KPI with WooCommerce Analytics for identical ranges, check GA4 unavailability handling, then review before any deployment.

**Important:** The sanitized copy is provided as source for review and portfolio demonstration, not a guaranteed installable drop-in across different stores. No independent public-build end-to-end test was performed.