<?php
/**
 * Typography Dynamic CSS
 *
 * Generates per-element typography CSS from Theme Options API settings.
 * Supports responsive font sizes (desktop, tablet, mobile) and
 * per-heading overrides (H1-H6) with inherit fallback to Headings (General).
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate responsive font-size CSS with media query wrappers.
 *
 * @param string $selector CSS selector.
 * @param string $desktop  Desktop font size (px).
 * @param string $tablet   Tablet font size (px).
 * @param string $mobile   Mobile font size (px).
 * @param string $property CSS property (default: font-size).
 * @return string Generated CSS.
 */
function Opulentia_typography_responsive_size( $selector, $desktop = '', $tablet = '', $mobile = '', $property = 'font-size' ) {
	$css = '';

	if ( ! empty( $desktop ) ) {
		$css .= $selector . '{' . $property . ':' . absint( $desktop ) . 'px;}';
	}

	if ( ! empty( $tablet ) ) {
		$css .= '@media (max-width:' . Opulentia_get_tablet_breakpoint() . 'px){';
		$css .= $selector . '{' . $property . ':' . absint( $tablet ) . 'px;}';
		$css .= '}';
	}

	if ( ! empty( $mobile ) ) {
		$css .= '@media (max-width:' . Opulentia_get_mobile_breakpoint() . 'px){';
		$css .= $selector . '{' . $property . ':' . absint( $mobile ) . 'px;}';
		$css .= '}';
	}

	return $css;
}

/**
 * Generate a single-element typography CSS block.
 *
 * @param string $selector  CSS selector.
 * @param array  $settings  {
 *     Typography settings with keys:
 *     @type string $family      Font family (Google Font name or empty for inherit).
 *     @type string $weight      Font weight (e.g., '400', '600') or empty for inherit.
 *     @type string $size        Desktop font size (px) or empty for inherit.
 *     @type string $size-tablet Tablet font size (px).
 *     @type string $size-mobile Mobile font size (px).
 *     @type string $line-height Line height (e.g., '1.5') or empty.
 *     @type string $transform   Text transform or empty.
 *     @type string $spacing     Letter spacing (e.g., '1px') or empty.
 * }
 * @return string Generated CSS.
 */
function Opulentia_typography_element_css( $selector, $settings ) {
	$css   = '';
	$rules = array();

	// Font family.
	if ( ! empty( $settings['family'] ) ) {
		$rules[] = 'font-family:\'' . esc_attr( $settings['family'] ) . '\',serif';
	}

	// Font weight.
	if ( ! empty( $settings['weight'] ) ) {
		$rules[] = 'font-weight:' . esc_attr( $settings['weight'] );
	}

	// Line height.
	if ( ! empty( $settings['line-height'] ) ) {
		$rules[] = 'line-height:' . esc_attr( $settings['line-height'] );
	}

	// Text transform.
	if ( isset( $settings['transform'] ) && '' !== $settings['transform'] ) {
		$rules[] = 'text-transform:' . esc_attr( $settings['transform'] );
	}

	// Letter spacing.
	if ( ! empty( $settings['spacing'] ) ) {
		$spacing = is_numeric( $settings['spacing'] ) ? $settings['spacing'] . 'px' : $settings['spacing'];
		$rules[] = 'letter-spacing:' . esc_attr( $spacing );
	}

	if ( ! empty( $rules ) ) {
		$css .= $selector . '{' . implode( ';', $rules ) . ';}';
	}

	// Responsive font sizes.
	$size_desktop = isset( $settings['size'] ) ? $settings['size'] : '';
	$size_tablet  = isset( $settings['size-tablet'] ) ? $settings['size-tablet'] : '';
	$size_mobile  = isset( $settings['size-mobile'] ) ? $settings['size-mobile'] : '';

	if ( ! empty( $size_desktop ) || ! empty( $size_tablet ) || ! empty( $size_mobile ) ) {
		$css .= Opulentia_typography_responsive_size( $selector, $size_desktop, $size_tablet, $size_mobile );
	}

	return $css;
}

/**
 * Generate all typography dynamic CSS.
 *
 * Reads settings via Opulentia_get_option() and builds complete
 * typography CSS for body, all headings, site title/tagline,
 * navigation, buttons, blog posts, and widget titles.
 *
 * @return string Inline CSS string.
 */
