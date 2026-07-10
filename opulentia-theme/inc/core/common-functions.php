<?php
/**
 * Common Utility Functions for Opulentia Theme
 *
 * CSS generation helpers, color utilities, spacing utils,
 * font helpers, and responsive breakpoints.
 *
 * @package Opulentia
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// -----------------------------------------------------------------------------
// CSS Generation
// -----------------------------------------------------------------------------

if ( ! function_exists( 'Opulentia_css' ) ) {
	/**
	 * Echo a simple CSS rule.
	 *
	 * @param  mixed  $value       CSS value.
	 * @param  string $css_property CSS property name.
	 * @param  string $selector    CSS selector.
	 * @param  string $unit        Optional unit (e.g., 'px', 'em').
	 * @return void
	 */
	function Opulentia_css( $value = '', $css_property = '', $selector = '', $unit = '' ) {
		if ( ! $selector || ! $css_property || '' === $value ) {
			return;
		}

		if ( '' !== $unit ) {
			$value .= $unit;
		}

		echo esc_html( $selector ) . '{' . esc_html( $css_property ) . ':' . esc_html( $value ) . ';}'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'Opulentia_parse_css' ) ) {
	/**
	 * Parse a CSS array into a string, with optional media query wrapping.
	 *
	 * @param  array $css_output Array of CSS in format [ selector => [ property => value ] ].
	 * @param  mixed $min_media  Optional minimum media breakpoint (px).
	 * @param  mixed $max_media  Optional maximum media breakpoint (px).
	 * @return string             Generated CSS string.
	 */
	function Opulentia_parse_css( $css_output = array(), $min_media = '', $max_media = '' ) {
		$parse_css = '';

		if ( ! is_array( $css_output ) || 0 === count( $css_output ) ) {
			return '';
		}

		foreach ( $css_output as $selector => $properties ) {
			if ( null === $properties ) {
				break;
			}
			if ( ! count( $properties ) ) {
				continue;
			}

			$temp_parse_css   = $selector . '{';
			$properties_added = 0;

			foreach ( $properties as $property => $value ) {
				if ( ( '' === $value && 0 !== $value ) || '!important' === $value ) {
					continue;
				}
				++$properties_added;
				$temp_parse_css .= $property . ':' . $value . ';';
			}

			$temp_parse_css .= '}';

			if ( $properties_added > 0 ) {
				$parse_css .= $temp_parse_css;
			}
		}

		if ( '' !== $parse_css && ( '' !== $min_media || '' !== $max_media ) ) {
			$media_css       = '@media ';
			$min_media_css   = '';
			$max_media_css   = '';
			$media_separator = '';

			if ( '' !== $min_media ) {
				$min_media_css = '(min-width:' . $min_media . 'px)';
			}
			if ( '' !== $max_media ) {
				$max_media_css = '(max-width:' . $max_media . 'px)';
			}
			if ( '' !== $min_media && '' !== $max_media ) {
				$media_separator = ' and ';
			}

			return $media_css . $min_media_css . $media_separator . $max_media_css . '{' . $parse_css . '}';
		}

		return $parse_css;
	}
}

if ( ! function_exists( 'Opulentia_get_css_value' ) ) {
	/**
	 * Get a CSS value with unit.
	 *
	 * @param  string $value   CSS value.
	 * @param  string $unit    CSS unit (px, em, %, url, font).
	 * @param  string $default Default fallback.
	 * @return string          Formatted CSS value.
	 */
	function Opulentia_get_css_value( $value = '', $unit = 'px', $default = '' ) {
		if ( '' === $value && '' === $default ) {
			return $value;
		}

		$css_val = '';

		switch ( $unit ) {
			case 'font':
				if ( 'inherit' !== $value ) {
					$css_val = $value;
				} elseif ( '' !== $default ) {
					$css_val = $default;
				} else {
					$css_val = '';
				}
				break;

			case 'px':
			case '%':
				if ( 'inherit' === strtolower( $value ) || 'inherit' === strtolower( $default ) ) {
					return $value;
				}
				$value   = '' !== $value ? $value : $default;
				$css_val = esc_attr( $value ) . $unit;
				break;

			case 'url':
				$css_val = $unit . '(' . esc_url( $value ) . ')';
				break;

			default:
				$value = '' !== $value ? $value : $default;
				if ( '' !== $value ) {
					$css_val = esc_attr( $value ) . $unit;
				}
		}

		return $css_val;
	}
}

