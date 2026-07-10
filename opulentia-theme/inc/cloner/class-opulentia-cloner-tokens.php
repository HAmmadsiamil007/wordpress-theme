<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

class Opulentia_Cloner_Tokens {
	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function generate_design_md( $analysis ) {
		$colors     = $analysis['color_palette'] ?? array();
		$typography = $analysis['typography'] ?? array();
		$layout     = $analysis['layout'] ?? array();
		$components = $analysis['components'] ?? array();
		$images     = $analysis['images'] ?? array();
		$metadata   = $analysis['metadata'] ?? array();

		$palette = $this->generate_color_palette( $colors );
		$fonts   = $this->recommend_font_stack( $typography );

		$md  = "# Design Tokens: {$metadata['title']}\n\n";
		$md .= "> Auto-generated from site analysis\n";
		$md .= "> Source: {$this->get_source_url()}\n\n";

		$md .= "## Brand Colors\n\n";
		$md .= "| Token | Value | Usage |\n|-------|-------|-------|\n";
		foreach ( $palette as $i => $color ) {
			$usage = $this->get_color_usage( $i, count( $palette ) );
			$md   .= "| `--color-{$color['slug']}` | `{$color['hex']}` | {$usage} |\n";
		}

		$md .= "\n## Typography\n\n";
		$md .= "- **Heading Font:** `{$fonts['heading']}`\n";
		$md .= "- **Body Font:** `{$fonts['body']}`\n";
		$md .= "- **Base Size:** {$typography['base_size']}\n";
		$md .= "- **Line Height:** {$typography['line_height']}\n";

		$md .= "\n## Layout\n\n";
		$md .= "- **Container Width:** {$layout['container_width']}\n";
		$md .= "- **Content Width:** {$layout['content_width']}\n";
		$md .= "- **Sidebar Width:** {$layout['sidebar_width']}\n";
		$md .= "- **Grid Columns:** {$layout['grid_columns']}\n";

		$md .= "\n## Components\n\n";
		$md .= "- **Header Style:** {$components['header_style']}\n";
		$md .= "- **Footer Style:** {$components['footer_style']}\n";
		$md .= "- **Button Style:** {$components['button_style']}\n";
		$md .= "- **Button Radius:** {$components['button_radius']}\n";
		$md .= "- **Navigation:** {$components['navigation_style']}\n";

		if ( ! empty( $images['logo'] ) || ! empty( $images['favicon'] ) ) {
			$md .= "\n## Assets\n\n";
			if ( ! empty( $images['logo'] ) ) {
				$md .= "- **Logo:** `{$images['logo']}`\n";
			}
			if ( ! empty( $images['favicon'] ) ) {
				$md .= "- **Favicon:** `{$images['favicon']}`\n";
			}
		}

		return $md;
	}

	public function analysis_to_theme_mods( $analysis ) {
		$colors     = $analysis['color_palette'] ?? array();
		$typography = $analysis['typography'] ?? array();
		$layout     = $analysis['layout'] ?? array();
		$components = $analysis['components'] ?? array();
		$palette    = $this->generate_color_palette( $colors );

		$mods = array();

		foreach ( $palette as $i => $color ) {
			$mods[ "opulentia-global-color-{$i}" ] = $color['hex'];
		}

		$fonts                                 = $this->recommend_font_stack( $typography );
		$mods['opulentia-body-font-family']    = $fonts['body'];
		$mods['opulentia-heading-font-family'] = $fonts['heading'];

		$mods['opulentia-container-max-width'] = $layout['container_width'];
		$mods['opulentia-button-radius']       = $components['button_radius'];
		$mods['opulentia-header-layout']       = $components['header_style'];

		return $mods;
	}

	private function generate_color_palette( $colors ) {
		$palette = array();
		$slugs   = array( 'primary-dark', 'secondary-dark', 'accent', 'accent-hover', 'gold', 'light-gold', 'text', 'text-muted', 'border' );
		$usages  = array( $colors['background'] ?? '#1a1a1a', $colors['primary'] ?? '#111', $colors['accent'] ?? '#b8860b', $this->lighten( $colors['accent'] ?? '#b8860b', 20 ), '#c9a96e', '#e8d5a3', $colors['text'] ?? '#f5f5f5', '#999', '#333' );

		foreach ( $slugs as $i => $slug ) {
			$palette[] = array(
				'slug' => $slug,
				'hex'  => $usages[ $i ] ?? '#ccc',
			);
		}

		if ( isset( $colors['primary'] ) ) {
			$palette[0]['hex'] = $colors['primary'];
			$palette[1]['hex'] = $this->darken( $colors['primary'], 10 );
		}

		return $palette;
	}

	private function recommend_font_stack( $typography ) {
		return array(
			'heading' => $typography['heading_font'] ?? 'Playfair Display',
			'body'    => $typography['body_font'] ?? 'Inter',
		);
	}

	private function get_source_url() {
		$session = get_option( 'opulentia_cloner_session', array() );
		return $session['url'] ?? 'unknown';
	}

	private function get_color_usage( $index, $total ) {
		$usages = array( 'Primary background', 'Cards & dropdowns', 'Buttons & highlights', 'Hover states', 'Headings & borders', 'Subtle accents', 'Body text', 'Secondary text', 'Borders & dividers' );
		return $usages[ $index ] ?? 'General purpose';
	}

