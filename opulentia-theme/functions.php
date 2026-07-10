<?php
/**
 * Opulentia Theme Functions
 *
 * @package Opulentia
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -----------------------------------------------------------------------------
// Constants
// -----------------------------------------------------------------------------

define( 'Opulentia_VERSION', '2.0.0' );
define( 'Opulentia_DIR', get_template_directory() );
define( 'Opulentia_URI', get_template_directory_uri() );
define( 'Opulentia_SETTINGS', 'Opulentia_settings' );

// -----------------------------------------------------------------------------
// Theme Update System — DB Version Migration & Background Updater
// Must load early so version checks run before other modules init.
// -----------------------------------------------------------------------------

require Opulentia_DIR . '/inc/theme-update/class-opulentia-theme-updater.php';
require Opulentia_DIR . '/inc/theme-update/functions-update.php';

// -----------------------------------------------------------------------------
// Phase 1 — Core Foundation (must load first)
// -----------------------------------------------------------------------------

/**
 * Theme Options API — singleton + helper functions.
 * Provides Opulentia_get_option(), Opulentia_update_option(), etc.
 */
require Opulentia_DIR . '/inc/core/class-opulentia-theme-options.php';

/**
 * Common Functions — CSS helpers, color utils, spacing, breakpoints.
 */
require Opulentia_DIR . '/inc/core/common-functions.php';

/**
 * HTML Attribute Builder — Opulentia_attr() for HTML element attributes.
 */
require Opulentia_DIR . '/inc/core/class-Opulentia-attr.php';

/**
 * Module Manager — register, activate, deactivate modules with dependency tracking.
 */
require Opulentia_DIR . '/inc/core/class-Opulentia-modules.php';

// -----------------------------------------------------------------------------
// Phase 1 — Legacy Setup (enhanced)
// -----------------------------------------------------------------------------

/** Theme setup: after_setup_theme, CPTs, taxonomies, widgets, pingback */
require Opulentia_DIR . '/inc/class-Opulentia-after-setup.php';

/** Blog Metabox: per-page layout, aspect ratio, featured image override */
require Opulentia_DIR . '/inc/metaboxes/class-Opulentia-blog-metabox.php';

/** Script/style enqueue, Vite HMR, Google Fonts, GSAP CDN */
require Opulentia_DIR . '/inc/class-Opulentia-enqueue.php';

/** SVG Icon utility — 30+ icons, Astra compatibility, wp_kses args */
require Opulentia_DIR . '/inc/class-Opulentia-icons.php';

/** Config-driven Customizer sections, settings, and controls */
require Opulentia_DIR . '/inc/class-Opulentia-customizer-config.php';

// -----------------------------------------------------------------------------
// Legacy Core Modules (made into Phase 1 modules)
// -----------------------------------------------------------------------------

/** Performance optimization engine */
require Opulentia_DIR . '/inc/core/class-Opulentia-performance.php';

/** Security hardening */
require Opulentia_DIR . '/inc/core/class-Opulentia-security.php';

/** Hook/filter system — Opulentia_*_before/after action hooks */
require Opulentia_DIR . '/inc/core/class-Opulentia-hooks.php';

/** Custom widget manager — Social, About, Newsletter widgets */
require Opulentia_DIR . '/inc/core/class-Opulentia-widgets.php';

// -----------------------------------------------------------------------------
// Dynamic CSS Engine (inc/dynamic-css/)
// -----------------------------------------------------------------------------

/** Shared color scheme presets (single source of truth — must load first) */
require Opulentia_DIR . '/inc/dynamic-css/presets.php';

/** Global CSS variables from customizer */
require Opulentia_DIR . '/inc/dynamic-css/global.php';

/** Container layout styles */
require Opulentia_DIR . '/inc/dynamic-css/container-layouts.php';

/** Header dynamic styles */
require Opulentia_DIR . '/inc/dynamic-css/header.php';

/** Footer dynamic styles */
require Opulentia_DIR . '/inc/dynamic-css/footer.php';

/** Blog & archive dynamic styles */
require Opulentia_DIR . '/inc/dynamic-css/blog.php';

/** Single post dynamic styles */
require Opulentia_DIR . '/inc/dynamic-css/single-post.php';

/** Navigation dynamic styles */
require Opulentia_DIR . '/inc/dynamic-css/navigation.php';

/** Sidebar dynamic styles */
require Opulentia_DIR . '/inc/dynamic-css/sidebar.php';

/** Comments dynamic styles */
require Opulentia_DIR . '/inc/dynamic-css/comments.php';

/** Content background dynamic styles */
require Opulentia_DIR . '/inc/dynamic-css/content-background.php';

/** Pagination dynamic styles */
require Opulentia_DIR . '/inc/dynamic-css/pagination.php';

/** WooCommerce dynamic styles */
require Opulentia_DIR . '/inc/dynamic-css/woocommerce.php';

/** Typography dynamic styles */
require Opulentia_DIR . '/inc/dynamic-css/typography.php';

/** Archive page dynamic styles */
require Opulentia_DIR . '/inc/dynamic-css/archive.php';

/** Search results dynamic styles */
require Opulentia_DIR . '/inc/dynamic-css/search.php';

/** Page dynamic styles */
require Opulentia_DIR . '/inc/dynamic-css/page.php';

/** 404 page dynamic styles */
require Opulentia_DIR . '/inc/dynamic-css/404.php';

/** Main dynamic CSS engine — compilation, caching, inline output */
require Opulentia_DIR . '/inc/dynamic-css/class-opulentia-dynamic-css.php';

// -----------------------------------------------------------------------------
// Builder System (inc/builder/)
// -----------------------------------------------------------------------------

/** Header Builder — 3 layout presets, component-based rows */
require Opulentia_DIR . '/inc/builder/class-Opulentia-header-builder.php';

/** Footer Builder — configurable column layouts, component visibility */
require Opulentia_DIR . '/inc/builder/class-Opulentia-footer-builder.php';

// -----------------------------------------------------------------------------
// Plugin Compatibility (inc/compatibility/)
// -----------------------------------------------------------------------------

/** Elementor custom widgets */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-elementor.php';

/** Elementor Pro Theme Builder — header/footer replacement, global color sync */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-elementor-pro.php';

/** Gutenberg custom blocks */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-gutenberg.php';

/** Jetpack — Infinite Scroll, Responsive Videos, Content Options, Sharing */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-jetpack.php';

/** Contact Form 7 — Theme-styled form fields, validation, submit button */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-contact-form-7.php';

/** Yoast SEO breadcrumb integration */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-yoast.php';

/** Rank Math SEO breadcrumb integration */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-rank-math.php';

/** Beaver Builder compatibility */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-beaver-builder.php';

/** AMP compatibility (superseded by modules/amp-support) */

/** Gravity Forms — Theme-styled form fields, validation, submit button */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-gravity-forms.php';

/** Easy Digital Downloads — Download archive, single download, checkout, cart */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-edd.php';

/** Divi Builder — Graceful coexistence, CSS suppression, theme builder overrides */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-divi.php';

/** Beaver Themer — Theme builder header/footer/parts support */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-beaver-themer.php';

/** WPBakery Visual Composer — Graceful coexistence, full-width support */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-visual-composer.php';

/** BuddyPress — Community profiles, groups, activity stream theming */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-buddypress.php';

/** SiteOrigin Page Builder — Theme support, full-width rows, responsive layouts */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-siteorigin.php';

/** WooCommerce Full Suite — product catalog, single product, cart/checkout, quick view, variation swatches */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-woocommerce.php';

/** SureCart compatibility */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-surecart.php';

/** Web Stories compatibility */
require Opulentia_DIR . '/inc/compatibility/class-Opulentia-web-stories.php';

// -----------------------------------------------------------------------------
// Third-Party Integrations (inc/integrations/)
// -----------------------------------------------------------------------------

/** Mailchimp API integration */
require Opulentia_DIR . '/inc/integrations/class-Opulentia-mailchimp.php';

/** Custom Fonts integration */
require Opulentia_DIR . '/inc/integrations/class-Opulentia-custom-fonts.php';

// -----------------------------------------------------------------------------
// Admin Dashboard
// -----------------------------------------------------------------------------

/** Opulentia Admin Dashboard — Module Manager UI, Import/Export */
require Opulentia_DIR . '/inc/admin/class-Opulentia-admin.php';

// -----------------------------------------------------------------------------
// Phase 6 — Typography System
// -----------------------------------------------------------------------------

/** Google Fonts Manager — font list, URL builder, preconnect, preload */
require_once Opulentia_DIR . '/inc/customizer/class-Opulentia-fonts.php';