// -----------------------------------------------------------------------------
// Background
// -----------------------------------------------------------------------------

if ( ! function_exists( 'Opulentia_get_background_obj' ) ) {
	/**
	 * Process a background object array into CSS properties.
	 *
	 * @param  array $bg_obj Background configuration array.
	 * @return array          CSS property => value pairs.
	 */
	function Opulentia_get_background_obj( $bg_obj ) {
		$gen_bg_css = array();

		if ( ! is_array( $bg_obj ) ) {
			return $gen_bg_css;
		}

		$bg_img   = isset( $bg_obj['background-image'] ) ? $bg_obj['background-image'] : '';
		$bg_color = isset( $bg_obj['background-color'] ) ? $bg_obj['background-color'] : '';
		$bg_type  = isset( $bg_obj['background-type'] ) ? $bg_obj['background-type'] : '';

		if ( '' !== $bg_type ) {
			switch ( $bg_type ) {
				case 'color':
					if ( '' !== $bg_img && '' !== $bg_color ) {
						$gen_bg_css['background-image'] = 'linear-gradient(to right, ' . $bg_color . ', ' . $bg_color . '), url(' . $bg_img . ');';
					} elseif ( '' === $bg_img ) {
						$gen_bg_css['background-color'] = $bg_color . ';';
					}
					break;

				case 'image':
					$overlay_type    = isset( $bg_obj['overlay-type'] ) ? $bg_obj['overlay-type'] : 'none';
					$overlay_color   = isset( $bg_obj['overlay-color'] ) ? $bg_obj['overlay-color'] : '';
					$overlay_opacity = isset( $bg_obj['overlay-opacity'] ) ? $bg_obj['overlay-opacity'] : '';
					$overlay_grad    = isset( $bg_obj['overlay-gradient'] ) ? $bg_obj['overlay-gradient'] : '';

					if ( '' !== $bg_img ) {
						if ( 'none' !== $overlay_type ) {
							if ( 'classic' === $overlay_type && '' !== $overlay_color ) {
								$color = $overlay_color;
								if ( '' !== $overlay_opacity && '#' === $color[0] ) {
									$color = Opulentia_hex_to_rgba( $color, $overlay_opacity );
								}
								$gen_bg_css['background-image'] = 'linear-gradient(to right, ' . $color . ', ' . $color . '), url(' . $bg_img . ');';
							} elseif ( 'gradient' === $overlay_type && '' !== $overlay_grad ) {
								$gen_bg_css['background-image'] = $overlay_grad . ', url(' . $bg_img . ');';
							} else {
								$gen_bg_css['background-image'] = 'url(' . $bg_img . ');';
							}
						} else {
							$gen_bg_css['background-image'] = 'url(' . $bg_img . ');';
						}
					}
					break;

				case 'gradient':
					if ( '' !== $bg_color ) {
						$gen_bg_css['background-image'] = $bg_color . ';';
					}
					break;
			}
		} elseif ( '' !== $bg_color ) {
			$gen_bg_css['background-color'] = $bg_color . ';';
		}

		// Standard background properties (only when image is set).
		if ( '' !== $bg_img ) {
			foreach ( array( 'background-repeat', 'background-position', 'background-size', 'background-attachment' ) as $prop ) {
				if ( isset( $bg_obj[ $prop ] ) ) {
					$gen_bg_css[ $prop ] = esc_attr( $bg_obj[ $prop ] );
				}
			}
		}

		return $gen_bg_css;
	}
}

// -----------------------------------------------------------------------------
// Color Utilities
// -----------------------------------------------------------------------------

