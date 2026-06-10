# Yes2Broker — WordPress to PHP Conversion Roadmap

> **Stack:** Laravel 12 · PHP 8.2 · MySQL · Blade · Tailwind CSS  
> **Local URL:** `http://localhost/yes2broker/public`  
> **Production:** [yes2broker.in](https://yes2broker.in/)

---

## Project Goal

Rebuild [yes2broker.in](https://yes2broker.in/) from WordPress into a custom Laravel PHP application with:

- Full design parity with the current site
- Property listing, search, and detail pages
- Lead capture forms and admin panel
- Better performance, security, and maintainability

---

## Phase 0 — Foundation ✅ (Current)

| Task | Status |
|------|--------|
| Site audit & feature inventory | ✅ Done — see `docs/SITE-AUDIT.md` |
| Laravel 12 project scaffold | ✅ Done |
| XAMPP PHP 8.2 + zip extension | ✅ Done |
| Composer setup | ✅ Done |
| Environment config (`.env`) | ✅ Done |
| MySQL database config | ✅ Configured (create DB manually) |

### Setup commands (run once)

```bash
# Create MySQL database in phpMyAdmin or CLI:
CREATE DATABASE yes2broker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Run migrations
cd c:\xampp\htdocs\yes2broker
C:\xampp\php\php.exe artisan migrate

# Start dev server (optional)
C:\xampp\php\php.exe artisan serve
```

---

## Phase 1 — Database & Data Migration (Week 1–2)

### 1.1 Database Schema

Create migrations for:

```
cities          → Ahmedabad, Gandhinagar
localities      → Motera, Zundal, Science City, Shela, etc.
property_types  → Apartment, Villa, Bungalow, etc.
properties      → Main listing table
property_images → Gallery images
builders        → Partner developers
partners        → Logo partners section
testimonials    → Client reviews
team_members    → About page team
pages           → CMS static content blocks
leads           → All form submissions
newsletter_subscribers
home_loan_banks → ICICI, Union Bank, etc.
settings        → Site-wide config (contact, social links)
```

### 1.2 WordPress Export

- Export MySQL dump from production/staging WordPress
- Download `wp-content/uploads/` media folder
- Identify property custom post type and meta fields
- Map WP fields → Laravel schema

### 1.3 Data Import Command

```bash
php artisan import:wordpress --sql=backup.sql --media=uploads/
```

Build an Artisan command to:
- Import all properties with slugs, prices, meta
- Import images to `storage/app/public/properties/`
- Preserve URL slugs for SEO redirects

---

## Phase 2 — Frontend Layout (Week 2–3)

### 2.1 Base Layout Components

```
resources/views/
├── layouts/
│   └── app.blade.php          # Master layout
├── components/
│   ├── header.blade.php       # Top bar + nav
│   ├── footer.blade.php
│   ├── search-form.blade.php  # Hero search
│   ├── property-card.blade.php
│   ├── consultation-modal.blade.php
│   └── usp-cards.blade.php
```

### 2.2 Design Approach

- Extract CSS/assets from WordPress theme OR rebuild with Tailwind
- Match colors, typography, spacing from live site
- Responsive breakpoints (mobile nav, carousels)
- Reuse partner logos and property images from WP export

### 2.3 Pages to Build

| Page | Route | Controller |
|------|-------|------------|
| Home | `/` | `HomeController@index` |
| About | `/about-us` | `PageController@about` |
| All Properties | `/all-properties` | `PropertyController@index` |
| Property Detail | `/property/{slug}` | `PropertyController@show` |
| Contact | `/contact` | `PageController@contact` |
| List Property | `/list-your-property` | `PageController@listProperty` |
| Channel Partner | `/become-channel-partner` | `PageController@channelPartner` |
| Home Loan | `/home-loan` | `PageController@homeLoan` |

---

## Phase 3 — Property System (Week 3–4)

### 3.1 Property Listing

- [ ] Grid view with cards (image, title, location, BHK, price, possession)
- [ ] AJAX "Load More" pagination
- [ ] Filter by city, locality, type, budget
- [ ] Sort by price, date, trending
- [ ] "New" badge for recent listings
- [ ] Trending section on homepage (featured flag)

### 3.2 Property Detail Page

- [ ] Image gallery / slider
- [ ] Full address, post code, project specs
- [ ] Price range, possession date, project area
- [ ] "Book Appointment" modal (property-specific)
- [ ] Related properties in same locality
- [ ] SEO meta tags per property

### 3.3 Search

- [ ] Hero search form → redirects to filtered `/all-properties`
- [ ] Query string filters: `?city=ahmedabad&type=apartment&budget=10000000`
- [ ] Locality browse page/section

### 3.4 Compare Feature

- [ ] Session-based compare list (max 3–4 properties)
- [ ] Compare bar in footer
- [ ] Compare page with side-by-side specs

---

## Phase 4 — Forms & Leads (Week 4–5)

### 4.1 Form Controllers

| Form | Endpoint | Storage |
|------|----------|---------|
| Consultation | `POST /leads/consultation` | `leads` table |
| Property appointment | `POST /leads/appointment` | `leads` + `property_id` |
| Channel partner | `POST /leads/channel-partner` | `leads` table |
| List property | `POST /leads/list-property` | `leads` table |
| Home loan | `POST /leads/home-loan` | `leads` table |
| Newsletter | `POST /newsletter/subscribe` | `newsletter_subscribers` |

### 4.2 Notifications

- Email to `contact@y2b.in` on new lead
- Optional: WhatsApp/SMS integration later
- Admin dashboard to view/manage leads

### 4.3 Validation & Security

- Laravel Form Requests for each form
- CSRF tokens (built-in)
- Rate limiting (throttle middleware)
- Google reCAPTCHA v3

---

## Phase 5 — Admin Panel (Week 5–6)

### Option A: Laravel Filament (Recommended)
Fast admin UI with CRUD for all models.

### Option B: Custom Admin
Build with Blade + auth middleware.

### Admin Features

- [ ] Dashboard (total properties, leads today, etc.)
- [ ] Property CRUD (create, edit, images, featured/trending flags)
- [ ] Locality & city management
- [ ] Leads inbox (mark read, export CSV)
- [ ] Partner logos upload
- [ ] Testimonials management
- [ ] Team members management
- [ ] Home loan banks CRUD
- [ ] Site settings (contact info, social links)
- [ ] User management (admin roles)

---

## Phase 6 — Home Loan & Calculators (Week 6)

- [ ] EMI calculator (JavaScript, no backend needed)
- [ ] Bank partners section with rates
- [ ] 4-step process section
- [ ] Home loan inquiry form

---

## Phase 7 — SEO & Performance (Week 7)

- [ ] Clean URLs matching current site (`/property/anand-paramount`)
- [ ] 301 redirect map (WordPress URLs → Laravel routes)
- [ ] XML sitemap generation
- [ ] Meta title/description per page and property
- [ ] Open Graph tags for social sharing
- [ ] Image optimization (WebP, lazy loading)
- [ ] Laravel caching (config, routes, views)
- [ ] `robots.txt` and `favicon`

---

## Phase 8 — Auth (Week 7–8)

**Decision needed:** Keep public user registration or admin-only?

| Current WP | Recommended PHP |
|------------|-----------------|
| Public login/register | Admin-only auth (Filament) |
| User accounts | Optional: customer portal in Phase 2 |

If admin-only:
- Laravel Breeze or Filament auth for `/admin`
- Remove public login/register modals from frontend

---

## Phase 9 — Testing & QA (Week 8)

- [ ] Cross-browser testing (Chrome, Firefox, Safari, Edge)
- [ ] Mobile responsive testing
- [ ] Form submission tests
- [ ] Search/filter accuracy
- [ ] All property detail pages load correctly
- [ ] Compare 301 redirects from old URLs
- [ ] Performance audit (Lighthouse)

---

## Phase 10 — Deployment (Week 9)

- [ ] Production server setup (PHP 8.2+, MySQL, SSL)
- [ ] Environment variables on server
- [ ] Database migration on production
- [ ] Media files upload to server
- [ ] DNS cutover from WordPress to new PHP app
- [ ] Post-launch monitoring

---

## Suggested Folder Structure (Laravel)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── PropertyController.php
│   │   ├── PageController.php
│   │   └── LeadController.php
│   └── Requests/
│       ├── ConsultationRequest.php
│       └── ChannelPartnerRequest.php
├── Models/
│   ├── Property.php
│   ├── Locality.php
│   ├── City.php
│   ├── Lead.php
│   └── Partner.php
├── Services/
│   ├── PropertySearchService.php
│   └── LeadNotificationService.php
└── Console/Commands/
    └── ImportWordPressData.php

database/migrations/
├── create_cities_table.php
├── create_localities_table.php
├── create_properties_table.php
└── create_leads_table.php

routes/
├── web.php          # Public routes
└── admin.php        # Admin routes (optional)
```

---

## Timeline Summary

| Phase | Duration | Deliverable |
|-------|----------|-------------|
| 0 — Foundation | ✅ Done | Laravel project + audit |
| 1 — Database | 1–2 weeks | Schema + WP data import |
| 2 — Frontend | 1 week | Layout + static pages |
| 3 — Properties | 1 week | Listing, detail, search |
| 4 — Forms | 1 week | All lead forms working |
| 5 — Admin | 1 week | Property & lead management |
| 6 — Home Loan | 3 days | Calculator + page |
| 7 — SEO | 3 days | Redirects, sitemap, meta |
| 8 — Auth | 3 days | Admin login |
| 9 — QA | 1 week | Testing |
| 10 — Deploy | 3 days | Go live |

**Total estimate: 8–10 weeks** (1 developer, full-time)

---

## Immediate Next Steps

1. **Create MySQL database** `yes2broker` in phpMyAdmin
2. **Export WordPress** — get SQL dump + uploads folder from live site
3. **Run migrations** — `php artisan migrate`
4. **Start Phase 1** — create property-related migrations
5. **Extract design** — screenshot/CSS from WordPress theme for frontend build

---

## Tech Decisions

| Decision | Choice | Reason |
|----------|--------|--------|
| Framework | Laravel 12 | MVC, Eloquent, Blade, ecosystem |
| Database | MySQL | Matches XAMPP + production standard |
| Admin | Filament PHP | Fast CRUD, no custom admin build |
| CSS | Tailwind CSS | Utility-first, fast development |
| Auth | Laravel Breeze/Filament | Admin panel auth |
| Images | Laravel Storage + public disk | Standard file handling |
| Search | Eloquent queries (Phase 1) | Simple; Scout/Algolia later if needed |
