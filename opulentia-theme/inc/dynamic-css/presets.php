<?php
/**
 * Color Scheme Presets — Single Source of Truth
 *
 * Defines all color presets in one place so both the Dynamic CSS engine
 * and the Customizer config reference the same data.
 *
 * Each preset includes:
 *   - 'name'              Display label for the customizer
 *   - '--css-var'         Legacy CSS custom property overrides
 *   - 'global'            Array of 9 colors for --opulentia-global-color-0 through 8
 *
 * Global Palette Index:
 *   0: Page background (darkest)
 *   1: Card / section background
 *   2: Accent / primary action
 *   3: Gold / heading accent
 *   4: Light gold / subtle accent
 *   5: Body text / foreground
 *   6: Muted text
 *   7: Border color
 *   8: White / brightest
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all available color scheme presets.
 *
 * @return array Associative array of preset_id => { name, --css-var: value, global: [] }.
 */
function Opulentia_get_color_presets() {
	return array(

		// ─────────────────────────────────────────────────────────────────────
		// 1. Dark Luxury — original Opulentia design (default)
		// ─────────────────────────────────────────────────────────────────────
		'dark-luxury'     => array(
			'name'                   => __( 'Dark Luxury (Default)', 'opulentia' ),
			'--color-primary-dark'   => '#1a1a1a',
			'--color-secondary-dark' => '#111111',
			'--color-accent'         => '#b8860b',
			'--color-gold'           => '#c9a96e',
			'--color-light-gold'     => '#e8d5a3',
			'--color-text'           => '#f5f5f5',
			'--color-medium-gray'    => '#999999',
			'--color-border'         => '#333333',
			'global'                 => array(
				'#1a1a1a', // 0: Page bg
				'#111111', // 1: Card bg
				'#b8860b', // 2: Accent
				'#c9a96e', // 3: Gold
				'#e8d5a3', // 4: Light gold
				'#f5f5f5', // 5: Body text
				'#999999', // 6: Muted text
				'#333333', // 7: Border
				'#ffffff', // 8: White
			),
		),

		// ─────────────────────────────────────────────────────────────────────
		// 2. Midnight Gold — deeper, richer gold on near-black
		// ─────────────────────────────────────────────────────────────────────
		'midnight-gold'   => array(
			'name'                   => __( 'Midnight Gold', 'opulentia' ),
			'--color-primary-dark'   => '#0d0d0d',
			'--color-secondary-dark' => '#0a0a0a',
			'--color-accent'         => '#c4932e',
			'--color-gold'           => '#d4a843',
			'--color-light-gold'     => '#f0d68a',
			'--color-text'           => '#f0ece4',
			'--color-medium-gray'    => '#8a8a8a',
			'--color-border'         => '#2a2a2a',
			'global'                 => array(
				'#0d0d0d',
				'#0a0a0a',
				'#c4932e',
				'#d4a843',
				'#f0d68a',
				'#f0ece4',
				'#8a8a8a',
				'#2a2a2a',
				'#ffffff',
			),
		),

		// ─────────────────────────────────────────────────────────────────────
		// 3. Obsidian Silver — sleek, modern monochrome with silver accents
		// ─────────────────────────────────────────────────────────────────────
		'obsidian-silver' => array(
			'name'                   => __( 'Obsidian Silver', 'opulentia' ),
			'--color-primary-dark'   => '#121212',
			'--color-secondary-dark' => '#0e0e0e',
			'--color-accent'         => '#888888',
			'--color-gold'           => '#c0c0c0',
			'--color-light-gold'     => '#e0e0e0',
			'--color-text'           => '#eeeeee',
			'--color-medium-gray'    => '#7a7a7a',
			'--color-border'         => '#2c2c2c',
			'global'                 => array(
				'#121212',
				'#0e0e0e',
				'#888888',
				'#c0c0c0',
				'#e0e0e0',
				'#eeeeee',
				'#7a7a7a',
				'#2c2c2c',
				'#ffffff',
			),
		),

		// ─────────────────────────────────────────────────────────────────────
		// 4. Espresso Brown — warm, rich brown tones
		// ─────────────────────────────────────────────────────────────────────
		'espresso-brown'  => array(
			'name'                   => __( 'Espresso Brown', 'opulentia' ),
			'--color-primary-dark'   => '#1c1814',
			'--color-secondary-dark' => '#14100c',
			'--color-accent'         => '#a0712e',
			'--color-gold'           => '#b8884a',
			'--color-light-gold'     => '#d4b483',
			'--color-text'           => '#f0e8dc',
			'--color-medium-gray'    => '#8a7d6e',
			'--color-border'         => '#3a3026',
			'global'                 => array(
				'#1c1814',
				'#14100c',
				'#a0712e',
				'#b8884a',
				'#d4b483',
				'#f0e8dc',
				'#8a7d6e',
				'#3a3026',
				'#ffffff',
			),
		),

		// ─────────────────────────────────────────────────────────────────────
		// 5. Royal Navy — dark navy blue with gold accents
		// ─────────────────────────────────────────────────────────────────────
		'royal-navy'      => array(
			'name'                   => __( 'Royal Navy', 'opulentia' ),
			'--color-primary-dark'   => '#0d1b2a',
			'--color-secondary-dark' => '#0a141f',
			'--color-accent'         => '#b8860b',
			'--color-gold'           => '#c9a96e',
			'--color-light-gold'     => '#e8d5a3',
			'--color-text'           => '#e8eef5',
			'--color-medium-gray'    => '#8899aa',
			'--color-border'         => '#1e3048',
			'global'                 => array(
				'#0d1b2a',
				'#0a141f',
				'#b8860b',
				'#c9a96e',
				'#e8d5a3',
				'#e8eef5',
				'#8899aa',
				'#1e3048',
				'#ffffff',
			),
		),

		// ─────────────────────────────────────────────────────────────────────
		// 6. Deep Burgundy — rich wine/burgundy palette
		// ─────────────────────────────────────────────────────────────────────
		'deep-burgundy'   => array(
			'name'                   => __( 'Deep Burgundy', 'opulentia' ),
			'--color-primary-dark'   => '#1a0f14',
			'--color-secondary-dark' => '#140a0e',
			'--color-accent'         => '#8b1a3b',
			'--color-gold'           => '#c9a06e',
			'--color-light-gold'     => '#e8cfa3',
			'--color-text'           => '#f0e8ea',
			'--color-medium-gray'    => '#8a7a7e',
			'--color-border'         => '#3a2028',
			'global'                 => array(
				'#1a0f14',
				'#140a0e',
				'#8b1a3b',
				'#c9a06e',
				'#e8cfa3',
				'#f0e8ea',
				'#8a7a7e',
				'#3a2028',
				'#ffffff',
			),
		),

		// ─────────────────────────────────────────────────────────────────────
		// 7. Emerald Night — deep forest green with gold
		// ─────────────────────────────────────────────────────────────────────
		'emerald-night'   => array(
			'name'                   => __( 'Emerald Night', 'opulentia' ),
			'--color-primary-dark'   => '#0e1a14',
			'--color-secondary-dark' => '#0a140e',
			'--color-accent'         => '#2d7a4a',
			'--color-gold'           => '#b8a060',
			'--color-light-gold'     => '#d4c888',
			'--color-text'           => '#e8f0ea',
			'--color-medium-gray'    => '#7a8a80',
			'--color-border'         => '#1e3026',
			'global'                 => array(
				'#0e1a14',
				'#0a140e',
				'#2d7a4a',
				'#b8a060',
				'#d4c888',
				'#e8f0ea',
				'#7a8a80',
				'#1e3026',
				'#ffffff',
			),
		),

		// ─────────────────────────────────────────────────────────────────────
		// 8. Platinum Frost — icy platinum with cool white
		// ─────────────────────────────────────────────────────────────────────
		'platinum-frost'  => array(
			'name'                   => __( 'Platinum Frost', 'opulentia' ),
			'--color-primary-dark'   => '#16181a',
			'--color-secondary-dark' => '#101214',
			'--color-accent'         => '#7a8a9a',
			'--color-gold'           => '#c8d0d8',
			'--color-light-gold'     => '#e0e8f0',
			'--color-text'           => '#e8eaec',
			'--color-medium-gray'    => '#888a8c',
			'--color-border'         => '#2a2e32',
			'global'                 => array(
				'#16181a',
				'#101214',
				'#7a8a9a',
				'#c8d0d8',
				'#e0e8f0',
				'#e8eaec',
				'#888a8c',
				'#2a2e32',
				'#ffffff',
			),
		),
	);
}

