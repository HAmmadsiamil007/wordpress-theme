# opulentia Theme — Premium Elevation Plan

> **Goal:** Transform opulentia from a custom boutique theme into a premium "mega theme" on par with Astra (1M+ downloads, 6K+ 5-star reviews).
> **Reference:** Astra v4.13.4 (free) + Astra Pro Addon v4.13.4
> **Date:** July 2026

---

## 1. COMPARATIVE ANALYSIS — Astra vs. opulentia

### 1.1 Architecture Comparison

| Dimension | Astra (Premium Reference) | opulentia (Current) | Gap |
|-----------|--------------------------|----------------------|-----|
| **File Organization** | `inc/core/`, `inc/customizer/`, `inc/compatibility/`, `inc/builder/`, `inc/addons/` — modular, namespaced, layered | `inc/` flat files + some singleton classes. Mixed old/new architecture. | **CRITICAL** — No layered architecture |
| **Customizer** | Full `WP_Customize_Manager` with dynamic CSS generation, selective refresh, live preview, 30+ sections | Basic customizer with 5 sections, text/url/image controls only | **CRITICAL** — Minimal customization |
| **Hook System** | `astra_*_before/after` hooks throughout every template for extensibility | No hooks system. Templates are static. | **CRITICAL** — Not extensible |
| **Dynamic CSS** | Dynamic CSS engine generates inline styles from customizer settings with caching | No dynamic CSS. Static stylesheet only. | **CRITICAL** — No design flexibility |
| **Builder System** | Header/Footer/Mobile builder with drag-drop rows, columns, widgets | Static header.php with hardcoded layout | **HIGH** — No layout builder |
| **Performance** | Asset minification, deferred scripts, Google Fonts local hosting, lazy loading | Basic enqueue with defer on some scripts. No minification. | **HIGH** — Needs optimization |
| **Compatibility** | 20+ plugin compatibility files (Elementor, Gutenberg, WooCommerce, LLMS, etc.) | Only WooCommerce (basic) | **HIGH** — No plugin ecosystem |
| **Breadcrumbs** | Built-in with schema.org markup, multiple source options | Basic breadcrumbs in template-tags.php (single post/page only) | **MEDIUM** — Limited breadcrumbs |
| **Schema Markup** | Full schema.org integration for header, footer, sidebar, nav, articles | None | **MEDIUM** — No SEO schema |
| **Mobile Header** | Separate mobile header builder with hamburger, off-canvas menu, sticky options | CSS-based hamburger toggle, no off-canvas | **MEDIUM** — Basic mobile nav |
| **Sticky Header** | Built-in with scroll effects, transparency options | Basic CSS `.scrolled` class only (no JS controls) | **MEDIUM** — Limited sticky header |
| **Colors/Palette** | Global color palette system with preset swatches, dynamic CSS variables | Static CSS custom properties in `:root` | **MEDIUM** — Hardcoded colors |
| **Typography** | Google Fonts manager with 800+ fonts, per-element font controls | 2 hardcoded fonts (Playfair Display + Inter) | **MEDIUM** — No font options |
| **Blog Layouts** | 6+ blog layout options (list, grid, masonry), multiple post formats | Single blog layout (index.php) | **MEDIUM** — One layout only |
| **Footer Builder** | Multi-row footer builder with widgets, copyright, social, payment icons | Static footer with hardcoded structure | **MEDIUM** — No footer builder |
| **Page Headers** | Custom page header per post/page with title, breadcrumb, background | Single page-header template | **LOW** — Basic page headers |
| **WooCommerce** | Full WC integration: quick view, AJAX cart, gallery, checkout styling, grid controls | Basic WC templates with CSS overrides | **MEDIUM** — Basic WC support |
| **Accessibility** | WCAG 2.1 AA, aria labels, skip links, keyboard nav, focus management | Skip link + focus-visible only | **MEDIUM** — Minimal a11y |
| **Translation** | Full .pot file, 25+ translations | .pot file exists | **LOW** — Single language |
| **White Label** | Complete white-labeling (replace branding, footer, screenshots) | None | **LOW** — Not needed yet |
| **Update System** | Background updater with version checks, migration functions | Manual updates only | **LOW** — Not critical yet |

### 1.2 Astra Pro Addon Features (Missing in opulentia)

The Astra Pro addon (`astro/astra-addon-v4.13.4/`) adds these premium modules:

