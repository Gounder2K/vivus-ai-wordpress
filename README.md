# Vivus AI — WordPress recreation

A from-scratch rebuild of the **Vivus AI** marketing site (an AI assistant for
clinical guidance and medical workflow support) using a classic PHP/WordPress
stack — no page builders. Built as a portfolio piece to demonstrate custom
theme **and** plugin development, MySQL schema design, secure REST handling,
and a responsive Bootstrap 5.3 + jQuery front end.

> The original product is a Next.js + FastAPI app. This project re-implements
> the public-facing marketing site as a hand-coded WordPress theme, plus a
> custom lead-capture plugin backed by a dedicated MySQL table.

---

## What's in here

```
vivus-ai-wordpress/
├── docker-compose.yml            # MySQL + WordPress + one-shot wp-cli provisioner
├── scripts/provision.sh          # Idempotent setup (install, activate, pages, menu, seed)
├── wp-content/
│   ├── themes/vivus-ai/          # Custom theme (PHP, Bootstrap 5.3, jQuery)
│   │   ├── functions.php         # Asset enqueue, menus, theme supports, lightweight SEO
│   │   ├── front-page.php        # Composed from template-parts/section-*.php
│   │   ├── header.php / footer.php
│   │   ├── page-about.php / page-contact.php / page.php / index.php / 404.php
│   │   ├── inc/                  # template-tags.php, customizer.php
│   │   ├── template-parts/       # hero, features, how-it-works, demo, testimonials, pricing, CTA
│   │   └── assets/               # theme.css, theme.js, chat-demo.js, brand images
│   └── plugins/vivus-leads/      # Custom plugin: lead capture + admin
│       ├── vivus-leads.php       # Bootstrap + activation hook
│       ├── includes/             # activator (schema), db (repo), rest, shortcode, admin
│       ├── uninstall.php         # Drops the table cleanly
│       └── assets/               # form.js, form.css, admin.css
└── .env.example
```

Only the **custom** theme and plugin are tracked in git — WordPress core and
uploads live in Docker volumes.

---

## Quick start

Requires Docker Desktop (or Docker Engine + Compose v2).

```bash
cd vivus-ai-wordpress
cp .env.example .env          # optional — defaults work out of the box
docker compose up -d          # boots MySQL + WordPress, then auto-provisions
```

Then open:

- **Site:** http://localhost:8080
- **Admin:** http://localhost:8080/wp-admin — `admin` / `admin`
- **Leads dashboard:** WP Admin → **Vivus Leads**

The first boot runs `scripts/provision.sh`, which installs WordPress, activates
the theme and plugin, creates the Home/About/Contact pages, builds the primary
nav menu, and seeds two demo leads. It is idempotent, so `docker compose up`
again is safe.

To stop / reset:

```bash
docker compose down            # stop containers (keeps data)
docker compose down -v         # stop + wipe the database/site volumes
```

---

## How it maps to the job's stack

| Requirement | Where it's demonstrated |
|---|---|
| **Clean, efficient PHP** | Namespaced-by-prefix classes, single-responsibility template parts, doc-blocked functions throughout the theme and plugin. |
| **Custom theme (not a page builder)** | `wp-content/themes/vivus-ai` — hand-written templates, custom nav walker, Customizer settings, widgetised footer. |
| **Custom plugin** | `wp-content/plugins/vivus-leads` — activation schema, repository layer, REST controller, shortcode, admin screen, uninstall. |
| **HTML5 / JS / jQuery / Bootstrap 5.3** | Semantic templates; Bootstrap 5.3.3 + Bootstrap Icons enqueued; jQuery (WP-bundled) powers `theme.js`, `chat-demo.js`, `form.js`. |
| **MySQL — schema & efficient queries** | `class-vivus-leads-activator.php` (InnoDB/utf8mb4, indexes on `email`, `status`, `created_at`); all reads/writes via `$wpdb->prepare()` with whitelisted `ORDER BY`. |
| **API integration (REST)** | `POST /wp-json/vivus/v1/leads` — registered route, nonce auth, validation, JSON responses; consumed by `form.js`. |
| **Web security** | REST nonce (CSRF), full sanitisation/validation, honeypot, per-IP rate limiting, prepared statements, capability + nonce checks on every admin action, `DISALLOW_FILE_EDIT`. |
| **SEO fundamentals** | `title-tag` support, meta description / Open Graph output (defers to Yoast/Rank Math if present), clean permalinks, semantic headings. |
| **Git / version control** | This repository, with WordPress core ignored and only custom code tracked. |

---

## The lead-capture flow

1. The Contact page renders `[vivus_contact_form]` (from the plugin).
2. `form.js` posts the form to `POST /wp-json/vivus/v1/leads` with the
   WordPress REST nonce in the `X-WP-Nonce` header.
3. The REST controller verifies the nonce, runs the honeypot + rate-limit
   checks, sanitises and validates every field, then stores the row via the
   `Vivus_Leads_DB` repository (prepared statements only).
4. The site admin gets an email; the submission appears under **Vivus Leads**,
   where it can be filtered, searched, status-changed, deleted, or exported to
   CSV.

## The chat demo

The homepage "Live demo" section is a **mocked** clinical chat (per scope):
`chat-demo.js` matches keywords to scripted, clearly-labelled illustrative
responses with a simulated typing delay. No API key or network call required,
so it works anywhere.

---

## Notes

- Tested against WordPress 6.5, PHP 8.2, MySQL 8.0.
- Theme and plugin are mounted into the container, so edits on disk are live —
  no rebuild needed.
- Brand palette and logo are taken from the original product
  (`#010109` ink, `#c2ebaf` mint, `#c7f8f8` aqua, `#4ea87a` accent green).
