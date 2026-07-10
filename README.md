# Opulentia — Premium WordPress Mega-Theme

**Version 2.0.0** | License: GPL v2 or later

A premium multipurpose WordPress mega-theme evolved from a luxury brand theme. Combines 67+ feature modules covering all Astra + Astra Pro functionality in a single integrated package with dark luxury aesthetic, GSAP animations, and Vite build system.

---

## Key Features

- **67+ Feature Modules** — Sticky Header, Transparent Header, Mega Menu, Blog Pro, Dark Mode, Scroll to Top, Live Search, Breadcrumbs, Spacing, Site Layouts, Advanced Hooks, Custom 404, Popup Builder, and more
- **Header & Footer Builder** — Drag-and-drop component system with 5 header layout presets (Standard, Centered, Minimal, Stacked, Off-Canvas) and 4 footer layout types
- **Full WooCommerce Integration** — 10+ template overrides (archive, single, cart, checkout, my account, quick view, variation swatches) plus dedicated modules (checkout customizer, wishlist/compare, product badges, 3D product viewer, recently viewed, catalog mode, product video)
- **21 Plugin Compatibility Layers** — Elementor, Elementor Pro, Gutenberg, Beaver Builder, Beaver Themer, Visual Composer, Divi, SiteOrigin, Yoast SEO, Rank Math, AMP, Jetpack, BuddyPress, Contact Form 7, Gravity Forms, LearnDash, LifterLMS, Easy Digital Downloads, SureCart, Web Stories, WPML
- **AI Site Cloner** — Capture, analyze, and apply any website design via URL or screenshot; multi-agent pipeline with token optimization
- **GSAP Animation Presets** — 12 scroll-triggered reveal animations with customizer controls (fade, slide, scale, flip, blur, clip, bounce, rotate, skew, stagger, parallax, custom)
- **Dark Luxury Design System** — 9-color palette (charcoal, gold, cream, white, gray tones), Playfair Display + Inter typography, CSS custom properties
- **Vite Build System** — Modern asset pipeline with dev server (HMR at localhost:5173) and production builds to `dist/`
- **Dynamic CSS Engine** — 19 modules generating real-time customizer-driven CSS with post-processor caching
- **Customizer Presets** — Save, apply, import, and export full design states
- **Layout Library** — 20+ pre-built section templates with one-click import (about, blog, contact, CTA, FAQ, features, footer, gallery, header, hero, portfolio, pricing, services, team, testimonials)
- **Custom Fonts Uploader** — WOFF2/WOFF/TTF/OTF upload with automatic `@font-face` generation
- **Icon Manager** — Upload, organize, and output custom SVG icons via shortcode or PHP
- **Popup Builder** — Modal, slide-in, notification bar, and fullscreen popups with triggers
- **Performance Dashboard** — Asset tracking, PageSpeed Insights integration, optimization recommendations
- **GDPR Cookie Consent** — Category opt-in bar with customizer controls and preset compliance modes
- **WP-CLI Commands** — `wp opulentia option get/set`, `wp opulentia module list/enable/disable`, `wp opulentia cloner run`
- **Child Theme Included** — `opulentia-child/` for safe customization
- **Accessibility Ready** — WCAG 2.1 AA (ARIA landmarks, focus styles, keyboard navigation, skip links)
- **Translation Ready** — `.pot` file included, WPML config, RTL support
- **White Label** — Rebrand the theme for client delivery
- **Schema Markup** — Article, Product, Organization, BreadcrumbList, FAQ, Review, VideoObject structured data
- **Security Hardening** — CSP headers, login hardening, CSRF nonces, role-based content restriction

---

## Quick Start

### Docker (Recommended)

```bash
docker compose up -d
```

Then visit http://localhost:8080 and activate the **Opulentia** theme.

### Manual Installation

1. Copy `opulentia-theme/` to your WordPress installation's `wp-content/themes/` directory
2. (Optional) Copy `opulentia-child/` to `wp-content/themes/` for child theme usage
3. Activate the theme via **Appearance > Themes** in wp-admin

### Vite Development

```bash
cd opulentia-theme
npm install
npm run dev      # Dev server at http://localhost:5173
```

### Production Build

```bash
cd opulentia-theme
npm run build    # Outputs to opulentia-theme/dist/
```

---

## Directory Structure

```
wordpress-theme-master/
├── docker-compose.yml          # Docker environment (WordPress + MySQL)
├── .wp-env.json                # WordPress-ENV configuration
├── opencode.json               # AI coding assistant config
│
├── opulentia-theme/            # Parent theme (activate this)
│   ├── style.css               # Theme metadata + 4300+ lines of base CSS
│   ├── functions.php           # Bootstrap — loads all modules
│   ├── theme.json              # FSE theme.json with design token palette
│   ├── screenshot.png          # Theme preview (1200x900)
│   ├── package.json            # Node deps (Vite)
│   ├── vite.config.js          # Vite configuration
│   │
│   ├── inc/
│   │   ├── core/               # Theme Options API, Module Manager, Hooks, Attr, Widgets
│   │   ├── builder/            # Header & Footer Builder
│   │   ├── customizer/         # Customizer config, Fonts
│   │   ├── dynamic-css/        # 19 dynamic CSS modules
│   │   ├── modules/            # 67 feature modules
│   │   ├── compatibility/      # 21 plugin compatibility layers
│   │   ├── integrations/       # Mailchimp, Custom Fonts
│   │   ├── cloner/             # AI Site Cloner pipeline
│   │   ├── metabox/            # Per-page meta boxes
│   │   ├── admin/              # Admin dashboard page
│   │   └── schema/             # Schema.org structured data
│   │
│   ├── page-templates/         # About, Contact, Gallery, Full Width, Blank
│   ├── template-parts/         # Blog layouts, hero, features, product card
│   ├── woocommerce/            # Full template override set
│   ├── js/                     # custom.js, navigation.js, animations.js, live-search.js
│   ├── css/                    # admin.css, responsive.css, woocommerce.css
│   ├── admin/                  # Theme info page
│   ├── languages/              # opulentia.pot translation template
│   └── dist/                   # Vite build output
│
├── opulentia-child/            # Child theme
│   ├── style.css
│   ├── functions.php
│   └── screenshot.png
│
└── plan/                       # Planning documentation
```

---

## Requirements

| Requirement | Minimum |
|---|---|
| WordPress | 6.0+ |
| PHP | 8.0+ |
| WooCommerce | (optional) 8.0+ |
| Node.js | (for Vite dev) 18+ |

---

## Changelog

See [CHANGELOG.md](opulentia-theme/CHANGELOG.md) for the full release history.

---

## License

GNU General Public License v2 or later — see [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).

Opulentia is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.
