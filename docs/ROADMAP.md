# Roadmap (not implemented in this public package)

1. Move hard-coded store-specific taxonomy IDs and product overrides to a validated admin mapping/configuration layer.
2. Add repeatable automated test fixtures for HPOS, refunds, partial refunds, coupon allocation and custom order statuses.
3. Add GA4 sandbox/mock contract tests and resilience tests for missing credentials, quotas and delayed data.
4. Document and test the separate Stage A/Stage B plugins as independently versioned repositories; verify the second Paymob retry/redirect path.
5. Create a public demo with synthetic orders, fake customers and masked screenshots; never publish actual customer data.
6. Complete a fresh public-build functional regression and independent security review before any third-party production use.

These are future work items, not shipped features.