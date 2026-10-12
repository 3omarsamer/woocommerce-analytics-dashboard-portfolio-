# Business context and reporting requirements

**Trigger:** The e-commerce business migrated its storefront from Shopify to WordPress/WooCommerce. WooCommerce's available reporting and the previous Shopify reporting experience differed, and the business wanted one easier, more actionable dashboard rather than relying on multiple administration and analytics screens.

**Catalog complexity:** The store carries multiple brands and a very large number of SKUs and variations. This created reporting needs that were not satisfied by a single aggregate sales chart. The team wanted views of brand, product type/category, purchased size, gender segment and brand-by-category combinations, alongside best sellers and customer/payment metrics.

**Traffic data:** Google Analytics 4 data, accessed using a Google Cloud service account and the GA4 Data API, adds sessions, visitors and conversion-funnel context to WooCommerce order and revenue reporting. The first-party live visitor signal is a separate component and must not be confused with GA4 realtime reporting.

**Project design decisions:**
- A Shopify-inspired admin presentation, tailored for the WooCommerce store's actual taxonomy and workflows.
- Store-specific explicit product overrides and category classification precedence, with an Unclassified fallback, to prevent sales from being double-counted across overlapping collections.
- WooCommerce as source of truth for orders/revenue; GA4 for visit and session metrics.
- Read-oriented financial reporting; no modifications to checkout, inventory, payment, fulfillment or order status.
- Auditable drill-downs, stage-gated reconciliation and conservative claims about historical attribution.

**Known limitations:** Some brand/category mappings use historical store taxonomy IDs; public source is a portfolio specimen and requires configuration and testing before reuse. Historical screenshot values represent a fixed captured reporting period, not an audited revenue claim or current statistics.