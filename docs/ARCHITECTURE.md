# Architecture

## Runtime

WordPress PHP plugin; WooCommerce/HPOS order APIs and analytics lookup data; MySQL through WordPress `$wpdb`; WordPress admin UI; vanilla JavaScript and CSS; GA4 Data API via server-side WordPress HTTP requests.

## Components (verified in v0.5.3 source)

| File | Responsibility |
|---|---|
| `azula-analytics.php` | Bootstraps dependencies and conditionally registers the plugin on an approved host with WooCommerce present |
| `class-order-eligibility.php` | Normalizes WooCommerce Analytics excluded statuses |
| `class-woo-data.php` | Woo financial/customer/product aggregation and drill-down query logic |
| `class-product-classifier.php` | Analytics-only category/brand/gender mapping |
| `class-ga4-data.php` | GA4 OAuth service-account JWT and Data API report requests, transients and errors |
| `class-date-range.php` | Selected/comparison period handling |
| `class-admin.php` | Authenticated admin endpoints, dashboard and reports |
| `class-presence.php`, `presence.js` | First-party live visitor presence heartbeat, separate from GA4 real-time |
| `class-checkout-compat.php`, `begin-checkout.js` | Compatibility instrumentation for begin_checkout event; does not control checkout |
| `dashboard.js`, `admin.css` | Interactive dashboard and visual styling |

## Data sources and separation of truth

WooCommerce is the source for orders, monetary amounts, line-item revenue and customer counts. GA4 supplies traffic/visitor/funnel metrics. **GA4 purchase sessions are not WooCommerce order counts.** Stage A (first-party browser attribution) and Stage B (immutable order snapshot) were independent plugins; the analytics plugin reads the Stage B order metadata when available. It must not rewrite past attribution or infer historical data.

## Order eligibility

The code merges `woocommerce_excluded_report_order_statuses`, `auto-draft`, `trash`, and the effective `woocommerce_analytics_excluded_order_statuses` filter, normalizing `wc-` prefixes. During the verified period, excluded statuses were auto-draft, trash, pending, failed, cancelled, checkout-draft; reportable were processing, on-hold, completed, refunded. The effective status list is installation-dependent.

## Metric definitions

- **AOV:** Net Sales / eligible Orders, not gross revenue / orders.
- **Returning Customer Rate:** returning eligible customers / eligible customers; validation 25/51 = 49.0196%.
- **Category/brand/gender:** classify each eligible sold line once; sum `wc_order_product_lookup.product_net_revenue`, not order Total Sales. Shipping/fees and other order-level differences are not silently forced into product revenue.
- **GA4 funnel:** sessions containing exact `add_to_cart`, `begin_checkout`, `purchase` event names; event counts are not the same metric.

## Classifier

`TYPE_OVERRIDES` takes precedence over approved product-category term ID mappings; specificity rules resolve limited overlaps (Sports Bras vs Tops, Socks vs Footwear/Accessories, Hoodies & Jackets vs Tops); ambiguous types become `Unclassified`. Brands use approved taxonomy term IDs; gender uses product tags. Product titles are not classification inputs. Historical taxonomy is generally **current taxonomy**, not a historical snapshot. All numeric IDs are installation-specific, not generic constants.

## GA4 security/performance

`class-ga4-data.php` reads a configured service-account JSON path outside the webroot, signs an RS256 JWT via OpenSSL, requests an OAuth token and calls `analyticsdata.googleapis.com`. Scope is `analytics.readonly`. Realtime report cache TTL is 45 seconds; standard report TTL is 600 seconds. HTTP errors produce unavailable responses instead of mutating Woo data. Credential file contents and access tokens are never part of the repository.

## Read-only boundaries

The financial/reporting components read existing data and do not change order status, stock, checkout or payment outcomes. Admin card-order preferences and transient caches are ancillary writes, and checkout compatibility and presence instrumentation emit events/heartbeats; therefore **read-only means no modification to commerce records**, not literally zero writes or browser effects.