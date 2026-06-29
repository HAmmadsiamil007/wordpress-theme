# SoleOrigine Theme Architecture

**Last updated:** 2026-06-29

---

## Overview

SoleOrigine is a luxury dark-themed WooCommerce WordPress theme for an Italian footwear brand. Premium aesthetic with gold accents, serif headings, and GSAP-powered scroll animations.

---

## Design Tokens

Defined in `style.css` `:root`:

| Token | Value | Usage |
|---|---|---|
| `--color-primary-dark` | `#1a1a1a` | Page bg, dark sections |
| `--color-secondary-dark` | `#111` | Cards, dropdowns, overlays |
| `--color-accent` | `#b8860b` | Buttons, highlights |
| `--color-accent-hover` | `#d4a843` | Hover states |
| `--color-gold` | `#c9a96e` | Headings, borders |
| `--color-light-gold` | `#e8d5a3` | Subtle gold |
| `--color-text` | `#f5f5f5` | Body text |
| `--color-text-muted` | `#999` | Secondary text |
| `--color-border` | `#333` | Borders, dividers |
| `--font-heading` | `Playfair Display` | H1–H6 |
| `--font-body` | `Inter` | Body, nav, buttons |

---

## File Structure (Theme)

```
soleorigine-theme/
├── style.css                     # Theme metadata + 1484-line CSS (design tokens, BEM)
├── screenshot.png                # 1200×900 customizer preview
├── functions.php                 # Constants, class requires, legacy flat requires
│
├── header.php                    # Opening <html>, <head>, <body>, #page wrap
├── footer.php                    # Closing #page, wp_footer(), </body></html>
├── index.php                     # Fallback template
├── front-page.php                # Static front page (hero, features, about, products)
├── home.php                      # Blog index
├── page.php                      # Default page
├── single.php                    # Single post
├── archive.php                   # Archive pages
├── search.php                    # Search results
├── 404.php                       # Not found
├── sidebar.php                   # Widget area sidebar
├── comments.php                  # Comment template
├── searchform.php                # Search form
│
├── inc/                          # Core includes
│   ├── class-soleorigine-after-setup.php    # Singleton: theme setup, CPTs, taxonomies
│   ├── class-soleorigine-enqueue.php        # Singleton: scripts/styles/Vite/GSAP
│   ├── theme-setup.php                      # Legacy flat (guarded, class-active = skip)
│   ├── enqueue.php                          # Legacy flat (guarded, class-active = skip)
│   ├── vite.php                             # Vite helpers (loaded by enqueue.php)
│   ├── customizer.php                       # ~14KB raw Customizer (flat, not yet migrated)
│   ├── template-tags.php                    # Template helper functions (flat)
│   ├── template-functions.php               # More template functions (flat, has duplicate pingback)
│   └── widgets.php                          # 3 custom widgets (flat)
│
├── template-parts/               # Reusable template fragments
│   ├── content.php               # Generic content loop
│   ├── product-card.php          # WooCommerce product card
│   ├── footer-widgets.php        # Footer widget columns
│   ├── hero.php                  # Front page hero section
│   ├── features.php              # Front page features
│   └── about.php                 # Front page about section
│
├── page-templates/               # Custom page templates
│   ├── template-about.php
│   ├── template-contact.php
│   ├── template-gallery.php
│   └── template-full-width.php
│
├── css/                          # Additional stylesheets
│   ├── woocommerce.css           # WooCommerce overrides (dark theme)
│   ├── responsive.css            # Breakpoints: 992px, 576px, 400px
│   └── admin.css                 # WordPress admin styling
│
├── js/                           # JavaScript
│   ├── navigation.js             # Mobile menu toggle
│   ├── customizer.js             # Customizer live preview
│   └── custom.js                 # GSAP 3.12.7 + ScrollTrigger animations
│
├── admin/                        # Admin features
│   └── theme-info.php            # Custom admin info page
│
├── languages/                    # i18n
│   └── soleorigine.pot           # Translation template
│
├── docs/                         # Documentation
│   ├── theme-architecture.md     # This file
│   └── plugin-features.md        # Plugin/WooCommerce documentation
│
├── vite.config.js                # Vite build config
└── package.json                  # Node dependencies
```

---

## CSS Conventions

- **BEM methodology** throughout (`block__element--modifier`)
- CSS custom properties in `:root` (`style.css`)
- No font icon libraries — SVG icons only
- No inline comments in production output
- Prefers GSAP for animations (no CSS animation fallbacks on key UI motion)

