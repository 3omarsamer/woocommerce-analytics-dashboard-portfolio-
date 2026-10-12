# Testing and evidence

## Methodology

1. Use identical date ranges in Woo Analytics and the custom dashboard.
2. Compare eligible order IDs/statuses first; only then reconcile sums and customer counts.
3. Reconcile payment sales against Total Sales and product-type/brand/gender buckets against **product net revenue**, not order Total Sales.
4. Inspect raw GA4 Data API output independently of UI presentation; preserve reporting timezone.
5. Validate on staging first and preserve versioned rollback artifacts. Treat frontend tagging availability as environment-specific.
6. Do not write to orders, stock or checkout for analytics verification.

## Confirmed clean period (STAGING, M2 v0.2.0)

| Measure | Result |
|---|---:|
| Period | 2026-08-01 to 2026-09-25 |
| Eligible orders | 63 |
| Total Sales | EGP 326,080.48 |
| Net Sales | EGP 322,580.48 |
| Customers | 51 |
| Returning customers | 25 |
| Returning Customer Rate | 49.0196% |
| Payment-method sales sum | EGP 326,080.48 |
| Category / Brand / Gender revenue sum (each) | EGP 322,275.48 |
| Discounted orders | 3 |
| Discounts | EGP 1,783.50 |

**Status exclusions effective at test time:** auto-draft, trash, pending, failed, cancelled, checkout-draft. Included: processing, on-hold, completed, refunded. No checkout-draft, on-hold or refunded rows appeared in this clean period; those status paths were therefore not all exercised by this baseline.

## GA4 evidence

- Sep 19–25, 2026: 113 sessions, with Sep 24 = 18, Sep 25 = 95; earlier dates in that period returned zero. GA4 reliable coverage began around Sep 24.
- Sep 24–25 session funnel: 113 sessions, 10 add-to-cart sessions, 0 begin-checkout sessions, 5 purchase sessions. Raw eventCount: 17 add_to_cart, 7 purchase.
- GA4 reporting timezone for validation: Africa/Cairo.

## Evidence boundaries

- This repository was inspected for hard-coded credentials and sensitive endpoints and underwent local PHP syntax checking **where PHP CLI is available**; see `SECURITY.md` and `HANDOVER_REPORT.md` for the scope.
- v0.5.3 was observed active on LIVE through WP-CLI; no new end-to-end live test is claimed by this documentation.
- Second Paymob retry/redirect was not tested.
- The public edition's host-binding change is not separately deployed or functionally tested.
- Real mail delivery, ad-platform event matching and other external systems are outside this plugin's acceptance criteria.