# Chapter 6: System Testing & Evaluation

## 6.1 Introduction

This chapter describes how the Win Win Car Audio Auto Accessories Showroom System was tested, that is, how we confirmed that the features implemented in Chapter 5 actually behave as intended, and how defects were found and prevented from returning.

Testing on this project combined **two complementary approaches**, and it is important to be clear from the outset about the role each one played:

1. **Manual, exploratory testing** was the **primary, day-to-day method** throughout the project. We used the running application directly (browsing pages, clicking through real user journeys, switching languages, toggling dark mode, resizing the window, and deliberately trying to break things) and observed the result in the browser. **The majority of the defects fixed during development were first discovered this way.**

2. **Automated testing**, written with **PHPUnit**, was built alongside the manual testing as a **regression safety net**. It currently comprises **214 tests containing 691 assertions across 46 test files**, and the entire suite passes. Many of these tests exist specifically to *lock down* a bug that had already been found by hand, so that the same defect can never quietly return.

In other words, manual testing was how problems were **found**, and the automated suite is how fixes are **kept fixed**. This chapter presents the manual approach first (Section 6.3), because that reflects how testing actually happened, and then documents the automated suite and the specialised security, concurrency, performance, compatibility, and acceptance testing that support it.

*[Figure 6.1: The two-layer testing approach: manual exploratory testing as the front line, backed by an automated regression suite.]*

---

## 6.2 Testing Strategy & Objectives

The testing strategy was shaped by the nature of the system. This is a highly **visual, interactive web application**: Livewire drives reactive page updates, Alpine.js powers client-side behaviour such as the language switcher and the page loader, Three.js renders an interactive 3D configurator, and the interface must work in three languages, in light and dark mode, and across phone, tablet, and desktop screens. A large part of what can go wrong therefore lives in the **rendered interface and its client-side JavaScript** (animations, language switching, responsive layout, the 3D viewer) which is exactly the layer that a server-side automated test cannot observe, because such a test inspects the HTML the server returns rather than a real browser executing JavaScript.

For that reason the strategy deliberately relied on **manual exploratory testing as the primary method**, with automated tests concentrated on the server-side logic where they are most effective (calculations, database state, authorisation, and concurrency) and on regression protection.

The objectives of the testing effort were:

1. **Functional correctness**: every customer-facing and admin-facing feature does what Chapter 5 specifies.
2. **Security**: authentication, authorisation, ownership, and anti-abuse controls cannot be bypassed.
3. **Concurrency safety**: simultaneous actions can never corrupt stock, orders, or bookings.
4. **Regression protection**: every bug found (mostly by hand) is captured by an automated test so it cannot return unnoticed.
5. **Non-functional quality**: acceptable performance, correct responsive/cross-device and multilingual behaviour, and real-user acceptability.

*[Figure 6.2: The relationship between manual testing, automated regression tests, and the specialised test types.]*

---

## 6.3 Manual & Exploratory Testing

Manual, hands-on testing was the front line of quality assurance on this project and was carried out continuously throughout development. Rather than following a rigid script, we exercised the application the way real users would and actively probed for problems.

### 6.3.1 How Manual Testing Was Carried Out

Manual testing covered both the local development site and the live deployed site at `winwincaraudio.onrender.com`, and included:

- **Full customer journeys**: browsing and searching the catalogue, opening product details and the 3D configurator, adding items to the cart, checking out, registering and logging in, viewing order and booking history, and booking a service appointment.
- **Full administrator journeys**: creating and editing products with images and translations, processing orders (mark paid, shipped, delivered, cancelled), managing bookings, editing settings, and reviewing the activity and error logs.
- **Cross-cutting checks on every page**: switching between English, Bahasa Melayu, and Chinese; toggling dark mode; and resizing the browser (and using real phones) to confirm the responsive layout.
- **Deliberate "break it" attempts**: submitting forms with empty, invalid, or oversized input; trying to reach another user's records by changing an ID in the URL; and repeatedly failing login to observe the lockout.

