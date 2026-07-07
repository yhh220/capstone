# Chapter 5: System Implementation

## 5.1 Introduction

This chapter documents how the Win Win Car Audio Auto Accessories Showroom System was translated from the design of Chapter 4 into a working, deployed web application. Where the previous chapter described *what* the system should do, this chapter explains *how* each feature was actually built, *why* each significant technical decision was taken, and how the individual pieces fit together into a single coherent system.

The system is a full-stack web application built on the **TALL stack** (Tailwind CSS, Alpine.js, Laravel, and Livewire) with a **Filament** administration panel layered on top of the same Laravel foundation. The public storefront is written as a set of full-page **Livewire 4** components, so that the responsiveness of a single-page application is achieved without the complexity of maintaining a separate JavaScript front-end framework: the server renders the initial HTML for fast first paint and good search-engine indexing, and Livewire then performs partial page updates over the wire as the customer interacts.

The application runs on **Laravel 13** with **PHP 8.4**, uses **SQLite** during local development for a zero-configuration setup, and runs against a **TiDB Serverless** database (a MySQL-compatible, horizontally-scalable cloud database, connected over TLS) in production. It is deployed as a **Docker** container on the **Render** cloud platform at `https://winwincaraudio.onrender.com`. Front-end assets are compiled with **Vite** using **Tailwind CSS v4**, and the three-dimensional product configurator is rendered with **Three.js**.

To keep the discussion readable, the chapter is organised feature by feature rather than file by file. Each of the sections that follow takes one area of the system (the homepage, the catalogue, the cart, and so on) and walks through its implementation from the customer-facing behaviour down to the server-side logic and database interaction that supports it. Cross-cutting concerns that touch many features at once, such as internationalisation, search-engine optimisation, caching, and background scheduling, are collected into their own sections toward the end so that they are explained once and referenced throughout.

*[Figure 5.1: High-level system architecture: browser, Livewire/Laravel application layer, Filament admin, TiDB database, and external services (Gmail API, cron-job.org).]*

---

## 5.2 Homepage & Landing Experience

The homepage is the first impression of the business and is implemented as a dedicated full-page Livewire component. Rather than a static marketing page, it is data-driven: the featured products, product categories, and brand logos it displays are all read from the database, so the shop owner controls the landing experience entirely through the admin panel without any code being touched.

The page is structured as a sequence of clearly separated sections: a hero banner, a set of featured/newest products, a category grid that links into the filtered catalogue, a brand strip, a short "why choose us" trust section, and a footer with contact details and the store map link. Each product and category shown on the homepage links directly into the relevant filtered view of the catalogue, so the landing page functions as the primary navigation hub into the rest of the store.

A deliberate performance decision was made on the homepage's hero image: it is marked with `fetchpriority="high"` so that the browser downloads it early as the **Largest Contentful Paint** element, which measurably improves the perceived load speed of the most visible part of the page. Conversely, heavier libraries that are *not* needed on the homepage (most notably the Leaflet mapping library) are deliberately *not* loaded here (see Section 5.13.5), keeping the landing page lean.

The homepage also respects the two global storefront modes. When the shop is placed in **showroom mode** (Section 5.10.10), the calls-to-action that would normally lead to the cart are suppressed for new visitors, turning the homepage into a pure catalogue showcase; and when the **site announcement bar** is enabled (Section 5.13.7), its message appears at the very top of the page above the hero.

*[Figure 5.2: The homepage hero and featured-products section.]*
*[Figure 5.3: The category grid and brand strip linking into the catalogue.]*

---

## 5.3 Product Catalogue, Search & Filtering

The product catalogue is the core browsing experience and is implemented as a single Livewire component that combines full-text search, category and brand filtering, price handling, sorting, and pagination into one reactive interface. As the customer types in the search box or changes a filter, Livewire re-queries the database and re-renders only the product grid, without a full page reload.

Every filter and search term the customer chooses is bound to a query-string parameter using Livewire's `#[Url]` attribute. This has two important consequences. First, the state of the page is fully described by its URL, which means a customer can **bookmark or share a filtered view** (for example a link showing only a particular category sorted by price) and it will reopen in exactly the same state (this shareable-URL behaviour is discussed further in Section 5.13.4). Second, browser back and forward navigation works naturally, because each filter change is reflected in the address bar.

On the server side, the filters are composed into a single database query rather than being applied in PHP after the fact, so that only the products actually needed for the current page are fetched. Pagination limits each request to a manageable number of products, and the search matches against the localised product name so that customers searching in Malay or Chinese (Section 5.11) find the same products a customer searching in English would.

