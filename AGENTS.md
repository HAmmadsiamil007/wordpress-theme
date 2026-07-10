# Opulentia — Multipurpose Premium WordPress Mega-Theme

Premium multipurpose WordPress mega-theme evolved from SoleOrigine. Combines all features of Astra + Astra Pro into a single integrated theme with dark luxury aesthetic, GSAP animations, and Vite build system.

## Theme Directory

`C:\Users\hamma\Downloads\wordpress-theme-master\wordpress-theme-master\opulentia-theme\`

## Design Tokens

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

## File Structure

```
opulentia-theme/
├── style.css              # Theme metadata + base CSS (4273+ lines)
├── screenshot.png         # 1200×900 customizer preview
├── functions.php          # Bootstrap — loads all 36+ modules
├── header.php / footer.php / index.php / front-page.php
├── page.php / single.php / archive.php / search.php / 404.php
├── sidebar.php / comments.php / searchform.php / home.php
├── inc/
│   ├── core/              # Theme Options API, Hooks, Performance, Security, A11y
│   ├── builder/           # Header & Footer Builder (3-row, component-based)
│   ├── customizer/        # Customizer sections, fonts, live preview
│   ├── dynamic-css/       # 18+ dynamic CSS modules (global, header, footer, blog, WC, etc.)
│   ├── modules/           # Sticky Header, Transparent Header, Mega Menu, Breadcrumbs, Blog Pro, Dark Mode, Spacing, Site Layouts, Scroll to Top, Live Search, Advanced Hooks
│   ├── compatibility/     # Elementor, Gutenberg, Beaver Builder, Yoast, Rank Math, LearnDash, LifterLMS, AMP, WooCommerce
│   ├── integrations/      # Mailchimp, Custom Fonts
│   ├── metabox/           # Per-page/post meta boxes (layout, sidebar, header overrides)
│   ├── schema/            # Schema.org structured data
│   ├── admin/             # Admin dashboard
│   ├── class-*.php        # After Setup, Enqueue, Icons, Customizer Config, Fonts
│   ├── template-tags.php  # Template tags
│   └── template-functions.php
├── page-templates/        # About, Contact, Gallery, Full Width, Blank
├── template-parts/        # Header layouts, footer layouts, blog layouts, hero, features
├── woocommerce/           # Full template overrides (archive, single, cart, checkout, my account)
├── js/                    # custom.js, navigation.js, customizer.js, woocommerce-pro.js
├── css/                   # admin.css, responsive.css, woocommerce.css, woocommerce-pro.css
├── admin/                 # Theme info page
├── languages/             # opulentia.pot translation template
├── dist/                  # Vite build output
├── package.json
└── vite.config.js
```

## Registered Features

- **Post types:** Collections (`collection`), Styles (`style`), Brands (`brand`)
- **Taxonomies:** Collection Category, Style Category, Brand Category
- **Nav menus:** Primary Menu, Footer Menu
- **Widget areas:** Sidebar, Footer 1/2/3
- **WooCommerce support:** Yes (full template overrides + quick view + variation swatches)
- **36+ modules:** Core, Header/Footer Builder, Blog Pro, Mega Menu, Sticky/Transparent Header, Live Search, Dark Mode, Breadcrumbs, Schema, A11y, Performance, Security, Spacing, Site Layouts

## Skill Loading Order

When starting work, load skills in this priority order:

1. **Core theme:** `wp-theme-development` (primary WordPress skill)
2. **WooCommerce:** `wp-woocommerce-dev` (product templates, cart, checkout)
3. **Design system:** `wpds` + `web-design-guidelines` + `brand-guidelines` + `brandkit` (design tokens, WP conventions, brand consistency)
4. **Design polish:** `ui-ux-pro-max-skill` + `color-expert` + `theme-factory` + `impeccable-design-polish` + `taste-skill-v1` (premium look, color harmony, theme palettes)
5. **Animations:** `gsap-core` + `gsap-scrolltrigger` + `gsap-plugins` (scroll-triggered reveals, micro-interactions, hero animations)
6. **Marketing & Content:** `marketing-psychology` + `brand` + `copywriting` + `ecommerce-image-workflow` (conversion optimization, brand storytelling, product images)
7. **Quality & Review:** `design-review` + `wp-security` (final polish, code audit, accessibility check)
8. **Automation & Quality Loops:** `loop-engineering` (iterative quality refinement, generate-review-improve cycles, escalation handling)

## Available Skills (`.agents/skills/`)

### WordPress Core (from wordpress-skills)
`wp-theme-development` `wp-woocommerce-dev` `wp-block-development` `wp-cli` `wp-cron` `wp-custom-post-types` `wp-database` `wp-debug` `wp-hooks` `wp-local-env` `wp-multisite` `wp-performance` `wp-rest-api` `wp-security` `wp-seo` `wp-testing` `wp-translations` `wp-user-management` `wp-gutenberg` `wp-mu-plugin`

### Design & Brand (from open-design + uiux)
`ui-ux-pro-max-skill` `web-design-guidelines` `brand-guidelines` `brand-guidelines-anthropic` `brand-guidelines-community` `color-expert` `theme-factory` `wpds` `impeccable-design-polish` `taste-skill-v1` `platform-design` `redesign-skill` `design-review` `design-brief` `brand-extract` `brandkit` `ecommerce-image-workflow` `mockup-device-3d` `copywriting`

### GSAP Animation (from open-design)
`gsap-core` `gsap-scrolltrigger` `gsap-plugins` `gsap-timeline` `gsap-utils` `gsap-frameworks` `gsap-react` `gsap-performance`

### Marketing
`marketing-psychology` `brand` `ad-creative` `content-strategy` `cold-email` `paid-ads`

### Automation & Quality Loops
`loop-engineering` — iterative quality refinement with generate-review-improve cycles. Sub-skills: `changelog-scan`, `ci-triage`, `dependency-triage`, `issue-triage`, `loop-budget`, `loop-verifier`, `minimal-fix`, `post-merge-scan`, `pr-review-triage`. Used for CI sweeps, dependency updates, changelog drafting, PR babysitting, and post-merge cleanup.

Installed from `https://github.com/cobusgreyling/loop-engineering` into `C:\Users\hammad\Downloads\worldpress\.agents\skills\loop-engineering\` (project-local, full repo).

