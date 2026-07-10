# Opulentia Master Plan — Premium WordPress Mega-Theme

> Status: Phase 1 (Core) ✅ · Phase 2 (Cloner) ✅ · Phase 3 (Premium) ❌
> Last Updated: 2026-07-09

---

## Phase 3A — Minimum Premium (DO FIRST)

### 3A.1 GSAP Animation Presets
**Files:** `inc/modules/animation-presets/class-opulentia-animation-presets.php`
**Customizer Panel:** Opulentia > Animations
**Controls:**
- Scroll reveal: fade/slide/zoom/rotate with direction, distance, duration, delay
- Parallax: speed (0.1–1.0), direction, limit
- Stagger: item delay (0.02–0.3), axis (x/y)
- Counter: number animation on scroll
- Text split: word/char reveal
- Per-page override via meta box
**Output:** `js/animations.js` + dynamic CSS with data attributes
**Reference:** GSAP ScrollTrigger + SplitText

### 3A.2 Layout Library
**Files:** `inc/modules/layout-library/class-opulentia-layout-library.php`, `admin/layout-library-page.php`
**Customizer Panel:** None (admin page only)
**Features:**
- 20+ pre-built sections (hero, features, testimonials, pricing, FAQ, CTA, footer, header)
- Import as reusable block / template part
- Category/industry filter (business, portfolio, ecommerce, landing)
- One-click preview in WP admin
- JSON export/import for custom sections
**Output:** JSON template files in `inc/modules/layout-library/templates/`

### 3A.3 Popup Builder
**Files:** `inc/modules/popup-builder/class-opulentia-popup-builder.php`
**Customizer Panel:** Opulentia > Popups
**Controls:**
- Popup trigger: time delay, scroll %, exit intent, click
- Popup types: modal, notification bar, slide-in, fullscreen
- Design: background, typography, close button, overlay
- Display conditions: pages, devices, user roles
- Frequency: once per session, once per day, always

### 3A.4 Conditional Display Logic
**Files:** `inc/modules/conditional-display/class-opulentia-conditional-display.php`
**Controls (meta box on every post/page):**
- Show/hide header, footer, sidebar, breadcrumbs, scroll-to-top
- Custom CSS class injection
- Based on: page template, category, tag, user role, device, date range, URL parameter
- Reusable condition sets (save named rules)
**Output:** Filter hooks applied in template files

### 3A.5 Demo Importer
**Files:** `inc/modules/demo-importer/class-opulentia-demo-importer.php`, `admin/demo-importer-page.php`
**Features:**
- 5 demo sites: Business, Portfolio, Shop, Agency, Landing
- One-click import: content, widgets, customizer settings, theme options
- Selective import (choose what to import)
- XML + JSON export/import format
- Demo preview thumbnails

### 3A.6 Customizer Presets
**Files:** `inc/modules/customizer-presets/class-opulentia-customizer-presets.php`
**Customizer Panel:** Opulentia > Presets
**Features:**
- Save full customizer state as named preset
- 6 included presets: Dark Luxury, Light Elegance, Midnight Blue, Forest Green, Rose Gold, Ocean Deep
- JSON export/import for presets
- Apply with one click
- Preview preset before applying

### 3A.7 Performance Dashboard
**Files:** `inc/modules/performance-dashboard/class-opulentia-performance-dashboard.php`, `admin/performance-page.php`
**Features:**
- Asset load time tracking
- Google PageSpeed Insights integration (API key optional)
- Module impact report (which modules add how much weight)
- Lazy load configuration UI
- Preload/preconnect/font-display controls
- CSS/JS minification toggle
- Recommendation engine

---

## Phase 3B — Enhanced Premium

### 3B.1 3D Product Viewer
**Files:** `inc/modules/product-viewer-3d/class-opulentia-product-viewer-3d.php`
**Dependencies:** Three.js (CDN) or `<model-viewer>` web component
**Customizer Panel:** Opulentia > WooCommerce > 3D Viewer
**Controls:**
- Auto-rotate speed, zoom limits, background color
- Fallback image for non-3D products
- Placement: replace gallery / alongside gallery / lightbox
- Supported formats: GLB, GLTF