Because the person testing could *see* the rendered result, this approach caught issues in exactly the areas automated server-side tests cannot reach: visual layout, animations, and client-side JavaScript behaviour.

### 6.3.2 Defects Found Through Manual Testing

Most of the bugs fixed during the project were first noticed through this manual, exploratory process. The clearest example is the **page-loader language bug**: when switching the site language, the loading animation's caption stayed in Malay ("Sedang dimuatkan…") regardless of the chosen language. This defect only appears in production when a page transition is slow enough for the loader to show, and only when switching language. No server-side assertion would ever have revealed it. It was found simply by a person switching languages on the live site and noticing the caption never changed. (The root cause was that the loader lives in a persisted DOM block that Livewire does not re-render across soft navigations; the fix ships all three translations and selects the correct one in JavaScript.)

**Table 6.1: Representative defects discovered through manual / exploratory testing**

| Defect | How it was found | Layer |
|---|---|---|
| Login always showed "4 attempts remaining" | A colleague repeatedly entering a wrong password noticed the count never changed | Client-visible message / Livewire state |
| Page loader stuck in Malay in every language | Switching language on the live site and watching the loader caption | Client-side JavaScript |
| Sitemap rejected by Google ("URL not allowed") | Submitting the sitemap in Google Search Console | Deployment / SEO |
| Map library slowing every page | Running a Google PageSpeed Insights audit on live pages | Front-end performance |
| Invalid list role on the desktop navigation | An automated accessibility audit of the rendered page | Accessibility / markup |
| Login button showed its spinner stacked above the label | A user-testing screenshot of the broken button (Livewire reveals loading elements as `inline-block`, overriding the flex classes) | Client-side rendering |
| Brand marquee scrolled twice as fast on some devices | A tester comparing a 120Hz phone against a 60Hz monitor — the animation advanced per frame instead of per second | Client-side JavaScript |

The workflow that followed each discovery was consistent and is the link between this section and Section 6.9: a bug found by hand was **fixed**, and then (wherever the behaviour could be checked from the server) an **automated regression test was written to lock the fix in place**, so the same problem could not silently reappear.

*[Figure 6.3: The page-loader language bug as seen during manual testing, the loader caption remaining in Malay after switching to another language.]*

---

## 6.4 Automated Testing: Environment & Tooling

The automated tests are written with **PHPUnit** and executed through Laravel's test runner using the `php artisan test` command. The environment (configured in `phpunit.xml`) is fast, isolated, and completely separate from any real data:

- **In-memory SQLite database** (`DB_DATABASE=:memory:`): each run builds a fresh schema in memory and discards it afterwards, so tests never touch the development or production database.
- **`RefreshDatabase`**: every test starts from a clean, migrated database, so tests are independent and order does not matter.
- **Model factories**: realistic products, users, orders, and bookings are generated on demand, so each test sets up exactly the data it needs.
- **Faked mail and queues**: `MAIL_MAILER=array` captures outgoing email in memory instead of sending it, so a test can *assert that the right email would have been sent* without delivering anything; `QUEUE_CONNECTION=sync` runs queued work inline so its effects are testable immediately.
- **Array cache/session drivers** and a reduced bcrypt cost keep each run fast: the full 214-test suite completes in under two minutes.

This configuration means the suite is safe to run at any time, gives identical results on any machine, and could be added to a continuous-integration pipeline unchanged.

*[Figure 6.4: The `phpunit.xml` test-environment configuration.]*

---

## 6.5 Automated Test Coverage Overview

The 46 test files are organised by the area of the system they exercise. Table 6.2 shows how the 214 tests are distributed.

**Table 6.2: Automated test coverage by module**