if ( ! function_exists( 'Opulentia_hex_to_rgba' ) ) {
	/**
	 * Convert HEX color to RGB or RGBA string.
	 *
	 * @param  string $color   HEX color code.
	 * @param  mixed  $opacity Optional opacity (0-1).
	 * @return string          RGB or RGBA string.
	 */
	function Opulentia_hex_to_rgba( $color, $opacity = false ) {
		$default = 'rgb(0,0,0)';

		if ( empty( $color ) ) {
			return $default;
		}

		if ( '#' === $color[0] ) {
			$color = substr( $color, 1 );
		}

		if ( 6 === strlen( $color ) ) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( 3 === strlen( $color ) ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		$rgb = array_map( 'hexdec', $hex );

		if ( false !== $opacity ) {
			if ( abs( $opacity ) > 1 ) {
				$opacity = 1.0;
			}
			return 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
		}

		return 'rgb(' . implode( ',', $rgb ) . ')';
	}
}

if ( ! function_exists( 'Opulentia_get_foreground_color' ) ) {
	/**
	 * Get contrasting foreground (black or white) for a given background HEX.
	 *
	 * @param  string $hex Background HEX color.
	 * @return string       '#000000' or '#ffffff'.
	 */
	function Opulentia_get_foreground_color( $hex ) {
		$hex = apply_filters( 'Opulentia_before_foreground_color_generation', $hex );

		if ( 'transparent' === $hex || 'false' === $hex || '#' === $hex || empty( $hex ) ) {
			return 'transparent';
		}

		$hex = str_replace( '#', '', $hex );

		if ( 3 === strlen( $hex ) ) {
			$hex = str_repeat( $hex[0], 2 ) . str_repeat( $hex[1], 2 ) . str_repeat( $hex[2], 2 );
		}

		if ( false !== strpos( $hex, 'rgba' ) ) {
			$rgba = explode( ',', preg_replace( '/[^0-9,]/', '', $hex ) );
			if ( isset( $rgba[0], $rgba[1], $rgba[2] ) ) {
				$hex = sprintf( '#%02x%02x%02x', $rgba[0], $rgba[1], $rgba[2] );
			}
		}

		if ( function_exists( 'ctype_xdigit' ) && is_callable( 'ctype_xdigit' ) ) {
			if ( ! ctype_xdigit( $hex ) ) {
				return $hex;
			}
		} elseif ( ! preg_match( '/^[a-f0-9]{2,}$/i', $hex ) ) {
			return $hex;
		}

		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
		$l = ( $r * 299 + $g * 587 + $b * 114 ) / 1000;

		return 128 <= $l ? '#000000' : '#ffffff';
	}
}

if ( ! function_exists( 'Opulentia_adjust_brightness' ) ) {
	/**
	 * Adjust HEX color brightness.
	 *
	 * @param  string $hex   HEX color code.
	 * @param  int    $steps Brightness adjustment steps.
	 * @param  string $type  'darken', 'brighten', or 'reverse'.
	 * @return string        Adjusted HEX color.
	 */
	function Opulentia_adjust_brightness( $hex, $steps, $type ) {
		$hex = str_replace( '#', '', $hex );

		if ( function_exists( 'ctype_xdigit' ) && is_callable( 'ctype_xdigit' ) ) {
			if ( ! ctype_xdigit( $hex ) ) {
				return $hex;
			}
		} elseif ( ! preg_match( '/^[a-f0-9]{2,}$/i', $hex ) ) {
			return $hex;
		}

		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );

		if ( 'reverse' === $type && $r + $g + $b > 382 ) {
			$steps = -$steps;
		} elseif ( 'darken' === $type ) {
			$steps = -$steps;
		}

		$steps = max( -255, min( 255, $steps ) );

		$r = max( 0, min( 255, $r + $steps ) );
		$g = max( 0, min( 255, $g + $steps ) );
		$b = max( 0, min( 255, $b + $steps ) );

		return sprintf( '#%02x%02x%02x', $r, $g, $b );
	}
}

// -----------------------------------------------------------------------------
// Responsive Spacing
// -----------------------------------------------------------------------------

