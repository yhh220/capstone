# Appendix: User Manual

This manual explains how to use the Win Win Car Audio Auto Accessories Showroom
System. It is written for two audiences: **customers** who browse and order
through the public website, and the **shop owner and staff** who run the business
through the admin panel. No technical knowledge is assumed.

The system can be reached either at its **hosted address**
(https://winwinautoaccessories.onrender.com) or, when it is run **locally** for
evaluation, at a local address such as **http://localhost:8000** (or the `.test`
address configured on the evaluator's machine). This manual refers to pages by
**name** rather than by full URL, so the steps apply the same way whether the
system is running online or locally. The admin panel is always at the same
address followed by **/admin**.

---

## Part 1 — Customer Guide

### 1.1 Browsing Products
1. Open the website and click **Products** in the top menu.
2. Use the **search box** to find a product by name, or pick a **category** from the sidebar (e.g. Speakers, Dashcams, Tinting).
3. If online shopping is on, you can also filter by **price range**.
4. Click any product to open its detail page, with photos, description, specifications, and vehicle-fitment notes.

*[Screenshot: the Products page with search, category filter, and product grid.]*

### 1.2 The 3D Car Configurator
1. On the Products page, click the **"Try Our 3D Configurator"** banner at the top.
2. Wait for the 3D showroom to load. **Drag** to rotate the car and **pinch / scroll** to zoom.
3. Use the side panel to change the **colour, rims, spoiler, bumper, window tint, brake colour** and more; the preview updates live.
4. Click **Enquire on WhatsApp** to send your chosen build to the shop for a quote.
5. Press **Esc** or the **X** button to close.

*[Screenshot: the 3D configurator with the car preview and the customisation panel.]*

### 1.3 Registering and Logging In
1. Click the **account icon** in the top-right, then **Log in / Register**.
2. To register with email: enter your details, then type the **6-digit code** emailed to you to verify your account.
3. Alternatively, click **Continue with Google** to sign in with a Google account.
4. (Optional) In your profile you can turn on **Two-Factor Authentication (2FA)** for extra security — you will then enter a one-time code each time you log in.

*[Screenshot: the login / register page.]*

### 1.4 Placing an Order
> Ordering online is only available when the shop has **online shopping switched on**. When it is off, the site is a showroom and you are directed to WhatsApp / the store instead.

1. On a product, choose the quantity and click **Add to Cart**. The cart icon shows the item count.
2. Click the **cart icon** → review items → **Checkout**. You must be logged in to check out.
3. **Step 1 – Delivery:** choose how to receive your order:
   - **Courier Delivery** — enter your address (West Malaysia only); a shipping fee applies below the free-shipping threshold.
   - **Store Pickup** — free; no address needed, you collect at the showroom.
4. **Step 2 – Payment:** choose a payment method and click **Place Order**.
5. Your order is created with a **15-minute countdown** to pay.

*[Screenshot: the checkout page showing the Delivery / Pickup choice.]*

### 1.5 Paying for an Order
1. On the payment page you see the order summary and the countdown timer.
2. Click **Pay** — depending on the shop's setting, you either pay through **Stripe's secure page** (card / FPX / GrabPay) or a **simulated** confirmation.
3. When payment succeeds, the order becomes **Processing** and you receive a **confirmation email**.
4. If the timer runs out before you pay, the order is **automatically cancelled** and the stock released — just order again.

*[Screenshot: the payment page with the countdown and Pay button.]*

### 1.6 Tracking an Order
1. Click **Track Order** in the menu (no login needed).
2. Enter your **order number** and the **email** used on the order.
3. You see a timeline: Order Placed → Processing → **Shipped / Ready for Pickup** → **Delivered / Collected**.

*[Screenshot: the order tracking timeline.]*

### 1.7 My Account
1. Log in and open **My Account**.
2. **Orders tab:** see all your orders, pay for unpaid ones, download invoices, and **copy** an order or tracking number with the copy button.
3. **Bookings tab:** see your service bookings and cancel upcoming ones.
4. You can **cancel an order** here before it ships; the refund amount (if any) is shown before you confirm.

*[Screenshot: My Account with the orders list and copy buttons.]*

### 1.8 Reviewing a Product You Bought
> You can review a product **only after you have bought it and the order has been delivered** — a "verified purchase". This keeps reviews genuine and blocks anonymous spam.

1. Open the product's page, or in **My Account → Orders** click **Write a review** next to an item from a delivered order.
2. Choose a **star rating (1–5)** and write your review (at least 10 characters).
3. Submit — your review appears on the product page immediately and counts toward the product's average rating.
4. You can update your own review later. Staff/administrators can hide a review from the storefront if necessary.

*[Screenshot: the product review form and the review list on a product page.]*

### 1.9 Booking a Service
1. Click **Booking** in the menu.
2. **Step 1:** choose a service (or leave it as a general visit).
3. **Step 2:** pick a date and time. Past dates and closed days are disabled.
4. **Step 3:** enter your vehicle model and plate.
5. **Step 4:** enter your name, phone, email, and any notes, then submit.
6. **Save the booking reference** shown on the success screen (use the copy button).

*[Screenshot: the four-step booking wizard.]*

### 1.10 Tracking or Cancelling a Booking
1. Click **Track Booking** (or the link on the booking success screen).
2. Enter your **booking reference** and **phone number**.
3. From there you can view the status or cancel an upcoming booking.

*[Screenshot: the booking tracker.]*

### 1.11 Contacting the Shop
- **Contact form:** the Contact page has a form for enquiries.
- **WhatsApp:** WhatsApp buttons appear throughout the site for instant questions.
- **Map & directions:** the Contact page shows the showroom on a map with a "Get directions" link.
- **Chatbot:** the floating chat button (bottom-right) answers common questions about products, services, prices, opening hours, and location in your chosen language; when it cannot answer, it hands you to WhatsApp.

*[Screenshot: the Contact page and the chatbot widget.]*

### 1.12 Language and Dark Mode
- **Language:** use the **EN / BM / ZH** switcher in the top bar to view the whole site in English, Bahasa Melayu, or Chinese.
- **Dark mode:** use the theme switcher to choose light, dark, or match-your-device.

*[Screenshot: the language switcher and theme toggle.]*

---

## Part 2 — Administrator Guide

The admin panel is used by the **owner** and **staff** to run the business. Access levels differ by role (see Section 2.14).

### 2.1 Logging In to the Admin Panel
1. Go to **/admin**.
2. Enter your admin email and password.
3. If 2FA is enabled, enter the one-time code.

*[Screenshot: the admin login page.]*

### 2.2 Dashboard
After logging in you see the **dashboard**: key figures at the top (active products, bookings, orders, revenue), followed by charts (revenue trend, category distribution, top products) and a recent-orders table. The layout adapts to phone and tablet screens.

*[Screenshot: the admin dashboard.]*

### 2.3 Managing Products
1. Open **Store Products → Products**.
2. Click **New product** to add one, or a row to edit it.
3. Fill in the name (and Malay / Chinese translations), category, brand, price, sale price, stock, SKU, specifications, and upload photos.
4. Use the **Active** toggle to show/hide it in the store, and **Show on Homepage** to feature it.
5. **Import / Export:** use the buttons on the list to import products from a spreadsheet or export the catalogue (export is admin-only).

*[Screenshot: the product edit form.]*

### 2.4 Managing Categories and Brands
- **Categories** and **Brands** each have their own list under **Store Products**.
- Add, edit, reorder (drag rows), and toggle visibility. Brands drive the homepage brand marquee.

*[Screenshot: the categories and brands lists.]*

### 2.5 Managing Services
1. Open **Sales → Services**.
2. The service menu is a **fixed set** — you can **edit** each service but not add or delete.
3. Edit the name and description (in all three languages), upload a photo, drag to reorder how they appear on the Services page, and use the visibility toggle to hide one temporarily.
4. No price is shown on the website; the optional price field is only used by the chatbot when a customer asks about pricing.

*[Screenshot: the Services list and edit form.]*

### 2.6 Processing Orders
1. Open **Sales → Orders**. The list shows status, payment, fulfilment (Delivery / Pickup), and the refund a cancellation would give.
2. Open an order and use the action buttons:
   - **Mark Paid** — for a manually reconciled payment (e.g. bank transfer); emails the customer.
   - **Mark Shipped / Ready for Pickup** — delivery orders ask for a tracking number; pickup orders just email the customer to come and collect.
   - **Mark Delivered** — confirms receipt.
   - **Cancel & Restock** — cancels the order, returns stock, records the refund, and emails both sides.
   - **Mark Refund Sent** — confirms a recorded refund was actually paid out.
   - **Invoice** — opens a printable / downloadable invoice.

*[Screenshot: an order with its action buttons.]*

### 2.7 Managing Bookings
1. Open **Sales → Bookings**.
2. **Confirm** a pending booking (emails the customer), **edit / reschedule** it (closed-day and clash checks apply), and use **Send Reminder** to email the customer manually. A reminder is also sent automatically the day before.

*[Screenshot: the bookings list with the Confirm action.]*

### 2.8 Customers, Feedback, and Contact Messages
- **Customers:** view registered customers and their order counts.
- **Feedback (Testimonials):** approve/hide customer testimonials shown on the homepage; drag to reorder.
- **Product Reviews:** inspect verified-purchase product reviews left by customers, edit or delete them, and toggle a review's visibility on the storefront. (This is separate from Feedback — testimonials are marketing quotes; product reviews are tied to a specific product and a real delivered order.)
- **Contacts:** read enquiries submitted through the contact form; unread ones show a badge.

*[Screenshot: the feedback and contacts lists.]*

### 2.9 FAQ and Chatbot Knowledge Base
- **FAQs** drive the public FAQ page; each entry has a question and answer in all three languages, a category, and a published toggle.
- **Chatbot FAQs** power the chat widget; each rule has keywords, a priority, and per-language answers. Higher priority wins when several rules match.

*[Screenshot: the FAQ and Chatbot FAQ forms.]*

### 2.10 Store Settings
Open **System Settings → Settings** to adjust, with no code change:
- Business opening/closing times and closed weekdays (these drive the booking calendar and the hours shown site-wide)
- Appointment slot length, backorder lead time
- Shipping flat rate and free-shipping threshold
- Cancellation full-refund window and fee
- Payment mode (simulated "demo" or Stripe test mode)
- Site announcement bar text and on/off
Each setting is validated and carries plain-language help text; boolean settings have a one-click toggle.

*[Screenshot: the Settings list.]*

### 2.11 Shop Mode (Online Shopping On/Off)
1. In Settings, set **Online Shopping Mode** to **true** (full e-commerce) or **false** (showroom only).
2. Turning it **off** automatically cancels and restocks all unpaid orders and emails those customers — remember to turn on the **announcement bar** so customers know shopping is paused.

*[Screenshot: the Online Shopping Mode setting and its help text.]*

### 2.12 User and Role Management
1. Open **System Settings → Users** (admin/owner only).
2. Create staff or admin accounts and manage them. Only the **owner** can assign roles.

*[Screenshot: the Users list.]*

### 2.13 System Status, Activity Log, Error Log
- **System Status:** a plain-language health check (database, cache, payments, email, disk, scheduler). Green = fine, amber = watch, red = fix.
- **Activity Log:** an audit trail of significant changes and who made them.
- **Log:** grouped application errors for monitoring.

*[Screenshot: the System Status page.]*

### 2.14 Roles and Permissions (Who Can Do What)
| Action | Staff | Admin | Owner |
|---|---|---|---|
| View products / orders / bookings | ✓ | ✓ | ✓ |
| Process orders & bookings (mark paid/shipped/ready/delivered, cancel, refund, confirm, reschedule) | ✓ | ✓ | ✓ |
| Create / edit products, curate testimonials, import data | ✓ | ✓ | ✓ |
| Delete records, export data | ✗ | ✓ | ✓ |
| Manage categories, brands, services, FAQs, chatbot, settings | ✗ | ✓ | ✓ |
| Manage staff accounts | ✗ | ✓ | ✓ |
| Assign roles, administer admins, permanently delete | ✗ | ✗ | ✓ |

---

*Note: replace every [Screenshot: …] placeholder with the actual screenshot before submission.*