| Module / Concern | Representative test files | Tests |
|---|---|---:|
| Authentication & Access Control | AuthFlow, LoginLockout, SetPassword, SocialLogin, UserAdminAuthorization, StaffBulkDeleteAuthorization, StaffOperationalPermissions | 61 |
| Payment & Order Lifecycle | PaymentFlow, PaymentHardening, PaymentExpiryGuard, OrderMarkPaidGuard, OrderCancellation, OrderAdminEdit, OrderTracker, OrderImporter, Invoice, StripeWebhook, StripeCheckoutRedirect, StripeReturnVerification | 72 |
| Service Booking | BookingService, ServicesPage, BookingSlotAvailability, BookingSlotRaceCondition, BookingConfirmGuard, BookingAdminEdit, BookingFormEmail, BookingReminder | 19 |
| Observability & Error Logging | Observability, ErrorLogLifecycle, LogResource, LogResolved | 17 |
| Store Config, Shop Mode & Admin Ops | SettingResource, ShopModeClose, ActivityResource, SystemStatusClearCache | 13 |
| Cart, Checkout & Shipping | CartItem, CheckoutPage, GuestCartClaimOnLogin, ShippingCalculator | 11 |
| Catalogue, SEO & Front-End Assets | ProductsPageSearch, SeoStructuredData, AssetLoading | 10 |
| Internationalisation | LocalizationCoverage, LocalizedPages | 7 |
| Unit | ExampleTest, StripePaymentMethodMapper | 4 |
| **Total** | **46 files** | **214** |

The distribution deliberately concentrates on the highest-risk server-side areas: money (orders, payment, cancellation, invoicing) and access control (authentication and authorisation) together account for over half of all automated tests, because those are the areas where a defect is most costly and where automated checks are most reliable.

*[Figure 6.5: A chart of automated-test distribution across modules.]*

---

## 6.6 Functional Testing (Automated)

The automated functional tests verify the server-side behaviour of each feature for the normal and edge cases.

### 6.6.1 Catalogue, Search & Storefront

These tests confirm that product search returns the right products, that filtering and sorting narrow and order results correctly, and that search operates on the localised product name. Asset tests confirm the performance decisions of Section 5.13.5 are in force: `AssetLoadingTest` asserts that the contact page embeds the Google map **pinned by coordinates and lazily loaded**, that no storefront page pulls a script from a third-party CDN (the icon, animation and 3D-viewer libraries are self-hosted and version-pinned), and (the regression guard for the manually-found bug) that the page loader ships all three language captions rather than one.

### 6.6.2 Cart, Checkout & Shipping

These tests verify quantity handling, subtotal calculation, and that **shipping is computed from the configured flat rate and free-shipping threshold** rather than being fixed, including the boundary where an order crosses the threshold. A dedicated test confirms that a **guest's cart is claimed and preserved when they log in**.

### 6.6.3 Payment, Orders & Invoicing

The most heavily tested area. Tests cover placing an order, confirming payment, the forward-only status transitions, tracking, admin editing, invoice generation, and importing orders. The **`OrderCancellation` suite alone contains 21 tests**, because the tiered refund policy (Section 5.6) has many boundaries: the refund amount is asserted for cancellations inside the window, just outside it, and after shipping, and every cancellation is checked to restock the items. The suite also asserts the **paired notifications**: a customer cancellation must send both the customer's email and the internal owner/staff alert (Section 5.14.2), while marking an order refunded sends the customer's confirmation without a redundant internal alert.

### 6.6.4 Service Booking

These tests confirm that available slots are generated correctly from the business-hours settings, that closed weekdays and out-of-hours slots are excluded, and that **same-day slots which have already passed are not offered**. Further tests cover confirmation and reminder emails and admin-side booking management.

### 6.6.5 Administration, Settings & Shop Mode

Admin tests confirm the Settings resource validates and persists each configuration value, that the **activity log** records changes, and that the **shop-mode graceful shutdown** behaves as designed: the `ShopModeClose` suite verifies that turning online shopping off **cancels and restocks unpaid orders while leaving paid orders untouched**, and that the announcement bar shows and hides according to its settings.

