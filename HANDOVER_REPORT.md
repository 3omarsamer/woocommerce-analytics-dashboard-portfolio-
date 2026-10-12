# Technical handover — WooCommerce Analytics Dashboard

**Artifact:** historical Azula Analytics v0.5.3, sanitized portfolio copy. **Date prepared:** 2026-10-12. **Scope:** source code, versioned archives, historical validation notes and development records available in this workspace. No access to the running server, live database or private credentials was used.

## Executive summary

A custom Shopify-inspired analytics experience was built following a move to WooCommerce. The core engineering goal was not to replicate Shopify's database but to present WooCommerce-derived order truth alongside GA4-derived traffic data with consistent eligibility, reconciled aggregations and explainable drill-downs. The project progressed through failed early KPI reconciliation, an approved six-report Woo baseline, GA4 connection/diagnostics, separate acquisition-attribution collectors, and later dashboard UX and merchandising reports through v0.5.3.

## Why custom reporting

The owner wanted one familiar decision dashboard rather than navigating separate WooCommerce and GA4 reports, with consistent global date controls, comparisons, customer behavior, payment methods, product-type/brand/gender performance and GA4 funnel visibility. Custom logic was required for this store's catalog taxonomy, status exclusions and attribution architecture. The original guardrails prohibited analytics from modifying orders, stock, checkout, payment, Site Kit or fulfillment.

## Implementation and milestone outcomes

Read `docs/DEVELOPMENT_HISTORY.md` for chronological details and `docs/FEATURES.md` for implemented versus untested status. The critical sequence was: v0.1.0 failed due to 46 trashed orders; v0.1.1 corrected eligibility/AOV; v0.2.0 six reports passed clean-period staging reconciliation; v0.3.0 connected GA4 read-only and confirmed raw 113-session baseline; v0.4.0 incorporated GA4 funnel/acquisition analysis with separate Stage A/B order attribution; v0.5.0–v0.5.3 added chart controls, order drill-downs, visitor metrics, best sellers/sizes and Brand × Category.

## Approved validation baseline

For Aug 1–Sep 25 2026, staging returned **63 orders, EGP 326,080.48 Total Sales, EGP 322,580.48 Net Sales, 51 customers, 25 returning customers (49.0196%), 3 discounted orders (EGP 1,783.50)**. Payment totals equaled Total Sales; category/brand/gender each equaled EGP 322,275.48 product net revenue. The different revenue denominators are deliberate. Sep 19–25 GA4 sessions totaled 113 (Sep 24 = 18; Sep 25 = 95). Sep 24–25 funnel was 113 sessions / 10 add-to-cart sessions / 0 begin-checkout sessions / 5 purchase sessions. These are dated, environment-specific tests, not ongoing guarantees.

## Architecture, safety and limitations

`docs/ARCHITECTURE.md` explains PHP/WordPress/HPOS, central status eligibility, analytics-only taxonomy mapping, GA4 service-account JWT/OAuth and caching, frontend charts and authenticated drill-downs. `docs/SECURITY.md` documents the public host-guard change and remaining store-specific IDs. Separate Stage A/B plugins are not included: a consumer installing only this repo will not reproduce the full original acquisition capture system. The plugin does not backfill old channel attribution. A checkout compatibility event script and live-presence heartbeat are instrumentations, not order/stock changes.

## Known problems and what was actually fixed

The most material bug was inflated M1 totals caused by trashed orders; root cause was verified and the central exclusion fix was followed by successful M2 reconciliation. Category ambiguity was addressed through approved mappings/overrides and explicit Unclassified buckets. GA4 add-to-cart had a separate AJAX/frontend-event compatibility issue; Tag Assistant demonstrated the missing event pathway, and a separate compatibility snippet was used. A Stage C staging test lacked Site Kit frontend tags, so no false claim of complete event coverage is made. GA4 funnel deliberately uses sessions-with-event, not eventCount. More details in `docs/CHALLENGES_AND_SOLUTIONS.md`.

## What is unfinished or not independently established

- The second Paymob retry/redirect attribution scenario was never tested.
- No historical GA4 reconstruction before reliable tracking started around Sep 24, 2026.
- Historical product taxonomy classification is not a sale-time taxonomy snapshot.
- Store-specific category/tag IDs and product overrides are not portable.
- No fresh public-copy end-to-end deployment, load/performance benchmark or formal security audit has been performed.
- The independent Stage A/Stage B plugins, GA4 add-to-cart compatibility snippet and Site Kit configuration are not bundled here.
- Stage A's live seven-day rolling-window code and current cookie expiry were observed; prior cookie-state transition was not captured before reloading, limiting proof of the exact historical expiry event.

## Source provenance

Original historical ZIP `azula-analytics-v0.5.3-LIVE.zip` SHA-256 `bad34e8f6059a1c269971978f01b97f30d5a1be28b9827ce5836c3e7baadc299`; source includes changelog, validation notes and v0.5.3 PHP/JS/CSS. Earlier milestone and rollback archives also available in the working materials, but intentionally excluded from the public package to reduce risk. The public source differs from the historical ZIP only by removing original embedded release notes and replacing original site hostname guards with a configurable host constant; see `SECURITY.md`. Recheck this claim with a diff before publishing.

## Suggested next acceptance gate

Review README claims, inspect the entire source for proprietary identifiers, adapt config for a sandbox host, run PHP lint and manual dashboard regression, replace any sensitive screenshots with synthetic ones, then explicitly approve publication. **No GitHub publication has been performed.**