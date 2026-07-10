<?php
/**
 * Blog & Archive Dynamic CSS
 *
 * Generates inline CSS for blog, archive, and single post
 * elements based on customizer settings.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate blog & archive dynamic CSS.
 *
 * @return string Inline CSS string.
 */
function Opulentia_dynamic_blog_css() {
	$css = '';

	// Blog layout: grid columns.
	$blog_columns = (int) get_theme_mod( 'blog_grid_columns', 2 );
	$blog_columns = max( 1, min( 4, $blog_columns ) );

	$blog_layout = get_theme_mod( 'blog_layout', 'grid' );

	if ( 'grid' === $blog_layout && 2 !== $blog_columns ) {
		$css .= ".posts-grid--grid {\n";
		$css .= "    grid-template-columns: repeat({$blog_columns}, 1fr);\n";
		$css .= "}\n";
	}

	// List layout: fix list image width based on grid columns value.
	if ( 'list' === $blog_layout ) {
		$image_width = 280 + ( $blog_columns * 10 );
		$css        .= ".post-list {\n";
		$css        .= "    grid-template-columns: {$image_width}px 1fr;\n";
		$css        .= "}\n";
	}

	// Image aspect ratio for blog cards.
	$aspect_ratio = get_theme_mod( 'blog_image_aspect_ratio', '16/10' );
	$valid_ratios = array( '16/10', '16/9', '4/3', '3/2', '1/1', 'original' );
	if ( ! in_array( $aspect_ratio, $valid_ratios, true ) ) {
		$aspect_ratio = '16/10';
	}

	if ( 'original' === $aspect_ratio ) {
		$css .= ".post-card__image { aspect-ratio: auto; }\n";
		$css .= ".post-classic__image { aspect-ratio: auto; }\n";
		$css .= ".post-list__image { aspect-ratio: auto; }\n";
		$css .= ".related-post-card__image { aspect-ratio: auto; }\n";
	} elseif ( '16/10' !== $aspect_ratio ) {
		$css .= ".post-card__image { aspect-ratio: {$aspect_ratio}; }\n";
		$css .= ".post-classic__image { aspect-ratio: {$aspect_ratio}; }\n";
		$css .= ".post-list__image { aspect-ratio: {$aspect_ratio}; }\n";
		$css .= ".related-post-card__image { aspect-ratio: {$aspect_ratio}; }\n";
	}

	// Card background color.
	$card_bg = get_theme_mod( 'blog_card_bg', '' );
	if ( ! empty( $card_bg ) ) {
		$css .= ".post-card { background-color: {$card_bg}; }\n";
	}

	// Card border color.
	$card_border = get_theme_mod( 'blog_card_border', '' );
	if ( ! empty( $card_border ) ) {
		$css .= ".post-card { border-color: {$card_border}; }\n";
		$css .= ".post-card:hover { border-color: var(--opulentia-global-color-3, #c9a96e); }\n";
	}

	// Card border radius.
	$card_radius = get_theme_mod( 'blog_card_radius', '8' );
	if ( '8' !== $card_radius ) {
		$css .= ".post-card { border-radius: {$card_radius}px; }\n";
		$css .= ".post-card__image { border-radius: {$card_radius}px {$card_radius}px 0 0; }\n";
	}

	// Card shadow.
	$card_shadow = get_theme_mod( 'blog_card_shadow', true );
	if ( ! $card_shadow ) {
		$css .= ".post-card:hover { box-shadow: none; }\n";
	}

	// Single post: featured image border radius.
	$image_radius = get_theme_mod( 'blog_image_radius', '8px' );
	if ( '8px' !== $image_radius ) {
		$css .= ".post__thumbnail img,\n";
		$css .= ".single-post__thumbnail img,\n";
		$css .= ".post-classic__image img,\n";
		$css .= ".post-card__image img {\n";
		$css .= "    border-radius: {$image_radius};\n";
		$css .= "}\n";
	}

	// Single post: show/hide related posts.
	$show_related = get_theme_mod( 'blog_single_show_related', true );
	if ( ! $show_related ) {
		$css .= '.related-posts { display: none; }' . "\n";
	}

	// Single post: show/hide author box.
	$show_author = get_theme_mod( 'blog_single_show_author', true );
	if ( ! $show_author ) {
		$css .= '.author-box { display: none; }' . "\n";
	}

	// Single post: show/hide post navigation.
	$show_nav = get_theme_mod( 'blog_single_show_navigation', true );
	if ( ! $show_nav ) {
		$css .= '.post-navigation { display: none; }' . "\n";
	}

	// Post navigation style.
	$nav_style = get_theme_mod( 'blog_post_nav_style', 'default' );
	if ( 'thumbnail' === $nav_style ) {
		$css .= '.post-navigation .nav-links { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }' . "\n";
		$css .= '.post-navigation .nav-previous, .post-navigation .nav-next { padding: 20px; background-color: var(--opulentia-global-color-1, #111); border: 1px solid var(--opulentia-global-color-7, #333); border-radius: 8px; transition: border-color 0.3s ease; }' . "\n";
		$css .= '.post-navigation .nav-previous:hover, .post-navigation .nav-next:hover { border-color: var(--opulentia-global-color-3, #c9a96e); }' . "\n";
		$css .= '.post-navigation .nav-subtitle { display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: var(--opulentia-global-color-6, #999); margin-bottom: 4px; }' . "\n";
		$css .= '.post-navigation .nav-title { font-size: 0.9375rem; line-height: 1.4; }' . "\n";
	}

	return $css;
}