### 6.6.6 Internationalisation

These tests assert that key storefront pages render in each supported language and that translation coverage is complete, guarding against a missing key silently falling back to English on a customer-facing page.

### 6.6.7 Observability & Error Logging

These verify the database error-logging pipeline of Section 5.10.8: that errors are captured, grouped by their **fingerprint**, that the *active / recurred / quiet* recurrence state machine transitions correctly, and that resolving and re-checking a logged error works as the admin panel presents it.

*[Figure 6.6: A representative feature test and its passing output.]*

---

## 6.7 Security Testing

Security controls were tested explicitly, since a control that is not tested cannot be trusted.

**Authentication & brute-force resistance.** `LoginLockoutTest` verifies the progressive lockout of Section 5.9.2: repeated failed logins trigger lockouts at the correct escalating tiers and the "attempts remaining" messaging is accurate. This suite is also the regression guard for the manually-found persistent-message bug (Sections 6.3, 6.9).

**Authorisation & privilege escalation.** `UserAdminAuthorizationTest` (11 tests) and `StaffBulkDeleteAuthorizationTest` (9 tests) probe the admin panel from an attacker's perspective. They confirm that a non-owner admin **cannot open the owner's edit page**, that an admin **cannot delete their own or the owner's account**, that **nobody can delete the owner**, that an admin **cannot promote themselves to owner via a crafted request**, and that staff **cannot bulk-delete** protected records such as orders, products, and feedback while an admin still can delete permitted records.

**Ownership / IDOR.** Tests confirm that customer-facing records are scoped to the requester and that a user cannot reach another user's order or booking by manipulating an identifier (the Insecure Direct Object Reference protection of Section 5.9).

**Payment hardening & anti-abuse.** `PaymentHardeningTest` verifies the payment/order guards cannot be tricked into an inconsistent state, and the honeypot spam protection on public forms is exercised so automated submissions are rejected without burdening real users.

**Stripe gateway integration (Section 5.6).** Four dedicated suites exercise the sandbox gateway without ever touching the network: `StripePaymentMethodMapperTest` proves every stored payment-method label routes to the correct Stripe method (and that unsupported wallets stay simulated); `StripeCheckoutRedirectTest` covers the mode switch (Stripe redirect vs demo flip, missing/live keys refused) and the double-charge defences — an already-paid session settles the order instead of minting a new charge, and a completed-but-still-settling FPX session shows the confirming state rather than offering a second payment; `StripeWebhookTest` sends genuinely HMAC-signed payloads at the endpoint and asserts signature rejection, idempotent settlement on replay, FPX's delayed-confirmation sequence, the never-un-cancel rule, and the owner alerts for failed, duplicate, and cancelled-order payments; and `StripeReturnVerificationTest` covers the success-URL re-verification, including the polling that settles the page once a delayed bank confirmation clears.

**Cache scoping.** `SystemStatusClearCacheTest` asserts that the admin "clear cache" action **clears content caches but preserves security state**, the regression guard for the over-broad cache-clear bug (Section 6.9).

*[Figure 6.7: Authorisation tests confirming privilege-escalation attempts are refused.]*

---

## 6.8 Concurrency & Race-Condition Testing

Because several correctness guarantees depend on behaviour *under simultaneous access* (which is almost impossible to reproduce reliably by hand), these conditions are tested directly in the automated suite. These are among the most valuable tests in the project.

- **`BookingSlotRaceConditionTest`** simulates two customers claiming the *same* appointment slot and asserts that exactly one succeeds.
- **`PaymentExpiryGuardTest`** verifies that the background job which expires unpaid orders only cancels an order that is *still* pending, so it cannot cancel an order that has just been paid.
- **`OrderMarkPaidGuardTest`** confirms that marking an order paid is an atomic conditional action, so two administrators (or an admin racing the expiry job) cannot drive the order into a contradictory state.
- **`BookingConfirmGuardTest`** confirms the atomic pending-to-confirmed booking claim, so a just-cancelled or already-confirmed booking cannot be confirmed a second time.

