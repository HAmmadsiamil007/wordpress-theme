# Changelog

All notable changes to the Opulentia theme are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-07-09

### Added
- 67 feature modules covering all Astra Addon functionality plus 49 premium extras
- AI Site Cloner — capture, analyze, and apply any website design via URL or screenshot
- GSAP Animation Presets — 12 scroll-triggered reveal animations with customizer controls
- Layout Library — 20+ pre-built section templates with one-click import
- Popup Builder — modal, slide-in, notification bar, and fullscreen popups
- 3D Product Viewer — GLB/GLTF model viewer for WooCommerce products
- White Label — rebrand theme for client delivery
- Performance Dashboard — asset tracking, PageSpeed integration, recommendations
- Customizer Presets — save, apply, import, export full design states
- Custom Fonts Uploader — WOFF2/WOFF/TTF/OTF upload with @font-face generation
- Icon Manager — upload, organize, and output custom SVG icons
- Color Palette Generator — harmony rules, image extraction, WCAG contrast checker
- Maintenance Mode — coming-soon / 503 page with countdown and subscribe form
- GDPR Cookie Consent — category opt-in bar with customizer controls
- CSS/JS Injection — global and per-page custom code injection
- Form Styler — CF7, Gravity Forms, WPForms, Elementor Forms theming
- Documentation Generator — auto-generate HTML docs from code
- Child theme (opulentia-child) for safe customization
- WP-CLI commands: `wp opulentia option get/set`, `wp opulentia module list/enable/disable`, `wp opulentia cloner run`

### Changed
- Complete brand rename from SoleOrigine to Opulentia
- Customizer reorganized into 6 panels (Global, Header, Footer, Layout, WooCommerce, Integrations)
- Dynamic CSS engine expanded to 19 modules (archive, search, page, 404 added)
- Compatibility expanded to 21 plugin integrations

### Fixed
- All SoleOrigine references cleaned across codebase
- Dynamic CSS function_exists() guard consistency
- Live Search JS implemented and enqueued
- theme.json created with full design token palette
- template-blank.php page template added

## [1.0.0] - 2026-06-01

### Added
- Core Foundation — Theme Options API, module manager, common functions
- Header Builder — 3-row system with 5 layout presets
- Footer Builder — 4 layout types with configurable columns
- Blog & Archive — classic, grid, list layouts with customizer controls
- Color System — 9-color global palette with 8 presets
- Typography — per-heading (H1-H6) responsive Google Fonts
- Site Layouts — full-width, boxed, content boxed, fluid, padded
- WooCommerce — full template overrides, quick view, variation swatches
- Advanced Headers — custom page banners with background and overlay
- Mega Menu — multi-column dropdowns with badges and icons
- Sticky/Transparent Header — scroll behavior and conditional display
- Live Search — AJAX dropdown search
- Scroll to Top — customizable back-to-top button
- Dark Mode — system detection and manual toggle
- Schema Markup — Article, Product, Organization, BreadcrumbList
- Performance — CSS minification, font optimization, lazy loading
- Security — CSP headers, login hardening, CSRF nonces
- Accessibility — focus styles, ARIA landmarks, keyboard navigation
- Page Builder Compatibility — Elementor, Gutenberg, Beaver Builder
- LMS Compatibility — LearnDash, LifterLMS
- SEO Compatibility — Yoast, Rank Math
- i18n — .pot translation template, WPML config
- Gutenberg blocks — Hero, Features, Brand Story, Testimonials, Product Grid
- Elementor widgets — matching the 5 Gutenberg blocks
- Customizer live preview (513-line customizer.js)
