# Chapter 4: System Design

## 4.1 System Architecture

The application is built on a solid client-server architecture based on the Model-View-Controller (MVC) design pattern, which is organised around the system's business logic, user interface, and data access into distinct layers. This separation makes the system easier to maintain, test, and extend. The following sections describe the component architecture, the technology stack, and the service and middleware layers that hold the system together.

### 4.1.1 MVC and Livewire Component Architecture

**Model-View-Controller (MVC)**

MVC stands for Model, View, and Controller. It is a design pattern that splits an application into three separate parts, so that data handling, the user interface, and the logic connecting them do not all get mixed into one file.

**Table 4.1: MVC Layer**

| Layer | Responsibility |
|---|---|
| Model | Handles data. Talks to the database, defines what fields exist, and how tables relate to each other |
| View | Handles the interface. This is the HTML the user actually sees in the browser |
| Controller | Handles logic. Receives the request from the user, decides what data to fetch from the Model, then passes that data to the View to display |

**How a traditional MVC request flows**

1. The user's browser sends a request to a route, for example /products.
2. The Controller receives the request and decides what needs to happen.
3. The Controller asks the Model to fetch data from the database, for example all active products.
4. The Model returns the data to the Controller.
5. The Controller passes that data to the View.
6. The View renders the data into HTML and sends the finished page back to the browser.

*[Figure 4.1: traditional MVC request flows]*

**How this project implements it**

This system does not use a separate Controller class for every page, which is the traditional MVC approach. Instead, most pages are built with Livewire components. A Livewire component is a PHP class paired with its own Blade template. The PHP class takes over the Controller's job (validating form input, querying the database, deciding what to show), while the Blade file remains the View. The Model layer is unchanged and still built entirely with Eloquent.

This means the three Model-View-Controller (MVC) roles are still present, they are just distributed differently. The table below shows where each role actually lives in the codebase.

**Table 4.2: MVC Architecture Implementation in the Project**

| Layer | Technology | Role in this project |
|---|---|---|
| Model | Eloquent ORM (21 models) | Data access, relationships, casts, accessors such as Product::getCurrentPriceAttribute() |
| View | Blade + Livewire | Server rendered templates that update without a full page reload |
| Controller (Livewire) | 21 Livewire component classes | Handle validation, database queries, and state for each page, for example BookingForm, CheckoutPage |
| Controller (classic) | 5 HTTP Controllers | Used only where Livewire does not fit: InvoiceController (PDF/HTML invoice), SocialAuthController (OAuth redirect/callback), GmailSendSetupController (one time mail OAuth setup), StripeWebhookController (signature-verified server-to-server payment confirmations from Stripe), plus the base Controller |
| Admin layer | Filament 5 Resources | A parallel MVC style structure. Schemas act as the form, Tables act as the list View, and Filament auto generates the CRUD logic that would normally sit in a Controller |

There are no dedicated Form Request classes in this project (app/Http/Requests/ does not exist). Validation rules are written directly inside each Livewire component's rules, or inside the Filament form schema for admin pages. There are also no custom Event, Listener, or Job classes. Anything that needs to happen automatically is instead handled by model boot() hooks (for example generating a product slug on save, or cancelling unpaid orders when the shop is switched to showroom mode) or by 5 scheduled Console Commands (sitemap regeneration, expiring unpaid orders, sending booking reminders, trimming the activity log, auto resolving error logs).

The diagram below shows our layered structure. Requests flow from the browser through Blade and Livewire, into the Livewire component "Controller" layer, down through the Service layer, into the Eloquent Models, and finally into a database abstraction layer. This layer can connect to any relational database Laravel supports, selected through a single DB_CONNECTION setting in the .env file, so the database engine is not hard-wired into the application code. The Filament admin panel runs as a separate branch behind its own admin guard, converging into the same Models and database layer at the bottom.

*[Figure 4.2: System architecture diagram]*

### 4.1.2 Technology Stack