if ( ! function_exists( 'Opulentia_responsive_spacing' ) ) {
	/**
	 * Get responsive spacing value from an option array.
	 *
	 * @param  array  $option  Spacing option array.
	 * @param  string $side    'top', 'right', 'bottom', 'left'.
	 * @param  string $device  'desktop', 'tablet', 'mobile'.
	 * @param  string $default Default fallback.
	 * @param  string $prefix  Optional prefix (e.g., 'margin-').
	 * @return string          CSS value.
	 */
	function Opulentia_responsive_spacing( $option, $side = '', $device = 'desktop', $default = '', $prefix = '' ) {
		if ( isset( $option[ $device ][ $side ] ) && isset( $option[ $device . '-unit' ] ) ) {
			$spacing = Opulentia_get_css_value( $option[ $device ][ $side ], $option[ $device . '-unit' ], $default );
		} elseif ( is_numeric( $option ) ) {
			$spacing = Opulentia_get_css_value( $option );
		} else {
			$spacing = ! is_array( $option ) ? $option : '';
		}

		if ( '' !== $prefix && '' !== $spacing ) {
			return $prefix . $spacing;
		}
		return $spacing;
	}
}

// -----------------------------------------------------------------------------
// Responsive Colors
// -----------------------------------------------------------------------------

if ( ! function_exists( 'Opulentia_color_responsive_css' ) ) {
	/**
	 * Generate responsive CSS for a color setting.
	 *
	 * @param  array  $setting     Responsive color array { desktop, tablet, mobile }.
	 * @param  string $css_property CSS property name.
	 * @param  string $selector    CSS selector.
	 * @return string              Generated CSS.
	 */
	function Opulentia_color_responsive_css( $setting, $css_property, $selector ) {
		$css = '';

		if ( isset( $setting['desktop'] ) && ! empty( $setting['desktop'] ) ) {
			$css .= $selector . '{' . $css_property . ':' . esc_attr( $setting['desktop'] ) . ';}';
		}
		if ( isset( $setting['tablet'] ) && ! empty( $setting['tablet'] ) ) {
			$css .= '@media (max-width:' . Opulentia_get_tablet_breakpoint() . 'px) {' . $selector . '{' . $css_property . ':' . esc_attr( $setting['tablet'] ) . ';} }';
		}
		if ( isset( $setting['mobile'] ) && ! empty( $setting['mobile'] ) ) {
			$css .= '@media (max-width:' . Opulentia_get_mobile_breakpoint() . 'px) {' . $selector . '{' . $css_property . ':' . esc_attr( $setting['mobile'] ) . ';} }';
		}

		return $css;
	}
}

// -----------------------------------------------------------------------------
// Font Helpers
// -----------------------------------------------------------------------------

if ( ! function_exists( 'Opulentia_responsive_font' ) ) {
	/**
	 * Get responsive font size value.
	 *
	 * @param  array  $font    Font size array { desktop, tablet, mobile, desktop-unit, tablet-unit, mobile-unit }.
	 * @param  string $device  Device key.
	 * @param  string $default Default fallback.
	 * @return string          CSS font-size value.
	 */
	function Opulentia_responsive_font( $font, $device = 'desktop', $default = '' ) {
		if ( isset( $font[ $device ] ) ) {
			if ( ! isset( $font[ $device . '-unit' ] ) ) {
				$font[ $device . '-unit' ] = 'px';
			}

			if ( '' !== $default ) {
				$font_size = Opulentia_get_css_value( $font[ $device ], $font[ $device . '-unit' ], $default );
			} else {
				$font_size = Opulentia_get_font_css_value( $font[ $device ], $font[ $device . '-unit' ] );
			}
		} elseif ( is_numeric( $font ) ) {
			$font_size = Opulentia_get_css_value( $font );
		} else {
			$font_size = ! is_array( $font ) ? $font : '';
		}

		return $font_size;
	}
}

if ( ! function_exists( 'Opulentia_get_font_css_value' ) ) {
	/**
	 * Get a font CSS value with unit.
	 *
	 * @param  mixed  $value Font size value.
	 * @param  string $unit  CSS unit (px, em, rem, %, vw).
	 * @param  string $device Device context for px-to-rem conversion.
	 * @return string        Formatted CSS value.
	 */
	function Opulentia_get_font_css_value( $value, $unit = 'px', $device = 'desktop' ) {
		if ( '' === $value || ( 0 == $value && ! Opulentia_zero_font_size_case() ) ) {
			return '';
		}

		$css_val = '';

		switch ( $unit ) {
			case 'em':
			case 'vw':
			case 'rem':
			case '%':
				$css_val = esc_attr( $value ) . $unit;
				break;

			case 'px':
				if ( is_numeric( $value ) || false !== strpos( $value, 'px' ) ) {
					$css_val = esc_attr( $value ) . $unit;
				} else {
					$css_val = esc_attr( $value );
				}
				break;

			default:
				$css_val = esc_attr( $value ) . $unit;
				break;
		}

		return $css_val;
	}
}