// -----------------------------------------------------------------------------
// Schema Markup
// -----------------------------------------------------------------------------

/** Schema.org structured data — Article, BreadcrumbList, Organization, Person */
require Opulentia_DIR . '/inc/schema/class-Opulentia-schema.php';

// -----------------------------------------------------------------------------
// Accessibility Module
// -----------------------------------------------------------------------------

/** Accessibility engine — focus styles, skip link, ARIA, keyboard navigation */
require Opulentia_DIR . '/inc/core/class-Opulentia-accessibility.php';

// -----------------------------------------------------------------------------
// Meta Box System
// -----------------------------------------------------------------------------

/** Per-page/post meta boxes — layout, sidebar, header, breadcrumb overrides */
require Opulentia_DIR . '/inc/metabox/class-Opulentia-meta-boxes.php';

// -----------------------------------------------------------------------------
// Feature Modules (inc/modules/)
// -----------------------------------------------------------------------------

/** Advanced Page Headers — custom banners with background, overlay, breadcrumbs */
require Opulentia_DIR . '/inc/modules/advanced-headers/class-Opulentia-advanced-headers.php';

/** Mega Menu — multi-column dropdowns, badges, icons */
require Opulentia_DIR . '/inc/modules/mega-menu/class-Opulentia-mega-menu.php';

/** Blog Pro — infinite scroll, read time, author box, related posts */
require Opulentia_DIR . '/inc/modules/blog-pro/class-Opulentia-blog-pro.php';

/** Advanced Hooks — Custom code injection, hook locations, display conditions */
require Opulentia_DIR . '/inc/modules/advanced-hooks/class-Opulentia-advanced-hooks.php';
require Opulentia_DIR . '/inc/modules/advanced-hooks/class-Opulentia-advanced-hooks-render.php';

/** Live Search — AJAX search with results dropdown */
require Opulentia_DIR . '/inc/modules/live-search/class-Opulentia-live-search.php';

/** Scroll to Top — customizable back-to-top button */
require Opulentia_DIR . '/inc/modules/scroll-to-top/class-Opulentia-scroll-to-top.php';

/** Transparent Header — conditional transparent header with per-page override */
require Opulentia_DIR . '/inc/modules/transparent-header/class-Opulentia-transparent-header.php';

/** Dark Mode — system detection, manual toggle, dark CSS vars */
require Opulentia_DIR . '/inc/modules/dark-mode/class-Opulentia-dark-mode.php';

/** Sticky Header — scroll behavior, separate logo, per-device, animation styles */
require Opulentia_DIR . '/inc/modules/sticky-header/class-Opulentia-sticky-header.php';

/** Breadcrumbs — native + Yoast + Rank Math integration with customizer controls */
require Opulentia_DIR . '/inc/modules/breadcrumbs/class-Opulentia-breadcrumbs.php';

/** Spacing System — per-element padding/margin controls with responsive breakpoints */
require Opulentia_DIR . '/inc/modules/spacing/class-Opulentia-spacing.php';

/** Site Layouts — full-width/boxed/padded layouts with per-page overrides */
require Opulentia_DIR . '/inc/modules/site-layouts/class-Opulentia-site-layouts.php';

/** Site Cloner — AI-powered design cloning from URL/screenshot */
require Opulentia_DIR . '/inc/cloner/class-Opulentia-site-cloner.php';

/** Animation Presets — GSAP scroll reveals, parallax, counters, text split */
require Opulentia_DIR . '/inc/modules/animation-presets/class-opulentia-animation-presets.php';

/** Layout Library — 20+ pre-built sections with one-click import */
require Opulentia_DIR . '/inc/modules/layout-library/class-opulentia-layout-library.php';

/** Popup Builder — modal, notification, slide-in, fullscreen popups with triggers and conditions */
require Opulentia_DIR . '/inc/modules/popup-builder/class-opulentia-popup-builder.php';

/** Conditional Display — per-page element visibility with role/device conditions and reusable condition sets */
require Opulentia_DIR . '/inc/modules/conditional-display/class-opulentia-conditional-display.php';

/** Demo Importer — 5 demo sites with one-click import via OCDI integration */
require Opulentia_DIR . '/inc/modules/demo-importer/class-opulentia-demo-importer.php';

/** Customizer Presets — save, apply, import, export full customizer states */
require Opulentia_DIR . '/inc/modules/customizer-presets/class-opulentia-customizer-presets.php';

/** Performance Dashboard — asset tracking, PageSpeed integration, recommendations */
require Opulentia_DIR . '/inc/modules/performance-dashboard/class-opulentia-performance-dashboard.php';

/** 3D Product Viewer — GLB/GLTF model viewer for WooCommerce products using <model-viewer> */
require Opulentia_DIR . '/inc/modules/product-viewer-3d/class-opulentia-product-viewer-3d.php';

/** Custom Fonts Uploader — WOFF2/WOFF/TTF/OTF upload with @font-face generation and preload */
require Opulentia_DIR . '/inc/modules/custom-fonts/class-opulentia-custom-fonts-uploader.php';

/** Icon Manager — upload, organize, and output custom SVG icons with shortcode support */
require Opulentia_DIR . '/inc/modules/icon-manager/class-opulentia-icon-manager.php';

/** Color Palette Generator — generate 5-color palettes, extract from images, WCAG contrast checker */
require Opulentia_DIR . '/inc/modules/color-palette/class-opulentia-color-palette.php';

/** Maintenance Mode — coming soon / 503 maintenance page with countdown, subscribe, social links and full background control */
require Opulentia_DIR . '/inc/modules/maintenance-mode/class-opulentia-maintenance-mode.php';

/** GDPR Cookie Consent — configurable cookie consent bar with category opt-in, customizer controls, and consent storage */
require Opulentia_DIR . '/inc/modules/gdpr-cookie-consent/class-opulentia-gdpr-cookie-consent.php';

/** CSS / JS Injection — global and per-page custom CSS and JavaScript injection through customizer and meta boxes */
require Opulentia_DIR . '/inc/modules/css-js-injection/class-opulentia-css-js-injection.php';

/** Responsive Controls Enhancement — per-device typography, spacing, visibility, and custom breakpoints */
require Opulentia_DIR . '/inc/modules/responsive-controls/class-opulentia-responsive-controls.php';

/** Form Styler — customizer-driven form styling for CF7, Gravity Forms, WPForms, and Elementor Forms */
require Opulentia_DIR . '/inc/modules/form-styler/class-opulentia-form-styler.php';

/** White Label — rebrand the theme for client use: custom name, author, footer, dashboard icon, and client-ready mode */
require Opulentia_DIR . '/inc/modules/white-label/class-opulentia-white-label.php';

