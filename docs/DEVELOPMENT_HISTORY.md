# Development history — reconstructed from release artifacts and recorded tests

## Initial design (September 2026)

Following a Shopify-to-WooCommerce migration, the owner wanted a familiar Shopify-style dashboard while retaining WooCommerce as the financial source of truth. Requirements included coherent global date/comparison controls, understandable KPI cards, drill-down reports, GA4 visibility and conservative operation. HPOS compatibility, no stock/order/checkout mutations and avoiding interference with Site Kit, payment and fulfillment integrations were hard constraints.

## Milestone 1 — shell and core KPIs (v0.1.0 → v0.1.1)

Implemented Shopify-inspired admin dashboard with Total Sales, Orders, AOV and date controls. **Initial validation failed:** the early implementation included trashed orders; August showed 65 orders / EGP 301,744.95 versus 19 orders / EGP 105,690 in the comparison. Investigation found **46 `wc-trash` orders** in the incorrect set. v0.1.1 centralized Woo Analytics status exclusions and changed AOV to Net Sales / qualifying Orders. This was an explicit failed-test-then-correction cycle, not a first-pass success.

## Milestone 2 — six Woo reports (v0.2.0)

Added Returning Customer Rate Over Time, Sales by Payment Method, Top Categories, Discounts, Sales by Brand and Sales by Gender. Category classification used taxonomy IDs plus approved explicit product overrides, not name heuristics. Clean-period staging validation **PASSED** (2026-08-01–2026-09-25): 63 orders; EGP 326,080.48 Total Sales; EGP 322,580.48 Net Sales; 51 customers; 25 returning; discounts EGP 1,783.50 on 3 orders; payment and product-revenue buckets reconciled. This milestone was frozen before GA4 work.

## Milestone 3 — GA4 (v0.3.0 STAGING)

Implemented server-side GA4 Data API access with a read-only service account, credential file outside webroot, configurable property, cached reports and graceful failure. Historical raw verification for 2026-09-19–25 returned **113 sessions**, including Sep 24 = 18 and Sep 25 = 95; earlier days = 0. This period was recognized as the beginning of reliable GA4 history, not evidence that prior visits were zero. Connection and raw-data stages passed; UI and production parity required their own validation.

## Milestone 4 — acquisition diagnostics and funnel (v0.4.0 family)

Enumerated GA4 events and acquisition dimensions. Stage A first-party browser attribution and Stage B order-snapshot writers were developed as **separate plugins**; analytics reads their results. Stage A used a 7-day inactivity policy; Stage B froze order attribution at creation via Woo/HPOS CRUD. Stage C used GA4 session-based event filters. Sep 24–25 diagnostic: sessions 113, add_to_cart sessions 10, begin_checkout sessions 0, purchase sessions 5; raw add_to_cart eventCount 17 and purchase eventCount 7. A staging environment lacked Site Kit frontend tags; this was an environment limitation rather than proof of funnel code failure. The begin_checkout event compatibility needed separate attention; never present the initial zero as an all-time stable conversion rate.

## v0.5.0 — UX and drill-down

Added Conversion Rate, enhanced timeseries axes/tooltips, Line/Bar switch, Day/Week/Month grouping and comparison series, read-only order modal, coupon breakdown and per-user card arrangement. Kept financial definitions, classifier and attribution boundaries frozen. The recorded release treated v0.4.0 as a rollback baseline.

## v0.5.1

Added first-party Live Visitors presence (~30-second heartbeat, ~90-second activity threshold), selected-range GA4 Total Visitors, Orders KPI drill-down, funnel percentages and returning-customer drill-downs.

## v0.5.2

Added Best Sellers, Best-Selling Sizes, customer details in order modal and customer-to-orders navigation. Built from a v0.5.1 rollback archive.

## v0.5.3

Added Top Categories and Sales by Brand compact top-six cards plus full reports; Best Sellers stock-by-size; size sold-quantity drill-down; Brand × Category. Historical archive SHA-256: `bad34e8f6059a1c269971978f01b97f30d5a1be28b9827ce5836c3e7baadc299`. A later WP-CLI inspection showed v0.5.3 active on LIVE. **This does not constitute a fresh end-to-end regression run of every v0.5.3 report.**

## Subsequent separate Stage A validation

Live source showed the 604800-second rolling inactivity window; an Incognito browser cookie inspection found a new capture timestamp and a seven-day future expiry. This supports the renewal logic, but the previous identity's original stored timestamp was not preserved for before/after comparison; therefore the precise old-session expiration transition is **not independently proven** by that screenshot alone.