The catalogue also honours the storefront mode: in showroom mode the "add to cart" affordances are replaced with view-only detail links for new visitors, while product information itself remains fully visible.

*[Figure 5.4: The product catalogue with the search box, category/brand filters, and sort control.]*
*[Figure 5.5: A shared/bookmarked filtered catalogue URL reopening in the same state.]*

---

## 5.4 Product Detail Page & 3D Configurator

Each product has a dedicated detail page that presents its image gallery, localised name and description, price, stock availability, and an add-to-cart control with a quantity selector. Product images are managed through the **Spatie Media Library** package, which stores each upload, associates it with the product, and generates the responsive image sizes used across the site.

The standout feature of the detail experience is the **three-dimensional product configurator**, built with **Three.js**. For products that support it, the customer can view and rotate a 3D model of the accessory directly in the browser. The models are loaded using Three.js's `GLTFLoader` and are compressed with **Draco** geometry compression, which dramatically reduces the download size of the 3D mesh so that the interactive model loads quickly even on a mobile connection. The configurator runs entirely client-side once loaded, giving the customer a smooth, immediate way to inspect a product from every angle before buying, a strong differentiator for a car-accessories showroom where fit and appearance matter.

The 3D models used in the configurator are open-source assets obtained from external sources rather than modelled in-house. Each model, along with its author, source, and licence, is credited in the Media & Asset Credits appendix. *[to add: the specific open-source 3D models used, with their sources and licences]*

Stock availability is surfaced honestly on this page: when an item is out of stock it is shown as backorderable with the lead time the owner configures (the `BACKORDER_DAYS` setting, Section 5.10.9), so the customer knows how long delivery will take rather than simply being blocked.

*[Figure 5.6: The product detail page with its image gallery and add-to-cart panel.]*
*[Figure 5.7: The interactive Three.js 3D configurator rotating a product model.]*

---

## 5.5 Shopping Cart & Checkout

The shopping cart and checkout flow is where correctness matters most, because it is the point at which stock, money, and customer expectations meet. The cart is a Livewire component that lets the customer adjust quantities and remove items with immediate feedback, recalculating the subtotal, shipping, and total on every change.

**Shipping is computed from configuration, not hard-coded.** A flat delivery rate (`SHIPPING_FLAT_RATE`) is charged on orders below a free-shipping threshold (`SHIPPING_FREE_THRESHOLD`), and waived above it. Because both values are owner-configurable settings, the shop can run a "free shipping over RM300" promotion simply by changing a number in the admin panel, with the checkout total updating accordingly.

The most important implementation work in this area is **concurrency safety at checkout**. When an order is placed, the system must guarantee that two customers cannot both buy the last unit of a product. This is achieved with a layered strategy: the checkout runs inside a database **transaction**; the relevant stock rows are locked with `lockForUpdate()` so a second concurrent checkout must wait; and stock is decremented with an **atomic conditional update** that only succeeds if sufficient stock still exists, using the number of affected rows to detect the race. If a customer loses the race for the last item, the transaction is rolled back and they are shown a clear "no longer available" message rather than the system silently overselling. This combination of a pessimistic lock and an atomic guarded update is what makes the checkout safe under simultaneous load.

On success, the order and its line items are persisted, stock is reduced, and the customer is taken into the payment step (Section 5.6). Every order is associated with the authenticated customer's user account, which is what later allows the account area (Section 5.9) and the admin panel to attribute and retrieve a customer's orders reliably.

*[Figure 5.8: The shopping cart with quantity controls and the shipping/total breakdown.]*
*[Figure 5.9: The checkout page.]*

---

## 5.6 Payment & Order Lifecycle

After checkout, an order enters a well-defined lifecycle that the system enforces as a **forward-only status chain**: *pending → processing → shipped → delivered*, with *cancelled* as a terminal branch. The status is never edited as a free-form field; it is advanced only through specific, guarded actions, which makes an illegal transition (such as shipping a cancelled order) impossible by construction.

A newly placed order begins as **pending payment**. To prevent unpaid orders from holding stock indefinitely, a scheduled background job (Section 5.15) **expires unpaid orders** after a timeout: the order is cancelled and its reserved stock is returned to inventory so it becomes available to other customers again. This expiry is written as an **atomic, guarded operation** (it only cancels an order that is still genuinely pending) so it can never collide with a payment that arrives at the same moment, and a payment landing first will cause the expiry to safely do nothing.