	private function lighten( $hex, $percent ) {
		return $this->adjust_brightness( $hex, $percent );
	}

	private function darken( $hex, $percent ) {
		return $this->adjust_brightness( $hex, -$percent );
	}

	private function adjust_brightness( $hex, $percent ) {
		$hex = ltrim( $hex, '#' );
		if ( strlen( $hex ) === 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		$r = min( 255, max( 0, hexdec( substr( $hex, 0, 2 ) ) + $percent * 2.55 ) );
		$g = min( 255, max( 0, hexdec( substr( $hex, 2, 2 ) ) + $percent * 2.55 ) );
		$b = min( 255, max( 0, hexdec( substr( $hex, 4, 2 ) ) + $percent * 2.55 ) );
		return '#' . dechex( (int) $r ) . dechex( (int) $g ) . dechex( (int) $b );
	}

	public function from_dembrandt_json( $dembrandt_data ) {
		$colors     = $dembrandt_data['colors']['palette'] ?? array();
		$semantic   = $dembrandt_data['colors']['semantic'] ?? array();
		$typography = $dembrandt_data['typography']['styles'] ?? array();
		$spacing    = $dembrandt_data['spacing']['commonValues'] ?? array();
		$components = $dembrandt_data['components'] ?? array();

		$color_map = array();

		if ( ! empty( $semantic['primary'] ) ) {
			$color_map['primary'] = $this->normalize_hex( $semantic['primary'] );
		}
		if ( ! empty( $semantic['secondary'] ) ) {
			$color_map['secondary'] = $this->normalize_hex( $semantic['secondary'] );
		}
		if ( ! empty( $semantic['background'] ) ) {
			$color_map['background'] = $this->normalize_hex( $semantic['background'] );
		}
		if ( ! empty( $semantic['text'] ) ) {
			$color_map['text'] = $this->normalize_hex( $semantic['text'] );
		}
		if ( ! empty( $semantic['accent'] ) ) {
			$color_map['accent'] = $this->normalize_hex( $semantic['accent'] );
		}

		foreach ( $colors as $c ) {
			$hex  = $c['normalized'] ?? '';
			$role = $c['role'] ?? '';
			if ( empty( $hex ) ) {
				continue; }
			if ( $role === 'surface' && ! isset( $color_map['background'] ) ) {
				$color_map['background'] = $hex;
			} elseif ( $role === 'neutral' && ! isset( $color_map['secondary'] ) ) {
				$color_map['secondary'] = $hex;
			} elseif ( $role === 'primary' && ! isset( $color_map['primary'] ) ) {
				$color_map['primary'] = $hex;
			} elseif ( $role === 'accent' && ! isset( $color_map['accent'] ) ) {
				$color_map['accent'] = $hex;
			}
		}

		$body_font     = '';
		$heading_font  = '';
		$used_families = array();

		foreach ( $typography as $t ) {
			$family = $t['family'] ?? '';
			$ctx    = $t['context'] ?? '';
			if ( empty( $family ) ) {
				continue; }
			if ( ! in_array( $family, $used_families, true ) ) {
				$used_families[] = $family;
			}
			if ( $ctx === 'heading-1' || $ctx === 'heading-2' ) {
				if ( empty( $heading_font ) ) {
					$heading_font = $family;
				}
			} elseif ( $ctx === 'body' && empty( $body_font ) ) {
				$body_font = $family;
			}
		}

		if ( empty( $heading_font ) && ! empty( $used_families ) ) {
			foreach ( $used_families as $f ) {
				if ( $f !== $body_font ) {
					$heading_font = $f;
					break;
				}
			}
		}

		$analysis = array(
			'color_palette' => $color_map,
			'typography'    => array(
				'body_font'    => $body_font ?: 'Inter',
				'heading_font' => $heading_font ?: 'Playfair Display',
				'base_size'    => '16px',
				'line_height'  => '1.6',
			),
			'layout'        => array(
				'container_width' => '1200px',
				'content_width'   => '66.67%',
				'sidebar_width'   => '33.33%',
				'grid_columns'    => 3,
			),
			'components'    => array(
				'header_style'     => 'default',
				'footer_style'     => 'default',
				'button_style'     => 'solid',
				'button_radius'    => '4px',
				'navigation_style' => 'horizontal',
			),
			'images'        => array(
				'logo'    => $dembrandt_data['logo']['url'] ?? $dembrandt_data['brand']['logo']['url'] ?? '',
				'favicon' => $dembrandt_data['favicons'][0]['url'] ?? '',
			),
			'metadata'      => array(
				'title'       => $dembrandt_data['siteName'] ?? $dembrandt_data['brand']['name'] ?? '',
				'description' => $dembrandt_data['meta']['description'] ?? '',
			),
		);

		$buttons = $components['buttons'] ?? array();
		if ( ! empty( $buttons[0]['states']['default']['borderRadius'] ) ) {
			$analysis['components']['button_radius'] = $buttons[0]['states']['default']['borderRadius'];
		}

		return $analysis;
	}

	private function normalize_hex( $color ) {
		if ( preg_match( '/^#[0-9a-fA-F]{3,8}$/', $color ) ) {
			return $color;
		}
		if ( preg_match( '/rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/', $color, $m ) ) {
			return sprintf( '#%02x%02x%02x', (int) $m[1], (int) $m[2], (int) $m[3] );
		}
		return $color;
	}
}
