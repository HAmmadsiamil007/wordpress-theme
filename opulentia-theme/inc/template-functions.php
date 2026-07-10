<?php
/**
 * Template Functions
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom body classes.
 *
 * @param array $classes Body classes.
 * @return array Modified body classes.
 */
function Opulentia_body_classes( $classes ) {
	// Add a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Add a class of no-sidebar when there is no sidebar.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	// Add sidebar position class from customizer.
	$sidebar_position = get_theme_mod( 'layout_sidebar_position', 'right' );
	if ( 'none' === $sidebar_position ) {
		$classes[] = 'no-sidebar';
	} elseif ( 'left' === $sidebar_position ) {
		$classes[] = 'sidebar-left';
	}

	// Add WooCommerce body classes.
	if ( class_exists( 'WooCommerce' ) ) {
		if ( is_woocommerce() ) {
			$classes[] = 'woocommerce-page';
		}
	}

	return $classes;
}
add_filter( 'body_class', 'Opulentia_body_classes' );

/**
 * Custom excerpt length.
 *
 * Reads from customizer setting blog_excerpt_length.
 *
 * @param int $length Excerpt length.
 * @return int Modified excerpt length.
 */
function Opulentia_excerpt_length( $length ) {
	$custom_length = get_theme_mod( 'blog_excerpt_length', 20 );
	return absint( $custom_length );
}
add_filter( 'excerpt_length', 'Opulentia_excerpt_length', 999 );

/**
 * Custom excerpt more.
 *
 * Reads from customizer setting blog_read_more_text.
 *
 * @param string $more Excerpt more text.
 * @return string Modified excerpt more text.
 */
function Opulentia_excerpt_more( $more ) {
	$read_more = get_theme_mod( 'blog_read_more_text', 'Read More' );
	return '&hellip; ' . esc_html( $read_more );
}
add_filter( 'excerpt_more', 'Opulentia_excerpt_more' );

/**
 * Add custom image sizes to WooCommerce product images.
 *
 * @param array $sizes Available image sizes.
 * @return array Modified image sizes.
 */

/**
 * Set WooCommerce product columns from customizer.
 *
 * @return int Number of columns.
 */
function Opulentia_wc_product_columns() {
	$columns = get_theme_mod( 'wc_product_columns', 4 );
	return absint( $columns );
}
add_filter( 'loop_shop_columns', 'Opulentia_wc_product_columns' );

/**
 * Set WooCommerce products per page from customizer.
 *
 * @return int Number of products per page.
 */
function Opulentia_wc_products_per_page() {
	$per_page = get_theme_mod( 'wc_products_per_page', 12 );
	return absint( $per_page );
}
add_filter( 'loop_shop_per_page', 'Opulentia_wc_products_per_page' );

function Opulentia_woocommerce_image_sizes( $sizes ) {
	unset( $sizes['shop_catalog'] );
	unset( $sizes['shop_single'] );
	$sizes['shop_catalog'] = array(
		'height' => 600,
		'width'  => 600,
		'crop'   => 1,
	);
	$sizes['shop_single']  = array(
		'height' => 600,
		'width'  => 600,
		'crop'   => 1,
	);

	return $sizes;
}
add_filter( 'woocommerce_add_image_sizes', 'Opulentia_woocommerce_image_sizes' );

/**
 * Get the current blog layout based on customizer setting,
 * with per-post metabox override support.
 *
 * Falls back to 'grid' if the setting is not set or invalid.
 *
 * @param int $post_id Optional post ID for metabox override.
 * @return string Layout name: 'classic', 'grid', or 'list'.
 */
function Opulentia_get_blog_layout( $post_id = 0 ) {
	// Check for per-post metabox override.
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if ( $post_id ) {
		$meta_layout = get_post_meta( $post_id, '_Opulentia_blog_layout', true );
		if ( ! empty( $meta_layout ) ) {
			return $meta_layout;
		}
	}

	// Fall back to global setting.
	$layout = get_theme_mod( 'blog_layout', 'grid' );
	$valid  = array( 'classic', 'grid', 'list' );

	if ( ! in_array( $layout, $valid, true ) ) {
		$layout = 'grid';
	}

	return $layout;
}

/**
 * Get the blog image aspect ratio, with per-post metabox override.
 *
 * @param int $post_id Optional post ID for metabox override.
 * @return string Aspect ratio value (e.g. '16/10', '4/3', 'original').
 */
function Opulentia_get_blog_image_aspect_ratio( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if ( $post_id ) {
		$meta_ratio = get_post_meta( $post_id, '_Opulentia_blog_image_aspect_ratio', true );
		if ( ! empty( $meta_ratio ) ) {
			return $meta_ratio;
		}
	}

	return get_theme_mod( 'blog_image_aspect_ratio', '16/10' );
}

/**
 * Check if the featured image should be hidden for this post.
 *
 * @param int $post_id Optional post ID.
 * @return bool
 */
function Opulentia_blog_hide_featured_image( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if ( $post_id ) {
		return '1' === get_post_meta( $post_id, '_Opulentia_blog_hide_featured_image', true );
	}

	return false;
}

/**
 * Get the related posts count from customizer.
 *
 * @return int
 */
function Opulentia_get_related_posts_count() {
	return (int) get_theme_mod( 'blog_related_count', 3 );
}

/**
 * Render the current blog layout template part.
 *
 * Loads the appropriate template part based on the
 * customizer blog_layout setting.
 */
function Opulentia_render_blog_layout() {
	$layout = Opulentia_get_blog_layout();
	get_template_part( 'template-parts/blog/layout', $layout );
}

/**
 * Display the post thumbnail.
 */
function Opulentia_post_thumbnail() {
	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	if ( is_singular() ) {
		printf(
			'<div class="post__thumbnail">%s</div>',
			get_the_post_thumbnail( null, 'large', array( 'loading' => 'lazy' ) )
		);
	} else {
		printf(
			'<a class="post__thumbnail" href="%s" aria-hidden="true">%s</a>',
			esc_url( get_the_permalink() ),
			get_the_post_thumbnail( null, 'opulentia-blog', array( 'loading' => 'lazy' ) )
		);
	}
}

/**
 * Add schema markup for products.
 */
function Opulentia_product_schema() {
	if ( ! is_singular( 'product' ) ) {
		return;
	}

	global $post;
	$product = wc_get_product( $post->ID );

	if ( ! $product ) {
		return;
	}

	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Product',
		'name'        => $product->get_name(),
		'description' => $product->get_short_description(),
		'image'       => wp_get_attachment_url( $product->get_image_id() ),
		'brand'       => array(
			'@type' => 'Brand',
			'name'  => 'opulentia',
		),
		'offers'      => array(
			'@type'         => 'Offer',
			'url'           => get_permalink(),
			'priceCurrency' => function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'USD',
			'price'         => $product->get_price(),
			'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
			'itemCondition' => 'https://schema.org/NewCondition',
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . '</script>';
}
add_action( 'wp_head', 'Opulentia_product_schema' );