### 3B.2 Custom Fonts Uploader
**Files:** `inc/modules/custom-fonts/class-opulentia-custom-fonts-uploader.php`
**Customizer Panel:** Opulentia > Typography > Custom Fonts
**Features:**
- Upload WOFF2, WOFF, TTF, OTF
- Font family naming, weight mapping
- Preview with sample text
- Preload configuration
- Google Fonts + Adobe Fonts + Custom Fonts unified selector

### 3B.3 Icon Manager
**Files:** `inc/modules/icon-manager/class-opulentia-icon-manager.php`
**Features:**
- 30+ built-in SVG icons (already exists in `class-opulentia-icons.php`)
- Upload custom SVG icons
- Organize into sets/categories
- Shortcode + PHP function output
- Inline SVG with color control

### 3B.4 Color Palette Generator
**Files:** `inc/modules/color-palette/class-opulentia-color-palette.php`
**Customizer Panel:** Opulentia > Global > Color Palette Generator
**Features:**
- Generate 5-color palette from any base color
- Color harmony rules: monochromatic, complementary, analogous, triadic, tetradic
- Extract palette from uploaded image
- WCAG contrast checker built in
- Apply palette with one click

### 3B.5 Maintenance Mode
**Files:** `inc/modules/maintenance-mode/class-opulentia-maintenance-mode.php`
**Customizer Panel:** Opulentia > Maintenance Mode
**Controls:**
- Enable/disable
- Custom logo, heading, description
- Countdown timer (date target)
- Social links
- Subscribe form (Mailchimp integration)
- Background: color, gradient, image, video
- Bypass for logged-in users / by IP / by URL parameter

### 3B.6 GDPR / Cookie Consent
**Files:** `inc/modules/cookie-consent/class-opulentia-cookie-consent.php`
**Customizer Panel:** Opulentia > GDPR
**Controls:**
- Cookie bar position (top/bottom), design customization
- Consent categories: necessary, analytics, marketing
- Auto-block scripts by category
- Cookie expiry duration
- Privacy policy page link
- Granular consent UI (toggle per category)

### 3B.7 CSS/JS Injection
**Files:** `inc/modules/code-injection/class-opulentia-code-injection.php`
**Customizer Panel:** Opulentia > Custom Code
**Controls:**
- Global CSS injection (in `<head>`)
- Global JS injection (in `<head>` or before `</body>`)
- Per-page CSS/JS via meta box
- Device-specific code (desktop/tablet/mobile)
- Syntax highlighting in admin

---

## Phase 3C — Luxury Polish

### 3C.1 Mega Menu Enhancement
**Files:** `inc/modules/mega-menu/` (enhance existing)
**New features:**
- Icon support per menu item (from Icon Manager)
- Background image per menu column
- Tabbed mega menus
- Widget areas in mega menu columns
- Responsive touch support

### 3C.2 Responsive Controls Enhancement
**Files:** `inc/modules/spacing/` + `inc/dynamic-css/` (enhance existing)
**New features:**
- Device preview icons in customizer
- Per-device typography controls
- Per-device spacing controls
- Per-device visibility (hide on mobile/tablet/desktop)
- Custom breakpoint editor

### 3C.3 Form Styler
**Files:** `inc/modules/form-styler/class-opulentia-form-styler.php`
**Compatibility:** Contact Form 7, Gravity Forms, WPForms, Elementor Forms
**Customizer Panel:** Opulentia > Forms
**Controls:**
- Input field styles (bg, border, radius, padding, text)
- Label styles (font, size, color)
- Submit button styles
- Error/success message styles
- Checkbox/radio custom styling