When payment is confirmed (or manually reconciled by the owner for a bank transfer, Section 5.10.3), the order moves to **processing**, stock is committed, and the customer receives a confirmation email. The subsequent transitions to shipped (with a tracking number) and delivered are driven by admin actions, each of which emails the customer so they are kept informed at every step.

Order **cancellation and refunds** follow a configurable, tiered policy. A customer who cancels within the full-refund window (`CANCELLATION_FULL_REFUND_HOURS`) receives a 100% refund; after that window but before the order ships, a processing fee (`CANCELLATION_FEE_PERCENT`) is deducted; once shipped, self-cancellation is no longer offered. This policy is enforced consistently on both the customer side and the admin side, so the refund amount is calculated the same way regardless of who initiates the cancellation, and cancelling always restocks the items.

*[Figure 5.10: The order status/lifecycle as seen by the customer.]*
*[Figure 5.11: The payment step and order confirmation.]*

---

## 5.7 Service Booking System

Beyond selling products, the shop offers installation and service appointments, and the booking system lets customers reserve a time slot online. The booking calendar is generated dynamically from the owner's configuration rather than a fixed timetable, which keeps the shop in full control of its own availability.

Available slots are computed from several settings working together: the daily opening window (`BUSINESS_HOURS_START` and `BUSINESS_HOURS_END`), the days the shop is closed (`BUSINESS_CLOSED_WEEKDAYS`, given as weekday numbers), and the length of each appointment (`BOOKING_SLOT_MINUTES`). From these, the system builds the list of selectable times for any chosen date and removes any slots that are already taken, so double-booking is not offered.

Two correctness issues received particular attention. The first is **time zones**: the application operates in **Asia/Kuala_Lumpur** time so that the slots a customer sees, the times stored, and the reminders sent all agree with local Malaysian time rather than drifting due to a server running in UTC. The second is **same-day filtering**: when a customer books for the current day, slots that have already passed are removed from the list, so it is impossible to book an appointment in the past.

Finally, confirming a booking is implemented as an **atomic pending-to-confirmed claim** (Section 5.9.8): the transition only succeeds if the booking is still pending, which prevents a race where a just-cancelled or already-confirmed booking could be confirmed a second time. Customers receive an email confirmation, and an automated reminder is sent ahead of the appointment (Section 5.14).

*[Figure 5.12: The booking page showing available appointment slots derived from business-hours settings.]*
*[Figure 5.13: A confirmed booking and its confirmation email.]*

---

## 5.8 Contact & Static Information Pages

The system includes the informational pages a real business needs, each implemented so its content is maintainable rather than hard-coded.

### 5.8.1 Contact Page

The contact page combines the shop's address, phone, email, and opening hours with an interactive map and a contact form. The map is rendered with the **Leaflet** library, centred on the shop's real coordinates. Crucially, Leaflet's CSS and JavaScript are loaded **only on this page** using Livewire's `@push` directive into the layout's style and script stacks, rather than globally. This keeps roughly 46 KB of render-blocking assets off every other page in the site (Section 5.13.5). The script that initialises the map is pushed *after* the Leaflet library itself so that the map object is only created once the library is available.

The contact form submits a message that is stored for the owner to review in the admin panel (Section 5.10.5), and it is protected against automated spam by a **honeypot** field (Section 5.9). Submissions are validated in the customer's language.

### 5.8.2 Frequently Asked Questions

The FAQ page presents common questions grouped by category in an accordion. The questions and answers are **not hard-coded**: they are read from published FAQ records that the owner manages through the admin panel (Section 5.10.6), in all three supported languages. This means the shop can answer new common questions itself, in every language, without a developer.

### 5.8.3 About and Other Static Content

Supporting pages such as the "about" and policy content are presented consistently within the same responsive layout, dark-mode theme, and internationalised navigation as the rest of the site, so the informational pages feel like an integrated part of the store rather than bolt-ons.

*[Figure 5.14: The contact page with the Leaflet store map and contact form.]*
*[Figure 5.15: The FAQ accordion driven by admin-managed records.]*

---

## 5.9 Authentication, Account & Security

Customer accounts and the security controls that protect them are treated as a first-class part of the system.

### 5.9.1 Registration and Login

Customers register and log in through Livewire components with validated forms. Registration is protected by a **honeypot** field (via the Spatie Honeypot package), an invisible field that legitimate users never fill in but automated bots do, allowing spam sign-ups to be rejected silently without inconveniencing real customers with a CAPTCHA.

### 5.9.2 Progressive Login Lockout

