# Opulentia MegaTheme — Final Completion Plan

> **Audit Date:** 2026-07-09
> **Real Completion:** ~98% (not 90% as old plan claimed)
> **Strategy:** Complete minor gaps → AI Cloner → Premium Features

---

## Phase 0: REALITY AUDIT — What Actually Exists

### File Audit Results

| Area | Old Plan Claim | Actual | Delta |
|------|---------------|--------|-------|
| Compat files | 9 of 20 | 19 of 20 | **+10 files found** |
| Dynamic CSS modules | 14 of 18 | 15 of 19 | Missing: archive, search, page, 404 |
| Advanced Hooks module | "NOT IMPLEMENTED" | 2 files exist | **Module exists** |
| live-search.js | "MISSING" | EXISTS | **Found** |
| SoleOrigine refs | "HIGH severity" | ZERO | **Already cleaned** |
| Core files (inc/core/) | 10 files | 9 files | Widgets moved? Verify |
| Gutenberg blocks | 5 | 1 compat file | Need to check actual blocks |
| Elementor widgets | 5 | 2 compat files | Need to check actual widgets |

### What ACTUALLY Needs Work

| Item | Status | Real Effort |
|------|--------|-------------|
| `theme.json` | MISSING | 30 min |
| `template-blank.php` | MISSING | 15 min |
| Dynamic CSS: archive.php | MISSING | 30 min |
| Dynamic CSS: search.php | MISSING | 30 min |
| Dynamic CSS: page.php | MISSING | 30 min |
| Dynamic CSS: 404.php | MISSING | 30 min |
| Customizer: panels org | NEEDS WORK | 2 hr |
| Custom fonts integration | MISSING | 1 hr |
| SureCart compat | MISSING | 30 min |
| Web Stories compat | MISSING | 30 min |
| AI Cloner system | NOT STARTED | 16 hr |
| Premium features | NOT STARTED | 16 hr |
| Child theme | NOT STARTED | 1 hr |
| WP CLI commands | NOT STARTED | 2 hr |
| **Total** | | **~35 hr** |

---

## Phase 1: Gap Filling (8 hr)

### 1a. theme.json (30 min)
- Create with: color palette, typography, layout, spacing settings
- Match current design tokens
- Enable FSE compatibility

### 1b. template-blank.php (15 min)
- Page template with no header/footer/sidebar
- For page builders (Elementor, Gutenberg full-width)

### 1c. Dynamic CSS modules (2 hr)
- archive.php — Archive page styles
- search.php — Search results styles
- page.php — Page-specific styles
- 404.php — 404 page styles

### 1d. Customizer panel organization (2 hr)
- Panels: Global, Header, Footer, Layout, WooCommerce, Integrations
- Move 20+ top-level sections into proper hierarchy
- Verify existing settings still work after reorganization

### 1e. Custom fonts integration (1 hr)
- inc/integrations/class-opulentia-custom-fonts.php
- Upload woff2/ttf fonts via Customizer
- Generate @font-face CSS

### 1f. Missing compat files (1 hr)
- class-opulentia-surecart.php
- class-opulentia-web-stories.php

---

## Phase 2: AI Website Cloner (16 hr)

### 2a. Architecture

```
User: URL or screenshot
  │
  ▼
[CAPTURE]  Playwright MCP → full-page screenshot
           Serena MCP → visual DOM snapshot
  │
  ▼
[ANALYZE]  AI vision → extract color palette
           Playwright computed styles → font detection
           DOM analysis → layout structure, components
  │
  ▼
[GENERATE] design.md with tokens: colors, fonts, spacing,
           layout, components, assets (images, logos)
  │
  ▼
[REVIEW]   User reads design.md → approve / modify
  │
  ▼
[APPLY]    Update customizer settings
           Download assets (images, fonts)
           Regenerate dynamic CSS
           Show live preview
  │
  ▼
[CONTENT]  If WP source: import posts, pages, products,
           menus, widgets via XML-RPC or WP REST API
```

### 2b. Files to Create
- `inc/cloner/class-opulentia-site-cloner.php` — Orchestrator
- `inc/cloner/class-opulentia-cloner-capture.php` — Capture + screenshot
- `inc/cloner/class-opulentia-cloner-analyzer.php` — Design analysis
- `inc/cloner/class-opulentia-cloner-tokens.php` — Token generation
- `inc/cloner/class-opulentia-cloner-applier.php` — Style application
- `inc/cloner/class-opulentia-cloner-importer.php` — Content import
- `admin/cloner-page.php` — Admin UI

### 2c. Tool Integration
| Step | Tool/MCP | Purpose |
|------|----------|---------|
| Capture | playwright-mcp | Full-page screenshot, computed style extraction |
| Capture | serena | Visual DOM snapshot, image analysis |
| Analyze | AI vision | Color palette extraction |
| Analyze | playwright-mcp | Font family detection via computed styles |
| Generate | AI | design.md generation with token mapping |
| Preview | browser_snapshot | Live preview of applied design |
| Apply | wordpress-pro | Customizer settings update, CSS regeneration |