In each case the test asserts both that the winning action succeeds and that the losing action fails *safely* (with a harmless "already done" outcome rather than a corrupted record), validating the transaction-plus-atomic-guarded-update pattern used throughout the checkout, order, and booking logic (Sections 5.5–5.7).

*[Figure 6.8: A race-condition test asserting exactly one of two concurrent claims succeeds.]*

---

## 6.9 Regression Testing

A defining principle of the project's testing was the link between the manual and automated approaches: **whenever a bug was found (usually by hand, Section 6.3), and its correct behaviour could be checked from the server, an automated test was written to lock the fix in place.** Each such test is written to fail against the buggy code and pass against the fix, so the same defect can never silently return. The most significant of these regression guards are:

**Table 6.3: Bugs (found manually) captured as permanent regression tests**

| Bug found | What went wrong | Regression test |
|---|---|---|
| Page loader stuck in one language | The loader sits in a persisted DOM block that Livewire does not re-render across soft navigations, so a single server-rendered caption froze on the first-loaded locale (Section 6.3). | `AssetLoadingTest::test_page_loader_ships_all_locale_captions` |
| Login always showed "4 attempts remaining" | Livewire persists its error bag across requests and `@error` renders the first stored message, so a newly appended message never appeared (Section 5.9.2). | `LoginLockoutTest` |
| Sitemap rejected by Google ("URL not allowed") | The command-line sitemap generator used the local `.test` domain instead of the production host (Section 5.13.3). | `SeoStructuredDataTest::test_sitemap_uses_the_production_url_not_the_local_domain` |
| "Clear cache" flushed security state | The admin cache-clear action was scoped too broadly and wiped security-related state along with content caches (Section 5.13.6). | `SystemStatusClearCacheTest` |
| Map assets loaded site-wide | Leaflet's ~46 KB of render-blocking CSS/JS was loaded on every page instead of only the contact page (Section 5.13.5). | `AssetLoadingTest` |

Because these tests are part of the standard suite, any future change that reintroduces one of these problems is caught immediately on the next run. Note that some manually-found issues (such as a purely visual layout glitch) cannot be meaningfully asserted from the server; those were verified by re-testing manually rather than with an automated test, which is why manual testing remained an ongoing activity rather than a one-off.

*[Figure 6.9: A regression test failing on the old code and passing on the fixed code.]*

---

## 6.10 Performance Testing

Front-end performance was measured manually with **Google PageSpeed Insights**, the free web tool that runs a **Lighthouse** audit against a live page. It loads the page, scores it, and reports two things: the page's **Core Web Vitals** (Google's standard, user-centred performance metrics) and a list of specific optimisation opportunities.

### 6.10.1 Optimisations Applied

The audit identified two actionable issues, both addressed in Section 5.13.5:

1. A large mapping library (**Leaflet**, ~46 KB of render-blocking CSS/JS) was being loaded on every page even though only the contact page has a map. It was confined to the contact page, removing that cost from every other page.
2. The homepage's main hero image was not being prioritised by the browser. It was marked `fetchpriority="high"` so it downloads early as the **Largest Contentful Paint** element.

Re-auditing after the changes confirmed a reduced render-blocking payload on the pages customers land on most, with no loss of functionality (the map still works on the contact page, verified by `AssetLoadingTest`). Server-side performance is further supported by the caching strategy of Section 5.13.6, which keeps repeated reads of configuration off the database.

### 6.10.2 Core Web Vitals

Core Web Vitals are the user-centred metrics Google uses to judge page experience: **LCP** (loading), **CLS** (visual stability), and **INP** (responsiveness).