## MCP Servers

Configured in `opencode.json` at project root:

- **Context7** — Live library/framework docs (WordPress, WooCommerce, PHP, GSAP)
- **Headroom MCP** — WordPress project auditing, PHP linting, template hierarchy checks
- **Serena MCP** — Design-to-code conversion, screen capture, visual regression

## Environment Setup (Windows — LocalWP)

| Tool | Path / Command |
|---|---|
| **PHP** | `C:\Users\hammad\AppData\Local\Programs\Local\resources\extraResources\lightning-services\php-8.2.29+0\bin\win64\php.exe` |
| **Composer** | `php C:\Users\hammad\AppData\Local\Temp\composer.phar [cmd]` |
| **Vite dev server** | `npm run dev` in `opulentia-theme/` — runs on `http://localhost:5173` |
| **Vite build** | `npm run build` in `opulentia-theme/` — outputs to `opulentia-theme/dist/` |
| **Git remote** | `https://github.com/HAmmadsiamil007/wordpress-theme.git` (branch: `master`) |

## Common Commands

- **Start Vite:** `npm run dev` from `opulentia-theme/`
- **Build Vite:** `npm run build` from `opulentia-theme/`
- **Composer install:** `php C:\Users\hammad\AppData\Local\Temp\composer.phar install`
- **Lint PHP:** `php -l` on any modified file
- **Lint CSS:** Check `style.css` `:root` variables match design tokens
- **Test theme:** Visit `wp-admin/themes.php`, activate Opulentia
- **Verify WooCommerce:** Visit a shop page, single product, cart, checkout
- **Check CPTs:** Visit `/wp-admin/edit.php?post_type=collection` (or style, brand)
- **Update .pot:** `wp i18n make-pot . languages/opulentia.pot`
- **Git push:** `git push origin master` (from workspace root)

## Phase Status

| Phase | Status |
|-------|--------|
| **1 — Core Theme** | ✅ Complete |
| **2 — AI Cloner** | ✅ Complete |
| **3A — Min Premium** | ✅ Complete |
| **3B — Enhanced** | ✅ Complete |
| **3C — Luxury Polish** | ✅ Complete |
| **Child Theme** | ✅ opulentia-child/ |
| **WP-CLI** | ✅ inc/class-opulentia-cli.php |
| **CHANGELOG** | ✅ CHANGELOG.md |

## Resume Sequence

1. Read `opulentia-theme/docs/master-plan.md` for full Phase 3 specs
2. Start **Phase 3A.1 — GSAP Animation Presets** (highest visibility)
3. Build each module: class → customizer controls → dynamic CSS → enqueue → register in functions.php
4. PHP lint after each module: `php -l file.php`
5. Every module follows the existing singleton pattern in `inc/modules/`

## Quick-Start Commands
```bash
# PHP lint
& "C:\Users\hammad\AppData\Local\Programs\Local\resources\extraResources\lightning-services\php-8.2.29+0\bin\win64\php.exe" -l file.php

# Vite
npm run dev    # dev server on :5173
npm run build  # build to dist/

# Dembrandt (site token extraction)
dembrandt --json-only --no-sandbox URL

# designlang (better for WP — has theme.json v3 emitter)
npx designlang URL --out output/site-name

# Export .pot
wp i18n make-pot . languages/opulentia.pot

# Git push
git push origin master
```

## SASS/CSS Conventions

- CSS custom properties in `:root` in `style.css`
- WooCommerce overrides in `css/woocommerce.css`
- Responsive breakpoints in `css/responsive.css`: 992px, 576px, 400px
- SVG icons only (no font icons)
- No comments in code output (AI generation)
- Animations use GSAP (loaded via enqueued `custom.js`)