if ( ! function_exists( 'Opulentia_get_font_family' ) ) {
	/**
	 * Get font family CSS value with fallback.
	 *
	 * @param  string $value Font family name.
	 * @return string        Font family with fallback.
	 */
	function Opulentia_get_font_family( $value = '' ) {
		$system_fonts = apply_filters(
			'Opulentia_system_fonts',
			array(
				'serif'      => array( 'fallback' => 'Georgia, serif' ),
				'sans-serif' => array( 'fallback' => 'Arial, Helvetica, sans-serif' ),
				'monospace'  => array( 'fallback' => 'Monaco, Consolas, monospace' ),
			)
		);

		if ( isset( $system_fonts[ $value ] ) && isset( $system_fonts[ $value ]['fallback'] ) ) {
			$value .= ',' . $system_fonts[ $value ]['fallback'];
		}

		return $value;
	}
}

if ( ! function_exists( 'Opulentia_zero_font_size_case' ) ) {
	/**
	 * Check if zero font size should be allowed.
	 *
	 * @return bool
	 */
	function Opulentia_zero_font_size_case() {
		$settings = Opulentia_get_options();
		return apply_filters( 'Opulentia_zero_font_size_case', isset( $settings['opulentia-zero-font-size-case-css'] ) ? false : true );
	}
}

// -----------------------------------------------------------------------------
// Breakpoints
// -----------------------------------------------------------------------------

if ( ! function_exists( 'Opulentia_get_tablet_breakpoint' ) ) {
	/**
	 * Get tablet breakpoint.
	 *
	 * @param  int $min Subtract from breakpoint.
	 * @param  int $max Add to breakpoint.
	 * @return int
	 */
	function Opulentia_get_tablet_breakpoint( $min = '', $max = '' ) {
		$breakpoint = apply_filters( 'Opulentia_tablet_breakpoint', 992 );

		if ( '' !== $min ) {
			$breakpoint -= $min;
		} elseif ( '' !== $max ) {
			$breakpoint += $max;
		}

		return absint( $breakpoint );
	}
}

if ( ! function_exists( 'Opulentia_get_mobile_breakpoint' ) ) {
	/**
	 * Get mobile breakpoint.
	 *
	 * @param  int $min Subtract from breakpoint.
	 * @param  int $max Add to breakpoint.
	 * @return int
	 */
	function Opulentia_get_mobile_breakpoint( $min = '', $max = '' ) {
		$breakpoint = apply_filters( 'Opulentia_mobile_breakpoint', 576 );

		if ( '' !== $min ) {
			$breakpoint -= $min;
		} elseif ( '' !== $max ) {
			$breakpoint += $max;
		}

		return absint( $breakpoint );
	}
}

// -----------------------------------------------------------------------------
// Post ID & Type
// -----------------------------------------------------------------------------

if ( ! function_exists( 'Opulentia_get_post_id' ) ) {
	/**
	 * Get current post ID.
	 *
	 * @return int
	 */
	function Opulentia_get_post_id() {
		if ( null === Opulentia_Theme_Options::$post_id ) {
			global $post;

			$post_id = 0;

			if ( is_home() ) {
				$post_id = get_option( 'page_for_posts' );
			} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
				$post_id = wc_get_page_id( 'shop' );
			} elseif ( is_archive() ) {
				global $wp_query;
				$post_id = $wp_query->get_queried_object_id();
			} elseif ( isset( $post->ID ) && ! is_search() && ! is_category() ) {
				$post_id = $post->ID;
			}

			Opulentia_Theme_Options::$post_id = $post_id;
		}

		return apply_filters( 'Opulentia_get_post_id', Opulentia_Theme_Options::$post_id );
	}
}