| Module | What It Does | Priority for opulentia |
|--------|-------------|--------------------------|
| **Advanced Headers** | Custom page headers per post/page type | **HIGH** |
| **Blog Pro** | Multiple blog layouts, infinite scroll, load more, author bio | **HIGH** |
| **Colors & Background** | Per-element color/background controls | **HIGH** |
| **Header Sections** | Above-header, below-header content areas, social icons, search | **HIGH** |
| **Typography** | Per-element font family, weight, size controls | **HIGH** |
| **Advanced Footer** | Multi-layout footer, copyright bar | **MEDIUM** |
| **Mobile Header** | Separate mobile menu, hamburger styles, off-canvas | **MEDIUM** |
| **Site Layouts** | Container width, content layout (boxed, full-width, etc.) | **MEDIUM** |
| **Spacing** | Per-element padding/margin controls | **MEDIUM** |
| **Sticky Header** | Sticky header with scroll effects | **MEDIUM** |
| **Advanced Hooks** | Custom PHP/HTML hooks for extending theme without child theme | **MEDIUM** |
| **Advanced Search** | Search result styling, AJAX search | **LOW** |
| **Nav Menu** | Mega menu, menu styling | **LOW** |
| **WooCommerce** | Quick view, variation swatches, cart popup | **MEDIUM** (important for WC site) |
| **Learndash/LifterLMS** | Learning management system styling | **LOW** (not needed) |
| **EDD** | Easy Digital Downloads styling | **LOW** (not needed) |

---

## 2. IDENTIFIED PROBLEMS IN opulentia

### Critical Issues

1. **Dual Code Paths** — Both singleton classes AND flat procedural functions are included in `functions.php`. This means setup hooks fire twice (once from `class-opulentia-after-setup.php`, once from `theme-setup.php`). Same for customizer.

2. **No Extensibility** — Zero hooks or filters. Any plugin or child theme developer cannot modify behavior without editing theme files directly.

3. **Static Customizer** — Only text/image fields. No color pickers, font selectors, layout toggles, or dynamic CSS generation. Users can't customize the premium look.

4. **Hardcoded Design Tokens** — Colors, fonts, spacing are hardcoded in `style.css`. Astra uses dynamic CSS with `theme_mod()` values — users can change everything from Customizer.

5. **No Dynamic CSS** — Every color change in Astra generates optimized inline CSS. opulentia requires editing `style.css` for any color change.

### High-Priority Issues

6. **No Schema/Structured Data** — Articles, products, breadcrumbs lack schema markup for SEO.

7. **No Page Builder Compatibility** — No compatibility with Elementor, Beaver Builder, Gutenberg blocks, or any page builder.

8. **Breadcrumbs Limited** — Works only for posts and pages. No archive, category, tag, CPT, or WooCommerce breadcrumbs.

9. **Mobile Menu** — Works but lacks off-canvas drawer, smooth animations, sub-menu support for multi-level navigation.

10. **No Mega Menu** — Standard wp_nav_menu with no dropdown animation, mega menu columns, or icon support.

### Medium-Priority Issues

11. **WooCommerce Integration** — Basic template overrides. Missing: quick view, AJAX cart, product gallery zoom, sticky add-to-cart, variations styling, checkout optimization.

12. **GSAP Underutilized** — GSAP Core + ScrollTrigger + ScrollTo loaded but only used for basic scroll animations in `custom.js`.

13. **No Performance Optimization** — No CSS/JS minification, no critical CSS, no font swapping strategy, no lazy load for images beyond browser default.

14. **Accessibility Gaps** — Missing: aria-current for nav, focus trap for mobile menu, form error announcements, skip link doesn't actually skip to content (no `#primary` tabindex).

15. **Blog Single Page** — Missing: author bio, related posts, social sharing, comment structure improvements.

---

## 3. ELEVATION PLAN — 10 Phases

### Phase 1: Architecture Cleanup (2-3 hrs)
- [ ] Remove dual code paths: keep singleton classes, delete flat `theme-setup.php` and `customizer.php` procedural fallbacks
- [ ] Add `Opulentia_*_before/after` hooks to all templates (header, footer, content, sidebar)
- [ ] Add filter hooks for key template parts and function outputs
- [ ] Create `inc/core/` directory structure: `class-opulentia-hooks.php`, `class-opulentia-dynamic-css.php`
- [ ] Migrate `widgets.php` into singleton class

