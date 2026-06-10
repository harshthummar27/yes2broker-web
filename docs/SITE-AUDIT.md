# Yes2Broker — WordPress Site Audit

> Source: [yes2broker.in](https://yes2broker.in/)  
> Audit date: June 10, 2026  
> Purpose: Map existing WordPress site for PHP (Laravel) conversion

---

## 1. Business Overview

**Yes2Broker** is a real estate brokerage based in Ahmedabad, Gujarat. Core value propositions:

| USP | Description |
|-----|-------------|
| ₹1,00,000 Cashback | Cashback when property is registered under a woman's name |
| Lowest Price Guarantee | Competitive pricing, no hidden charges |
| Dedicated Relationship Manager | End-to-end buying support |

**Contact**
- Email: contact@y2b.in
- Phone: +91 95125 98980
- Address: 401 Amrakunj Avis, Nigam Nagar, Near Tapovan Circle, Chandkheda, Ahmedabad, Gujarat 382424

**Social:** Facebook, Instagram, LinkedIn

---

## 2. Site Map & Pages

| Page | URL | Type | Priority |
|------|-----|------|----------|
| Home | `/` | Dynamic | P0 |
| About Us | `/about-us/` | Static + sections | P0 |
| All Properties | `/all-properties/` | Dynamic listing | P0 |
| Property Detail | `/property/{slug}/` | Dynamic single | P0 |
| List Your Property | `/list-your-property/` | Form page | P1 |
| Become Channel Partner | `/become-channel-partner/` | Form page | P1 |
| Home Loan | `/home-loan/` | Static + calculator + form | P1 |
| Contact | `/contact/` | Static + form | P0 |
| Login | Modal / `/wp-login.php` | Auth | P2 |
| Register | Modal | Auth | P2 |
| Forgot Password | Modal | Auth | P2 |

---

## 3. Homepage Sections (Top → Bottom)

1. **Top bar** — Email, phone, promo banners (cashback, lowest price, dedicated manager)
2. **Header / Navigation** — Logo, main menu, social links, "Inquire Now" CTA
3. **Hero search form** — City, Area/Project, Type, Budget filters + Search button
4. **USP cards** — Cashback, Lowest Price, Dedicated Manager
5. **Trending Properties** — Carousel/grid of featured projects (12+ on homepage)
6. **About Company** — Welcome text + bullet points + "Explore More"
7. **Popular Locations** — Location chips/links
8. **Partner logos** — Builder/developer logos (Shivalik, Shahasya, etc.)
9. **Testimonials** — Client reviews slider
10. **Localities grid** — Multi-column locality → property links (Ambali, Science City, etc.)
11. **Free Consultation form** — First name, last name, phone, email, property type, message
12. **Footer** — Quick links, top properties, contact, copyright, login/register

---

## 4. Property Data Model (from live site)

Each property typically includes:

| Field | Example |
|-------|---------|
| Title | Anand Paramount |
| Slug | `anand-paramount` |
| Address | Opp. S Mall, Near Uma Party Plot, Motera, Ahmedabad |
| Post Code | 380005 |
| City | Ahmedabad / Gandhinagar |
| Locality / Area | Motera, Zundal, Science City, Shela, etc. |
| Property Type | Apartment, Villa, Bungalow, Office, Showroom, Shop, FarmHouse, Land |
| BHK | 2 BHK, 3 & 4 BHK, etc. |
| Project Area | 1.43 Acres |
| Possession Date | December 2026 / Ready to Move |
| Price Range | ₹1.23 Cr – ₹1.98 Cr |
| Price Min (filter) | Numeric for search |
| Is New | Badge flag |
| Is Trending | Homepage featured flag |
| Images | Gallery (from WP media) |
| Description | Full project details (detail page) |
| Builder / Developer | Optional |
| Status | Active / Inactive |

**Estimated property count:** 100+ (30 per page with "Load More" AJAX pagination)

---

## 5. Search & Filter System

### Homepage hero search
- **City:** Ahmedabad, Gandhinagar
- **Area / Project:** Text or dropdown (dynamic from DB)
- **Type:** Apartment, Villa, Home, Bungalow, Office, Showroom, Shop, FarmHouse, Land
- **Budget:** ₹50 Lac → ₹10 Cr+

### All Properties page
- Grid layout with property cards
- AJAX "Load More Properties" (infinite scroll / pagination)
- Same filter criteria as hero search

### Locality browsing
- Grouped by locality names (Ambali, Science City, etc.)
- Each locality lists linked properties

---

## 6. Forms & Lead Capture

| Form | Location | Fields |
|------|----------|--------|
| Free Consultation | Home, Contact, Footer modal | first_name, last_name, phone, email, looking_for (dropdown), message |
| Property Appointment | Per-property modal | first_name, last_name, phone, email, message (+ property_id) |
| Channel Partner | `/become-channel-partner/` | name, email, mobile, city, address, company_name, gst_number, remark |
| List Your Property | `/list-your-property/` | (TBD — page timed out; likely seller listing form) |
| Home Loan Inquiry | `/home-loan/` | Online form + bank partner CTAs |
| Newsletter | About page | Email subscription |

**Form handling requirements:**
- Store leads in database
- Email notification to admin
- Optional CRM integration later
- CSRF protection, validation, spam protection (reCAPTCHA)

---

## 7. Home Loan Page Features

- Hero + process steps (4-step "How it works")
- Bank partners: ICICI, Union Bank, Saraswat, Bajaj Housing Finance (with rates)
- **EMI Calculator** — Loan amount (₹1L–₹5Cr), tenure (1–30 yrs), interest rate (0.5%–15%)
- Lead capture form

---

## 8. About Us Page Sections

- Company intro + USP cards
- Newsletter signup
- Services: Buying, Selling, Consultation
- How it works (3 steps)
- Popular locations
- Partner logos
- Team members (consultants/agents)

---

## 9. Interactive Features

| Feature | Description |
|---------|-------------|
| Property Compare | Side-by-side comparison (footer "Compare" widget) |
| Load More | AJAX property loading on listing pages |
| Appointment Modals | Per-property booking modals |
| Login / Register | WordPress user auth (may simplify to admin-only in PHP version) |
| EMI Calculator | Client-side JS on Home Loan page |
| Search | Filter-based property search |

---

## 10. WordPress → PHP Migration Assets Needed

Before development, export from WordPress:

- [ ] **Database dump** (`.sql`) — posts, postmeta, terms, users
- [ ] **Media uploads** — `wp-content/uploads/` (property images, logos, banners)
- [ ] **Plugin inventory** — property plugin name (likely Houzez, WP Residence, or custom CPT)
- [ ] **Theme files** — for CSS/design reference
- [ ] **URL redirect map** — old URLs → new Laravel routes (SEO)
- [ ] **Form submission history** — if stored in WP DB or plugin

---

## 11. Technical Observations (WordPress)

- Custom post type for **Properties** (`/property/{slug}/` URL pattern)
- Likely page builder (Elementor-style sections)
- AJAX pagination for property listings
- Multiple duplicate nav menus (desktop/mobile)
- Modal-based forms (consultation + per-property appointments)
- User authentication via WordPress core
- Possible SEO plugin (Yoast/RankMath) — preserve meta titles/descriptions

---

## 12. Non-Functional Requirements

| Requirement | Target |
|-------------|--------|
| Performance | Page load < 3s, lazy-load images |
| SEO | Clean URLs, meta tags, sitemap, 301 redirects |
| Mobile | Fully responsive (current site is mobile-friendly) |
| Security | CSRF, XSS prevention, rate limiting on forms |
| Admin | CMS for properties, pages, leads, partners |
| Hosting | XAMPP local → production PHP/MySQL server |

---

## 13. Out of Scope (Phase 1)

- Multi-language support
- Payment gateway
- Full CRM
- Mobile app
- Blog (unless needed — not prominent on current site)