The system is built on the TALL stack (Tailwind, Alpine.js, Laravel, Livewire), extended with Filament for the admin panel and Three.js for 3D rendering. The tables below list every major package actually used in production, grouped into backend, frontend, and database/hosting.

**Backend**

The backend runs on Laravel with PHP 8.4. Livewire handles the reactive frontend logic, Filament powers the admin panel, and a set of Spatie packages cover media handling, audit logging, and spam protection, so we did not need to build these from scratch.

**Table 4.3: Backend technologies**

| Component | Technology |
|---|---|
| Language | PHP |
| Framework | Laravel |
| Reactive UI layer | Livewire |
| Admin panel | Filament |
| Payment gateway (test mode) | Stripe PHP SDK (stripe/stripe-php) |
| Media handling | Spatie Laravel MediaLibrary |
| Audit logging | Spatie Laravel ActivityLog |
| Spam protection | Spatie Laravel Honeypot |
| PDF generation | barryvdh/laravel-dompdf |
| SEO | artesaos/seotools |
| Social login | Laravel Socialite |
| Sitemap | Spatie Laravel Sitemap |

**Frontend**

The frontend is styled with Tailwind CSS and built through Vite. Three.js renders the 3D car configurator, and its models are compressed with DRACO through the gltf-transform pipeline so the initial page load stays fast. Every third-party frontend library is self-hosted and version-pinned under the application's own origin rather than loaded from a CDN, so no page waits on a third-party host and the Content-Security-Policy allows no external script origin at all.

**Table 4.4: Frontend technologies**

| Component | Technology |
|---|---|
| 3D rendering (configurator) | Three.js |
| 3D rendering (homepage showcase) | model-viewer (self-hosted, injected only when the 3D section approaches the viewport) |
| CSS framework | Tailwind CSS |
| Build tool | Vite |
| 3D model compression | @gltf-transform/cli (DRACO) |
| HTTP client | Axios |
| Scroll animation | AOS (self-hosted, version-pinned) |
| Icons | Inline SVG (Lucide-derived) site-wide; the Lucide runtime bundle is self-hosted and loaded only on the homepage |
| Map | Google Maps embed (lazily-loaded iframe pinned by coordinates; no mapping JavaScript library ships at all) |

**Database and Hosting**

The local and production environments use different databases, switched through configuration rather than code. The application is containerised and deployed to Render.

**Table 4.5: Hosting technologies**

| Component | Technology |
|---|---|
| Production database | TiDB Serverless (MySQL-compatible) |
| Local development database | SQLite |
| Hosting | Render (Docker, 3-stage build) |
| Local dev environment | Laravel Herd |

Both databases connect through Laravel's database abstraction layer, switched by a single DB_CONNECTION variable in .env. No Eloquent model code changes when the underlying database engine changes.

### 4.1.3 Service Layer and Middleware

Logic that is reused across pages, or that does not belong inside a single Livewire component, is pulled into a Service layer. This keeps each Livewire component focused on its own page.

**Service classes**

**Table 4.6: Service classes**

| Service | Responsibility |
|---|---|
| BookingService | Generates available time slots, checks business hours and closed weekdays, prevents double booking with lock aware checks, and composes the localised opening-hours label shown in the footer, contact page, and chatbot |
| EmailOtpService | Generates, hashes, and verifies 6 digit OTP codes for registration, password reset, setting a password, login 2FA, enabling and disabling 2FA, and account deletion (7 purposes in total) |
| ShippingCalculator | Computes shipping cost and the free shipping threshold at checkout |
| RefundCalculator | Computes the refund amount when an order is cancelled, based on the cancellation policy settings |
| Payments\StripeCheckoutService | The only class that talks to the Stripe SDK: creates or reuses one hosted Checkout session per order (test mode only, live keys refused) and voids sessions when an order leaves play |
| Payments\OrderPaymentService | The single idempotent settle step every payment path funnels into (demo button, Stripe webhook, success-URL return), so an order can only ever be marked paid once |
| ShopModeService | The graceful shutdown when online shopping is switched off: cancels and restocks every unpaid order, voids their open Stripe sessions, and emails the affected customers |
| Chat\MockDriver | The keyword matching chatbot engine, bound through the Chat Service Interface |