/** Documentation Generator — auto-generate HTML docs from code: modules, shortcodes, filters, actions, templates, customizer panels */
require Opulentia_DIR . '/inc/modules/docs-generator/class-opulentia-docs-generator.php';
require Opulentia_DIR . '/inc/modules/mobile-header/class-opulentia-mobile-header.php';
require Opulentia_DIR . '/inc/modules/lifterlms/class-opulentia-lifterlms.php';
require Opulentia_DIR . '/inc/modules/learndash/class-opulentia-learndash.php';
require Opulentia_DIR . '/inc/modules/amp-support/class-opulentia-amp-support.php';
require Opulentia_DIR . '/inc/modules/rtl/class-opulentia-rtl.php';
require Opulentia_DIR . '/inc/modules/custom-404/class-opulentia-custom-404.php';
require Opulentia_DIR . '/inc/modules/social-sharing/class-opulentia-social-sharing.php';
require Opulentia_DIR . '/inc/modules/woocommerce-catalog/class-opulentia-woocommerce-catalog.php';
require Opulentia_DIR . '/inc/modules/woocommerce-recently-viewed/class-opulentia-woocommerce-recently-viewed.php';
require Opulentia_DIR . '/inc/modules/sidebar-manager/class-opulentia-sidebar-manager.php';
require Opulentia_DIR . '/inc/modules/table-of-contents/class-opulentia-table-of-contents.php';
require Opulentia_DIR . '/inc/modules/woocommerce-product-video/class-opulentia-woocommerce-product-video.php';
require Opulentia_DIR . '/inc/modules/portfolio-cpt/class-opulentia-portfolio-cpt.php';
require Opulentia_DIR . '/inc/modules/team-cpt/class-opulentia-team-cpt.php';
require Opulentia_DIR . '/inc/modules/testimonial-cpt/class-opulentia-testimonial-cpt.php';
require Opulentia_DIR . '/inc/modules/faq-cpt/class-opulentia-faq-cpt.php';
require Opulentia_DIR . '/inc/modules/wishlist-compare/class-opulentia-wishlist-compare.php';
require Opulentia_DIR . '/inc/modules/ajax-filtering/class-opulentia-ajax-filtering.php';
require Opulentia_DIR . '/inc/modules/content-restriction/class-opulentia-content-restriction.php';
require Opulentia_DIR . '/inc/modules/google-maps/class-opulentia-google-maps.php';
require Opulentia_DIR . '/inc/modules/countdown-timer/class-opulentia-countdown-timer.php';
require Opulentia_DIR . '/inc/modules/social-login/class-opulentia-social-login.php';
require Opulentia_DIR . '/inc/modules/login-customizer/class-opulentia-login-customizer.php';
require Opulentia_DIR . '/inc/modules/nav-menu-roles/class-opulentia-nav-menu-roles.php';
require Opulentia_DIR . '/inc/modules/advanced-search/class-opulentia-advanced-search.php';
require Opulentia_DIR . '/inc/modules/custom-widgets/class-opulentia-custom-widgets.php';
require Opulentia_DIR . '/inc/modules/scroll-reveal/class-opulentia-scroll-reveal.php';
require Opulentia_DIR . '/inc/modules/reading-progress/class-opulentia-reading-progress.php';
require Opulentia_DIR . '/inc/modules/pricing-tables/class-opulentia-pricing-tables.php';
require Opulentia_DIR . '/inc/modules/count-up/class-opulentia-count-up.php';
require Opulentia_DIR . '/inc/modules/image-hotspots/class-opulentia-image-hotspots.php';
require Opulentia_DIR . '/inc/modules/gradient-builder/class-opulentia-gradient-builder.php';
require Opulentia_DIR . '/inc/modules/author-box/class-opulentia-author-box.php';

// -----------------------------------------------------------------------------
// Legacy Helpers (shared functions, no dual code paths)
// -----------------------------------------------------------------------------

/** Template tags — posted_on, posted_by, breadcrumbs, pagination, etc. */
require Opulentia_DIR . '/inc/template-tags.php';

/** Template functions — body classes, excerpt, thumbnail, schema, etc. */
require Opulentia_DIR . '/inc/template-functions.php';

// -----------------------------------------------------------------------------
// WP-CLI Commands
// -----------------------------------------------------------------------------

/**
 * WP-CLI integration for theme management.
 * Commands: wp opulentia option get/set, wp opulentia module list/enable/disable, wp opulentia cloner run
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require Opulentia_DIR . '/inc/class-opulentia-cli.php';
}

// -----------------------------------------------------------------------------
// Module Manager — Register System Modules
// -----------------------------------------------------------------------------

/**
 * Register all built-in system modules.
 *
 * Each module maps to a feature area that can be independently
 * enabled/disabled via filter or customizer setting.
 *
 * Hooked early (priority 1 on 'init') so they're registered before
 * the Module Manager's own init at 'after_setup_theme' priority 20.
 */