function Opulentia_dynamic_typography_css() {
	$css = '';

	// -------------------------------------------------------------------------
	// Headings (General) — base for all h1-h6
	// -------------------------------------------------------------------------
	$headings_family    = Opulentia_get_option( 'typo-headings-family', 'Playfair Display' );
	$headings_weight    = Opulentia_get_option( 'typo-headings-weight', '600' );
	$headings_line_h    = Opulentia_get_option( 'typo-headings-line-height', '1.2' );
	$headings_transform = Opulentia_get_option( 'typo-headings-transform', '' );
	$headings_spacing   = Opulentia_get_option( 'typo-headings-spacing', '' );

	$h_base_rules = array();
	if ( ! empty( $headings_family ) ) {
		$h_base_rules[] = 'font-family:\'' . esc_attr( $headings_family ) . '\',serif';
	}
	if ( ! empty( $headings_weight ) ) {
		$h_base_rules[] = 'font-weight:' . esc_attr( $headings_weight );
	}
	if ( ! empty( $headings_line_h ) ) {
		$h_base_rules[] = 'line-height:' . esc_attr( $headings_line_h );
	}
	if ( '' !== $headings_transform ) {
		$h_base_rules[] = 'text-transform:' . esc_attr( $headings_transform );
	}
	if ( ! empty( $headings_spacing ) ) {
		$s              = is_numeric( $headings_spacing ) ? $headings_spacing . 'px' : $headings_spacing;
		$h_base_rules[] = 'letter-spacing:' . esc_attr( $s );
	}
	if ( ! empty( $h_base_rules ) ) {
		$css .= 'h1,h2,h3,h4,h5,h6{' . implode( ';', $h_base_rules ) . ';}';
	}

	// -------------------------------------------------------------------------
	// Per-Heading Overrides (H1 - H6)
	// -------------------------------------------------------------------------
	$heading_tags      = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
	$heading_selectors = array(
		'h1' => 'h1,.h1',
		'h2' => 'h2,.h2',
		'h3' => 'h3,.h3',
		'h4' => 'h4,.h4',
		'h5' => 'h5,.h5',
		'h6' => 'h6,.h6',
	);

	foreach ( $heading_tags as $tag ) {
		$selector = $heading_selectors[ $tag ];
		$settings = array(
			'family'      => Opulentia_get_option( 'typo-' . $tag . '-family', '' ),
			'weight'      => Opulentia_get_option( 'typo-' . $tag . '-weight', '' ),
			'size'        => Opulentia_get_option( 'typo-' . $tag . '-size', '' ),
			'size-tablet' => Opulentia_get_option( 'typo-' . $tag . '-size-tablet', '' ),
			'size-mobile' => Opulentia_get_option( 'typo-' . $tag . '-size-mobile', '' ),
			'line-height' => Opulentia_get_option( 'typo-' . $tag . '-line-height', '' ),
			'transform'   => Opulentia_get_option( 'typo-' . $tag . '-transform', '' ),
			'spacing'     => Opulentia_get_option( 'typo-' . $tag . '-spacing', '' ),
		);

		// Check if any override is set — if not, skip (inherits from general).
		$has_value = false;
		foreach ( $settings as $val ) {
			if ( ! empty( $val ) ) {
				$has_value = true;
				break;
			}
		}

		if ( $has_value ) {
			$css .= Opulentia_typography_element_css( $selector, $settings );
		}
	}

	// -------------------------------------------------------------------------
	// Body
	// -------------------------------------------------------------------------
	$body_settings = array(
		'family'      => Opulentia_get_option( 'typo-body-family', 'Inter' ),
		'weight'      => Opulentia_get_option( 'typo-body-weight', '400' ),
		'size'        => Opulentia_get_option( 'typo-body-size', '16' ),
		'size-tablet' => Opulentia_get_option( 'typo-body-size-tablet', '15' ),
		'size-mobile' => Opulentia_get_option( 'typo-body-size-mobile', '14' ),
		'line-height' => Opulentia_get_option( 'typo-body-line-height', '1.6' ),
		'transform'   => Opulentia_get_option( 'typo-body-transform', 'none' ),
		'spacing'     => Opulentia_get_option( 'typo-body-spacing', '' ),
	);
	$css          .= Opulentia_typography_element_css( 'body', $body_settings );

	// -------------------------------------------------------------------------
	// Site Title
	// -------------------------------------------------------------------------
	$site_title_settings = array(
		'family'      => Opulentia_get_option( 'typo-site-title-family', '' ),
		'weight'      => Opulentia_get_option( 'typo-site-title-weight', '600' ),
		'size'        => Opulentia_get_option( 'typo-site-title-size', '24' ),
		'size-tablet' => Opulentia_get_option( 'typo-site-title-size-tablet', '20' ),
		'size-mobile' => Opulentia_get_option( 'typo-site-title-size-mobile', '18' ),
		'transform'   => Opulentia_get_option( 'typo-site-title-transform', 'none' ),
		'spacing'     => Opulentia_get_option( 'typo-site-title-spacing', '' ),
	);
	$css                .= Opulentia_typography_element_css( '.site-title,.site-logo__text', $site_title_settings );

	// -------------------------------------------------------------------------
	// Tagline
	// -------------------------------------------------------------------------
	$tagline_settings = array(
		'family'    => Opulentia_get_option( 'typo-tagline-family', '' ),
		'weight'    => Opulentia_get_option( 'typo-tagline-weight', '400' ),
		'size'      => Opulentia_get_option( 'typo-tagline-size', '14' ),
		'transform' => Opulentia_get_option( 'typo-tagline-transform', 'uppercase' ),
		'spacing'   => Opulentia_get_option( 'typo-tagline-spacing', '2' ),
	);
	$css             .= Opulentia_typography_element_css( '.site-description,.site-logo__tagline', $tagline_settings );

	// -------------------------------------------------------------------------
	// Navigation
	// -------------------------------------------------------------------------
	$nav_family   = Opulentia_get_option( 'typo-nav-family', '' );
	$nav_selector = '.main-navigation a,.main-navigation ul li a';

	$nav_settings = array(
		'family'      => $nav_family,
		'weight'      => Opulentia_get_option( 'typo-nav-weight', '500' ),
		'size'        => Opulentia_get_option( 'typo-nav-size', '14' ),
		'transform'   => Opulentia_get_option( 'typo-nav-transform', 'uppercase' ),
		'spacing'     => Opulentia_get_option( 'typo-nav-spacing', '1' ),
		'line-height' => Opulentia_get_option( 'typo-nav-line-height', '' ),
	);
	$css         .= Opulentia_typography_element_css( $nav_selector, $nav_settings );

	// -------------------------------------------------------------------------
	// Buttons
	// -------------------------------------------------------------------------
	$btn_selector = '.btn,.button,.wp-block-button__link,.wc-block-grid__product-add-to-cart a';
	$btn_settings = array(
		'family'      => Opulentia_get_option( 'typo-btn-family', '' ),
		'weight'      => Opulentia_get_option( 'typo-btn-weight', '500' ),
		'size'        => Opulentia_get_option( 'typo-btn-size', '14' ),
		'transform'   => Opulentia_get_option( 'typo-btn-transform', 'uppercase' ),
		'spacing'     => Opulentia_get_option( 'typo-btn-spacing', '1' ),
		'line-height' => Opulentia_get_option( 'typo-btn-line-height', '1.5' ),
	);
	$css         .= Opulentia_typography_element_css( $btn_selector, $btn_settings );

	// -------------------------------------------------------------------------
	// Blog — Post Title, Meta, Taxonomy
	// -------------------------------------------------------------------------
	$post_title_size        = Opulentia_get_option( 'typo-post-title-size', '' );
	$post_title_size_tablet = Opulentia_get_option( 'typo-post-title-size-tablet', '' );
	$post_title_size_mobile = Opulentia_get_option( 'typo-post-title-size-mobile', '' );
	if ( ! empty( $post_title_size ) || ! empty( $post_title_size_tablet ) || ! empty( $post_title_size_mobile ) ) {
		$css .= Opulentia_typography_responsive_size(
			'.post-card__title,.post-classic__title,.post-list__title,.entry-title',
			$post_title_size,
			$post_title_size_tablet,
			$post_title_size_mobile
		);
	}

	$post_meta_size = Opulentia_get_option( 'typo-post-meta-size', '13' );
	if ( ! empty( $post_meta_size ) ) {
		$css .= '.post-card__meta,.post-classic__meta,.post-list__meta,.entry-meta{font-size:' . absint( $post_meta_size ) . 'px;}';
	}

	$post_tax_size = Opulentia_get_option( 'typo-post-taxonomy-size', '12' );
	if ( ! empty( $post_tax_size ) ) {
		$css .= '.cat-links,.post-card__categories,.taxonomy-links{font-size:' . absint( $post_tax_size ) . 'px;}';
	}

	// -------------------------------------------------------------------------
	// Widget Titles
	// -------------------------------------------------------------------------
	$widget_settings = array(
		'family'    => Opulentia_get_option( 'typo-widget-family', '' ),
		'weight'    => Opulentia_get_option( 'typo-widget-weight', '600' ),
		'size'      => Opulentia_get_option( 'typo-widget-size', '18' ),
		'transform' => Opulentia_get_option( 'typo-widget-transform', '' ),
		'spacing'   => Opulentia_get_option( 'typo-widget-spacing', '' ),
	);
	$css            .= Opulentia_typography_element_css( '.widget__title,.widget-title', $widget_settings );

	return apply_filters( 'Opulentia_dynamic_typography_css', $css );
}