**Middleware (6 custom)**

**Table 4.7: Middleware**

| Middleware | Purpose |
|---|---|
| AdminMiddleware | Blocks non admin and non staff users from /admin routes |
| SecurityHeaders | Attaches CSP, HSTS, X Frame Options, X Content Type Options, Referrer Policy, and Permissions Policy to every response |
| SetLocale | Reads the session locale and applies EN, MS, or ZH translation context |
| ShoppingEnabled | Gates the cart, checkout, and payment routes behind the ONLINE_SHOPPING_ENABLED setting (Shop Mode) |
| AssignTraceId | Attaches a trace ID to each request for the observability and logging pipeline |
| LogoutAdminGuardOnly | Makes sure logging out of the admin panel does not also destroy the customer's web session |

Two authentication guards run side by side, web for customers and admin for Filament, so a customer session and an admin session never overlap in the same browser.

## 4.2 UML Diagrams

### 4.2.1 Use Case Diagram (Customer vs Admin/Staff/Owner Actors)

Actors: Guest (not logged in), Registered Customer, Staff, Admin, Owner. WhatsApp is treated as an external system the customer is directed to, not as an actor in the diagram.

**Table 4.8: Actors use cases**

| Actor | Use Cases |
|---|---|
| Guest | Browse products, use the 3D configurator, view services, submit the contact form, chat with the chatbot, track an order or booking by reference, register, log in |
| Registered Customer | Everything a Guest can do, plus manage profile, enable 2FA, add to cart, checkout with a choice of courier delivery or free store pickup (Shop Mode only), pay online (simulated, or through Stripe Checkout in test mode for card / FPX / GrabPay), view order and booking history, copy reference numbers, cancel own order or booking, and review a product they have bought once its order is delivered (verified-purchase review) |
| Staff | Log in to the admin panel and run the day-to-day operations: confirm, edit, and reschedule bookings, open order details, mark orders paid, ready for pickup, shipped, and delivered, cancel orders and process refunds, import product and order data, create and edit products, curate homepage testimonials, and work the contact inbox. Staff can never delete anything, never export data, and never manage user accounts |
| Admin | Everything Staff can do, plus delete records, export data, manage categories, brands, services, the FAQ and chatbot knowledge bases, setting values, and staff accounts, and monitor the system through the dashboard analytics, activity log, error log, and system status pages |
| Owner | Everything Admin can do, plus assign roles, administer Admin accounts, permanently force delete records, and create or delete Setting keys |

*[Figure 4.3: storefront use case diagram]*

*[Figure 4.4: admin panel use case diagram]*

### 4.2.2 Class Diagram (Eloquent Models and Relationships)

Before showing the diagram, a few terms specific to the framework this project is built on.

**Table 4.9: Specific Terms**

| Term | Plain explanation |
|---|---|
| Model | A PHP class that represents one table in the database. Each row in the table becomes one object in the code |
| Eloquent | The name Laravel gives its own tool for talking to the database. It lets the code read and write data using plain PHP, for example Product::find(1), instead of writing raw SQL like SELECT * FROM products WHERE id = 1 |
| Migration | A small script that makes one change to the database structure, for example creating a table or adding a new column. Migrations are how the actual tables get built and updated over time, one step at a time |

The table below lists the core relationships between the 21 models, followed by the diagram itself.

**Table 4.10: Models Relationship**