To defend against password-guessing (brute-force) attacks, failed logins are rate-limited with a **progressive lockout**. As failed attempts accumulate against an account, lockout durations are applied at escalating tiers (at the 5th, 10th, 15th, 20th, and 25th failed attempt), so a persistent attacker faces rapidly increasing delays while a legitimate user who simply mistypes their password once or twice is barely affected.

A subtle but important bug was found and fixed in this area during implementation. Livewire **persists its error bag across requests** within a component's lifetime, and the `@error` directive renders the *first* stored message. An earlier version of the lockout messaging appended a new "attempts remaining" error on every failed submission, so the view kept displaying the very first message, which is why a colleague testing the login always saw "4 attempts remaining" no matter how many times they failed. The fix was twofold: the component now calls `resetErrorBag()` at the start of each attempt so stale messages are cleared, and the tier logic was corrected so the countdown only fires at the real lockout boundaries. This is documented because it is a genuine, non-obvious framework gotcha rather than a simple typo.

### 5.9.3–5.9.7 Account Area and Access Control

Authenticated customers have an account area where they can view their **order history** and **booking history**, track order status, and manage their details. Every record shown is scoped to the logged-in user's own `user_id`, and (critically) every direct-access route re-checks **ownership** on the server before returning a record. This closes the class of **Insecure Direct Object Reference (IDOR)** vulnerabilities in which a user could otherwise change an ID in the URL to view someone else's order: the server refuses to return a record that does not belong to the requester. Sensitive tokenised links (such as those in emails) are gated with a constant-time `hash_equals` comparison so that a token cannot be discovered by timing the response.

The application also sets **security headers** including a Content-Security-Policy, trusts the correct proxy headers so it sees real client addresses behind Render's load balancer (`trustProxies`), and keeps the admin panel excluded from search engines via `robots.txt`.

It is worth stating plainly, as was discussed during development, that these measures substantially raise the security bar but do not make any system "completely un-hackable"; the goal is defence in depth (honeypot, lockout, ownership checks, token gating, and security headers layered together) so that the common, realistic attack paths are closed.

### 5.9.8 Atomic State Transitions

Several sensitive actions (confirming a booking, marking an order paid, cancelling) are implemented as **atomic conditional updates** that only take effect if the record is still in the expected starting state, with the outcome decided by the number of rows the update affected. This is the same defensive pattern used at checkout (Section 5.5) and for order expiry (Section 5.6), applied consistently so that two users, or a user and a background job, acting at the same moment can never drive a record into a contradictory state; the loser of the race receives a harmless "already done" notice.

*[Figure 5.16: The login screen showing the progressive-lockout messaging.]*
*[Figure 5.17: The customer account area with order and booking history.]*

---

## 5.10 Administration Panel (Filament)

The entire back office is built with **Filament 5** on top of the same Laravel application and database, giving the shop owner a polished, responsive, permission-controlled admin panel without a separate system to maintain. The panel is organised into **fourteen resources**, plus a set of custom actions and pages. Because Filament and the storefront share the same models, a change made in the admin panel is reflected on the storefront immediately.

### 5.10.1 Access Control and Layout

The admin panel is reachable only by authenticated administrators and is served under a separate guard from customer accounts. The sidebar navigation was made **responsive** so that the panel is fully usable on a phone or tablet, collapsing into a mobile-friendly layout, important for a shop owner who manages orders from the floor rather than a desk.

### 5.10.2 Catalogue Management: Products, Categories, Brands

The **Product**, **Category**, and **Brand** resources let the owner manage the catalogue completely. Products are created and edited with all of their localised fields (Section 5.11), price, stock, category and brand associations, and images (uploaded through the Media Library). Categories and brands are managed as their own resources and drive both the storefront navigation and the catalogue filters. Creating a product here is what makes it appear on the homepage and in the catalogue; the storefront has no separate content system.

### 5.10.3 Booking & Order Processing

Orders and bookings are processed through dedicated, guarded actions rather than a free-form status field, so the forward-only lifecycles of Sections 5.6 and 5.7 are enforced from the admin side too.

**Table 5.1: Order processing actions**

| Action | Effect |
|---|---|
| Mark Paid | Marks a pending order paid and processing, and emails the customer the same confirmation the online payment flow sends (used to reconcile a manual bank transfer). |
| Mark Shipped | Requires a tracking number, sets the order to shipped, and emails the customer the tracking detail. |
| Mark Delivered | Confirms receipt and emails the customer. |
| Cancel & Restock | Cancels the order, returns its stock to inventory, records a tiered refund amount, and emails both the customer and the owner. |
| Mark Refund Sent | Confirms that a recorded refund was actually transferred, and emails the customer. |
| Resend Confirmation | Re-sends the order confirmation email on demand. |

