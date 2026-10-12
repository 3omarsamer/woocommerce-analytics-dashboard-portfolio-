# Feature inventory (v0.5.3 historical source)

| Capability | Status | Notes |
|---|---|---|
| Total Sales, Net Sales, Orders, AOV | Implemented; M1/M2 figures validated | Centralized eligibility; AOV net/orders |
| Returning Customer Rate / over time | Implemented; baseline validated | Customer drill-down added later |
| Sales by Payment Method | Implemented; reconciled | Totals equal eligible Total Sales in baseline |
| Top Categories | Implemented; reconciled | Top six card and full report in v0.5.3 |
| Discounts | Implemented; validated | Coupon breakdown expanded v0.5.0 |
| Sales by Brand / Gender | Implemented; reconciled | Brand top six/full report v0.5.3 |
| Date filters/comparisons | Implemented | Day/week/month grouping and chart style toggles v0.5.0 |
| GA4 sessions / timeline | Implemented; raw baseline checked | Reporting timezone Africa/Cairo in historical validation |
| GA4 Total Visitors / live visitors | Implemented | Selected-range totalUsers and first-party presence/GA4 realtime are distinct |
| GA4 funnel | Implemented; diagnostic baseline | Sessions with exact events, not eventCount |
| Sales by Channel | Implemented | Reads Stage B snapshots; older orders may be Legacy/Unattributed |
| Best Sellers | Implemented v0.5.2 | Parent-product grouping, stock readout and order drill-down |
| Best-Selling Sizes | Implemented v0.5.2 | Purchased pa_size, gender/category classification, footwear excluded for this report |
| Brand × Category | Implemented v0.5.3 | Read-only cross-tab |
| Orders drill-down modal | Implemented v0.5.0+ | Authenticated, paginated, Woo order links; customer column v0.5.2 |
| Per-user report-card order | Implemented v0.5.0 | UI preference, not a commerce-data change |
| Second Paymob retry/redirect | **Not tested** | Do not claim coverage |
| Historical acquisition backfill | **Not implemented by design** | No reconstructed attribution |
| Stage A / Stage B writers | **Separate plugins, not bundled** | Main plugin consumes snapshots |