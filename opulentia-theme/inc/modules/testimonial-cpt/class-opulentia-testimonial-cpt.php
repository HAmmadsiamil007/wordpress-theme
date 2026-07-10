<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Testimonial_Cpt {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_meta' ) );
		add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
		add_shortcode( 'op_testimonials', array( $this, 'shortcode_testimonials' ) );
	}

	public function register_post_type() {
		$labels = array(
			'name'               => __( 'Testimonials', 'opulentia' ),
			'singular_name'      => __( 'Testimonial', 'opulentia' ),
			'add_new'            => __( 'Add New', 'opulentia' ),
			'add_new_item'       => __( 'Add New Testimonial', 'opulentia' ),
			'edit_item'          => __( 'Edit Testimonial', 'opulentia' ),
			'new_item'           => __( 'New Testimonial', 'opulentia' ),
			'view_item'          => __( 'View Testimonial', 'opulentia' ),
			'search_items'       => __( 'Search Testimonials', 'opulentia' ),
			'not_found'          => __( 'No testimonials found', 'opulentia' ),
			'not_found_in_trash' => __( 'No testimonials found in trash', 'opulentia' ),
			'all_items'          => __( 'All Testimonials', 'opulentia' ),
			'menu_name'          => __( 'Testimonials', 'opulentia' ),
		);

		register_post_type(
			'testimonial',
			array(
				'labels'       => $labels,
				'public'       => true,
				'has_archive'  => true,
				'rewrite'      => array( 'slug' => 'testimonial' ),
				'supports'     => array( 'title', 'editor' ),
				'menu_icon'    => 'dashicons-format-quote',
				'show_in_rest' => true,
			)
		);
	}

	public function register_meta() {
		register_post_meta(
			'testimonial',
			'_testimonial_rating',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'integer',
				'default'      => 5,
			)
		);
		register_post_meta(
			'testimonial',
			'_testimonial_company',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
				'default'      => '',
			)
		);
		register_post_meta(
			'testimonial',
			'_testimonial_position',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
				'default'      => '',
			)
		);
	}

	public function register_customizer( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_testimonials',
			array(
				'title'    => __( 'Testimonials', 'opulentia' ),
				'panel'    => 'Opulentia_global_settings',
				'priority' => 130,
			)
		);

		$wp_customize->add_setting(
			'testimonial-style',
			array(
				'default'           => 'grid',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'testimonial-style',
			array(
				'label'   => __( 'Display Style', 'opulentia' ),
				'section' => 'opulentia_testimonials',
				'type'    => 'select',
				'choices' => array(
					'grid'   => __( 'Grid', 'opulentia' ),
					'slider' => __( 'Slider', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'testimonial-columns',
			array(
				'default'           => 2,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'testimonial-columns',
			array(
				'label'   => __( 'Columns', 'opulentia' ),
				'section' => 'opulentia_testimonials',
				'type'    => 'select',
				'choices' => array(
					1 => __( '1 Column', 'opulentia' ),
					2 => __( '2 Columns', 'opulentia' ),
					3 => __( '3 Columns', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'testimonial-autoplay',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'testimonial-autoplay',
			array(
				'label'   => __( 'Autoplay Slider', 'opulentia' ),
				'section' => 'opulentia_testimonials',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'testimonial-autoplay-speed',
			array(
				'default'           => 5000,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'testimonial-autoplay-speed',
			array(
				'label'       => __( 'Autoplay Speed (ms)', 'opulentia' ),
				'section'     => 'opulentia_testimonials',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 1000,
					'max'  => 30000,
					'step' => 500,
				),
			)
		);

		$wp_customize->add_setting(
			'testimonial-show-rating',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'testimonial-show-rating',
			array(
				'label'   => __( 'Show Rating Stars', 'opulentia' ),
				'section' => 'opulentia_testimonials',
				'type'    => 'checkbox',
			)
		);
	}

	public function shortcode_testimonials( $atts ) {
		$atts = shortcode_atts(
			array(
				'count'   => 5,
				'columns' => Opulentia_get_option( 'testimonial-columns', 2 ),
				'orderby' => 'rand',
				'style'   => Opulentia_get_option( 'testimonial-style', 'grid' ),
			),
			$atts
		);

		$args = array(
			'post_type'      => 'testimonial',
			'posts_per_page' => intval( $atts['count'] ),
			'orderby'        => sanitize_text_field( $atts['orderby'] ),
		);

		$query = new WP_Query( $args );
		if ( ! $query->have_posts() ) {
			return '<p>' . __( 'No testimonials found.', 'opulentia' ) . '</p>';
		}

		$style       = sanitize_text_field( $atts['style'] );
		$cols        = intval( $atts['columns'] );
		$show_rating = Opulentia_get_option( 'testimonial-show-rating', true );
		$autoplay    = Opulentia_get_option( 'testimonial-autoplay', true );
		$speed       = Opulentia_get_option( 'testimonial-autoplay-speed', 5000 );

		$classes  = 'op-testimonial-wrapper op-testimonial--' . esc_attr( $style );
		$classes .= ' op-testimonial-cols-' . $cols;

		$scroller_id = 'op-test-scroll-' . uniqid();
		$output      = '<div class="' . $classes . '">';

		if ( 'slider' === $style ) {
			$output .= '<div class="op-testimonial-scroll" id="' . esc_attr( $scroller_id ) . '">';
		} else {
			$output .= '<div class="op-testimonial-grid">';
		}

		while ( $query->have_posts() ) {
			$query->the_post();
			$id       = get_the_ID();
			$rating   = intval( get_post_meta( $id, '_testimonial_rating', true ) );
			$company  = get_post_meta( $id, '_testimonial_company', true );
			$position = get_post_meta( $id, '_testimonial_position', true );

			$output .= '<div class="op-testimonial-card">';
			$output .= '<div class="op-testimonial-text">' . apply_filters( 'the_content', get_the_content() ) . '</div>';
			$output .= '<div class="op-testimonial-author">';
			$output .= '<strong class="op-testimonial-name">' . get_the_title() . '</strong>';
			if ( $position ) {
				$output .= '<span class="op-testimonial-position">' . esc_html( $position ) . '</span>';
			}
			if ( $company ) {
				$output .= '<span class="op-testimonial-company">' . esc_html( $company ) . '</span>';
			}
			$output .= '</div>';
			if ( $show_rating && $rating && $rating >= 1 && $rating <= 5 ) {
				$output .= '<div class="op-testimonial-rating">';
				for ( $i = 1; $i <= 5; $i++ ) {
					$output .= '<span class="op-star' . ( $i <= $rating ? ' op-star--filled' : '' ) . '">&#9733;</span>';
				}
				$output .= '</div>';
			}
			$output .= '</div>';
		}

		$output .= '</div>';

		if ( 'slider' === $style ) {
			$output .= '<div class="op-testimonial-nav">';
			$output .= '<button class="op-test-nav-btn op-test-prev" data-scroll="' . esc_attr( $scroller_id ) . '" aria-label="' . esc_attr__( 'Previous', 'opulentia' ) . '">&#10094;</button>';
			$output .= '<button class="op-test-nav-btn op-test-next" data-scroll="' . esc_attr( $scroller_id ) . '" aria-label="' . esc_attr__( 'Next', 'opulentia' ) . '">&#10095;</button>';
			$output .= '</div>';

			$output .= '<script>
            (function(){
                var scroller = document.getElementById("' . esc_js( $scroller_id ) . '");
                if (!scroller) return;
                var prev = scroller.parentElement.querySelector(".op-test-prev");
                var next = scroller.parentElement.querySelector(".op-test-next");
                if (prev) prev.addEventListener("click", function(){ scroller.scrollBy({ left: -scroller.clientWidth, behavior: "smooth" }); });
                if (next) next.addEventListener("click", function(){ scroller.scrollBy({ left: scroller.clientWidth, behavior: "smooth" }); });
                ' . ( $autoplay ? '
                var interval = setInterval(function(){
                    if (scroller.classList.contains("op-test-autoplay-paused")) return;
                    var maxScroll = scroller.scrollWidth - scroller.clientWidth;
                    if (scroller.scrollLeft >= maxScroll) {
                        scroller.scrollTo({ left: 0, behavior: "smooth" });
                    } else {
                        scroller.scrollBy({ left: scroller.clientWidth, behavior: "smooth" });
                    }
                }, ' . intval( $speed ) . ');
                scroller.addEventListener("mouseenter", function(){ scroller.classList.add("op-test-autoplay-paused"); });
                scroller.addEventListener("mouseleave", function(){ scroller.classList.remove("op-test-autoplay-paused"); });
                ' : '' ) . '
            })();
            </script>';
		}

		wp_reset_postdata();
		$output .= '</div>';

		return $output;
	}

	public function inline_css() {
		global $post;
		if ( ! is_singular( 'testimonial' ) && ! is_post_type_archive( 'testimonial' ) ) {
			if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'op_testimonials' ) ) {
				return;
			}
		}

		$css = '
        .op-testimonial--grid .op-testimonial-grid {
            display: grid;
            grid-template-columns: repeat(var(--op-test-cols, 2), 1fr);
            gap: 24px;
        }
        .op-testimonial-cols-1 .op-testimonial-grid,
        .op-testimonial-cols-1 .op-testimonial-scroll {
            --op-test-cols: 1;
        }
        .op-testimonial-cols-2 .op-testimonial-grid,
        .op-testimonial-cols-2 .op-testimonial-scroll {
            --op-test-cols: 2;
        }
        .op-testimonial-cols-3 .op-testimonial-grid,
        .op-testimonial-cols-3 .op-testimonial-scroll {
            --op-test-cols: 3;
        }
        .op-testimonial-card {
            background: var(--color-secondary-dark);
            border: 1px solid var(--color-border);
            border-radius: 8px;
            padding: 24px;
            position: relative;
        }
        .op-testimonial-card::before {
            content: "\\201C";
            font-size: 3rem;
            color: var(--color-accent);
            position: absolute;
            top: 8px;
            left: 16px;
            line-height: 1;
            font-family: serif;
            opacity: 0.3;
        }
        .op-testimonial-text {
            font-style: italic;
            color: var(--color-text);
            margin-bottom: 16px;
            padding-top: 16px;
            line-height: 1.6;
        }
        .op-testimonial-author {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .op-testimonial-name {
            font-family: var(--font-heading);
            color: var(--color-gold);
            font-size: 1rem;
        }
        .op-testimonial-position,
        .op-testimonial-company {
            font-size: 0.85rem;
            color: var(--color-text-muted);
        }
        .op-testimonial-rating {
            margin-top: 12px;
            font-size: 1.1rem;
            color: var(--color-border);
        }
        .op-star--filled {
            color: var(--color-gold);
        }
        .op-testimonial--slider .op-testimonial-scroll {
            display: flex;
            gap: 24px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            padding-bottom: 8px;
        }
        .op-testimonial--slider .op-testimonial-scroll::-webkit-scrollbar {
            display: none;
        }
        .op-testimonial--slider .op-testimonial-card {
            flex: 0 0 calc((100% / var(--op-test-cols, 2)) - 12px);
            scroll-snap-align: start;
            min-width: 280px;
        }
        .op-testimonial-nav {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
        }
        .op-test-nav-btn {
            background: var(--color-secondary-dark);
            border: 1px solid var(--color-border);
            color: var(--color-text);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.2s ease, color 0.2s ease;
        }
        .op-test-nav-btn:hover {
            background: var(--color-accent);
            color: #fff;
        }
        @media (max-width: 768px) {
            .op-testimonial--slider .op-testimonial-card {
                flex: 0 0 80%;
            }
            .op-testimonial-grid {
                grid-template-columns: repeat(1, 1fr);
            }
        }
        @media (max-width: 576px) {
            .op-testimonial--slider .op-testimonial-card {
                flex: 0 0 100%;
            }
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}
}

add_action( 'init', array( 'Opulentia_Testimonial_Cpt', 'get_instance' ) );