Every one of these actions runs inside a transaction with the order row locked and re-checks the order's state under that lock, so two administrators (or an administrator racing the automatic expiry job) cannot produce a contradictory result; a lost race surfaces as a harmless "already done" notice. For bookings, the **Confirm** action is the atomic pending-to-confirmed claim described in Section 5.9.8, and a **Send Reminder** action emails the appointment reminder on demand in addition to the scheduled daily job.

### 5.10.4 Customer & Feedback Management

The **Customer** resource lists registered customers and, because every order carries the customer's `user_id`, shows each customer's order count and lets the owner drill into that customer's orders. The customer is always identified by their user account, which is the reliable link between a person and their purchases. The **Feedback** resource collects and displays customer feedback/reviews for the owner to moderate.

### 5.10.5 Contact Message Management

The **Contact** resource holds the messages submitted through the contact form (Section 5.8.1), so the owner has a single place to read and act on customer enquiries.

### 5.10.6 FAQ & Chatbot Knowledge Management

Two separate knowledge bases are maintained by staff without any code deployment. The **public FAQ** (the `Faq` resource) drives the storefront FAQ accordion (Section 5.8.2): each entry has a question and answer in all three languages, a category used to group related questions, a sort order, and a published toggle. The **chatbot knowledge base** (the `ChatbotFaq` resource) is managed separately and powers the support widget (Section 5.12); each rule has per-language answers and a priority field that decides which rule wins when more than one keyword matches. Keeping the two distinct lets the shop write long-form public FAQ answers independently of the short, keyword-triggered chatbot replies.

### 5.10.7 Activity Log (Audit Trail)

The **Activity** resource exposes an audit trail, built on the **Spatie Activitylog** package, recording significant changes made in the system. This gives the owner accountability (a record of what was changed and when), which is valuable when more than one person has admin access.

### 5.10.8 Application (Error) Log Monitoring

The **Log** resource is a purpose-built error-monitoring view. Application errors are recorded to the database, and (rather than showing an undifferentiated stream) each error is reduced to a stable **fingerprint** stored in an indexed, multibyte-safe column, so repeated occurrences of the *same* error are grouped together. A small **state machine** classifies each grouped error as *active*, *recurred*, or *quiet*, and a "Check for recurrence" control (shown only on rows that are actually errors) lets the owner see whether a previously-seen problem has come back. This turns a raw log into something a non-developer owner can actually act on.

### 5.10.9 Store Configuration (Settings)

A dedicated **Settings** resource lets the owner adjust the shop's operating parameters directly, with **no code change or redeployment required**, an essential requirement for a system handed over to a non-technical shopkeeper. Each setting is edited through a form that validates the value against its type (a time in `HH:MM` format, an integer within a range, a percentage capped at 100, and so on), so a malformed value is rejected with a clear, human-readable message rather than silently breaking a downstream calculation; boolean settings additionally offer a one-click toggle. Every setting carries an owner-facing help text explaining in plain language what it does.

**Table 5.2: Owner-configurable settings**

| Setting | Controls |
|---|---|
| Online Shopping Mode | The storefront mode toggle: cart & checkout on, or showroom mode (Section 5.10.10). |
| Business Start Time / Business End Time | The daily window within which appointment slots are generated. |
| Closed Weekdays | The weekdays on which no bookings are offered. |
| Appointment Slot Length | The duration, in minutes, of each booking slot. |
| Backorder Lead Time (days) | The delivery time quoted for out-of-stock items. |
| Shipping Flat Rate (RM) | The delivery fee charged below the free-shipping threshold. |
| Free Shipping Threshold (RM) | The subtotal at or above which shipping is free. |
| Full Refund Window (hours) | The period after payment during which a cancellation is fully refunded. |
| Cancellation Fee (%) | The processing fee deducted from a refund after that window, before shipping. |
| Site Announcement Bar (on/off) | Whether the site-wide announcement banner is shown. |
| Site Announcement Text | The message displayed in that banner. |

Because every value is read through a **cached accessor that is explicitly invalidated when the setting is saved** (Section 5.13.6), a change takes effect on the storefront immediately without a redeploy. The same settings feed multiple parts of the system from a single source of truth. The business hours, for example, drive both the booking calendar (Section 5.7) and the opening-hours entry in the site's structured data (Section 5.13.2), so the shop's configuration can never contradict itself across features.

