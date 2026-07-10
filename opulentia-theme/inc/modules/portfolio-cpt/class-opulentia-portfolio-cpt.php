<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Portfolio_Cpt {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
		add_shortcode( 'op_portfolio_grid', array( $this, 'shortcode_grid' ) );
		add_shortcode( 'op_portfolio_single', array( $this, 'shortcode_single' ) );
		add_filter( 'single_template', array( $this, 'load_single_template' ) );
	}

	public function register_post_type() {
		$labels = array(
			'name'               => __( 'Portfolio', 'opulentia' ),
			'singular_name'      => __( 'Portfolio Item', 'opulentia' ),
			'add_new'            => __( 'Add New', 'opulentia' ),
			'add_new_item'       => __( 'Add New Portfolio Item', 'opulentia' ),
			'edit_item'          => __( 'Edit Portfolio Item', 'opulentia' ),
			'new_item'           => __( 'New Portfolio Item', 'opulentia' ),
			'view_item'          => __( 'View Portfolio Item', 'opulentia' ),
			'search_items'       => __( 'Search Portfolio', 'opulentia' ),
			'not_found'          => __( 'No portfolio items found', 'opulentia' ),
			'not_found_in_trash' => __( 'No portfolio items found in trash', 'opulentia' ),
			'all_items'          => __( 'All Portfolio', 'opulentia' ),
			'menu_name'          => __( 'Portfolio', 'opulentia' ),
		);

		register_post_type(
			'portfolio',
			array(
				'labels'       => $labels,
				'public'       => true,
				'has_archive'  => true,
				'rewrite'      => array( 'slug' => 'portfolio' ),
				'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
				'menu_icon'    => 'dashicons-portfolio',
				'show_in_rest' => true,
			)
		);
	}

	public function register_taxonomy() {
		register_taxonomy(
			'portfolio_category',
			'portfolio',
			array(
				'hierarchical' => true,
				'labels'       => array(
					'name'              => __( 'Portfolio Categories', 'opulentia' ),
					'singular_name'     => __( 'Portfolio Category', 'opulentia' ),
					'search_items'      => __( 'Search Categories', 'opulentia' ),
					'all_items'         => __( 'All Categories', 'opulentia' ),
					'parent_item'       => __( 'Parent Category', 'opulentia' ),
					'parent_item_colon' => __( 'Parent Category:', 'opulentia' ),
					'edit_item'         => __( 'Edit Category', 'opulentia' ),
					'update_item'       => __( 'Update Category', 'opulentia' ),
					'add_new_item'      => __( 'Add New Category', 'opulentia' ),
					'new_item_name'     => __( 'New Category Name', 'opulentia' ),
					'menu_name'         => __( 'Categories', 'opulentia' ),
				),
				'rewrite'      => array( 'slug' => 'portfolio/category' ),
				'show_in_rest' => true,
			)
		);
	}

	public function register_customizer( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_portfolio',
			array(
				'title'    => __( 'Portfolio', 'opulentia' ),
				'panel'    => 'Opulentia_global_settings',
				'priority' => 120,
			)
		);

		$wp_customize->add_setting(
			'portfolio-grid-columns',
			array(
				'default'           => 3,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'portfolio-grid-columns',
			array(
				'label'   => __( 'Grid Columns', 'opulentia' ),
				'section' => 'opulentia_portfolio',
				'type'    => 'select',
				'choices' => array(
					2 => __( '2 Columns', 'opulentia' ),
					3 => __( '3 Columns', 'opulentia' ),
					4 => __( '4 Columns', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'portfolio-grid-gap',
			array(
				'default'           => 20,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'portfolio-grid-gap',
			array(
				'label'   => __( 'Grid Gap (px)', 'opulentia' ),
				'section' => 'opulentia_portfolio',
				'type'    => 'select',
				'choices' => array(
					10 => __( '10px', 'opulentia' ),
					20 => __( '20px', 'opulentia' ),
					30 => __( '30px', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'portfolio-hover-effect',
			array(
				'default'           => 'zoom',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'portfolio-hover-effect',
			array(
				'label'   => __( 'Hover Effect', 'opulentia' ),
				'section' => 'opulentia_portfolio',
				'type'    => 'select',
				'choices' => array(
					'none'    => __( 'None', 'opulentia' ),
					'zoom'    => __( 'Zoom', 'opulentia' ),
					'overlay' => __( 'Overlay', 'opulentia' ),
					'slide'   => __( 'Slide', 'opulentia' ),
				),
			)
		);
	}

	public function shortcode_grid( $atts ) {
		$atts = shortcode_atts(
			array(
				'columns'  => Opulentia_get_option( 'portfolio-grid-columns', 3 ),
				'count'    => -1,
				'category' => '',
				'orderby'  => 'date',
				'order'    => 'DESC',
			),
			$atts
		);

		$args = array(
			'post_type'      => 'portfolio',
			'posts_per_page' => intval( $atts['count'] ),
			'orderby'        => sanitize_text_field( $atts['orderby'] ),
			'order'          => sanitize_text_field( $atts['order'] ),
		);

		if ( ! empty( $atts['category'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'portfolio_category',
					'field'    => 'slug',
					'terms'    => sanitize_text_field( $atts['category'] ),
				),
			);
		}

		$query = new WP_Query( $args );
		if ( ! $query->have_posts() ) {
			return '<p>' . __( 'No portfolio items found.', 'opulentia' ) . '</p>';
		}

		$gap    = Opulentia_get_option( 'portfolio-grid-gap', 20 );
		$effect = Opulentia_get_option( 'portfolio-hover-effect', 'zoom' );
		$cols   = intval( $atts['columns'] );

		$output = '<div class="op-portfolio-grid" style="--op-col:' . $cols . ';--op-gap:' . $gap . 'px">';

		while ( $query->have_posts() ) {
			$query->the_post();
			$terms = wp_get_post_terms( get_the_ID(), 'portfolio_category', array( 'fields' => 'names' ) );
			$cat   = ! empty( $terms ) ? esc_html( implode( ', ', $terms ) ) : '';

			$output .= '<article class="op-portfolio-item op-portfolio-hover--' . esc_attr( $effect ) . '">';
			if ( has_post_thumbnail() ) {
				$output .= '<div class="op-portfolio-thumb">';
				$output .= '<a href="' . get_permalink() . '">' . get_the_post_thumbnail( get_the_ID(), 'medium' ) . '</a>';
				$output .= '</div>';
			}
			$output .= '<div class="op-portfolio-info">';
			$output .= '<h3 class="op-portfolio-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
			if ( $cat ) {
				$output .= '<span class="op-portfolio-category">' . $cat . '</span>';
			}
			$output .= '</div>';
			$output .= '</article>';
		}

		wp_reset_postdata();
		$output .= '</div>';

		return $output;
	}

	public function shortcode_single( $atts ) {
		$atts = shortcode_atts( array( 'id' => 0 ), $atts );
		$id   = intval( $atts['id'] );
		if ( ! $id ) {
			$id = get_the_ID();
		}
		if ( ! $id || 'portfolio' !== get_post_type( $id ) ) {
			return '';
		}

		$post = get_post( $id );
		setup_postdata( $post );

		$output  = '<div class="op-portfolio-single">';
		$output .= '<h1 class="op-portfolio-single-title">' . get_the_title( $id ) . '</h1>';
		if ( has_post_thumbnail( $id ) ) {
			$output .= '<div class="op-portfolio-single-thumb">' . get_the_post_thumbnail( $id, 'large' ) . '</div>';
		}
		$output .= '<div class="op-portfolio-single-content">' . apply_filters( 'the_content', get_post_field( 'post_content', $id ) ) . '</div>';
		$output .= '</div>';

		wp_reset_postdata();

		return $output;
	}

	public function load_single_template( $template ) {
		global $post;
		if ( 'portfolio' === $post->post_type ) {
			$found = locate_template( 'single-portfolio.php' );
			if ( ! $found ) {
				$module_dir = __DIR__ . '/templates/single-portfolio.php';
				if ( file_exists( $module_dir ) ) {
					return $module_dir;
				}
			}
		}
		return $template;
	}

	public function inline_css() {
		if ( ! is_singular( 'portfolio' ) && ! is_post_type_archive( 'portfolio' ) && ! is_tax( 'portfolio_category' ) ) {
			global $post;
			if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'op_portfolio_grid' ) ) {
				return;
			}
		}

		$effect = Opulentia_get_option( 'portfolio-hover-effect', 'zoom' );

		$css = '
        .op-portfolio-grid {
            display: grid;
            grid-template-columns: repeat(var(--op-col, 3), 1fr);
            gap: var(--op-gap, 20px);
        }
        .op-portfolio-item {
            position: relative;
            overflow: hidden;
            background: var(--color-secondary-dark);
            border-radius: 8px;
        }
        .op-portfolio-thumb {
            overflow: hidden;
        }
        .op-portfolio-thumb img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.4s ease;
        }
        .op-portfolio-info {
            padding: 16px;
        }
        .op-portfolio-title {
            margin: 0 0 4px;
            font-family: var(--font-heading);
            font-size: 1.1rem;
        }
        .op-portfolio-title a {
            color: var(--color-text);
            text-decoration: none;
        }
        .op-portfolio-title a:hover {
            color: var(--color-accent);
        }
        .op-portfolio-category {
            font-size: 0.85rem;
            color: var(--color-text-muted);
        }
        .op-portfolio-hover--zoom .op-portfolio-thumb img:hover {
            transform: scale(1.1);
        }
        .op-portfolio-hover--overlay .op-portfolio-thumb {
            position: relative;
        }
        .op-portfolio-hover--overlay .op-portfolio-thumb::after {
            content: "";
            position: absolute;
            inset: 0;
            background: var(--color-accent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .op-portfolio-hover--overlay .op-portfolio-thumb:hover::after {
            opacity: 0.3;
        }
        .op-portfolio-hover--slide .op-portfolio-info {
            transform: translateY(100%);
            transition: transform 0.3s ease;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, var(--color-secondary-dark));
        }
        .op-portfolio-hover--slide .op-portfolio-item:hover .op-portfolio-info {
            transform: translateY(0);
        }
        .op-portfolio-single-thumb {
            margin-bottom: 24px;
        }
        .op-portfolio-single-thumb img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .op-portfolio-single-title {
            font-family: var(--font-heading);
            margin-bottom: 16px;
        }
        @media (max-width: 768px) {
            .op-portfolio-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 576px) {
            .op-portfolio-grid {
                grid-template-columns: repeat(1, 1fr);
            }
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}
}

add_action( 'init', array( 'Opulentia_Portfolio_Cpt', 'get_instance' ) );
