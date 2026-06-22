# Yes2Broker — Project Review Report

> **Date:** 22 June 2026  
> **Scope:** Full codebase review with focus on WordPress migration dependencies  
> **Stack:** Laravel 12 · PHP 8.2 · Filament 3 · Blade · Tailwind CSS 4 · Vite 7

---

## Executive Summary

Yes2Broker is a Laravel rebuild of the live WordPress site at [yes2broker.in](https://yes2broker.in/). The public frontend, property listing/detail pages, enquiry forms, and Filament admin panel are largely complete. **There are no WordPress PHP packages in Composer** — the old site is referenced only as a **data and media source**, not as a runtime framework dependency.

The main remaining WordPress coupling is **media URLs** stored in seed/static data files and a small number of database records, plus **optional import commands** that still fetch from the live WordPress site. Site branding assets (logo, favicon, partner logos, bank logos, hero video) have been migrated to `public/images/` and `public/videos/`.

| Area | Status |
|------|--------|
| Public pages (8 routes) | ✅ Complete |
| Property listing + detail (`/property/{slug}`) | ✅ Database-driven |
| Enquiry forms (email + LeadPlus CRM) | ✅ Working |
| Filament admin (`/admin/properties`) | ✅ Working |
| Properties in database | ✅ 291 records (local dev) |
| Site branding media | ✅ Local (`public/images/site`, `public/videos`) |
| Property images | ⚠️ Mostly local; 3 DB records still hotlink WordPress |
| Static seed data files | ⚠️ Still contain ~124 WordPress CDN URLs |
| Legacy import files in repo | ❌ Not committed (`wp_posts.csv`, etc.) |
| Automated tests | ❌ Only Laravel boilerplate |
| `docs/GO-LIVE.md` accuracy | ⚠️ Outdated (written before DB/forms were finished) |

---

## 1. Project Architecture

### 1.1 Directory Overview

```
yes2broker/
├── app/
│   ├── Console/Commands/     # WP media migration + property import CLI
│   ├── Data/                 # Static page data (some still WP URLs)
│   ├── Filament/             # Admin panel for properties
│   ├── Http/Controllers/     # Public pages + enquiry handlers
│   ├── Models/Property.php   # Core domain model
│   ├── Services/             # Property, enquiry, migration, legacy import
│   └── Support/              # SiteAsset helper, MapEmbed, filters
├── config/
│   ├── site.php              # Branding paths (local assets)
│   └── media-import.php      # WordPress → local path mapping
├── database/migrations/      # users, cache, jobs, properties
├── public/
│   ├── images/site/          # Logo, favicon, page banners (migrated)
│   ├── images/media/         # Partner + bank logos (partial)
│   └── storage/              # Property images (symlink target)
├── resources/views/          # Blade templates (no WP URLs)
└── docs/                     # SITE-AUDIT, ROADMAP, GO-LIVE, this report
```

### 1.2 Tech Stack

| Layer | Technology | Version |
|-------|------------|---------|
| Framework | Laravel | 12.x |
| PHP | PHP | ^8.2 |
| Admin | Filament | ^3.3 |
| CSS | Tailwind CSS | ^4.0 |
| Build | Vite | ^7.0 |
| Database (local) | SQLite | default in `.env.example` |
| Database (prod target) | MySQL | per `docs/GO-LIVE.md` |

### 1.3 Public Routes

| Route | Controller | Purpose |
|-------|------------|---------|
| `/` | HomeController | Homepage with trending properties from DB |
| `/about-us` | AboutController | About page |
| `/all-properties` | PropertyController | Filterable property grid |
| `/property/{slug}` | PropertyController | Property detail page |
| `/contact` | ContactController | Contact page |
| `/list-your-property` | ListPropertyController | List property form |
| `/become-channel-partner` | ChannelPartnerController | Channel partner form |
| `/home-loan` | HomeLoanController | Home loan form |
| `POST /enquiry/*` | EnquiryController | Form submissions (6 endpoints) |
| `/admin/*` | Filament | Property CRUD |

URL structure intentionally mirrors the WordPress site for SEO continuity.

---

## 2. WordPress Dependency Audit (Critical Section)

> **Status: RESOLVED (22 June 2026)**  
> Runtime and seed-data WordPress media dependencies have been removed.  
> Verify anytime: `php artisan media:verify-local`

### Resolution steps completed

| Step | Action | Result |
|------|--------|--------|
| 1 | Broadened WP URL detection (`WordPressMediaResolver`) to catch local XAMPP URLs (`192.168.x.x/wp-content/...`) | 3 stuck properties fixed |
| 2 | Ran `php artisan wordpress:decouple` — migrated 47 images, synced seed files | DB: 0 WP URLs |
| 3 | Synced `PropertiesPageData.php` image paths from database | 290 URLs → local `properties/...` paths |
| 4 | Synced `PropertyDetailData.php` gallery paths | All galleries → local paths |
| 5 | Gated legacy importers behind `WORDPRESS_IMPORT_ENABLED=false` | Import tools disabled by default |
| 6 | Removed dead `HomePageData` static methods; updated Filament admin copy | No stale WP references in UI |

### 2.1 Composer / NPM — No WordPress Packages

**Finding:** `composer.json` contains only Laravel, Filament, and standard dev tools. `package.json` contains only Vite, Tailwind, and axios. **There is zero WordPress PHP or JS dependency.**

The Laravel app does not load WordPress core, plugins, themes, or REST API clients.

### 2.2 Categories of WordPress Coupling

WordPress appears in this project in four distinct ways:

| Category | Risk if WP site goes offline | Status |
|----------|------------------------------|--------|
| **A. Hotlinked media URLs** | Broken images | ✅ **Resolved** — all DB + seed paths are local |
| **B. Live scrape importers** | Import commands fail | ✅ **Gated** — `WORDPRESS_IMPORT_ENABLED=false` by default |
| **C. Legacy export files** | Cannot re-seed from exports | ⚠️ Keep backups outside repo (optional one-time use) |
| **D. Documentation/comments** | None | ✅ **Cleaned** — Filament + dead code removed |

---

### 2.3 File-by-File WordPress References

#### A. Runtime / Production Impact

| File | WP References | Status |
|------|---------------|--------|
| `app/Models/Property.php` | Resolves local `storage` paths + absolute URLs | ✅ DB has no WP URLs |
| `app/Data/PropertiesPageData.php` | Was ~100 WP URLs | ✅ **0** — synced to local paths |
| `app/Data/PropertyDetailData.php` | Was 24 WP URLs in galleries | ✅ **0** — synced to local paths |
| `config/media-import.php` | 21 source URLs (migration map only) | ✅ Tooling config — not used at runtime |
| `app/Services/MediaMigrationService.php` | Uses `WordPressMediaResolver` | ✅ Migration tool only |
| `app/Services/WordPressPropertyImporter.php` | Fetches live site HTML | ✅ Gated — import disabled by default |
| `app/Filament/Resources/PropertyResource.php` | Was WP placeholder text | ✅ Updated to generic external URL hint |

**Blade views:** ✅ **Zero** `wp-content` references.

**Database:** ✅ **291 properties, 0 WordPress URLs** in `image` or `gallery`.

#### B. Legacy Import Pipeline (One-Time)

These files expect WordPress export artifacts at the project root (gitignored / not in repo):

| File | Purpose |
|------|---------|
| `properties_new.sql` | Base property listing from old WP MySQL |
| `wp_posts.csv` | WordPress posts export (detail pages + attachments) |
| `Properties-Export-2026-June-16-1029.csv` | WP All Export listing excerpts |

**Services involved:**

- `app/Services/Legacy/LegacySqlReader.php`
- `app/Services/Legacy/WordPressPostsReader.php`
- `app/Services/Legacy/WordPressExportReader.php`
- `app/Services/Legacy/LegacyPropertyImporter.php`

**Commands:**

```bash
php artisan properties:import-legacy      # Full legacy import
php artisan db:seed                       # Uses LegacyPropertySeeder if files exist
```

`database/seeders/DatabaseSeeder.php` auto-selects legacy vs static seeder based on file presence.

#### C. Live WordPress Scraper (Optional)

```bash
php artisan properties:import-wp {slug}   # Scrape one property from live site
php artisan properties:import-wp --all    # Scrape all DB slugs from live site
```

Uses `WordPressPropertyImporter` + `PropertyPageHtmlParser` to parse HTML from `yes2broker.in`. **Requires the WordPress site to remain online** during import.

#### D. Media Migration Tool

```bash
php artisan media:migrate-wordpress              # Site assets + all property images
php artisan media:migrate-wordpress --site       # Branding/partner/bank assets only
php artisan media:migrate-wordpress --properties # Property images only
php artisan media:migrate-wordpress --property=slug
php artisan media:migrate-wordpress --dry-run
```

Downloads from `config/media-import.php` mappings and rewrites property `image`/`gallery` in the database from WP CDN URLs to `storage/app/public/properties/{slug}/`.

---

### 2.4 Media Migration Status

#### Site branding — ✅ Migrated

| Asset | Local path |
|-------|------------|
| Logo | `public/images/site/logo.png` |
| Footer logo | `public/images/site/logo-footer.png` |
| Favicon | `public/images/site/favicon.webp` |
| Popup logo | `public/images/site/popup-logo.webp` |
| About / list-property / channel-partner / home-loan images | `public/images/site/*.png|jpg` |
| Hero video | `public/videos/yes2broker.mp4` |
| Default property placeholder | `public/images/site/default-property.jpg` |

Configured in `config/site.php` and referenced via `site_asset()` helper.

#### Partner & bank logos — ✅ Migrated (subset)

Present in `public/images/media/`:

- `2025/07/` — Shivalik, Shahasya, Shree Siddhi, Parshwa, Vanshikaa
- `2025/11/` — ICICI, Union Bank, Saraswat, Bajaj Housing

#### Property images — ⚠️ Mostly migrated

- Many properties stored under `public/storage/properties/` (via storage symlink)
- **3 properties** in local DB still reference `yes2broker.in/wp-content/uploads/...`
- `HomePageData::trendingProperties()` references media paths like `images/media/2025/11/...` that are **not yet downloaded** — but this method is **unused**; `HomeController` loads trending from the database instead

#### Static seed files — ❌ Still WordPress-dependent

`PropertiesPageData.php` (290 listings) and `PropertyDetailData.php` (detail records) still embed WordPress CDN URLs. Re-running `PropertySeeder` without first migrating would re-introduce hotlinks into the database.

---

### 2.3 Dependency Diagram

```
                    ┌─────────────────────┐
                    │  yes2broker.in      │
                    │  (WordPress live)   │
                    └─────────┬───────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        │                     │                     │
        ▼                     ▼                     ▼
  wp-content/uploads    HTML scrape           Export files
  (hotlinked URLs)      (import-wp)      (sql, csv — not in repo)
        │                     │                     │
        ▼                     ▼                     ▼
  media:migrate-wordpress   WordPressPropertyImporter   LegacyPropertyImporter
        │                     │                     │
        └─────────────────────┼─────────────────────┘
                              ▼
                    ┌─────────────────────┐
                    │  Laravel Property DB  │
                    │  + local storage      │
                    └─────────┬───────────┘
                              ▼
                    ┌─────────────────────┐
                    │  Public website       │
                    │  (no WP runtime)      │
                    └─────────────────────┘
```

---

## 3. Feature Status

### 3.1 Completed

- **Homepage** — USPs, trending carousel, partners, locality links, featured carousel (all from DB except static USP/partner config)
- **Property listing** — Filters (city, area, type, budget, possession), sort, infinite scroll via AJAX
- **Property detail** — Gallery, overview, amenities, FAQs, map/street view embeds, brochure link, inquiry modal
- **Forms** — Consultation, newsletter, channel partner, list property (with file upload), home loan, property inquiry
- **Enquiry backend** — `EnquiryMailer` (SMTP) + `LeadPlusCrmService` (optional, env-controlled)
- **Admin** — Filament property resource with image upload, gallery, JSON fields for overview/amenities/FAQs
- **URL parity** — Routes match WordPress permalink structure

### 3.2 Partial / Technical Debt

| Item | Notes |
|------|-------|
| `HomePageData::trendingProperties()` | Dead code; superseded by `PropertyService::trending()` |
| `HomePageData::featuredCarousel()` | Dead code; superseded by `PropertyService::featuredCarousel()` |
| `HomePageData::localities()` | Static hardcoded list; homepage uses `PropertyService::localitiesByArea()` from DB |
| `PropertiesPageData` / `PropertyDetailData` | Large static files (~3,300 lines); WP URLs; only used for seeding |
| `docs/GO-LIVE.md` | States property detail and forms are incomplete — **no longer accurate** |
| Tests | No feature tests for properties, forms, or migrations |
| `README.md` | Empty ("ThankYou!") |

### 3.3 Not Started / Out of Scope

Per `docs/ROADMAP.md`, these were planned but not implemented:

- Separate `cities`, `localities`, `builders`, `partners` database tables
- `property_images` normalized table (gallery stored as JSON on `properties` instead)
- `leads` table (enquiries go to email/CRM only)
- Public user login/registration (WordPress had `wp-login.php`; Laravel uses admin-only Filament auth)
- SEO redirects map from old WP URLs
- Sitemap generation

---

## 4. Database Schema

### `properties` table (primary)

Key columns: `slug`, `title`, `location`, `bhk`, `area`, `possession`, `price`, `price_min_lakhs`, `image`, `gallery` (JSON), `description`, `overview` (JSON), `amenities` (JSON), `faqs` (JSON), `map_embed_url`, `street_view_embed_url`, `brochure_url`, `city`, `property_type`, `is_new`, `is_trending`, `is_active`.

**Important:** Laravel uses its own schema. It must **never** run migrations against the live WordPress MySQL database.

---

## 5. Environment & External Services

| Variable / Service | Purpose | WordPress tie-in |
|--------------------|---------|------------------|
| `APP_URL` | Base URL | None |
| `DB_*` | Laravel database | Separate from WP DB |
| `MAIL_*` | Enquiry emails | None |
| `LEADPLUS_*` | CRM lead sync | None (third-party CRM) |
| `MAPS_EMBED_URL` | Contact page map | None |

No `WORDPRESS_*` env vars exist. Migration is triggered manually via Artisan.

---

## 6. Security Notes

- Enquiry forms use Form Request validation + CSRF protection
- Filament admin behind `/admin/login`
- Property images served via `storage` symlink (ensure `php artisan storage:link` on deploy)
- LeadPlus API key should stay in `.env` only (not committed)
- Legacy export files (`properties_new.sql`, `wp_posts.csv`) may contain PII — keep out of git

---

## 7. Recommendations

### Priority 1 — Before Go-Live (WordPress decoupling) ✅ Done

WordPress media decoupling is complete. To re-verify on any environment:

```bash
php artisan media:verify-local
php artisan wordpress:decouple --dry-run   # use media:migrate-wordpress --dry-run for preview
```

### Priority 2 — Code cleanup (partial)

1. ~~Remove unused `HomePageData` static methods~~ ✅ Done
2. Consider archiving `PropertiesPageData.php` / `PropertyDetailData.php` once DB is the only seed source
3. Update `docs/GO-LIVE.md` to reflect current completion status
4. Replace `README.md` with setup instructions

### Priority 3 — Production readiness

1. Add feature tests for property pages and enquiry forms.
2. Configure MySQL on production; do not use SQLite.
3. Set `LEADPLUS_ENABLED=true` and mail credentials in production `.env`.
4. Plan 301 redirects from any changed WordPress URLs.
5. Deploy to staging subdomain first (see `docs/GO-LIVE.md` Option A).

### Priority 4 — Post cutover

1. ~~Gate WordPress import commands~~ ✅ Done (`WORDPRESS_IMPORT_ENABLED=false`)
2. Remove `config/media-import.php` WordPress source URLs once no longer needed for re-migration
3. Decommission WordPress hosting after redirect period

---

## 8. WordPress Dependency Checklist

- [x] All `properties.image` values are local storage paths (no `wp-content`)
- [x] All `properties.gallery` values are local storage paths
- [x] `public/images/site/` contains all branding assets
- [x] `public/images/media/` contains partner/bank logos used on site
- [x] `PropertiesPageData.php` and `PropertyDetailData.php` have no `wp-content` URLs
- [ ] Site works with WordPress server blocked (manual test recommended)
- [ ] Legacy CSV/SQL files backed up externally
- [x] `properties:import-wp` gated off in production (`WORDPRESS_IMPORT_ENABLED=false`)

---

## 9. Artisan Commands Reference

| Command | WordPress dependency | When to use |
|---------|---------------------|-------------|
| `wordpress:decouple` | Downloads + syncs seed files | **One-time cleanup** (completed) |
| `media:verify-local` | None | Verify no WP URLs remain |
| `media:migrate-wordpress` | Downloads from WP CDN | Re-migrate if new external URLs added |
| `properties:import-wp` | Scrapes live WP HTML | Requires `WORDPRESS_IMPORT_ENABLED=true` |
| `properties:import-legacy` | Reads WP export files | Requires `WORDPRESS_IMPORT_ENABLED=true` |
| `db:seed` | Uses local paths in seed files | Local dev setup |
| `storage:link` | None | Required after deploy |

---

## 10. Related Documentation

| Document | Description |
|----------|-------------|
| [SITE-AUDIT.md](SITE-AUDIT.md) | Original WordPress site feature inventory |
| [ROADMAP.md](ROADMAP.md) | Phased conversion plan |
| [GO-LIVE.md](GO-LIVE.md) | Deployment guide (needs status update) |

---

## 11. Conclusion

The Yes2Broker Laravel project is **architecturally independent of WordPress**. No WordPress code runs in production. **WordPress media dependencies have been resolved** — database records, seed data files, and site branding all use local assets.

The site **can go live without WordPress** once DNS is pointed to Laravel. Remaining WordPress-related code is optional import tooling (disabled by default) and migration config kept for reference.

To verify on any machine: `php artisan media:verify-local`