### 5.10.10 Online Shopping Mode Toggle & Graceful Shutdown

The `ONLINE_SHOPPING_ENABLED` setting switches the storefront between full e-commerce and **showroom mode**. This was designed as a **"soft showroom"**: when shopping is turned off, new visitors see a pure product showcase with the cart and checkout removed, while customers who already have an account remain able to log in and view their existing orders, so turning off online sales never locks paying customers out of their own order history.

Turning shopping **off** also performs a **graceful shutdown** of pending business. A domain event fires when the setting changes from enabled to disabled, and a dedicated service then **cancels every unpaid order and restocks it**, emailing each affected customer, while **paid orders are never touched**, because those represent committed sales the shop still intends to fulfil. This prevents unpaid orders from being stranded in limbo when the shop pauses online selling. The setting's own help text reminds the owner to switch on the announcement bar at the same time so customers understand why shopping is paused.

### 5.10.11 User & Role Management

The **User** resource manages the staff accounts that can access the panel, supporting the role-based access control that separates administrators from customers. This keeps administrative capability restricted to authorised staff.

*[Figure 5.18: The Filament admin dashboard with the responsive sidebar.]*
*[Figure 5.19: The Product resource edit form with localised fields and media upload.]*
*[Figure 5.20: The Orders table with the guarded processing actions.]*
*[Figure 5.21: The Settings resource with per-setting validation and help text.]*
*[Figure 5.22: The Application Log resource grouping errors by fingerprint.]*

---

## 5.11 Internationalisation (i18n)

The storefront is fully trilingual (**English**, **Bahasa Melayu**, and **Chinese**) because the shop's customer base in Malaysia is genuinely multilingual. Internationalisation was implemented with a deliberate split between two kinds of translatable content, each handled the way that suits it best.

**Interface strings** (buttons, labels, navigation, validation messages, and other fixed text) use Laravel's translation system through the `__()` helper, with a key/value file per language (including a translated `validation.php` so form errors appear in the customer's language). Fixed structural content such as service names and category labels is keyed in the same way.

**Product content** (the parts that the owner writes and that differ per product) is stored in **per-language database columns** (for example a Malay name column and a Chinese name column alongside the default). When a page renders, it selects the column matching the active language and falls back to the default when a translation has not been supplied. This lets the owner translate a product's name and description directly in the product form (Section 5.10.2) without any code, and it means search and sorting operate on the localised text so a customer browsing in Malay gets a consistent Malay experience.

Dates and times are localised as well: a listener responds to the application's locale changing and updates **Carbon's** locale accordingly, so a date rendered with `translatedFormat` appears in the correct language. A small, pragmatic decision was made to keep **outgoing emails pinned to English** and to leave the **admin panel untranslated**, since the recipients and operators of those are consistent, which kept the translation effort focused where it benefits customers most.

*[Figure 5.23: The same product page shown in English, Bahasa Melayu, and Chinese via the language switcher.]*

---

## 5.12 Chatbot Support Widget

A lightweight customer-support **chatbot** is available across the storefront as a floating widget. It answers common questions from a keyword-matched knowledge base managed by the owner (the `ChatbotFaq` resource, Section 5.10.6). When a customer's message matches a rule's keywords, the widget replies with that rule's answer in the customer's current language; where several rules match, the **priority** field decides which answer is returned. Because the knowledge base is owner-editable and multilingual, the shop can extend the bot's coverage over time without any development work, giving customers instant answers to routine questions without waiting for staff.

*[Figure 5.24: The chatbot widget answering a customer question.]*

---

## 5.13 Cross-Cutting Concerns

Several capabilities apply across the whole system rather than to one feature. They are gathered here so each is explained once.

### 5.13.1 Responsive Design & Dark Mode

The entire site (storefront and admin) is responsive, using Tailwind's utility classes to adapt from mobile to desktop. A site-wide **dark mode** is implemented with Alpine.js, remembering the visitor's preference, so the interface is comfortable in different lighting and on different devices.

### 5.13.2 Search-Engine Optimisation & Structured Data

SEO was implemented thoroughly using the **artesaos/seotools** package. Every page generates its own descriptive `<title>`, meta description, and social-sharing tags (**Open Graph** and **Twitter Card**) so that links to the site render as rich previews when shared. On top of that, the homepage emits **JSON-LD structured data** typed as an `AutoPartsStore` local business, enriched with the shop's real details: a `PostalAddress`, telephone and email, price range, geographic coordinates (`GeoCoordinates`), a `sameAs` link to the business's social page, and (importantly) an `openingHoursSpecification` **derived from the very same business-hours settings** that drive the booking calendar (Section 5.10.9). Because the structured opening hours are generated from the live settings rather than typed separately, they can never drift out of sync with the hours the shop actually offers. This structured data is what allows Google to present the shop as a rich local-business result in Search and Maps rather than a plain link.

