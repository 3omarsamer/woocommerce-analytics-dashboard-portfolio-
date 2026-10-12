# Real challenges and resolutions

### Trashed orders inflated revenue and order counts

**Symptom:** M1 dashboard disagreed with Woo Analytics. **Investigation:** August comparison found 65 orders / EGP 301,744.95 against 19 / EGP 105,690; 46 `wc-trash` orders explained the order-count difference. **Change:** centralized exclusion policy using Woo configuration and filters, including auto-draft/trash. **Evidence:** clean M2 validation reconciled. Avoid assuming every site excludes identical custom statuses.

### AOV definition was ambiguous

**Problem:** gross-like numerator could disagree with intended Woo Analytics reporting. **Decision:** Net Sales / eligible Orders. **Validation:** AOV displayed consistently with selected qualifying orders; clean baseline inputs were verified.

### Category and brand reporting initially needed business-specific mapping

**Problem:** Woo taxonomy mixes product types, brands and navigational collections; titles are not reliable types. **Decision:** explicit product-ID overrides > approved taxonomy IDs > Unclassified; limited overlap rules; each line exactly one type bucket. **Validation:** category/brand/gender each reconciled to EGP 322,275.48 eligible product net revenue. **Limitation:** historical taxonomy is generally current taxonomy, not an immutable sale-time snapshot.

### GA4 instrumentation and environment gaps

**Problem:** custom AJAX Add to Cart did not emit the expected Woo `added_to_cart` frontend signal. **Investigation:** manually emitting it produced an Add to Cart event in Tag Assistant. **Solution:** separate compatibility snippet for GA4 Add to Cart; native Site Kit purchase separately investigated. **Limitation:** this snippet is not part of this analytics plugin's bundled source. STAGING lacked Site Kit frontend tags in one Stage C test; no frontend event results could be assumed from that environment.

### GA4 event counts versus sessions

**Problem:** raw `eventCount` does not equal number of sessions that experienced an event. **Decision:** funnel uses GA4 `sessions` metric filtered by exact eventName. **Validation:** Sep 24–25: 113 sessions; 10 add-to-cart sessions, 0 begin-checkout sessions, 5 purchase sessions; raw counts 17 add_to_cart and 7 purchase. Do not conflate Woo orders with GA4 purchases.

### Acquisition continuity across visits and checkout

**Problem:** browser campaign evidence must be preserved without changing orders or rewriting history. **Architecture:** independent Stage A rolling first-party cookie/Woo-session state and Stage B immutable order-time snapshot; analytics consumes order metadata. **Limitation:** Stage A/B are separate deployables; Paymob second retry/redirect edge case was not tested.

### Drill-down trust and UX

**Problem:** attractive charts without explainable order sets are hard to audit. **Decision:** use the same eligible order-set/bucket helpers for modal drill-downs; paginate authenticated results; add customer context and coupon breakdown without redefining metrics. **Status:** implemented in v0.5.x; no claim of a separate exhaustive penetration/performance test.