| Model | Relationship |
|---|---|
| Product | Belongs to Category. Has many OrderItem, CartItem, and ProductReview. Belongs to many CarModel through the product_compatibilities pivot |
| Category | Has many Product |
| Brand | Standalone, drives the homepage marquee |
| CarModel | Belongs to many Product through the product_compatibilities pivot (structured vehicle-fitment data) |
| ProductCompatibility | The pivot linking Product and CarModel |
| Order | Belongs to User. Has many OrderItem |
| OrderItem | Belongs to Order and Product |
| ProductReview | Belongs to Product and User (a verified-purchase rating + comment; one per customer per product) |
| CartItem | Belongs to User (or a guest session), and Product |
| Booking | Belongs to Service and User (user is nullable for guest bookings) |
| Service | Has many Booking |
| User | Has many Order, Booking, CartItem, SocialAccount, ProductReview |
| SocialAccount | Belongs to User |
| Feedback (Testimonial) | Standalone |
| Contact | Standalone, the contact form inbox |
| Setting | Key value pair, standalone |
| Faq | Standalone, the public FAQ page |
| ChatbotFaq | Standalone, the chatbot knowledge base |
| ChatLog | Standalone, auto pruned after 90 days |
| AppLog | Standalone, auto pruned |
| Activity (Spatie) | Polymorphic. Belongs to any loggable model as its subject, and to User as the causer |

*[Figure 4.5: Class Diagram]*

### 4.2.3 Activity Diagram (Booking Flow / Checkout Flow)

**Booking Flow**

The diagram is split into two lanes, Customer and System, so it is clear which actions the customer performs in the browser and which actions run automatically on the system. We checked the booking flow against the actual BookingForm component and BookingService class in the codebase rather than assuming how it works, so the steps below reflect what the system really does.

**Table 4.11: Booking Steps**

| Step | Actor | Action |
|---|---|---|
| 1 | Customer | Opens /booking. Selecting a service is optional, since a booking can simply be a general visit |
| 2 | Customer | Picks a date and a time slot on the calendar. Past dates and closed weekdays are disabled |
| 3 | Customer | Enters vehicle model and plate, then name, phone, email, and notes, and submits the form |
| 4 | System | Checks the honeypot field and the rate limit (max 5 attempts per 10 minutes, 8 per day, per IP) before doing anything else |
| 5 | System | Checks that the chosen slot is not in the past and is not already booked |
| 6 | System | Acquires an application level lock for that exact time slot, then re-checks availability again inside a database transaction with a row lock |
| 7a | System | If the slot was taken in the meantime, releases the lock and shows an error, returning the customer to step 2 |
| 7b | System | If the slot is still free, creates the Booking row (status pending) and generates a unique reference (BK-YYYY-NNNNN), then releases the lock |
| 8 | System | Notifies the shop owner of the new booking, with a link to view it in the admin panel |
| 9 | System | If the customer provided an email address, sends BookingConfirmationMail. Guest bookings may not have one, and a failed email never blocks the booking itself |
| 10 | Customer | Views and saves the booking reference on screen (a one-tap copy button is provided) |

Below shows the activity diagram of the booking process.

*[Figure 4.6: Activity Diagram of booking flow, part 1]*

*[Figure 4.7: Activity Diagram of booking flow, part 2]*

**Checkout Flow**

The checkout flow only runs when Shop Mode is switched on by the admin. Unlike booking, checkout requires the customer to be logged in, since an order needs to be tied to an account for order history and tracking.

Placing an order does not immediately take payment. It creates the order with a pending status and a 15 minute countdown, then sends the customer to a separate payment page. Payment then runs in one of two modes, switched at runtime by the owner's Payment Mode setting: a controlled simulation (demo mode), or Stripe Checkout in sandbox (test) mode for card, FPX, and GrabPay, where the customer is redirected to Stripe's hosted payment page and the payment window is stretched to outlive the Stripe session. If a product is out of stock, the order is still allowed to go through, and the stock figure is simply allowed to go negative, representing units owed to the customer.

**Table 4.12: Checkout Steps**