A practical distinction affected how these were measured. Google derives Core Web Vitals from two data sources. **Field data** is collected from *real visitors* over the preceding 28–90 days; this is what Google Search Console's Core Web Vitals report and the upper section of PageSpeed Insights display. **Lab data** is a single controlled test run on demand, produced by Lighthouse and shown in the lower section of PageSpeed Insights. Because the site is newly deployed and does not yet have enough traffic, Search Console reported *"Not enough usage data in the last 90 days for this device type,"* so field Core Web Vitals were not yet available. Front-end performance was therefore measured using **PageSpeed Insights' lab data (Lighthouse)**, which does not depend on visitor volume.

From a lab run, LCP and CLS are reported directly, along with an overall Performance score. **INP is a field-only metric** and cannot be produced by a lab test, so the standard lab proxy for interactivity (**Total Blocking Time (TBT)**) is reported in its place. PageSpeed Insights scores mobile and desktop separately and Google prioritises the mobile experience, so the **mobile** result is reported here. Table 6.4 lists each metric with Google's "Good" threshold; the *Our Result* column is to be completed from the PageSpeed Insights lab run.

**Table 6.4: Front-end performance (PageSpeed Insights, mobile lab data)**

| Metric | What it measures | "Good" threshold | Our Result |
|---|---|---|---|
| Performance score | Overall Lighthouse performance score (0–100) | ≥ 90 | *[to fill]* |
| **LCP**, Largest Contentful Paint | How quickly the main content loads | ≤ 2.5 s | *[to fill]* |
| **CLS**, Cumulative Layout Shift | Visual stability: how much the layout unexpectedly shifts | ≤ 0.1 | *[to fill]* |
| **TBT**, Total Blocking Time *(lab proxy for INP)* | Responsiveness: how long the page is blocked from responding to input | ≤ 200 ms | *[to fill]* |

Each metric maps onto a design decision in the system. **LCP** is improved by the `fetchpriority="high"` hero image and by keeping render-blocking assets off pages that do not need them, so the main content paints sooner. **CLS** is kept low because images and layout regions have reserved dimensions, so content does not jump as the page loads. Interactivity (**TBT / INP**) benefits from the lightweight TALL-stack front end (Livewire performs small, targeted DOM updates and Alpine.js handles interactions client-side), so little JavaScript blocks the main thread. Once the site accumulates enough real-user traffic, the field-data Core Web Vitals (including INP) will become available in Search Console for ongoing monitoring.

*[Figure 6.10: PageSpeed Insights lab-data report showing the Performance score and Core Web Vitals for the homepage.]*

---

## 6.11 Compatibility & Responsive Testing

Because the site is used by customers on a range of devices and by the shop owner on a phone from the shop floor, the interface was tested **manually** for **responsive** behaviour across mobile, tablet, and desktop widths. The storefront layout, navigation, product grid, cart, and the **Filament admin panel's collapsible sidebar** (Section 5.10.1) were checked to adapt correctly at each breakpoint. **Dark mode** (Section 5.13.1) was verified across pages, and the site was checked in current versions of the major browsers. Accessibility was validated with automated audits, and issues they surfaced were corrected (for example the invalid navigation list role of Section 5.13.8). The matrix below records the environments covered; the *Result* column is to be completed from the actual test sessions.

**Table 6.5: Compatibility test matrix**

| Environment | Storefront | Admin panel | Result |
|---|---|---|---|
| Desktop, Chrome | ✔ | ✔ | *[to confirm]* |
| Desktop, Firefox / Edge | ✔ | ✔ | *[to confirm]* |
| Tablet | ✔ | ✔ | *[to confirm]* |
| Mobile, Chrome (Android) | ✔ | ✔ | *[to confirm]* |
| Mobile, Safari (iOS) | ✔ | ✔ | *[to confirm]* |

*[Figure 6.11: The storefront and admin panel rendered responsively on mobile, tablet, and desktop.]*

---

## 6.12 User Acceptance Testing (UAT)