### 3C.4 White Label
**Files:** `inc/modules/white-label/class-opulentia-white-label.php`
**Features:**
- Replace "Opulentia" brand name everywhere
- Custom admin footer text
- Custom dashboard icon
- Hide/rename theme page
- Remove update notifications
- Client-ready mode

### 3C.5 Documentation Generator
**Files:** `inc/modules/docs-generator/class-opulentia-docs-generator.php`
**Features:**
- Auto-generate theme documentation from code
- Module documentation with screenshots
- Shortcode reference
- Filter/hook reference
- Template hierarchy diagram
- Export as HTML

### 3C.6 Layout Library Expansion
**Files:** `inc/modules/layout-library/templates/` (add templates)
**New templates:**
- Header layouts (10 styles)
- Footer layouts (8 styles)
- Hero sections (12 styles)
- About sections (6 styles)
- Team sections (4 styles)
- Testimonials (4 styles)
- Pricing tables (4 styles)
- FAQ (3 styles)
- Contact (3 styles)
- Portfolio (6 styles)
- Blog layouts (4 styles)
- Shop sections (6 styles)

---

## Architecture Pattern

Every module follows this pattern:

```
inc/modules/module-name/
├── class-opulentia-module-name.php    # Main class
├── js/                                # Frontend JS (if needed)
│   └── module-name.js
├── css/                               # Frontend CSS (if needed)
│   └── module-name.css
└── templates/                         # Template files (if needed)
```

**Module class skeleton:**
```php
class Opulentia_Module_Name {
    private static $instance = null;
    public static function get_instance() { ... }

    public function __construct() {
        add_action( 'customize_register', array( $this, 'customize_register' ) );
        add_filter( 'opulentia_dynamic_css', array( $this, 'dynamic_css' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    public function customize_register( $wp_customize ) { ... }
    public function dynamic_css() { ... }
    public function enqueue_scripts() { ... }
}
```

**Registration in functions.php:**
```php
require Opulentia_DIR . '/inc/modules/module-name/class-opulentia-module-name.php';
```

**For modules with admin pages:** add tab in `inc/admin/class-opulentia-admin.php`

---

## What Each Module Must Include

1. **Customizer controls** — settings with sanitization, transport, priority
2. **Frontend output** — dynamic CSS, JS enqueue, template hooks
3. **Sanitization** — `sanitize_hex_color`, `absint`, `wp_kses_post`, `sanitize_text_field`
4. **Escaping** — `esc_attr()`, `esc_html()`, `esc_url()` on output
5. **Capability checks** — `current_user_can('manage_options')` on admin
6. **Nonce verification** — on all form submissions
7. **Default values** — every setting has a sensible default
8. **Internationalization** — `__( 'Text', 'opulentia' )` on all strings
9. **Singleton pattern** — consistent with existing modules
10. **Responsive** — mobile/tablet/desktop breakpoints

---

## Resume Sequence

When you reopen, start here:

1. `git status` to check file state
2. Open `opulentia-theme/docs/master-plan.md`
3. Start Phase 3A.1 (GSAP Animation Presets) — highest visibility feature
4. Each module takes ~2-4 hours to build end-to-end
5. After each module: `php -l` all modified files, check output
6. Test in WP admin customizer after every 2 modules

### Quick-Start Commands
```bash
# PHP syntax check
& "C:\Users\hammad\AppData\Local\Programs\Local\resources\extraResources\lightning-services\php-8.2.29+0\bin\win64\php.exe" -l file.php

# Vite dev
npm run dev

# Vite build
npm run build

# Dembrandt extract (for testing cloner)
dembrandt --json-only --no-sandbox URL

# designlang extract (better for WP)
npx designlang URL --out output/site-name
```

---

## Existing-First Rule

Before building any Phase 3 module, check:
1. Is there a free WP plugin that does this? → Integrate instead of build
2. Is there a free npm/PHP package? → Install instead of build
3. Is there a free MCP server? → Configure instead of build
4. Only build custom if nothing exists

This is enforced by the `existing-first` skill at `.agents/skills/existing-first/`.