| Step | Actor | Action |
|---|---|---|
| 1 | System | Checks the customer is logged in, has a password set (social login accounts must set one first), and has items in the cart. Missing any of these redirects the customer elsewhere before checkout even starts |
| 2 | System | Pre-fills the customer name, email, phone, and address: the last delivery order wins, and a first-time buyer falls back to the address saved on their profile |
| 3 | Customer | Step 1 of the wizard: chooses how to receive the order, courier delivery or free store pickup. Delivery asks for a full West Malaysia address; pickup removes the address fields entirely (a card shows the showroom address instead) and waives the shipping fee. The choice switches instantly on the page and is re-validated server-side |
| 4 | Customer | Step 2 of the wizard: chooses a payment method (FPX, e-wallet, or card) and places the order |
| 5 | System | Checks the rate limit (max 5 successful orders per hour per account), then locks the cart items and the affected product rows for the duration of the transaction |
| 6 | System | Creates the Order and OrderItem rows (status pending, 15 minute expiry, with the chosen fulfilment method recorded), decrements stock (allowing backorder), and clears the cart |
| 7 | System | Redirects the customer to the payment page, which shows the order summary and a live countdown |
| 8a | Customer | Pays before the timer runs out. In demo mode the system settles on the spot; in Stripe mode the customer completes payment on Stripe's hosted page and the system confirms it through a signature-verified webhook and a server-side re-verification. Both paths funnel into the same idempotent settle step, which atomically flips the order to paid, logs the change to the activity log, and sends OrderConfirmationMail on a best effort basis |
| 8b | System | If the timer runs out first, the order is automatically cancelled, the stock is restored, OrderCancelledMail is sent to the customer, and the owner is notified |
| 9 | Customer | Sees a success or cancellation message. A PDF invoice can be downloaded later from the order page, generated on request rather than automatically |

*[Figure 4.8: Activity Diagram of checkout flow, part 1]*

*[Figure 4.9: Activity Diagram of checkout flow, part 2]*

## 4.3 Database Design

### 4.3.1 Entity Relationship (ER) Diagram (21 models)

The ER diagram is meant to represent the schema exactly as it exists in the database, including how each relationship is structured.