### Responsive Breakpoints (responsive.css)
- 992px (tablet)
- 576px (mobile landscape)
- 400px (mobile portrait)

---

## JavaScript & Animations

### GSAP 3.12.7 (CDN)
- **gsap-core**: Core animation engine
- **ScrollTrigger**: Scroll-linked reveals, parallax, pinning
- **ScrollToPlugin**: Smooth anchor scrolling

All enqueued via `SoleOrigine_Enqueue` singleton with `defer` attribute. Custom animations in `js/custom.js`.

### Custom JS
- `navigation.js` — Mobile menu hamburger toggle + dropdowns
- `customizer.js` — Live preview binding for Customizer
- `custom.js` — GSAP ScrollTrigger animations (hero, features, products, counters)

---

## Singleton Architecture (Astra-Inspired)

Migrating from flat `inc/` files to singleton classes. Current state:

| Class | File | Status |
|---|---|---|
| `SoleOrigine_After_Setup` | `inc/class-soleorigine-after-setup.php` | ✅ Complete |
| `SoleOrigine_Enqueue` | `inc/class-soleorigine-enqueue.php` | ✅ Complete |
| `SoleOrigine_Icons` | — | ⬜ Planned |
| `SoleOrigine_Customizer_Config` | — | ⬜ Planned |
| `SoleOrigine_Dynamic_CSS` | — | ⬜ Planned |

Early-return guards on legacy flat files (`if class_exists → return`) prevent double-registration.

---

## Vite Build System

- Dev server: port 5173, detected via `fsockopen` (15s transient cache)
- Production: `npm run build` outputs to `dist/` with manifest
- Vite HMR helpers are private methods on `SoleOrigine_Enqueue`
- Admin CSS also processed through Vite (`css/admin.css`)

---

## Customizer

Raw `inc/customizer.php` (~14KB) with sections:
- **Hero Section** — headline, subtitle, button text/URL, background image
- **Featured Section** — headline, 3 featured items (icon, title, text)
- **About Section** — headline, body text, image
- **Newsletter Section** — headline, placeholder text
- **General Options** — footer text, accent color, logo upload

Planned: Refactor into `inc/class-soleorigine-customizer-config.php` with per-section config files in `inc/customizer/configurations/`.

---

## WooCommerce Integration

See [plugin-features.md](./plugin-features.md) for full details.

Templates overridden at: `woocommerce/archive-product.php`, `single-product.php`, `content-product.php`, `content-single-product.php`, `cart/cart.php`, `checkout/checkout.php`, `myaccount/my-account.php`, plus product type templates `simple.php`, `variable.php`.

---

## Registered Features

- **Post types:** Collections (`collection`), Styles (`style`), Brands (`brand`)
- **Taxonomies:** Collection Category, Style Category, Brand Category
- **Nav menus:** Primary Menu, Footer Menu
- **Widget areas:** Sidebar, Footer 1/2/3
- **WooCommerce support:** Yes (template overrides active)

---

## Known Issues

1. **Duplicate `soleorigine_pingback_header()`** — defined in both `inc/theme-setup.php` AND `inc/template-functions.php`. Will cause fatal error if both load without guards. Currently mitigated by early-return in `theme-setup.php` when class is active. Fix: remove from `template-functions.php` (Step 11 in migration plan).

---

## Next Steps (Migration Plan)

| Step | Task | Status |
|------|------|--------|
| 1 | `inc/class-soleorigine-after-setup.php` | ✅ |
| 2 | `inc/class-soleorigine-enqueue.php` | ✅ |
| 3 | `functions.php` — class requires + init, flat file guards | ✅ |
| 4 | `inc/class-soleorigine-icons.php` — SVG icon utility | ⬜ |
| 5 | `inc/class-soleorigine-customizer-config.php` | ⬜ |
| 6 | Per-section customizer configs | ⬜ |
| 7 | Dynamic CSS generator class | ⬜ |
| 8 | Hook-driven template functions | ⬜ |
| 9 | Hook-based header/footer | ⬜ |
| 10 | Guards on remaining flat files | ⬜ |
| 11 | Remove duplicate pingback | ⬜ |
| 12 | PHP lint + verify all pages | ⬜ |
| 13 | Flush rewrite rules | ⬜ |