### Phase 2: Dynamic CSS Engine (3-4 hrs)
- [ ] Create `class-opulentia-dynamic-css.php` that generates inline styles from customizer settings
- [ ] Implement CSS variable output in `<head>` based on `theme_mod()` values
- [ ] Add caching mechanism for generated CSS (transient-based)
- [ ] Create base CSS file structure: `dynamic-css/header.php`, `dynamic-css/footer.php`, `dynamic-css/blog.php`, `dynamic-css/woocommerce.php`
- [ ] Register all `:root` variables dynamically from customizer settings
- [ ] Add color scheme presets (3-5 luxury palettes) that users can switch

### Phase 3: Premium Customizer (4-5 hrs)
- [ ] Add color controls for every element (header bg, footer bg, accent, text, links, buttons, borders)
- [ ] Add typography controls: Google Fonts dropdown (100+ fonts), weight, size, line-height, letter-spacing per element (headings, body, nav, buttons)
- [ ] Add layout controls: container width (960-1400px), content layout (boxed/full-width), sidebar position
- [ ] Add spacing controls: section padding (top/bottom), element gaps
- [ ] Add header controls: sticky, transparent, top bar toggle
- [ ] Add footer controls: columns layout, copyright, social links
- [ ] Add blog controls: layout (list/grid), columns, excerpt length, read more text
- [ ] Add WooCommerce controls: product columns, rows per page, cart layout
- [ ] Add live preview JS for all new controls

### Phase 4: Builder System — Header & Footer (4-5 hrs)
- [ ] Create header builder concept: rows (top/main/bottom), each with columns (1-4) and content areas
- [ ] Create footer builder concept: rows, columns, widget areas
- [ ] Implement settings arrays for row layouts
- [ ] Replace static `header.php` with builder-driven template
- [ ] Replace static `footer.php` with builder-driven template
- [ ] Add social icons component, search component, cart icon component
- [ ] Add sticky header behavior: scroll detection, class toggling, background opacity transition

### Phase 5: Blog Enhancement Suite (3-4 hrs)
- [ ] Create 3 blog layout templates: classic (full), grid (2-3 cols), list (sidebar thumbnail)
- [ ] Add blog settings to customizer: layout picker, post meta toggle, excerpt length
- [ ] Create `template-parts/blog/`: `content-grid.php`, `content-list.php`, `content-classic.php`
- [ ] Add single post: author box, related posts (by category), post navigation styling
- [ ] Add reading progress bar to single posts
- [ ] Improve pagination with numbered pages and prev/next

### Phase 6: Page Builder & Plugin Compatibility (2-3 hrs)
- [ ] Create `inc/compatibility/` directory
- [ ] Add Elementor compatibility: full-width template, header/footer support
- [ ] Add Gutenberg/Block Editor compatibility: block styles, editor-width, color palette registration
- [ ] Add contact-form-7 compatibility: styled forms matching theme design
- [ ] Add Yoast SEO compatibility: breadcrumb integration, schema improvement
- [ ] Add AMP compatibility: basic valid AMP markup

### Phase 7: WooCommerce Pro Upgrades (3-4 hrs)
- [ ] Quick View modal with AJAX product loading
- [ ] AJAX add-to-cart with mini-cart dropdown in header
- [ ] Product gallery: thumbnails with zoom, lightbox
- [ ] Sticky add-to-cart bar on scroll for single products
- [ ] Product grid: hover effects, color/image swap, size badges
- [ ] Checkout optimization: modern two-step or styled single-page checkout
- [ ] Cart page: improved cross-sells, quantity input styling, coupon styling
- [ ] My Account: dashboard styling, order history table, address display

### Phase 8: Performance & Optimization (2-3 hrs)
- [ ] Implement asset minification (CSS + JS) with cached versions
- [ ] Add critical CSS extraction for above-the-fold content
- [ ] Implement "Defer non-critical CSS" option
- [ ] Add Google Fonts strategy: swap display, preload, subset
- [ ] Add lazy load for images (beyond default loading="lazy")
- [ ] Implement `preconnect` and `dns-prefetch` for external resources
- [ ] Add CSS variable inline to `<head>` (bypass render-blocking external CSS)
- [ ] Cache dynamic CSS output with version busting