### 2d. Workflow Detail
1. User pastes URL or uploads screenshot in admin
2. If URL: Playwright navigates, captures full-page screenshot + computed styles
3. AI analyzes screenshot: dominant colors, font stack, spacing patterns
4. Design tokens mapped to Opulentia system (--opulentia-global-color-0..8, --font-heading, --font-body, etc.)
5. design.md generated with: palette, typography, layout config, asset URLs
6. User reviews design.md, can modify before applying
7. Apply button: updates theme_mods, downloads assets, regenerates CSS
8. Preview mode: user sees the restyled site before finalizing

---

## Phase 3: Premium Features (16 hr)

### 3a. GSAP Animation Presets (6 hr)
- `js/animation-presets.js` — 12 presets
- Each preset: configurable duration, ease, delay, trigger
- Customizer controls for per-element animation
- `inc/modules/animation-presets/` — PHP backend

### 3b. WooCommerce 3D Product Viewer (6 hr)
- Three.js-based product rotation
- Fallback to standard gallery
- `js/product-viewer-3d.js`
- `inc/modules/product-viewer-3d/` — PHP backend

### 3c. Layout Library (4 hr)
- `inc/layout-library/` — Pre-built section templates
- 10 section types: Hero, Features, About, Team, Testimonials, FAQ, Pricing, Contact, Gallery, Portfolio
- Gutenberg block patterns
- One-click import

---

## Phase 4: Developer Infrastructure (4 hr)

### 4a. Child Theme (1 hr)
- `opulentia-child/style.css`
- `opulentia-child/functions.php`
- Documentation in `docs/child-theme.md`

### 4b. WP CLI Commands (2 hr)
| Command | Description |
|---------|-------------|
| `wp opulentia option get <key>` | Get theme option |
| `wp opulentia option set <key> <value>` | Set theme option |
| `wp opulentia module list` | List modules + status |
| `wp opulentia module enable <slug>` | Enable module |
| `wp opulentia module disable <slug>` | Disable module |
| `wp opulentia cloner run <url>` | Clone website from URL |

### 4c. Versioning + Changelog (1 hr)
- `CHANGELOG.md` with keepachangelog format
- Version numbering: 1.0.0 for completion release
- Upgrade hooks for future versions

---

## Phase 5: Testing + Verification (4 hr)

### 5a. PHP Verification
- `php -l` on every PHP file
- WPCS standards check on modified files

### 5b. Template Verification (via LocalWP)
- Visit every template: front-page, home, single, page, archive, search, 404, shop, product, cart, checkout, my-account
- Verify header/footer builder renders correctly
- Verify mega menu works
- Verify mobile responsive

### 5c. Customizer Verification
- Every panel/section loads
- Color changes reflect live
- Typography changes reflect live
- Layout changes reflect live
- Spacing changes reflect live

### 5d. WooCommerce Verification
- Product catalog renders correctly
- Single product page renders
- Cart + checkout flow works
- Quick view works
- Variation swatches work

### 5e. Performance Check
- Dynamic CSS generates correctly
- No PHP notices/warnings
- No JS console errors

---

## Tool & Skill Mapping

| Phase | Skill | MCP/Tool | Purpose |
|-------|-------|----------|---------|
| 1a | wordpress-pro | context7 | theme.json schema |
| 1b | wp-theme-development | — | Page template creation |
| 1c | wordpress-pro | — | Dynamic CSS generation |
| 1d | wordpress-pro | context7 | WP_Customize_Manager API |
| 1e | wordpress-pro | context7 | Custom fonts @font-face |
| 1f | wordpress-pro | context7 | Plugin compatibility APIs |
| 2a-d | impeccable | serena, playwright, open-design | Design analysis, clone |
| 3a | gsap-core, gsap-scrolltrigger | — | Animation presets |
| 3b | wordpress-pro | context7 | Three.js + WooCommerce |
| 3c | wp-block-development | — | Gutenberg block patterns |
| 4a | wp-theme-development | — | Child theme setup |
| 4b | wp-cli | — | WP CLI command registration |
| 5 | design-review | playwright-mcp | Visual verification |

---

## Success Criteria

- [ ] theme.json created with design tokens
- [ ] template-blank.php created
- [ ] 4 missing dynamic-css modules created
- [ ] Customizer panels organized into 6 panels
- [ ] Custom fonts integration working
- [ ] SureCart + Web Stories compat files created
- [ ] AI Cloner: URL → screenshot → analysis → design.md → apply works end-to-end
- [ ] 12 GSAP animation presets working
- [ ] 3D product viewer working for WooCommerce
- [ ] 10 layout library sections created as Gutenberg patterns
- [ ] Child theme works
- [ ] WP CLI commands registered and working
- [ ] All existing features still work (no regressions)
- [ ] PHP lint passes on all files
- [ ] No JS console errors
- [ ] WooCommerce flow works end-to-end

---

## Risk Register (Updated)

| Risk | Likelihood | Impact | Mitigation |
|------|-----------|--------|------------|
| AI Cloner inaccurate colors | Medium | Low | design.md review gate before apply |
| Customizer panel reorg breaks settings | Low | Medium | Backup theme_mods before reorg |
| Three.js conflicts with gallery | Low | Medium | Graceful fallback to standard gallery |
| WP CLI commands conflict with other plugins | Low | Low | Unique command namespace `opulentia` |
| Child theme not detected by WordPress | Low | Low | Follow WordPress exactly |
| Theme too large for WordPress.org | Low | Medium | Not publishing on .org per user goal |