/**
 * Get preset choices array for use in Customizer select control.
 *
 * @return array Select choices.
 */
function Opulentia_get_preset_choices() {
	$choices = array();
	foreach ( Opulentia_get_color_presets() as $key => $preset ) {
		$choices[ $key ] = $preset['name'];
	}
	return $choices;
}

/**
 * Get the global palette array for a given preset ID.
 *
 * Returns the 9-color array for --opulentia-global-color-0 through 8.
 *
 * @param string $preset_id Preset ID (defaults to 'dark-luxury').
 * @return array 9-color array.
 */
function Opulentia_get_global_palette_by_preset( $preset_id = 'dark-luxury' ) {
	$presets = Opulentia_get_color_presets();

	if ( isset( $presets[ $preset_id ]['global'] ) ) {
		return $presets[ $preset_id ]['global'];
	}

	// Fallback to Dark Luxury.
	return $presets['dark-luxury']['global'];
}

/**
 * Get labels for the 9 global palette colors.
 *
 * @return array Label strings.
 */
function Opulentia_get_global_palette_labels() {
	return array(
		0 => __( 'Page Background', 'opulentia' ),
		1 => __( 'Card / Section Background', 'opulentia' ),
		2 => __( 'Accent Color', 'opulentia' ),
		3 => __( 'Gold / Heading Color', 'opulentia' ),
		4 => __( 'Light Gold / Subtle Accent', 'opulentia' ),
		5 => __( 'Body Text', 'opulentia' ),
		6 => __( 'Muted Text', 'opulentia' ),
		7 => __( 'Border Color', 'opulentia' ),
		8 => __( 'White / Brightest', 'opulentia' ),
	);
}