### Phase 9: Accessibility & SEO (2 hrs)
- [ ] Audit and fix WCAG 2.1 AA compliance:
  - Focus management in mobile menu (trap focus when open)
  - `aria-current="page"` on nav items
  - `aria-expanded` states on mobile toggle and dropdowns
  - Skip link functional fix (focus management on `#primary`)
  - Form error announcements with `aria-live`
  - Alt text requirements enforcement for WooCommerce products
- [ ] Add schema markup: `Organization`, `WebSite`, `BreadcrumbList`, `Article`, `Product`
- [ ] Add Open Graph and Twitter Card meta tags
- [ ] Add JSON-LD structured data for local business

### Phase 10: Premium Templates & Prebuilt Sites (4-5 hrs)
- [ ] Create 3 prebuilt starter sites/demos for the theme
- [ ] Each demo: homepage, about, collection/category, product, blog, contact
- [ ] Provide demo import XML files + customizer settings export
- [ ] Create admin welcome page with theme info, documentation, import guide
- [ ] Create theme documentation (setup guide, customization guide, FAQ)
- [ ] Add notice for WordPress.org theme directory compliance (if publishing)

---

## 4. IMPLEMENTATION PRIORITY MATRIX

| Phase | Impact | Effort | User Visibility | Dependencies | Start Order |
|-------|--------|--------|----------------|--------------|-------------|
| 1. Architecture | 🟢 Critical | 2-3 hrs | None (dev) | None | **1st** |
| 2. Dynamic CSS | 🟢 Critical | 3-4 hrs | High | Phase 1 | **2nd** |
| 3. Premium Customizer | 🟢 Critical | 4-5 hrs | Very High | Phase 2 | **3rd** |
| 5. Blog Enhancement | 🟡 High | 3-4 hrs | High | Phase 1 | **4th** |
| 4. Builder System | 🟡 High | 4-5 hrs | Very High | Phase 3 | **5th** |
| 7. WooCommerce Pro | 🟡 High | 3-4 hrs | High (WC site) | Phase 1 | **6th** |
| 6. Plugin Compat | 🟡 High | 2-3 hrs | Medium | Phase 1 | **7th** |
| 8. Performance | 🟠 Medium | 2-3 hrs | Medium | Phase 1-3 | **8th** |
| 9. Accessibility | 🟠 Medium | 2 hrs | Medium | Phase 1 | **9th** |
| 10. Premium Templates | 🟠 Medium | 4-5 hrs | Very High | All above | **10th** |

---

## 5. KEY FILES TO MODIFY/CREATE

### New Files to Create
```
opulentia-theme/
├── inc/
│   ├── core/
│   │   ├── class-opulentia-hooks.php          # Hook/filter registration
│   │   ├── class-opulentia-extensibility.php   # Action/filter definitions
│   │   └── class-opulentia-markup.php          # Schema markup helpers
│   ├── dynamic-css/
│   │   ├── class-opulentia-dynamic-css.php     # Main dynamic CSS engine
│   │   ├── global.php                            # Global CSS variables
│   │   ├── header.php                            # Header dynamic styles
│   │   ├── footer.php                            # Footer dynamic styles
│   │   ├── blog.php                              # Blog dynamic styles
│   │   ├── woocommerce.php                       # WooCommerce dynamic styles
│   │   └── typography.php                        # Typography CSS generation
│   ├── builder/
│   │   ├── class-opulentia-header-builder.php  # Header builder logic
│   │   ├── class-opulentia-footer-builder.php  # Footer builder logic
│   │   ├── header-components.php                 # Header component templates
│   │   └── footer-components.php                 # Footer component templates
│   ├── compatibility/
│   │   ├── class-opulentia-elementor.php       # Elementor compatibility
│   │   ├── class-opulentia-gutenberg.php       # Gutenberg compatibility
│   │   ├── class-opulentia-cf7.php             # Contact Form 7
│   │   ├── class-opulentia-yoast.php           # Yoast SEO integration
│   │   └── class-opulentia-amp.php             # AMP compatibility
│   ├── customizer/
│   │   ├── class-opulentia-customizer-colors.php   # Color controls
│   │   ├── class-opulentia-customizer-typography.php # Typography controls
│   │   ├── class-opulentia-customizer-layout.php   # Layout controls
│   │   ├── class-opulentia-customizer-blog.php     # Blog controls
│   │   └── class-opulentia-customizer-wc.php       # WooCommerce controls
│   ├── blog/
│   │   ├── class-opulentia-blog-layouts.php        # Blog layout logic
│   │   ├── class-opulentia-single-post.php         # Single post enhancement
│   │   └── class-opulentia-related-posts.php       # Related posts
│   ├── woocommerce/
│   │   ├── class-opulentia-wc-quick-view.php       # Quick view module
│   │   ├── class-opulentia-wc-ajax-cart.php        # AJAX cart module
│   │   └── class-opulentia-wc-customizer.php       # WC Customizer settings
│   └── classes/
│       └── class-opulentia-white-label.php         # White labeling (optional)
├── template-parts/
│   ├── blog/
│   │   ├── content-grid.php
│   │   ├── content-list.php
│   │   └── content-classic.php
│   └── header/
│       ├── header-row.php
│       ├── header-component-logo.php
│       ├── header-component-nav.php
│       ├── header-component-search.php
│       ├── header-component-cart.php
│       └── header-component-social.php
├── admin/
│   └── theme-info.php                              # Admin welcome page
├── assets/
│   └── demo-content/
│       ├── demo-1/
│       │   ├── content.xml
│       │   ├── widgets.json
│       │   └── customizer.json
│       ├── demo-2/
│       └── demo-3/
└── docs/
    ├── setup-guide.md
    ├── customization-guide.md
    └── developer-api.md
```

