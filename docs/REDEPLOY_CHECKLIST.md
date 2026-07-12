# Re-hosting Checklist — new Render account

Moving the live site to a new Render account (`winwinautoaccessories.onrender.com`)
while the old account (`winwincaraudio.onrender.com`, printed on physical QR codes)
stays alive as a **redirect stub**. Data is untouched — it lives in TiDB Cloud, not
on Render, so both deployments connect to the **same database**.

```
Scan printed QR
  -> winwincaraudio.onrender.com   (OLD account · redirect stub · REDIRECT_TO set)
       -> 302, ~0.5KB, preserves path+query
  -> winwinautoaccessories.onrender.com  (NEW account · real site · new 5GB)
       -> TiDB Cloud (unchanged · all data intact)
```

---

## Step 1 — NEW account: deploy the real site (do this first)

1. New Render account (the one with the working card) → **New Web Service** → connect
   the **same GitHub repo**. Docker build; the entrypoint runs `migrate` automatically.
2. **Service name:** `winwinautoaccessories` → gives `winwinautoaccessories.onrender.com`.
3. **Environment variables:** copy every var from the old service, with two changed:
   - `APP_URL=https://winwinautoaccessories.onrender.com`
   - `STORE_URL=https://winwinautoaccessories.onrender.com`
   - **Do NOT set `REDIRECT_TO`** here — the real site must not redirect.
   - Everything else copied as-is: `APP_KEY`, `APP_ENV`, `APP_DEBUG`, `APP_VERSION`,
     all `DB_*` + `MYSQL_ATTR_SSL_CA`, `CACHE_STORE`, `SESSION_DRIVER`,
     `SESSION_SECURE_COOKIE`, all `MAIL_*` + `GMAIL_SEND_REFRESH_TOKEN` +
     `GOOGLE_CLIENT_ID/SECRET`, all `STRIPE_*`, `CRON_SECRET`, `STORE_ALERT_EMAIL`.
4. **TiDB access:** in the TiDB Cloud console, confirm the network allowlist permits
   the new Render egress IPs — simplest is allow-all `0.0.0.0/0`. If it was already
   allow-all, nothing to do. (Same DB = all products/orders/settings already there.)
5. Deploy, then open `https://winwinautoaccessories.onrender.com` and confirm the
   homepage loads and the admin panel logs in. **Get this green before touching the
   old account.**

## Step 2 — OLD account: turn it into a redirect stub (only after Step 1 works)

Do **not** delete the old service (that would release the `winwincaraudio` name,
which the printed QR depends on, and it may not come back).

1. **Resume** the suspended old Web Service.
2. Add ONE environment variable:
   - `REDIRECT_TO=https://winwinautoaccessories.onrender.com`
3. Redeploy. Now every request to `winwincaraudio.onrender.com` answers with a tiny
   302 to the new host (path + query preserved), booting none of the app.
4. **Verify:** open `https://winwincaraudio.onrender.com` — it should jump straight
   to the new site. Scan the printed QR to double-check.

Bandwidth on the stub is a non-issue: each redirect is ~0.5KB, so the remaining
~0.92GB covers ~1.8 million redirects.

## Step 3 — Two cron jobs (cron-job.org)

The old cron used to run the scheduled tasks; now the stub only redirects, so tasks
must run on the NEW site, and the old cron is repurposed to keep the stub awake.

| Cron | Target URL | Purpose |
|---|---|---|
| **NEW site cron** (create) | `https://winwinautoaccessories.onrender.com/cron/run-schedule/<CRON_SECRET>` | Runs order-expiry / booking reminders **and** keeps the new site awake |
| **OLD stub cron** (keep/repoint) | `https://winwincaraudio.onrender.com/` | Just wakes the stub so a scanned QR never hits a cold start |

`CRON_SECRET` is unchanged (copied in Step 1), so the new cron uses the same token.
Both Free services sleep after ~15 min idle; these pings keep them warm. Cron pings
are ~0.5KB each — negligible bandwidth.

## Step 4 — Point integrations at the new domain (only what you'll demo)

- **Stripe webhook** — Stripe Dashboard (test mode) → Webhooks → change the endpoint
  URL to `https://winwinautoaccessories.onrender.com/stripe/webhook`, copy the new
  signing secret into the new service's `STRIPE_WEBHOOK_SECRET`. *(Skip if demoing
  demo-mode payments only.)*
- **Google OAuth** — Google Cloud Console → Credentials → add the new callbacks to
  Authorized redirect URIs: `/auth/google/callback`, `/auth/microsoft/callback`,
  `/gmail-send/callback` on the new domain. The refresh token itself is unchanged.
  *(Skip if not demoing social login / live email.)*

## Step 5 — After the presentation (no rush)

- `public/robots.txt` — update the `Sitemap:` line to the new domain; re-run
  `php artisan sitemap:generate`.
- **Google Search Console** — add the new domain as a property, verify, submit the
  new sitemap. (Pure SEO; irrelevant to the demo.)
- Report chapters + `SYSTEM_AUDIT.md` — swap `winwincaraudio.onrender.com` for the
  new domain (or keep the old as the canonical brand URL, your call).
- If you want the stub to never cold-start for the public long-term, convert the old
  service to a Render **Static Site** with a redirect rule — but only when losing the
  `winwincaraudio` name no longer matters (the QR demo is already done by then).

## Presentation-day note

Both Free services sleep after 15 min idle. A few minutes before presenting, open the
new site once (and scan the QR once) to warm both. With the Step 3 crons in place they
stay warm on their own.