### 5.13.3 Sitemap, robots.txt & Google Search Console

A **sitemap** is generated with the Spatie Sitemap package. A production bug was fixed here: because the sitemap is generated from the command line, it initially used the local development domain, which caused Google to reject it with a "URL not allowed" error. The generator now forces the canonical production root URL and the `https` scheme, so every URL in the sitemap matches the live domain it is served from. A `robots.txt` file declares the sitemap's location and blocks the admin path from indexing. The site was then verified in **Google Search Console** using an HTML-file verification token committed to the public directory; verification succeeded and the sitemap (22 pages) was submitted and accepted, so the site is now properly registered with Google.

### 5.13.4 Shareable Search & Filter URLs

As introduced in Section 5.3, the catalogue's search term, filters, and sort order are each bound to the URL query string via Livewire's `#[Url]` attributes. This makes any browsing state a **shareable, bookmarkable link**: a customer (or the shop, in a promotion) can copy the address of a specific filtered, sorted view of the catalogue and send it to someone else, and it will open in exactly that state. It also makes the browser's back/forward buttons behave intuitively across filter changes.

### 5.13.5 Front-End Performance

Guided by **Google PageSpeed Insights / Lighthouse** audits, several targeted performance fixes were made without altering functionality. The Leaflet mapping library (around 46 KB of render-blocking CSS and JavaScript) was moved off every page and loaded only on the contact page where the map actually appears (Section 5.8.1). The homepage's hero image was given `fetchpriority="high"` so it loads as the Largest Contentful Paint element as early as possible. These changes reduced the render-blocking payload on the pages customers land on most, improving load times while leaving every feature intact.

### 5.13.6 Caching & Cache Invalidation

Frequently-read, rarely-changed data (the store settings in particular) is served through **cached accessors** so that a page render does not repeatedly hit the database for the same configuration values. The important discipline here is **explicit invalidation**: when a setting is saved in the admin panel, its cache entry is cleared, so the new value is visible on the storefront immediately. A related bug was fixed in the admin "clear cache" action, which had been scoped too broadly and was flushing security-related state; it was narrowed to clear only the intended caches.

### 5.13.7 Site Announcement Bar

A dismissible **announcement bar** can be shown at the top of every page, controlled by the `SITE_ANNOUNCEMENT_ENABLED` and `SITE_ANNOUNCEMENT_TEXT` settings. It is implemented with Alpine.js and keyed to the message text, so that when the owner publishes a *new* message it reappears even for a visitor who dismissed the previous one. Its primary use is to tell customers when online shopping is temporarily paused (Section 5.10.10), but it serves for any site-wide notice.

### 5.13.8 Accessibility

Accessibility was considered throughout (semantic markup, keyboard-navigable controls, and appropriate ARIA roles) and specific issues surfaced by automated audits were corrected (for example, removing an invalid list role that had been misapplied to the desktop navigation), so the markup is both valid and friendlier to assistive technology.

*[Figure 5.25: The storefront in dark mode.]*
*[Figure 5.26: A rich Google search result produced by the AutoPartsStore structured data.]*
*[Figure 5.27: Google Search Console showing the verified property and accepted sitemap.]*
*[Figure 5.28: The site announcement bar in use.]*

---

## 5.14 Email & Notifications

The system keeps customers informed through transactional email at every meaningful step: order confirmation, shipping with tracking, delivery, cancellation and refund, booking confirmation, and appointment reminders. These are implemented as Laravel Mailables with consistent, branded templates.

### 5.14.1 Gmail API Delivery and One-Time Authorisation

Rather than sending through a traditional SMTP relay, the application delivers mail through the **Gmail API**, so email is sent as the shop's own Gmail account with strong deliverability. Setting this up is a **one-time administrator action**: a token-gated setup route (`/gmail-send/connect`, restricted to the admin guard) walks the owner through Google's OAuth consent screen, and the resulting **refresh token** is stored so the application can thereafter send mail as the shop's account indefinitely, without any password being kept. This authorisation flow is deliberately admin-only, because it grants the application the ability to send email as the business.

*[Figure 5.29: An order confirmation email.]*
*[Figure 5.30: The one-time Gmail API authorisation (OAuth consent) setup.]*