The diagram covers 21 business tables. Nine framework and package managed tables (sessions, cache, jobs, media, notifications, password_reset_tokens, and Filament's imports, exports, failed_import_rows) are left out of the diagram, since they hold no business data and would only add clutter without helping the reader understand the design.

We wrote out the full schema in DBML (Database Markup Language), a simple text format for describing tables, columns, and foreign keys, and generated the diagrams using dbdiagram.io, which lays out each table as a box and draws the relationship lines automatically from the foreign keys we defined. Fitting all the tables into a single diagram made the relationship lines cross over each other and become hard to follow, so the schema is presented across three figures instead of one, grouped by how closely the tables are related to each other.

Figure 4.10 shows the nine tables that stand on their own, with no foreign key linking them to any other table in the system. These cover configuration and content that does not depend on a specific user, order, or booking, such as settings, brands, contacts, feedback, faqs, chatbot_faqs, chat_logs, app_logs, and activity_log. Because none of them reference each other, they are grouped together purely for layout convenience rather than because they form a logical cluster.

*[Figure 4.10: ERD diagram nine standalone]*

Figure 4.11 covers the catalogue and commerce side of the system: categories, products, car_models, product_compatibilities, users, orders, order_items, cart_items, and product_reviews. This is the busiest part of the schema, since almost every purchase-related action eventually touches the products and users tables, whether that is adding an item to a cart, placing an order, recording what was bought in order_items, or leaving a verified-purchase review after delivery. The car_models and product_compatibilities pair stores structured vehicle-fitment data alongside the free-text fitment notes on the product itself.

*[Figure 4.11: ERD diagram Catalogue & Commerce]*

Figure 4.12 covers the people and services side of the system: users, services, bookings, and social_accounts. users appear in both Figure 4.11 and Figure 4.12, since it is referenced from both sides of the system, by orders and carts on the commerce side, and by bookings and linked social login accounts on the services side. Splitting the schema this way keeps each diagram to a handful of tables and a small number of relationship lines, so the connections stay easy to trace by eye.

*[Figure 4.12: ERD diagram People & Services]*

### 4.3.2 Table Descriptions / Data Dictionary

While the ER diagrams in 4.3.1 show how the tables connect to each other, this section lists what each table actually stores. It is meant to be read alongside those diagrams, since a foreign key line only shows that two tables are related, not what information sits inside either one. The Key Columns listed below are not exhaustive for every table, tables with many optional or auto generated fields (such as orders and products, which carry multilingual and lifecycle fields not shown here) are summarised rather than listed column by column.

Before the table below, one term is worth explaining. Several tables use what Laravel calls a soft delete, meaning a row is not actually removed from the database when it is deleted. Instead, a deleted_at timestamp column is filled in, and the system's normal queries automatically skip over any row where this column is set, so the row behaves as if it were gone without the record itself being lost. This keeps a recoverable history for cases such as a customer deleting their account after placing several orders, or an admin needing to check what was removed and when.

**Table 4.13: Table Descriptions**

| Table | Key Columns | Notes |
|---|---|---|
| users | name, email (unique), password (nullable), role, phone, address fields, two_factor_enabled | Soft deletes, default role is client. Password is nullable because social login accounts do not set one until they choose to |
| categories | name, slug, description, image, is_active, sort_order | - |
| products | category_id (FK), name (with _ms/_zh translations), price, sale_price, sku, stock, images (json), specs (json), compatible_vehicles (json), has_3d, model_url | Multilingual fields cover EN/MS/ZH. compatible_vehicles is a simple JSON field for vehicle fitment notes, complemented by the structured car_models/product_compatibilities pair |
| brands | name, logo, display_type, website_url, sort_order | Drives the homepage marquee |
| car_models | brand, model, year_from, year_to | Structured vehicle-fitment reference data |
| product_compatibilities | product_id (FK), car_model_id (FK) | Pivot between products and car models |
| services | name (with _ms/_zh translations), description (with _ms/_zh translations), price, duration_minutes, buffer_after, is_active, sort_order | Managed through an edit-only admin resource: the service menu is a fixed set (no create or delete, by the owner's decision), but names, translations, photos, ordering, and visibility are admin-editable. No price is shown on the storefront; the price column only feeds the chatbot's optional "from RM X" answers |
| bookings | reference, customer name/phone/email, service_id (FK, nullable), user_id (FK, nullable), vehicle_model, vehicle_plate, start_at, end_at, status, reminder_sent_at | Soft deletes, service is optional |
| orders | order_number, user_id (FK), status, payment_status, payment_method, delivery_method, total_amount, expires_at, stripe_session_id, stripe_payment_intent_id, and lifecycle timestamps (paid_at, shipped_at, delivered_at, cancelled_at, refunded_at) | Soft deletes. delivery_method records courier delivery or store pickup (pickup orders carry no shipping address and no fee); the stripe columns tie the order to its hosted Checkout session and confirmed payment |
| order_items | order_id (FK), product_id (FK), product_name, quantity, unit_price, subtotal | product_name is stored at the time of purchase, so the order stays accurate even if the product is later renamed |
| product_reviews | product_id (FK), user_id (FK), rating (1–5), comment, is_approved | One verified-purchase review per customer per product; only submittable after that customer has a delivered order containing the product. is_approved controls storefront visibility |
| cart_items | user_id (nullable), session_id (nullable), product_id (FK), quantity | Auto pruned after 30 days |
| contacts | name, email, phone, subject, message, is_read | Soft deletes |
| feedback | name, location, message, rating, image, is_active, sort_order | Soft deletes, shown as "Testimonials", curated by staff in the admin panel |
| settings | key (PK), value | Key value config, for example ONLINE_SHOPPING_ENABLED and PAYMENT_MODE. All 13 keys are guaranteed to exist by an idempotent migration |
| faqs | category, question_en/ms/zh, answer_en/ms/zh, sort_order, is_active | Public FAQ page, each language stored in its own column |
| chatbot_faqs | topic, keywords (json), priority, reply_en/ms/zh, is_active | Chatbot knowledge base. Higher priority rules win when a message matches more than one entry |
| chat_logs | driver, feature, request_payload (json), response_payload (json), status, error_message, ip_address | Records each chatbot interaction and its outcome, not the conversation text itself |
| social_accounts | user_id (FK), provider, provider_id, provider_email, avatar | Google and Microsoft OAuth links |
| app_logs | level, level_name, message, channel, trace_id, user_id, ip, method, path, context (json), logged_at, resolved_at, fingerprint | Auto pruned. fingerprint groups repeated occurrences of the same underlying error together |
| activity_log | log_name, description, subject_type/subject_id (morph), causer_type/causer_id (morph), event, properties (json) | Spatie audit trail |
| sessions, cache, jobs, media, notifications | - | Framework and package managed tables, not part of the business domain |

Soft deletes are applied to users, orders, bookings, feedback, and contacts, meaning a deleted row is hidden rather than physically removed. Auto pruned tables, cleaned up on a schedule instead of kept forever, are cart_items, chat_logs, and app_logs.

## 4.4 User Interface and Experience Design

### 4.4.1 Color System and Typography

The starting point for this palette was not our own choices. As discussed in section 3.2.1, the shop owner specified red, black, and white as the brand's colours during requirements gathering, based on the signage and branding already used at the physical store. From that starting point, we made the detailed design decisions ourselves, including the exact shades, how those colours behave differently in light and dark mode, and the accessibility engineering behind the button colours explained below. Typography was entirely our own decision, since the owner did not specify any preference there.

Rather than picking colors page by page as the interface was built, we defined a small set of reusable color variables once, in resources/css/app.css and the main layout template, and referenced them everywhere else. We named this system "Ember Carbon" internally. This keeps the storefront and the Filament admin panel visually consistent, since both read from the same variables instead of each having their own separate styling.

The palette itself is a fixed set of named colors, listed below with their roles.

**Table 4.14: Website Colors**

| Name | Hex | Role |
|---|---|---|
| Ember Red | #C8413D | Primary brand color, used for CTAs and focus rings in light mode |
| Ember Dark | #A83432 | Hover state for red elements |
| Ember Light | #E86460 | Accent glow in light mode, becomes the primary brand color in dark mode |
| Carbon Black | #121212 | Page background in dark mode, primary text color in light mode |
| Asphalt | #1C1917 | Card surface in dark mode |
| Deep Slate | #2E2A28 | Secondary surface in dark mode |
| Bone White | #E8E0D8 | Primary text in dark mode, secondary surface in light mode |
| Chalk | #F7F5F3 | Page background in light mode |
| Warm Ash | #8C8480 | Muted or secondary text, the same shade in both light and dark mode |
| Gunmetal | #3A3330 | Border color in dark mode |

What makes this system more than just a static list of colors is that several of the roles above are not tied to one fixed hex value. They are variables that resolve to a different actual color depending on whether the page is in light or dark mode, so the same role always represents "the brand color" or "the page background," even though the underlying value flips underneath it. One consequence worth noting: a panel whose background is brand-red in both themes must not use the theme-aware accent inside itself (in light mode the accent resolves to Ember Light, a light red that vanishes against red), so such panels pin their icons and headings to Bone White directly.

**Table 4.15: Light and Dark Mode Color Roles**

| Role | Light mode value | Dark mode value |
|---|---|---|
| Brand accent | Ember Red | Ember Light |
| Page background | Chalk | Carbon Black |
| Card surface | White | Asphalt |
| Text | Carbon Black | Bone White |
| Border | Bone White tint | Gunmetal |

Typography follows the same idea of a small, fixed set of choices used everywhere rather than mixing fonts page by page.

**Table 4.16: Typography of the Website**

| Role | Font | Notes |
|---|---|---|
| Heading | Anton | Heavy condensed display font, forced uppercase, gives the brand a strong, sporty feel |
| Body text | DM Sans | Plain, easy to read font used for all other interface text, including the admin panel |

Anton only covers Latin characters, headings automatically drop the uppercase transform and letter spacing whenever the page language is not English, falling back to a plain, readable style for Malay and Chinese content instead of risking a broken uppercase rendering on non-Latin scripts.

*[Figure 4.13: Anton Font]*

*[Figure 4.14: DM Sans Font]*

### 4.4.2 Responsive Design Approach

The site needs to work as well on a phone as it does on a desktop monitor, since most customers are expected to browse on their phones before ever visiting the store in person. The layout is designed mobile first, meaning the smallest screen is designed for first, and layout changes are only added on top for wider screens.

**Table 4.17: Responsive Breakpoints**

| Breakpoint | Width | Typical use |
|---|---|---|
| Base | under 640px | Single column layout, hamburger menu |
| Small | 640px and up | 2 column product and testimonial grids |
| Medium | 768px and up | Desktop nav replaces the hamburger menu, admin sidebar becomes fixed |
| Large | 1024px and up | 3 to 4 column grids, full dashboard widget layout |
| Extra large | 1280px and up | Widest layouts, such as the admin dashboard's 12 column widget grid |

The admin panel and the 3D configurator both needed extra consideration beyond page layout. Admin tables were designed to drop less important columns on narrower screens rather than shrink illegibly, and the 3D configurator was designed to support touch gestures such as pinch to zoom and drag to rotate, since many visitors are expected to try it on a phone rather than a desktop. The technical implementation of these behaviours is covered in Chapter 5.

### 4.4.3 Dark / Light Theme System

*[Figure 4.15: Theme System]*

We designed the interface to support both a dark and a light theme, defaulting to whatever the visitor's device is already set to, so the site respects an existing preference rather than forcing one look on everyone.

The technical mechanism behind switching, storing, and applying the theme without a flash of the wrong colour is covered in Chapter 5.

### 4.4.4 Layout and Navigation Priorities

The interface is organised differently for customers and for the shop owner, since each group opens the system with a different goal in mind.

On the customer side, the storefront is built around the WhatsApp-first direction established in Chapter 1. WhatsApp contact buttons appear at key points throughout the site rather than only on a single contact page, so a customer who is ready to enquire is never more than one tap away from doing so. The booking form reflects the same priority in reverse: rather than a single long form, it is broken into four short steps (service, date and time, vehicle details, contact details), and choosing a specific service is optional, since a customer only browsing for a general visit should not be blocked by a required field that does not apply to them. Checkout requires an account, since an order needs to be tied to a customer for order history, but booking does not, keeping the lower-commitment action free of friction. Because the business is fundamentally a walk-in showroom, checkout also opens with a choice between courier delivery and free store pickup, so a customer who prefers to collect in person is never forced through address fields and a shipping fee that do not apply to them. The chatbot follows the same principle: when it cannot answer a question, it hands the customer off to WhatsApp rather than leaving them stuck with no next step.

On the admin side, the navigation is grouped by how often a typical day to day operator, the shop owner, needs each area. Sales, product catalogue, and customer interactions are grouped and left open by default, while more technical areas such as user management and system settings are grouped separately and collapsed by default, so the first thing a non-technical owner sees is what is directly relevant to running the business, not configuration screens they rarely touch. The admin dashboard follows a Z-pattern layout, placing the most important summary figures across the top where a viewer's eyes naturally land first, before the more detailed charts and tables further down the page.

## 4.5 Summary

This chapter set out how the Win Win Car Audio Auto Accessories showroom system is structured before any code was written. Section 4.1 explained how the project adapts the MVC pattern using Livewire components in place of separate Controller classes, and listed the full technology stack behind it. Section 4.2 walked through the system from four different UML perspectives: who can do what (Use Case), how the 21 core models relate to each other (Class Diagram), how a booking and a checkout actually flow step by step (Activity Diagrams). Section 4.3 documented the database itself, a 21 table schema split across three diagrams to keep the relationships readable, along with a full data dictionary explaining what each table stores. Section 4.4 covered the visual and interaction design, including the brand colour system and its accessibility considerations, responsive breakpoints, the dark and light theme mechanism, and the layout priorities behind both the customer-facing storefront and the admin panel. Together, these sections form the design blueprint that Chapter 5 builds from.