if ( ! function_exists( 'Opulentia_get_post_type' ) ) {
	/**
	 * Get current post type, including from taxonomy archives.
	 *
	 * @return string
	 */
	function Opulentia_get_post_type() {
		$post_type = get_post_type();

		if ( ! $post_type ) {
			$queried_object = get_queried_object();

			if ( is_category() || is_tag() || is_tax() ) {
				$taxonomy   = $queried_object->taxonomy ?? '';
				$post_types = get_post_types();

				foreach ( $post_types as $type ) {
					if ( in_array( $taxonomy, get_object_taxonomies( $type ), true ) ) {
						$post_type = $type;
						break;
					}
				}
			}
		}

		return apply_filters( 'Opulentia_get_post_type', strval( $post_type ) );
	}
}

// -----------------------------------------------------------------------------
// Check Helpers
// -----------------------------------------------------------------------------

if ( ! function_exists( 'Opulentia_check_pagination_enabled' ) ) {
	/**
	 * Check if pagination is enabled on current page.
	 *
	 * @return bool
	 */
	function Opulentia_check_pagination_enabled() {
		global $wp_query;
		return $wp_query->max_num_pages > 1 && apply_filters( 'Opulentia_pagination_enabled', true );
	}
}

if ( ! function_exists( 'Opulentia_is_amp_endpoint' ) ) {
	/**
	 * Check if we're being delivered via AMP.
	 *
	 * @return bool
	 */
	function Opulentia_is_amp_endpoint() {
		return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();
	}
}

if ( ! function_exists( 'Opulentia_parse_selector' ) ) {
	/**
	 * Parse selectors and conditionally remove plugin-specific selectors.
	 *
	 * @param  string       $selectors Comma-separated CSS selectors.
	 * @param  string|array $keywords  Keywords to filter out (e.g., 'wc', 'elementor').
	 * @return string                  Filtered selector string.
	 */
	function Opulentia_parse_selector( $selectors, $keywords = '' ) {
		$selector_array     = explode( ',', $selectors );
		$filtered_selectors = array();

		if ( is_string( $keywords ) ) {
			$keywords = array( $keywords );
		}

		foreach ( $selector_array as $selector ) {
			$selector        = trim( $selector );
			$ignore_selector = false;

			foreach ( $keywords as $keyword ) {
				switch ( $keyword ) {
					case 'wc':
					case 'woocommerce':
						if ( ! defined( 'WC_VERSION' ) && false !== strpos( $selector, 'woocommerce' ) ) {
							$ignore_selector = true;
							break 2;
						}
						break;

					case 'el':
					case 'elementor':
						if ( ! defined( 'ELEMENTOR_VERSION' ) && false !== strpos( $selector, 'elementor' ) ) {
							$ignore_selector = true;
							break 2;
						}
						break;
				}
			}

			if ( ! $ignore_selector ) {
				$filtered_selectors[] = $selector;
			}
		}

		return implode( ', ', $filtered_selectors );
	}
}

// -----------------------------------------------------------------------------
// Sanitization Callbacks
// -----------------------------------------------------------------------------

if ( ! function_exists( 'Opulentia_sanitize_checkbox' ) ) {
	/**
	 * Sanitize a checkbox value.
	 *
	 * @param  mixed $input Checkbox value.
	 * @return bool
	 */
	function Opulentia_sanitize_checkbox( $input ) {
		return (bool) $input;
	}
}

if ( ! function_exists( 'Opulentia_sanitize_multi_choice' ) ) {
	/**
	 * Sanitize a multi-choice select value (comma-separated string to array).
	 *
	 * @param  mixed $input Comma-separated string.
	 * @return string
	 */
	function Opulentia_sanitize_multi_choice( $input ) {
		if ( is_array( $input ) ) {
			$input = implode( ',', $input );
		}
		$valid = array_map( 'sanitize_text_field', explode( ',', $input ) );
		return implode( ',', array_filter( $valid ) );
	}
}

if ( ! function_exists( 'Opulentia_flip_rtl_alignment' ) ) {
	/**
	 * Flip horizontal alignment for RTL languages.
	 *
	 * @param  string $alignment 'left', 'right', or other.
	 * @return string
	 */
	function Opulentia_flip_rtl_alignment( $alignment ) {
		if ( ! is_rtl() ) {
			return $alignment;
		}

		switch ( $alignment ) {
			case 'left':
				return 'right';
			case 'right':
				return 'left';
			default:
				return $alignment;
		}
	}
}