### 5.14.2 Owner & Staff Alert Notifications

Alongside the customer-facing mail, the system sends an **internal alert** to the shop for every event that needs staff attention: a new booking, a paid order, a cancellation (whether by the customer, an administrator, or the automatic expiry job), and a new contact enquiry. All of these share a single generic mailable, so one template covers every alert type.

Getting these alerts to actually *reach* staff surfaced a non-obvious production issue. Because the application sends mail **as** the shop's own Gmail account (Section 5.14.1), and the alerts were addressed **to** that same account, Gmail treated them as self-sent mail: it keeps a single copy under "Sent" and never delivers it to the inbox, so alerts were effectively invisible unless someone thought to check the Sent folder. Two complementary measures fixed this. First, every user with the *admin* role is **CC'd** on each alert — the recipient list is read from the user table at send time, so hiring or removing staff changes who is notified without touching code — and because those are separate mailboxes, the alerts arrive in the staff's own inboxes normally. Second, the alert's To address uses Gmail's **plus-alias** form (`…+alerts@gmail.com`), which delivers to the same shop mailbox but gives Gmail's filters something to match: a one-time filter applies a dedicated **"Shop Alerts" label**, marks the mail important, and exempts it from spam, turning that label into a reliable notification tray inside the shop's own account.

*[Figure 5.31: An owner-alert email in a staff inbox, and the "Shop Alerts" label in the shop's Gmail.]*

---

## 5.15 Scheduling & Background Jobs

Some work must happen on a schedule rather than in response to a customer action, chiefly **expiring unpaid orders** (Section 5.6) and **sending appointment reminders** (Section 5.7). These are defined as Laravel scheduled tasks.

Because the Render hosting plan does not provide an always-running system cron daemon, the schedule is driven **externally**: the free service **cron-job.org** is configured to call a dedicated, **token-protected** endpoint (`/cron/run-schedule/{token}`) every ten minutes, and that endpoint runs Laravel's `schedule:run`. The token means only the configured pinger can trigger the scheduler, and running every ten minutes is frequent enough to expire stale orders and dispatch reminders promptly. The same pings also serve a **second purpose**: Render's free tier spins an instance down after roughly fifteen minutes without traffic, so a request arriving every ten minutes keeps the application warm and largely prevents the slow "cold start" a visitor would otherwise experience after a quiet period. This gives the deployed application reliable background processing — and, as a side benefit, better perceived availability — on a platform that offers neither cron nor an always-on instance natively, which is a genuinely important detail for the system to function in production.

*[Figure 5.32: The cron-job.org job configured to ping the schedule endpoint every ten minutes.]*

---

## 5.16 Summary

This chapter has documented the implementation of the Win Win Car Audio Auto Accessories Showroom System from the customer-facing storefront through to the administrative back office and the operational concerns that keep it running in production. The storefront was built as full-page Livewire components on the TALL stack, delivering an interactive homepage, a searchable and shareable product catalogue, a Three.js 3D product configurator, a concurrency-safe cart and checkout, a configuration-driven booking system, and fully trilingual content. The back office was built with Filament across fourteen resources, giving the owner complete, permission-controlled, mobile-friendly control over the catalogue, orders, bookings, customers, content, and (through the Settings resource) the shop's own operating parameters, with no code changes required to run the business day to day.

Throughout, particular care was taken over the concerns that separate a demonstration from a deployable system: **correctness under concurrency** (transactions, row locks, and atomic guarded updates protecting stock, orders, and bookings), **security in depth** (progressive lockout, honeypot spam protection, ownership/IDOR checks, constant-time token comparison, and security headers), **operability by a non-technical owner** (owner-configurable settings with validation, an actionable error-log view, and a graceful showroom-mode shutdown), and **production realities** (Gmail API delivery, externally-driven scheduling on a cron-less host, SEO with verified Search Console registration, and performance tuning guided by PageSpeed Insights). Several non-obvious bugs discovered during implementation (the persistent-error-bag login message, the sitemap's local-domain URLs, the over-broad cache clear, and the self-addressed owner alerts that Gmail never delivered to the inbox among them) were diagnosed and fixed rather than worked around. The result is a complete, coherent, and deployed system that meets the objectives set out in the earlier chapters. The verification of that system is the subject of Chapter 6.

*[Figure 5.32: The deployed application running at winwincaraudio.onrender.com.]*
*[Figure 5.33: End-to-end overview: a customer journey from catalogue to confirmed order and the corresponding admin view.]*