function Opulentia_register_system_modules() {
	$modules = array(
		'core'                        => array(
			'name'        => __( 'Core Foundation', 'opulentia' ),
			'description' => __( 'Theme options API, common functions, attribute builder.', 'opulentia' ),
			'default'     => true,
			'priority'    => 1,
			'category'    => 'core',
		),
		'header-builder'              => array(
			'name'         => __( 'Header Builder', 'opulentia' ),
			'description'  => __( '3-row header builder with logo, nav, search, cart components.', 'opulentia' ),
			'default'      => true,
			'priority'     => 10,
			'category'     => 'builder',
			'dependencies' => array( 'core' ),
		),
		'footer-builder'              => array(
			'name'         => __( 'Footer Builder', 'opulentia' ),
			'description'  => __( 'Configurable footer columns, widgets, social icons.', 'opulentia' ),
			'default'      => true,
			'priority'     => 20,
			'category'     => 'builder',
			'dependencies' => array( 'core' ),
		),
		'blog-layouts'                => array(
			'name'         => __( 'Blog & Archive Layouts', 'opulentia' ),
			'description'  => __( 'Classic, grid, and list layouts for blog archives.', 'opulentia' ),
			'default'      => true,
			'priority'     => 30,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'blog-pro'                    => array(
			'name'         => __( 'Blog Pro', 'opulentia' ),
			'description'  => __( 'Infinite scroll, read time, author box, related posts.', 'opulentia' ),
			'default'      => true,
			'priority'     => 35,
			'category'     => 'content',
			'dependencies' => array( 'core', 'blog-layouts' ),
		),
		'woocommerce'                 => array(
			'name'         => __( 'WooCommerce Enhancements', 'opulentia' ),
			'description'  => __( 'Product grid, quick view, cart/checkout styling, variation swatches.', 'opulentia' ),
			'default'      => true,
			'priority'     => 40,
			'category'     => 'ecommerce',
			'dependencies' => array( 'core' ),
		),
		'dynamic-css'                 => array(
			'name'         => __( 'Dynamic CSS Engine', 'opulentia' ),
			'description'  => __( 'Real-time CSS generation from customizer settings.', 'opulentia' ),
			'default'      => true,
			'priority'     => 5,
			'category'     => 'core',
			'dependencies' => array( 'core' ),
		),
		'customizer'                  => array(
			'name'         => __( 'Customizer Controls', 'opulentia' ),
			'description'  => __( 'Config-driven customizer sections, settings, and controls.', 'opulentia' ),
			'default'      => true,
			'priority'     => 15,
			'category'     => 'core',
			'dependencies' => array( 'core' ),
		),
		'performance'                 => array(
			'name'         => __( 'Performance Optimizations', 'opulentia' ),
			'description'  => __( 'CSS minification, font optimization, lazy loading, defer JS.', 'opulentia' ),
			'default'      => true,
			'priority'     => 50,
			'category'     => 'optimization',
			'dependencies' => array( 'core' ),
		),
		'security'                    => array(
			'name'         => __( 'Security Hardening', 'opulentia' ),
			'description'  => __( 'CSRF tokens, security headers, login hardening, sanitization.', 'opulentia' ),
			'default'      => true,
			'priority'     => 60,
			'category'     => 'optimization',
			'dependencies' => array( 'core' ),
		),
		'accessibility'               => array(
			'name'         => __( 'Accessibility', 'opulentia' ),
			'description'  => __( 'Focus styles, ARIA landmarks, keyboard navigation, skip link.', 'opulentia' ),
			'default'      => true,
			'priority'     => 13,
			'category'     => 'core',
			'dependencies' => array( 'core' ),
		),
		'schema'                      => array(
			'name'         => __( 'Schema Markup', 'opulentia' ),
			'description'  => __( 'JSON-LD structured data for SEO (Article, Product, Organization, etc.).', 'opulentia' ),
			'default'      => true,
			'priority'     => 65,
			'category'     => 'seo',
			'dependencies' => array( 'core' ),
		),
		'advanced-headers'            => array(
			'name'         => __( 'Advanced Page Headers', 'opulentia' ),
			'description'  => __( 'Custom page banners with background images, overlays, breadcrumbs.', 'opulentia' ),
			'default'      => true,
			'priority'     => 25,
			'category'     => 'builder',
			'dependencies' => array( 'core' ),
		),
		'mega-menu'                   => array(
			'name'         => __( 'Mega Menu', 'opulentia' ),
			'description'  => __( 'Multi-column mega dropdowns, menu badges, icons, animations.', 'opulentia' ),
			'default'      => false,
			'priority'     => 12,
			'category'     => 'builder',
			'dependencies' => array( 'core' ),
		),
		'live-search'                 => array(
			'name'         => __( 'Live Search', 'opulentia' ),
			'description'  => __( 'AJAX live search with results dropdown for posts and products.', 'opulentia' ),
			'default'      => true,
			'priority'     => 45,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'scroll-to-top'               => array(
			'name'         => __( 'Scroll to Top', 'opulentia' ),
			'description'  => __( 'Customizable back-to-top button.', 'opulentia' ),
			'default'      => true,
			'priority'     => 55,
			'category'     => 'ui',
			'dependencies' => array( 'core' ),
		),
		'transparent-header'          => array(
			'name'         => __( 'Transparent Header', 'opulentia' ),
			'description'  => __( 'Transparent header with conditional page display.', 'opulentia' ),
			'default'      => true,
			'priority'     => 11,
			'category'     => 'builder',
			'dependencies' => array( 'core' ),
		),
		'sticky-header'               => array(
			'name'         => __( 'Sticky Header', 'opulentia' ),
			'description'  => __( 'Sticky header with scroll behavior, separate logo, animation styles, and hide-on-scroll-up.', 'opulentia' ),
			'default'      => true,
			'priority'     => 9,
			'category'     => 'builder',
			'dependencies' => array( 'core', 'header-builder' ),
		),
		'breadcrumbs'                 => array(
			'name'         => __( 'Breadcrumbs', 'opulentia' ),
			'description'  => __( 'Native breadcrumbs with Yoast SEO & Rank Math integration, customizer controls, and Schema.org markup.', 'opulentia' ),
			'default'      => true,
			'priority'     => 8,
			'category'     => 'seo',
			'dependencies' => array( 'core' ),
		),
		'site-layouts'                => array(
			'name'         => __( 'Site Layouts', 'opulentia' ),
			'description'  => __( 'Full-width, boxed, and padded layout options with container width control and per-page override meta box.', 'opulentia' ),
			'default'      => true,
			'priority'     => 6,
			'category'     => 'layout',
			'dependencies' => array( 'core' ),
		),
		'spacing'                     => array(
			'name'         => __( 'Spacing System', 'opulentia' ),
			'description'  => __( 'Per-element padding/margin controls for containers, header, footer, content, blog, sections, and widgets with responsive breakpoints.', 'opulentia' ),
			'default'      => true,
			'priority'     => 7,
			'category'     => 'layout',
			'dependencies' => array( 'core' ),
		),
		'dark-mode'                   => array(
			'name'         => __( 'Dark Mode', 'opulentia' ),
			'description'  => __( 'Automatic and manual dark mode with toggle.', 'opulentia' ),
			'default'      => false,
			'priority'     => 75,
			'category'     => 'ui',
			'dependencies' => array( 'core' ),
		),
		'meta-boxes'                  => array(
			'name'         => __( 'Page/Post Meta Boxes', 'opulentia' ),
			'description'  => __( 'Per-page overrides for layout, sidebar, header, breadcrumbs.', 'opulentia' ),
			'default'      => true,
			'priority'     => 14,
			'category'     => 'core',
			'dependencies' => array( 'core' ),
		),
		'integrations'                => array(
			'name'         => __( 'Third-Party Integrations', 'opulentia' ),
			'description'  => __( 'Mailchimp, Elementor, Gutenberg, Yoast SEO, Rank Math, LearnDash, LifterLMS, Beaver Builder, AMP compatibility.', 'opulentia' ),
			'default'      => true,
			'priority'     => 70,
			'category'     => 'compatibility',
			'dependencies' => array( 'core' ),
		),
		'site-cloner'                 => array(
			'name'         => __( 'Site Cloner', 'opulentia' ),
			'description'  => __( 'AI-powered website cloner: capture, analyze, and apply any site design into your theme.', 'opulentia' ),
			'default'      => true,
			'priority'     => 80,
			'category'     => 'tools',
			'dependencies' => array( 'core' ),
		),
		'animation-presets'           => array(
			'name'         => __( 'Animation Presets', 'opulentia' ),
			'description'  => __( 'GSAP-powered scroll reveals, parallax, counters, stagger, and text split animations with full customizer controls.', 'opulentia' ),
			'default'      => true,
			'priority'     => 12,
			'category'     => 'ui',
			'dependencies' => array( 'core' ),
		),
		'layout-library'              => array(
			'name'         => __( 'Layout Library', 'opulentia' ),
			'description'  => __( '20+ pre-built sections with one-click import as reusable blocks. Category and industry filtering.', 'opulentia' ),
			'default'      => true,
			'priority'     => 22,
			'category'     => 'builder',
			'dependencies' => array( 'core' ),
		),
		'popup-builder'               => array(
			'name'         => __( 'Popup Builder', 'opulentia' ),
			'description'  => __( 'Modal, notification bar, slide-in, and fullscreen popups with time/scroll/exit/click triggers, frequency control, and display conditions.', 'opulentia' ),
			'default'      => false,
			'priority'     => 24,
			'category'     => 'builder',
			'dependencies' => array( 'core' ),
		),
		'conditional-display'         => array(
			'name'         => __( 'Conditional Display', 'opulentia' ),
			'description'  => __( 'Per-page element visibility (header, footer, sidebar, breadcrumbs, scroll-to-top) with user role, device, and reusable condition set rules.', 'opulentia' ),
			'default'      => true,
			'priority'     => 26,
			'category'     => 'layout',
			'dependencies' => array( 'core' ),
		),
		'demo-importer'               => array(
			'name'         => __( 'Demo Importer', 'opulentia' ),
			'description'  => __( '5 one-click demo imports (Business, Portfolio, Shop, Agency, Landing) with content, widgets, and customizer settings.', 'opulentia' ),
			'default'      => true,
			'priority'     => 28,
			'category'     => 'tools',
			'dependencies' => array( 'core' ),
		),
		'customizer-presets'          => array(
			'name'         => __( 'Customizer Presets', 'opulentia' ),
			'description'  => __( '6 built-in design presets (Dark Luxury, Light Elegance, Midnight Blue, Forest Green, Rose Gold, Ocean Deep) with save, apply, import, and export.', 'opulentia' ),
			'default'      => true,
			'priority'     => 4,
			'category'     => 'core',
			'dependencies' => array( 'core' ),
		),
		'performance-dashboard'       => array(
			'name'         => __( 'Performance Dashboard', 'opulentia' ),
			'description'  => __( 'Asset weight tracking, PageSpeed Insights integration, module impact report, lazy load, minification, preload, and recommendations.', 'opulentia' ),
			'default'      => true,
			'priority'     => 130,
			'category'     => 'optimization',
			'dependencies' => array( 'core' ),
		),
		'product-viewer-3d'           => array(
			'name'         => __( '3D Product Viewer', 'opulentia' ),
			'description'  => __( 'GLB/GLTF 3D model viewer for WooCommerce products using <model-viewer> web component.', 'opulentia' ),
			'default'      => false,
			'priority'     => 42,
			'category'     => 'ecommerce',
			'dependencies' => array( 'core', 'woocommerce' ),
		),
		'custom-fonts-uploader'       => array(
			'name'         => __( 'Custom Fonts Uploader', 'opulentia' ),
			'description'  => __( 'Upload WOFF2/WOFF/TTF/OTF fonts, generate @font-face CSS, preload hints, and integrate with font selectors.', 'opulentia' ),
			'default'      => true,
			'priority'     => 3,
			'category'     => 'core',
			'dependencies' => array( 'core' ),
		),
		'icon-manager'                => array(
			'name'         => __( 'Icon Manager', 'opulentia' ),
			'description'  => __( 'Upload, organize, and output custom SVG icons. Shortcode and PHP function output with color/size control.', 'opulentia' ),
			'default'      => true,
			'priority'     => 11,
			'category'     => 'ui',
			'dependencies' => array( 'core' ),
		),
		'color-palette'               => array(
			'name'         => __( 'Color Palette Generator', 'opulentia' ),
			'description'  => __( 'Generate 5-color palettes using harmony rules, extract colors from images, and check WCAG contrast ratios.', 'opulentia' ),
			'default'      => true,
			'priority'     => 2,
			'category'     => 'ui',
			'dependencies' => array( 'core' ),
		),
		'maintenance-mode'            => array(
			'name'         => __( 'Maintenance Mode', 'opulentia' ),
			'description'  => __( 'Coming-soon / 503 maintenance page with countdown timer, subscribe form, social links, and custom backgrounds.', 'opulentia' ),
			'default'      => true,
			'priority'     => 40,
			'category'     => 'utility',
			'dependencies' => array( 'core' ),
		),
		'gdpr-cookie-consent'         => array(
			'name'         => __( 'GDPR / Cookie Consent', 'opulentia' ),
			'description'  => __( 'Customizable cookie consent bar with category opt-in (necessary, analytics, marketing), Cookie Policy page linking, and consent persistence.', 'opulentia' ),
			'default'      => false,
			'priority'     => 45,
			'category'     => 'utility',
			'dependencies' => array( 'core' ),
		),
		'css-js-injection'            => array(
			'name'         => __( 'CSS / JS Injection', 'opulentia' ),
			'description'  => __( 'Add custom CSS and JavaScript globally via the customizer, plus per-page CSS/JS via meta boxes. Includes responsive media query zones.', 'opulentia' ),
			'default'      => true,
			'priority'     => 50,
			'category'     => 'developer',
			'dependencies' => array( 'core' ),
		),
		'responsive-controls'         => array(
			'name'         => __( 'Responsive Controls Enhancement', 'opulentia' ),
			'description'  => __( 'Per-device typography (body, H1–H3 sizes), device visibility toggles (hide header/footer/sidebar on mobile/tablet/desktop), custom breakpoint editor, and responsive content width control.', 'opulentia' ),
			'default'      => true,
			'priority'     => 55,
			'category'     => 'layout',
			'dependencies' => array( 'core' ),
		),
		'form-styler'                 => array(
			'name'         => __( 'Form Styler', 'opulentia' ),
			'description'  => __( 'Customizer-driven form styling for Contact Form 7, Gravity Forms, WPForms, and Elementor Forms. Controls inputs, labels, buttons, messages, and checkboxes.', 'opulentia' ),
			'default'      => true,
			'priority'     => 60,
			'category'     => 'compatibility',
			'dependencies' => array( 'core' ),
		),
		'white-label'                 => array(
			'name'         => __( 'White Label', 'opulentia' ),
			'description'  => __( 'Rebrand the theme for client delivery — custom brand name, author, admin footer, dashboard icon, hide theme page, and client-ready mode.', 'opulentia' ),
			'default'      => false,
			'priority'     => 100,
			'category'     => 'developer',
			'dependencies' => array( 'core' ),
		),
		'docs-generator'              => array(
			'name'         => __( 'Documentation Generator', 'opulentia' ),
			'description'  => __( 'Auto-generate theme documentation from code: module inventory, shortcode reference, filter/hook list, template hierarchy, and customizer panels. Downloadable HTML export.', 'opulentia' ),
			'default'      => true,
			'priority'     => 110,
			'category'     => 'developer',
			'dependencies' => array( 'core' ),
		),
		'mobile-header'               => array(
			'name'         => __( 'Mobile Header', 'opulentia' ),
			'description'  => __( 'Dedicated mobile header with separate logo, hamburger/text toggle, slide/fade/dropdown animations, sticky option, and per-device breakpoint control.', 'opulentia' ),
			'default'      => true,
			'priority'     => 8,
			'category'     => 'builder',
			'dependencies' => array( 'core' ),
		),
		'lifterlms'                   => array(
			'name'         => __( 'LifterLMS Integration', 'opulentia' ),
			'description'  => __( 'Full LifterLMS styling: course grid columns, color controls, styled cards, progress bars, access plans, lesson previews, checkout, student dashboard.', 'opulentia' ),
			'default'      => true,
			'priority'     => 72,
			'category'     => 'compatibility',
			'dependencies' => array( 'core' ),
		),
		'learndash'                   => array(
			'name'         => __( 'LearnDash Integration', 'opulentia' ),
			'description'  => __( 'Full LearnDash styling: course grid columns, color controls, focus mode, progress bars, quizzes, lesson/topic item lists, styled cards.', 'opulentia' ),
			'default'      => true,
			'priority'     => 73,
			'category'     => 'compatibility',
			'dependencies' => array( 'core' ),
		),
		'amp-support'                 => array(
			'name'         => __( 'AMP Support', 'opulentia' ),
			'description'  => __( 'AMP compatibility: disables non-AMP JS, replaces nav with amp-sidebar, custom AMP logo, AMP-specific CSS sanitization, customizer controls.', 'opulentia' ),
			'default'      => true,
			'priority'     => 74,
			'category'     => 'compatibility',
			'dependencies' => array( 'core' ),
		),
		'rtl'                         => array(
			'name'         => __( 'RTL Language Support', 'opulentia' ),
			'description'  => __( 'Full RTL direction support: flips navigation, header, footer, sidebar, WooCommerce, forms, widgets. Custom RTL font family, base font size, and line height controls.', 'opulentia' ),
			'default'      => true,
			'priority'     => 75,
			'category'     => 'compatibility',
			'dependencies' => array( 'core' ),
		),
		'custom-404'                  => array(
			'name'         => __( 'Custom 404 Page', 'opulentia' ),
			'description'  => __( 'Full custom 404 page builder: centered/split/minimal/illustrated layouts, custom title/subtitle/message, illustration upload, search form, recent posts, popular pages, CTA button, background image.', 'opulentia' ),
			'default'      => true,
			'priority'     => 76,
			'category'     => 'builder',
			'dependencies' => array( 'core' ),
		),
		'social-sharing'              => array(
			'name'         => __( 'Social Sharing', 'opulentia' ),
			'description'  => __( 'Share buttons for Facebook, X/Twitter, LinkedIn, Pinterest, WhatsApp, Email, and Copy Link on posts, pages, and products. Customizer: position, style, per-network toggle, accent color.', 'opulentia' ),
			'default'      => true,
			'priority'     => 77,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'woocommerce-catalog'         => array(
			'name'         => __( 'WooCommerce Catalog Mode', 'opulentia' ),
			'description'  => __( 'Turn your store into a catalog: hide prices, remove add-to-cart buttons, show inquiry/contact button, redirect cart/checkout. Full customizer control.', 'opulentia' ),
			'default'      => false,
			'priority'     => 41,
			'category'     => 'ecommerce',
			'dependencies' => array( 'core', 'woocommerce' ),
		),
		'woocommerce-recently-viewed' => array(
			'name'         => __( 'Recently Viewed Products', 'opulentia' ),
			'description'  => __( 'Track and display recently viewed products via localStorage. Shortcode, widget, and auto-display on single product pages. Custom columns and count.', 'opulentia' ),
			'default'      => true,
			'priority'     => 42,
			'category'     => 'ecommerce',
			'dependencies' => array( 'core', 'woocommerce' ),
		),
		'sidebar-manager'             => array(
			'name'         => __( 'Sidebar Manager', 'opulentia' ),
			'description'  => __( 'Create unlimited custom widget areas, assign default sidebars per post type, and override per-page via meta box. Customizer textarea for sidebar names.', 'opulentia' ),
			'default'      => true,
			'priority'     => 78,
			'category'     => 'layout',
			'dependencies' => array( 'core' ),
		),
		'table-of-contents'           => array(
			'name'         => __( 'Table of Contents', 'opulentia' ),
			'description'  => __( 'Auto-generate TOC from h2/h3 headings on posts. Three display modes: inline, sticky sidebar, floating. Collapsible, smooth scroll, active heading highlight.', 'opulentia' ),
			'default'      => true,
			'priority'     => 79,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'woocommerce-product-video'   => array(
			'name'         => __( 'Product Video', 'opulentia' ),
			'description'  => __( 'Add YouTube, Vimeo, or self-hosted MP4 videos to WooCommerce products. Meta box on product edit. Display options: replace image, below image, or gallery thumbnail.', 'opulentia' ),
			'default'      => false,
			'priority'     => 43,
			'category'     => 'ecommerce',
			'dependencies' => array( 'core', 'woocommerce' ),
		),
		'portfolio-cpt'               => array(
			'name'         => __( 'Portfolio CPT', 'opulentia' ),
			'description'  => __( 'Portfolio custom post type with grid shortcode, taxonomy, hover effects, and single template. Customizer: columns, gap, hover style.', 'opulentia' ),
			'default'      => true,
			'priority'     => 82,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'team-cpt'                    => array(
			'name'         => __( 'Team CPT', 'opulentia' ),
			'description'  => __( 'Team member custom post type with position, bio, email, social links. Grid shortcode with hover effects and category filtering.', 'opulentia' ),
			'default'      => true,
			'priority'     => 83,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'testimonial-cpt'             => array(
			'name'         => __( 'Testimonial CPT', 'opulentia' ),
			'description'  => __( 'Testimonial custom post type with rating, company attribution. Grid/slider shortcode with scroll-snap carousel, star ratings, auto-play.', 'opulentia' ),
			'default'      => true,
			'priority'     => 84,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'faq-cpt'                     => array(
			'name'         => __( 'FAQ CPT', 'opulentia' ),
			'description'  => __( 'FAQ custom post type with category taxonomy. Accordion/toggle/grouped display shortcode. FAQPage schema.org markup. Live search filter.', 'opulentia' ),
			'default'      => true,
			'priority'     => 85,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'wishlist-compare'            => array(
			'name'         => __( 'Wishlist & Compare', 'opulentia' ),
			'description'  => __( 'Wishlist with session/cookie storage and comparison table with max product limit. AJAX toggle buttons on product pages and shortcodes.', 'opulentia' ),
			'default'      => true,
			'priority'     => 44,
			'category'     => 'ecommerce',
			'dependencies' => array( 'core', 'woocommerce' ),
		),
		'ajax-filtering'              => array(
			'name'         => __( 'AJAX Product Filtering', 'opulentia' ),
			'description'  => __( 'AJAX-powered product filtering by category, price range, and attributes on shop archives. Customizer position and filter controls.', 'opulentia' ),
			'default'      => true,
			'priority'     => 45,
			'category'     => 'ecommerce',
			'dependencies' => array( 'core', 'woocommerce' ),
		),
		'content-restriction'         => array(
			'name'         => __( 'Content Restriction', 'opulentia' ),
			'description'  => __( 'Restrict content by user role or login status via shortcodes and per-post meta box. Customizable restriction notice and redirect options.', 'opulentia' ),
			'default'      => true,
			'priority'     => 81,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'nav-menu-roles'              => array(
			'name'         => __( 'Nav Menu Roles', 'opulentia' ),
			'description'  => __( 'Restrict menu item visibility by user role via custom walker and meta box. Customizer toggle for enable/disable and default fallback behavior.', 'opulentia' ),
			'default'      => true,
			'priority'     => 93,
			'category'     => 'ui',
			'dependencies' => array( 'core' ),
		),
		'advanced-search'             => array(
			'name'         => __( 'Advanced Search', 'opulentia' ),
			'description'  => __( 'AJAX-powered search form with post type tabs, keyboard navigation, search history via localStorage, and WooCommerce product tab support.', 'opulentia' ),
			'default'      => true,
			'priority'     => 94,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'custom-widgets'              => array(
			'name'         => __( 'Custom Widgets', 'opulentia' ),
			'description'  => __( 'Premium widget pack: Social Icons, Recent Posts with thumbnails, Author Bio, and Contact Info widgets with Opulentia dark styling.', 'opulentia' ),
			'default'      => true,
			'priority'     => 95,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'scroll-reveal'               => array(
			'name'         => __( 'Scroll Reveal', 'opulentia' ),
			'description'  => __( 'IntersectionObserver-based content reveal on scroll. Shortcode wrapper, auto-reveal on post content, 7 effects, respects reduced-motion.', 'opulentia' ),
			'default'      => true,
			'priority'     => 96,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'reading-progress'            => array(
			'name'         => __( 'Reading Progress', 'opulentia' ),
			'description'  => __( 'Thin gold progress bar at page top showing reading progress on posts. Customizable height, color, gradient, and position.', 'opulentia' ),
			'default'      => true,
			'priority'     => 97,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'pricing-tables'              => array(
			'name'         => __( 'Pricing Tables', 'opulentia' ),
			'description'  => __( 'Pricing table shortcode with multiple columns, featured plan highlight, checkmark feature list, and responsive card layout.', 'opulentia' ),
			'default'      => true,
			'priority'     => 98,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'count-up'                    => array(
			'name'         => __( 'Count-up Numbers', 'opulentia' ),
			'description'  => __( 'Animated number counter shortcode with IntersectionObserver, easeOutQuad easing, prefix/suffix, labels, and prefers-reduced-motion support.', 'opulentia' ),
			'default'      => true,
			'priority'     => 99,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'image-hotspots'              => array(
			'name'         => __( 'Image Hotspots', 'opulentia' ),
			'description'  => __( 'Interactive image hotspots with clickable gold pins showing popup tooltips. Percentage-based positioning for responsive layouts.', 'opulentia' ),
			'default'      => true,
			'priority'     => 100,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'gradient-builder'            => array(
			'name'         => __( 'Gradient Builder', 'opulentia' ),
			'description'  => __( 'Customizer gradient presets with CSS variable output, gradient background shortcode, and gradient button shortcode. Gold/dark/accent presets.', 'opulentia' ),
			'default'      => true,
			'priority'     => 101,
			'category'     => 'ui',
			'dependencies' => array( 'core' ),
		),
		'author-box'                  => array(
			'name'         => __( 'Author Box', 'opulentia' ),
			'description'  => __( 'Enhanced author box on single posts with avatar, bio, social links, user profile fields, and recent posts list. Shortcode support.', 'opulentia' ),
			'default'      => true,
			'priority'     => 102,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'login-customizer'            => array(
			'name'         => __( 'Login Customizer', 'opulentia' ),
			'description'  => __( 'Customize the WordPress login page: upload logo, set background color/image, form background/text colors, button colors, custom CSS. Brand the entire login experience.', 'opulentia' ),
			'default'      => true,
			'priority'     => 80,
			'category'     => 'ui',
			'dependencies' => array( 'core' ),
		),
		'google-maps'                 => array(
			'name'         => __( 'Google Maps', 'opulentia' ),
			'description'  => __( 'Leaflet-based interactive maps via shortcode. Customizer controls for default address, coordinates, zoom, height, and marker icon. Responsive 16:9/4:3 container, no API key required.', 'opulentia' ),
			'default'      => true,
			'priority'     => 90,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'countdown-timer'             => array(
			'name'         => __( 'Countdown Timer', 'opulentia' ),
			'description'  => __( 'Countdown timer shortcode with days/hours/minutes/seconds. WooCommerce sale end date integration. Gutenberg block. Customizer: default style, size, WC toggle, expiry text. Dark/light/inline styles with gold accent.', 'opulentia' ),
			'default'      => true,
			'priority'     => 91,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
		'social-login'                => array(
			'name'         => __( 'Social Login', 'opulentia' ),
			'description'  => __( 'Google OAuth and Facebook login buttons on login, registration, and comment forms. Customizer controls for client ID/secret, button style, and per-form visibility toggles.', 'opulentia' ),
			'default'      => false,
			'priority'     => 92,
			'category'     => 'content',
			'dependencies' => array( 'core' ),
		),
	);

	foreach ( $modules as $slug => $args ) {
		Opulentia_register_module( $slug, $args );
	}
}
add_action( 'init', 'Opulentia_register_system_modules', 1 );

// -----------------------------------------------------------------------------
// WooCommerce Pro — AJAX Endpoints
// -----------------------------------------------------------------------------

/**
 * AJAX: Load product HTML for quick view modal.
 */
function Opulentia_ajax_quick_view() {
	check_ajax_referer( 'Opulentia_wc_pro_nonce', 'nonce' );

	$product_id = absint( $_POST['product_id'] );
	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => __( 'Invalid product.', 'opulentia' ) ) );
	}

	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		wp_send_json_error( array( 'message' => __( 'Product not found.', 'opulentia' ) ) );
	}

	// Set global product and post data for template functions.
	$GLOBALS['product'] = $product;
	$GLOBALS['post']    = get_post( $product_id );
	setup_postdata( $GLOBALS['post'] );

	ob_start();
	wc_get_template_part( 'quick-view' );
	$html = ob_get_clean();

	wp_reset_postdata();

	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_Opulentia_quick_view', 'Opulentia_ajax_quick_view' );
add_action( 'wp_ajax_nopriv_Opulentia_quick_view', 'Opulentia_ajax_quick_view' );

/**
 * AJAX: Add product to cart without page refresh.
 */
function Opulentia_ajax_add_to_cart() {
	check_ajax_referer( 'Opulentia_wc_pro_nonce', 'nonce' );

	$product_id = absint( $_POST['product_id'] );
	$quantity   = absint( $_POST['quantity'] );

	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => __( 'Invalid product.', 'opulentia' ) ) );
	}

	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		wp_send_json_error( array( 'message' => __( 'Product not found.', 'opulentia' ) ) );
	}

	$cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity );

	if ( $cart_item_key ) {
		ob_start();
		woocommerce_mini_cart();
		$mini_cart = ob_get_clean();

		wp_send_json_success(
			array(
				'cart_item_key' => $cart_item_key,
				'fragments'     => array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
				),
				'cart_hash'     => WC()->cart->get_cart_hash(),
			)
		);
	} else {
		wp_send_json_error(
			array(
				'message' => wc_get_notices( 'error' ),
			)
		);
	}
}
add_action( 'wp_ajax_Opulentia_ajax_add_to_cart', 'Opulentia_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_Opulentia_ajax_add_to_cart', 'Opulentia_ajax_add_to_cart' );

/**
 * AJAX: Get current cart count.
 */
function Opulentia_ajax_cart_count() {
	check_ajax_referer( 'Opulentia_wc_pro_nonce', 'nonce' );

	if ( ! WC()->cart ) {
		wp_send_json_success( array( 'count' => 0 ) );
	}

	wp_send_json_success(
		array(
			'count' => WC()->cart->get_cart_contents_count(),
		)
	);
}
add_action( 'wp_ajax_Opulentia_cart_count', 'Opulentia_ajax_cart_count' );
add_action( 'wp_ajax_nopriv_Opulentia_cart_count', 'Opulentia_ajax_cart_count' );

/**
 * AJAX: Get mini cart HTML.
 */
function Opulentia_ajax_mini_cart() {
	check_ajax_referer( 'Opulentia_wc_pro_nonce', 'nonce' );

	ob_start();
	?>
	<div class="mini-cart-dropdown__header">
		<?php
		printf(
			esc_html__( 'Shopping Cart (%d)', 'opulentia' ),
			WC()->cart->get_cart_contents_count()
		);
		?>
	</div>

	<?php if ( WC()->cart->is_empty() ) : ?>
		<div class="mini-cart-dropdown__empty">
			<?php esc_html_e( 'Your cart is currently empty.', 'opulentia' ); ?>
		</div>
	<?php else : ?>
		<ul class="mini-cart-dropdown__items">
			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
				$_product = $cart_item['data'];
				if ( ! $_product || ! $_product->exists() ) {
					continue;
				}
				?>
				<li class="mini-cart-dropdown__item">
					<div class="mini-cart-dropdown__item-image">
						<?php echo $_product->get_image( 'thumbnail' ); ?>
					</div>
					<div class="mini-cart-dropdown__item-details">
						<a href="<?php echo esc_url( $_product->get_permalink() ); ?>" class="mini-cart-dropdown__item-name">
							<?php echo esc_html( $_product->get_name() ); ?>
						</a>
						<div class="mini-cart-dropdown__item-quantity">
							<?php echo esc_html( $cart_item['quantity'] ); ?> &times;
							<?php echo WC()->cart->get_product_price( $_product ); ?>
						</div>
					</div>
					<?php
					echo wc_get_formatted_cart_item_data( $cart_item );
					?>
					<div class="mini-cart-dropdown__item-price">
						<?php echo WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ); ?>
					</div>
					<a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>"
						class="mini-cart-dropdown__item-remove"
						aria-label="<?php esc_attr_e( 'Remove item', 'opulentia' ); ?>">
						&times;
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="mini-cart-dropdown__footer">
			<div class="mini-cart-dropdown__subtotal">
				<span class="mini-cart-dropdown__subtotal-label"><?php esc_html_e( 'Subtotal', 'opulentia' ); ?></span>
				<span class="mini-cart-dropdown__subtotal-amount"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
			</div>
			<div class="mini-cart-dropdown__buttons">
				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn btn--primary">
					<?php esc_html_e( 'View Cart', 'opulentia' ); ?>
				</a>
				<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn--outline">
					<?php esc_html_e( 'Checkout', 'opulentia' ); ?>
				</a>
			</div>
		</div>
	<?php endif; ?>
	<?php
	$html = ob_get_clean();

	wp_send_json_success( array( 'html' => $html ) );
}
add_action( 'wp_ajax_Opulentia_mini_cart', 'Opulentia_ajax_mini_cart' );
add_action( 'wp_ajax_nopriv_Opulentia_mini_cart', 'Opulentia_ajax_mini_cart' );

// -----------------------------------------------------------------------------
// Contact Form Handler
// -----------------------------------------------------------------------------

/**
 * Handle contact form submission via admin-post.
 */
function Opulentia_handle_contact_form() {
	// Verify nonce.
	if ( ! Opulentia_Security::verify_nonce( 'Opulentia_contact_form', 'Opulentia_contact_nonce' ) ) {
		wp_die( esc_html__( 'Security check failed. Please try again.', 'opulentia' ) );
	}

	// Sanitize inputs.
	$name    = Opulentia_Security::sanitize_input( 'name', '' );
	$email   = Opulentia_Security::sanitize_email_input( 'email', '' );
	$subject = Opulentia_Security::sanitize_input( 'subject', '' );
	$message = Opulentia_Security::sanitize_textarea_input( 'message', '' );

	// Validate required fields.
	if ( empty( $name ) || empty( $email ) || empty( $subject ) || empty( $message ) ) {
		wp_die( esc_html__( 'All fields are required.', 'opulentia' ) );
	}

	if ( ! is_email( $email ) ) {
		wp_die( esc_html__( 'Invalid email address.', 'opulentia' ) );
	}

	// Redirect back with a success/error parameter.
	$redirect_url = add_query_arg(
		array( 'contact' => 'success' ),
		wp_get_referer() ? wp_get_referer() : home_url( '/contact' )
	);

	wp_safe_redirect( $redirect_url );
	exit;
}
add_action( 'admin_post_Opulentia_contact_form', 'Opulentia_handle_contact_form' );
add_action( 'admin_post_nopriv_Opulentia_contact_form', 'Opulentia_handle_contact_form' );

/**
 * Handle newsletter signup submission via admin-post.
 *
 * Sends the email to Mailchimp via the API wrapper.
 * Falls back gracefully if Mailchimp is not configured (logs the email to an option).
 */
function Opulentia_handle_newsletter_signup() {
	// Verify nonce.
	if ( ! Opulentia_Security::verify_nonce( 'Opulentia_newsletter', 'Opulentia_newsletter_nonce' ) ) {
		wp_die( esc_html__( 'Security check failed. Please try again.', 'opulentia' ) );
	}

	// Sanitize email.
	$email = Opulentia_Security::sanitize_email_input( 'email', '' );

	if ( ! is_email( $email ) ) {
		wp_die( esc_html__( 'Invalid email address.', 'opulentia' ) );
	}

	// Capture optional name merge fields.
	$merge_fields = array();
	$fname        = Opulentia_Security::sanitize_input( 'fname', '' );
	$lname        = Opulentia_Security::sanitize_input( 'lname', '' );

	if ( ! empty( $fname ) ) {
		$merge_fields['FNAME'] = $fname;
	}
	if ( ! empty( $lname ) ) {
		$merge_fields['LNAME'] = $lname;
	}

	// Attempt Mailchimp subscription with merge fields.
	$mailchimp = Opulentia_Mailchimp::get_instance();
	$result    = $mailchimp->subscribe( $email, $merge_fields );

	if ( is_wp_error( $result ) ) {
		// Mailchimp failed — fall back to local storage (option) for resilience.
		$subscribers = get_option( 'Opulentia_newsletter_subscribers', array() );
		if ( ! in_array( $email, $subscribers, true ) ) {
			$subscribers[] = sanitize_email( $email );
			update_option( 'Opulentia_newsletter_subscribers', $subscribers, false );
		}
	}

	// Redirect back with success message.
	$redirect_url = add_query_arg(
		array( 'newsletter' => 'success' ),
		wp_get_referer() ? wp_get_referer() : home_url( '/' )
	);

	wp_safe_redirect( $redirect_url );
	exit;
}
add_action( 'admin_post_Opulentia_newsletter_signup', 'Opulentia_handle_newsletter_signup' );
add_action( 'admin_post_nopriv_Opulentia_newsletter_signup', 'Opulentia_handle_newsletter_signup' );

// -----------------------------------------------------------------------------
// WooCommerce Checkout — Newsletter Opt-in
// -----------------------------------------------------------------------------

/**
 * Add a newsletter signup checkbox to the checkout billing section.
 *
 * Hooked to 'woocommerce_after_checkout_billing_form' so it appears
 * right after the billing fields (including the billing email).
 */
function Opulentia_checkout_newsletter_field() {
	?>
	<div class="woocommerce-checkout-newsletter">
		<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
			<input type="checkbox"
					class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"
					name="Opulentia_newsletter_optin"
					id="Opulentia_newsletter_optin"
					value="1"
					checked>
			<span><?php esc_html_e( 'Subscribe to our newsletter for exclusive offers and style insights.', 'opulentia' ); ?></span>
		</label>
	</div>
	<?php
}
add_action( 'woocommerce_after_checkout_billing_form', 'Opulentia_checkout_newsletter_field' );

/**
 * Save the newsletter opt-in preference as order meta.
 */
function Opulentia_save_checkout_newsletter_optin( $order_id ) {
	if ( isset( $_POST['Opulentia_newsletter_optin'] ) ) {
		update_post_meta( $order_id, '_Opulentia_newsletter_optin', 'yes' );
	}
}
add_action( 'woocommerce_checkout_update_order_meta', 'Opulentia_save_checkout_newsletter_optin' );

/**
 * Subscribe the customer to Mailchimp after order completion.
 *
 * Checks the order meta for the opt-in flag, then subscribes
 * the billing email via the Mailchimp API wrapper.
 *
 * @param int $order_id The completed order ID.
 */
function Opulentia_subscribe_checkout_customer( $order_id ) {
	$optin = get_post_meta( $order_id, '_Opulentia_newsletter_optin', true );

	if ( 'yes' !== $optin ) {
		return;
	}

	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}

	// Only subscribe for confirmed/completed orders.
	// Prevents subscription for pending payment methods (e.g. bank transfers).
	if ( ! $order->has_status( array( 'completed', 'processing' ) ) ) {
		return;
	}

	// One-shot guard — prevent redundant API calls on page refresh.
	if ( get_post_meta( $order_id, '_Opulentia_newsletter_subscribed', true ) ) {
		return;
	}

	$email = $order->get_billing_email();
	if ( ! is_email( $email ) ) {
		return;
	}

	$mailchimp = Opulentia_Mailchimp::get_instance();
	$result    = $mailchimp->subscribe( $email );

	if ( is_wp_error( $result ) ) {
		error_log(
			sprintf(
				'[Opulentia Checkout] Mailchimp subscribe failed for order %d: %s',
				$order_id,
				$result->get_error_message()
			)
		);
	} else {
		update_post_meta( $order_id, '_Opulentia_newsletter_subscribed', 'yes' );
	}
}
add_action( 'woocommerce_thankyou', 'Opulentia_subscribe_checkout_customer' );

/** -------------------------------------------------------------------------- */

/**
 * Render sticky add-to-cart bar on single product pages.
 */
function Opulentia_sticky_add_to_cart() {
	if ( ! is_product() || ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	if ( ! get_theme_mod( 'wc_sticky_add_to_cart', true ) ) {
		return;
	}

	global $product;
	if ( ! $product || ! $product->is_visible() ) {
		$product = wc_get_product( get_the_ID() );
	}

	if ( ! $product ) {
		return;
	}

	$price_html = $product->get_price_html();
	$image      = $product->get_image( 'thumbnail', array( 'class' => 'sticky-add-to-cart__thumb' ) );
	$title      = $product->get_name();
	?>
	<div class="sticky-add-to-cart" data-default-price="<?php echo esc_attr( $price_html ); ?>">
		<div class="container">
			<div class="sticky-add-to-cart__inner">
				<div class="sticky-add-to-cart__info">
					<?php if ( $image ) : ?>
						<div class="sticky-add-to-cart__image">
							<?php echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>
					<span class="sticky-add-to-cart__title"><?php echo esc_html( $title ); ?></span>
					<span class="sticky-add-to-cart__price"><?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				</div>
				<div class="sticky-add-to-cart__actions">
					<?php if ( $product->is_type( 'simple' ) && $product->is_purchasable() ) : ?>
						<input type="number" class="sticky-add-to-cart__qty" value="1" min="1" max="99" step="1">
					<?php endif; ?>
					<button type="button" class="sticky-add-to-cart__button">
						<?php esc_html_e( 'Add to Cart', 'opulentia' ); ?>
					</button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'Opulentia_sticky_add_to_cart', 100 );

/** -------------------------------------------------------------------------- */

/**
 * Localize WC Pro AJAX script.
 */
function Opulentia_wc_pro_localize() {
	if ( ! function_exists( 'is_woocommerce' ) ) {
		return;
	}

	wp_localize_script(
		'Opulentia-woocommerce-pro',
		'OpulentiaWcPro',
		array(
			'ajaxUrl'                => admin_url( 'admin-ajax.php' ),
			'nonce'                  => wp_create_nonce( 'Opulentia_wc_pro_nonce' ),
			'closeText'              => __( 'Close', 'opulentia' ),
			'errorText'              => __( 'Something went wrong. Please try again.', 'opulentia' ),
			'addToWishlistText'      => __( 'Add to Wishlist', 'opulentia' ),
			'removeFromWishlistText' => __( 'Remove from Wishlist', 'opulentia' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'Opulentia_wc_pro_localize', 20 );

// -----------------------------------------------------------------------------
// Initialize Singletons.
// Hook registration happens inside each private constructor.
// -----------------------------------------------------------------------------
Opulentia_After_Setup::get_instance();
Opulentia_Blog_Metabox::get_instance();
Opulentia_Fonts::get_instance();
Opulentia_Enqueue::get_instance();
Opulentia_Icons::get_instance();
Opulentia_Customizer_Config::get_instance();
Opulentia_Performance::get_instance();
Opulentia_Security::get_instance();
Opulentia_Elementor::get_instance();
Opulentia_Gutenberg::get_instance();
Opulentia_Yoast::get_instance();
Opulentia_Rank_Math::get_instance();
Opulentia_Beaver_Builder::get_instance();
Opulentia_AMP_Support::get_instance();
Opulentia_LearnDash::get_instance();
Opulentia_LifterLMS::get_instance();
Opulentia_Gravity_Forms::get_instance();
Opulentia_EDD::get_instance();
Opulentia_Divi::get_instance();
Opulentia_Beaver_Themer::get_instance();
Opulentia_Visual_Composer::get_instance();
Opulentia_BuddyPress::get_instance();
Opulentia_SiteOrigin::get_instance();
Opulentia_Hooks::get_instance();
Opulentia_Widgets::get_instance();
Opulentia_Dynamic_CSS::get_instance();
Opulentia_Header_Builder::get_instance();
Opulentia_Footer_Builder::get_instance();
Opulentia_Mailchimp::get_instance();
Opulentia_Schema::get_instance();
Opulentia_Accessibility::get_instance();
Opulentia_Meta_Boxes::get_instance();
Opulentia_Advanced_Headers::get_instance();
Opulentia_Advanced_Hooks::get_instance();
Opulentia_Advanced_Hooks_Render::get_instance();
Opulentia_Mega_Menu::get_instance();
Opulentia_Blog_Pro::get_instance();
Opulentia_Live_Search::get_instance();
Opulentia_Scroll_To_Top::get_instance();
Opulentia_Transparent_Header::get_instance();
Opulentia_Sticky_Header::get_instance();
Opulentia_Breadcrumbs::get_instance();
Opulentia_Spacing::get_instance();
Opulentia_Site_Layouts::get_instance();
Opulentia_Dark_Mode::get_instance();
Opulentia_Animation_Presets::get_instance();
Opulentia_Layout_Library::get_instance();
Opulentia_Popup_Builder::get_instance();
Opulentia_Conditional_Display::get_instance();
Opulentia_Demo_Importer::get_instance();
Opulentia_Customizer_Presets::get_instance();
Opulentia_Performance_Dashboard::get_instance();
Opulentia_Product_Viewer_3D::get_instance();
Opulentia_Custom_Fonts_Uploader::get_instance();
Opulentia_Icon_Manager::get_instance();
Opulentia_Color_Palette::get_instance();
Opulentia_Maintenance_Mode::get_instance();
Opulentia_GDPR_Cookie_Consent::get_instance();
Opulentia_CSS_JS_Injection::get_instance();
Opulentia_Responsive_Controls::get_instance();
Opulentia_Form_Styler::get_instance();
Opulentia_White_Label::get_instance();
Opulentia_Docs_Generator::get_instance();
Opulentia_Mobile_Header::get_instance();
Opulentia_RTL::get_instance();
Opulentia_Custom_404::get_instance();
Opulentia_Social_Sharing::get_instance();
Opulentia_WooCommerce_Catalog::get_instance();
Opulentia_WooCommerce_Recently_Viewed::get_instance();
Opulentia_Sidebar_Manager::get_instance();
Opulentia_Table_Of_Contents::get_instance();
Opulentia_WooCommerce_Product_Video::get_instance();
Opulentia_Wishlist_Compare::get_instance();
Opulentia_Ajax_Filtering::get_instance();
Opulentia_Content_Restriction::get_instance();
Opulentia_Google_Maps::get_instance();
Opulentia_Countdown_Timer::get_instance();
Opulentia_Social_Login::get_instance();
Opulentia_Login_Customizer::get_instance();
Opulentia_Nav_Menu_Roles::get_instance();
Opulentia_Advanced_Search::get_instance();
Opulentia_Custom_Widgets::get_instance();
Opulentia_Scroll_Reveal::get_instance();
Opulentia_Reading_Progress::get_instance();
Opulentia_Pricing_Tables::get_instance();
Opulentia_Count_Up::get_instance();
Opulentia_Image_Hotspots::get_instance();
Opulentia_Gradient_Builder::get_instance();
Opulentia_Author_Box::get_instance();

// -----------------------------------------------------------------------------
// Initialize Admin Dashboard Singleton.
// -----------------------------------------------------------------------------
Opulentia_Admin::get_instance();