### Existing Files to Modify
```
opulentia-theme/
├── style.css                    # Move hardcoded tokens to dynamic, keep as fallback
├── functions.php                # Clean up dual paths, add new includes
├── header.php                   # Convert to builder-driven template
├── footer.php                   # Convert to builder-driven template
├── index.php                    # Blog layout selector
├── single.php                   # Author box, related posts
├── page.php                     # Full-width option
├── archive.php                  # Archive layout selector
├── inc/class-opulentia-after-setup.php    # Refactor to avoid duplication
├── inc/class-opulentia-enqueue.php        # Add minification, defer strategy
├── inc/class-opulentia-customizer-config.php  # Expand config dramatically
├── inc/template-tags.php        # Add more template functions
├── inc/template-functions.php   # Expand with builder helpers
└── inc/widgets.php              # Enhance existing widgets
```

---

## 6. DESIGN TOKENS TO REMAIN

The opulentia brand identity is strong — preserve these design tokens:

| Token | Value | Preserve? |
|-------|-------|-----------|
| `--color-primary-dark` | `#1a1a1a` | ✅ Make dynamic (default) |
| `--color-secondary-dark` | `#111` | ✅ Make dynamic (default) |
| `--color-accent` | `#b8860b` | ✅ Make dynamic (default) |
| `--color-gold` | `#c9a96e` | ✅ Make dynamic (default) |
| `--font-heading` | `Playfair Display` | ✅ Make switchable (default) |
| `--font-body` | `Inter` | ✅ Make switchable (default) |
| Container width | 1200px | ✅ Make adjustable (default) |
| Dark background | ✅ | ✅ Keep as default |

---

## 7. KEY SKILLS TO USE DURING IMPLEMENTATION

| Skill | Phase | Purpose |
|-------|-------|---------|
| `impeccable` | All | Design review, polish, critique of UI |
| `frontend-design` | 3, 5, 7 | Customizer UI, blog, WC interface design |
| `brainstorming` | 3, 10 | Customizer controls, demo content planning |
| `loop-engineering` | 2, 3, 4, 7 | Quality improvement loops for complex modules |
| `emil-design-eng` | 4, 7, 9 | Animation polish, interaction design |
| `design-motion-principles` | 4, 9 | Scroll animations, micro-interactions |
| `web-design-guidelines` | 9 | Accessibility audit |

---

## 8. RECOMMENDED STARTING POINT

Begin with **Phase 1 (Architecture Cleanup)** followed immediately by **Phase 2 (Dynamic CSS Engine)**. These are foundational — all other phases depend on them.

The fastest path to visible premium-feeling improvements:
1. Phase 1 → Phase 2 → Phase 3 (Customizer controls for colors + fonts)
2. This immediately gives users the "Astra experience" of changing colors/fonts live
3. Then layer on Phase 5 (blog) and Phase 4 (builder) for structural improvements