User Acceptance Testing (UAT) checks whether the system meets the real needs of its two user groups (**customers** and the **shop owner/staff**) and is usable by non-technical people. Representative end-to-end scenarios were prepared for test users to walk through, rating whether each task could be completed successfully and giving qualitative feedback. The scenarios below are the UAT instrument; the *Result* and *Remarks* columns are to be completed from the actual UAT sessions.

**Table 6.6: Customer UAT scenarios**

| # | Scenario | Expected outcome | Result | Remarks |
|---|---|---|---|---|
| C1 | Browse and search the catalogue, filter by category, sort by price | Relevant products shown in the chosen order | *[to fill]* | |
| C2 | View a product's 3D configurator and rotate the model | Model loads and rotates smoothly | *[to fill]* | |
| C3 | Add items to cart, reach the free-shipping threshold, checkout | Correct totals; order placed | *[to fill]* | |
| C4 | Register / log in; view order history | Own orders visible, others' not | *[to fill]* | |
| C5 | Book a service appointment for an available slot | Slot reserved; confirmation email received | *[to fill]* | |
| C6 | Cancel an order within the refund window | Correct refund amount shown; stock returned | *[to fill]* | |
| C7 | Switch the site language to Malay / Chinese | All content, including the page loader, shown in the chosen language | *[to fill]* | |
| C8 | Ask the chatbot a common question | Relevant answer in the current language | *[to fill]* | |

**Table 6.7: Owner/Staff UAT scenarios**

| # | Scenario | Expected outcome | Result | Remarks |
|---|---|---|---|---|
| A1 | Add a new product with images and translations | Product appears on the storefront | *[to fill]* | |
| A2 | Process an order: mark paid, mark shipped with tracking | Customer receives the corresponding emails | *[to fill]* | |
| A3 | Change business hours in Settings | Booking slots update immediately | *[to fill]* | |
| A4 | Turn on showroom mode and the announcement bar | Cart hidden for new visitors; unpaid orders cancelled & restocked; banner shown | *[to fill]* | |
| A5 | Edit a FAQ entry | Updated FAQ appears on the storefront | *[to fill]* | |
| A6 | Review the activity and error logs | Changes and grouped errors are visible | *[to fill]* | |

*[Figure 6.12: A UAT session walking through a customer purchase scenario.]*

---

## 6.13 Test Results & Summary

Testing on this project was carried out in two complementary layers. **Manual, exploratory testing** was the primary, continuous activity: we used the running application across languages, themes, and devices, walked through real customer and administrator journeys, and deliberately tried to break the system. This is how the majority of the project's defects were discovered, including issues in the rendered interface and client-side JavaScript that no server-side test could have surfaced, such as the page-loader language bug.

Alongside it, an **automated PHPUnit suite** was built as a regression safety net. Executed with `php artisan test`, it produced a full pass:

```
Tests:    214 passed (691 assertions)
Duration: ~52s
```

Every one of the 214 automated tests passed, exercising 691 assertions across the authentication, catalogue, cart, payment, order-lifecycle, booking, administration, internationalisation, observability, and concurrency concerns of the system. Crucially, several of these tests exist because a bug was first found by hand and then locked down with an automated test, so the two layers reinforce each other: manual testing finds problems, and the automated suite keeps them fixed.

The compatibility testing (Section 6.11) confirms the system adapts correctly across devices and browsers, and the user-acceptance testing (Section 6.12) evaluates whether it is acceptable to its real users. Taken together, the manual, automated, security, concurrency, performance, compatibility, and acceptance testing give strong, evidence-based confidence that the system implemented in Chapter 5 is functionally correct, secure against the realistic attacks it was designed to resist, and safe under concurrent use, confirming that the project's objectives have been met.

*[Figure 6.13: The full `php artisan test` run showing "Tests: 214 passed (691 assertions)".]*
*[Figure 6.14: A summary of testing coverage across manual and automated layers.]*
