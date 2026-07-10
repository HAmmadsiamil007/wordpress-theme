<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Custom_404 {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'Opulentia_404_template', array( $this, 'override_404_template' ) );
	}

	public function register_customizer( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_custom_404',
			array(
				'title'    => __( 'Custom 404 Page', 'opulentia' ),
				'panel'    => 'Opulentia_global_settings',
				'priority' => 100,
			)
		);

		$wp_customize->add_setting(
			'custom-404-enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'custom-404-enable',
			array(
				'label'   => __( 'Enable Custom 404 Page', 'opulentia' ),
				'section' => 'opulentia_custom_404',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'custom-404-layout',
			array(
				'default'           => 'centered',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'custom-404-layout',
			array(
				'label'   => __( 'Layout Style', 'opulentia' ),
				'section' => 'opulentia_custom_404',
				'type'    => 'select',
				'choices' => array(
					'centered'    => __( 'Centered', 'opulentia' ),
					'split'       => __( 'Split (Text + Image)', 'opulentia' ),
					'minimal'     => __( 'Minimal', 'opulentia' ),
					'illustrated' => __( 'Illustrated', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'custom-404-title',
			array(
				'default'           => __( '404', 'opulentia' ),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'custom-404-title',
			array(
				'label'   => __( 'Title', 'opulentia' ),
				'section' => 'opulentia_custom_404',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'custom-404-subtitle',
			array(
				'default'           => __( 'Page Not Found', 'opulentia' ),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'custom-404-subtitle',
			array(
				'label'   => __( 'Subtitle', 'opulentia' ),
				'section' => 'opulentia_custom_404',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'custom-404-message',
			array(
				'default'           => __( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'opulentia' ),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'custom-404-message',
			array(
				'label'   => __( 'Description Text', 'opulentia' ),
				'section' => 'opulentia_custom_404',
				'type'    => 'textarea',
			)
		);

		$wp_customize->add_setting(
			'custom-404-image',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'custom-404-image',
				array(
					'label'   => __( '404 Illustration / Image', 'opulentia' ),
					'section' => 'opulentia_custom_404',
				)
			)
		);

		$wp_customize->add_setting(
			'custom-404-icon',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'custom-404-icon',
			array(
				'label'       => __( 'Icon Shortcode', 'opulentia' ),
				'description' => __( 'e.g. [op_icon id="123"]. Shows above title in centered/minimal layouts.', 'opulentia' ),
				'section'     => 'opulentia_custom_404',
				'type'        => 'text',
			)
		);

		$wp_customize->add_setting(
			'custom-404-bg-color',
			array(
				'default'           => 'var(--color-primary-dark)',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'custom-404-bg-color',
			array(
				'label'       => __( 'Background Color', 'opulentia' ),
				'section'     => 'opulentia_custom_404',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => 'var(--color-primary-dark)' ),
			)
		);

		$wp_customize->add_setting(
			'custom-404-bg-image',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'custom-404-bg-image',
				array(
					'label'   => __( 'Background Image', 'opulentia' ),
					'section' => 'opulentia_custom_404',
				)
			)
		);

		$wp_customize->add_setting(
			'custom-404-title-color',
			array(
				'default'           => 'var(--color-gold)',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'custom-404-title-color',
			array(
				'label'       => __( 'Title Color', 'opulentia' ),
				'section'     => 'opulentia_custom_404',
				'type'        => 'text',
				'input_attrs' => array( 'placeholder' => 'var(--color-gold)' ),
			)
		);

		$wp_customize->add_setting(
			'custom-404-show-search',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'custom-404-show-search',
			array(
				'label'   => __( 'Show Search Form', 'opulentia' ),
				'section' => 'opulentia_custom_404',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'custom-404-show-recent-posts',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'custom-404-show-recent-posts',
			array(
				'label'   => __( 'Show Recent Posts', 'opulentia' ),
				'section' => 'opulentia_custom_404',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'custom-404-show-popular-pages',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'custom-404-show-popular-pages',
			array(
				'label'   => __( 'Show Popular Page Links', 'opulentia' ),
				'section' => 'opulentia_custom_404',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'custom-404-cta-text',
			array(
				'default'           => __( 'Back to Homepage', 'opulentia' ),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'custom-404-cta-text',
			array(
				'label'   => __( 'CTA Button Text', 'opulentia' ),
				'section' => 'opulentia_custom_404',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'custom-404-cta-url',
			array(
				'default'           => home_url( '/' ),
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'custom-404-cta-url',
			array(
				'label'   => __( 'CTA Button URL', 'opulentia' ),
				'section' => 'opulentia_custom_404',
				'type'    => 'url',
			)
		);
	}

	public function body_class( $classes ) {
		if ( is_404() && Opulentia_get_option( 'custom-404-enable', true ) ) {
			$layout    = Opulentia_get_option( 'custom-404-layout', 'centered' );
			$classes[] = 'custom-404-active';
			$classes[] = 'custom-404-' . $layout;
		}
		return $classes;
	}

	public function override_404_template() {
		if ( ! Opulentia_get_option( 'custom-404-enable', true ) ) {
			return;
		}
		add_action( 'Opulentia_404_content', array( $this, 'render_custom_404' ) );
	}

	public function render_custom_404() {
		$layout      = Opulentia_get_option( 'custom-404-layout', 'centered' );
		$title       = Opulentia_get_option( 'custom-404-title', __( '404', 'opulentia' ) );
		$subtitle    = Opulentia_get_option( 'custom-404-subtitle', __( 'Page Not Found', 'opulentia' ) );
		$message     = Opulentia_get_option( 'custom-404-message', __( 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'opulentia' ) );
		$image       = Opulentia_get_option( 'custom-404-image', '' );
		$icon        = Opulentia_get_option( 'custom-404-icon', '' );
		$show_search = Opulentia_get_option( 'custom-404-show-search', true );
		$show_recent = Opulentia_get_option( 'custom-404-show-recent-posts', true );
		$show_pages  = Opulentia_get_option( 'custom-404-show-popular-pages', true );
		$cta_text    = Opulentia_get_option( 'custom-404-cta-text', __( 'Back to Homepage', 'opulentia' ) );
		$cta_url     = Opulentia_get_option( 'custom-404-cta-url', home_url( '/' ) );
		?>
		<section class="custom-404 custom-404--<?php echo esc_attr( $layout ); ?>">
			<div class="container">
				<?php if ( 'split' === $layout && ! empty( $image ) ) : ?>
				<div class="custom-404__grid">
					<div class="custom-404__content-col">
				<?php endif; ?>

				<div class="custom-404__content">
					<?php if ( ! empty( $icon ) ) : ?>
						<div class="custom-404__icon"><?php echo do_shortcode( $icon ); ?></div>
					<?php endif; ?>

					<?php if ( 'illustrated' === $layout && ! empty( $image ) ) : ?>
						<div class="custom-404__illustration">
							<img src="<?php echo esc_url( $image ); ?>" alt="" class="custom-404__image">
						</div>
					<?php endif; ?>

					<h1 class="custom-404__title"><?php echo esc_html( $title ); ?></h1>
					<h2 class="custom-404__subtitle"><?php echo esc_html( $subtitle ); ?></h2>
					<p class="custom-404__message"><?php echo esc_html( $message ); ?></p>

					<a href="<?php echo esc_url( $cta_url ); ?>" class="custom-404__cta btn btn--primary">
						<?php echo esc_html( $cta_text ); ?>
					</a>

					<?php if ( $show_search ) : ?>
						<div class="custom-404__search">
							<?php get_search_form(); ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( 'split' === $layout && ! empty( $image ) ) : ?>
					</div>
					<div class="custom-404__image-col">
						<img src="<?php echo esc_url( $image ); ?>" alt="" class="custom-404__split-image">
					</div>
				</div>
				<?php endif; ?>

				<?php if ( $show_recent || $show_pages ) : ?>
				<div class="custom-404__extras">
					<div class="custom-404__extra-grid">
						<?php if ( $show_recent ) : ?>
						<div class="custom-404__recent-posts">
							<h3 class="custom-404__extra-title"><?php esc_html_e( 'Recent Posts', 'opulentia' ); ?></h3>
							<ul>
								<?php
								$recent = wp_get_recent_posts(
									array(
										'numberposts' => 5,
										'post_status' => 'publish',
									)
								);
								foreach ( $recent as $post ) {
									echo '<li><a href="' . esc_url( get_permalink( $post['ID'] ) ) . '">' . esc_html( $post['post_title'] ) . '</a></li>';
								}
								?>
							</ul>
						</div>
						<?php endif; ?>

						<?php if ( $show_pages ) : ?>
						<div class="custom-404__popular-pages">
							<h3 class="custom-404__extra-title"><?php esc_html_e( 'Popular Pages', 'opulentia' ); ?></h3>
							<ul>
								<?php
								$pages = get_pages(
									array(
										'sort_column' => 'menu_order',
										'number'      => 6,
									)
								);
								foreach ( $pages as $page ) {
									echo '<li><a href="' . esc_url( get_permalink( $page->ID ) ) . '">' . esc_html( $page->post_title ) . '</a></li>';
								}
								?>
							</ul>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	public function inline_css() {
		if ( ! is_404() || ! Opulentia_get_option( 'custom-404-enable', true ) ) {
			return;
		}

		$bg_color  = Opulentia_get_option( 'custom-404-bg-color', 'var(--color-primary-dark)' );
		$bg_image  = Opulentia_get_option( 'custom-404-bg-image', '' );
		$title_clr = Opulentia_get_option( 'custom-404-title-color', 'var(--color-gold)' );

		$bg_css = 'background-color: ' . $bg_color . ';';
		if ( ! empty( $bg_image ) ) {
			$bg_css .= ' background-image: url(' . $bg_image . '); background-size: cover; background-position: center;';
		}

		$css = '
        .custom-404 {
            background: ' . $bg_color . ';
            padding: 80px 0;
            min-height: 60vh;
            display: flex;
            align-items: center;
        }
        .custom-404__content {
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }
        .custom-404__icon {
            margin-bottom: 20px;
        }
        .custom-404__icon svg {
            width: 48px;
            height: 48px;
            color: ' . $title_clr . ';
        }
        .custom-404__illustration {
            margin-bottom: 30px;
        }
        .custom-404__image {
            max-width: 100%;
            height: auto;
        }
        .custom-404__title {
            font-family: var(--font-heading);
            font-size: 6rem;
            color: ' . $title_clr . ';
            line-height: 1;
            margin: 0 0 8px;
        }
        .custom-404__subtitle {
            font-family: var(--font-heading);
            font-size: 1.8rem;
            color: var(--color-text);
            margin: 0 0 16px;
        }
        .custom-404__message {
            font-size: 1rem;
            color: var(--color-text-muted);
            margin-bottom: 32px;
            line-height: 1.6;
        }
        .custom-404__cta {
            display: inline-block;
            margin-bottom: 32px;
        }
        .custom-404__search {
            max-width: 400px;
            margin: 0 auto;
        }
        .custom-404__search .search-form {
            display: flex;
            gap: 8px;
        }
        .custom-404__search .search-form input[type="search"] {
            flex: 1;
            padding: 12px 16px;
            background: var(--color-secondary-dark);
            border: 1px solid var(--color-border);
            color: var(--color-text);
            border-radius: 6px;
            font-size: 0.95rem;
        }
        .custom-404__search .search-form input[type="search"]:focus {
            border-color: ' . $title_clr . ';
            outline: none;
        }
        .custom-404__search .search-form .search-submit {
            padding: 12px 20px;
            background: ' . $title_clr . ';
            color: #000;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        .custom-404__extras {
            margin-top: 60px;
            padding-top: 40px;
            border-top: 1px solid var(--color-border);
        }
        .custom-404__extra-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            max-width: 700px;
            margin: 0 auto;
        }
        .custom-404__extra-title {
            font-family: var(--font-heading);
            font-size: 1.1rem;
            color: var(--color-text);
            margin-bottom: 16px;
        }
        .custom-404__extra-grid ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .custom-404__extra-grid li {
            margin-bottom: 8px;
        }
        .custom-404__extra-grid a {
            color: var(--color-text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        .custom-404__extra-grid a:hover {
            color: ' . $title_clr . ';
        }
        .custom-404--split .custom-404__grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }
        .custom-404--split .custom-404__content {
            text-align: left;
            max-width: 100%;
            margin: 0;
        }
        .custom-404--split .custom-404__image-col img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .custom-404--minimal .custom-404__content {
            max-width: 450px;
        }
        .custom-404--minimal .custom-404__title {
            font-size: 4rem;
        }
        .custom-404--minimal .custom-404__subtitle {
            font-size: 1.4rem;
        }
        .custom-404--illustrated .custom-404__illustration {
            text-align: center;
        }
        .custom-404--illustrated .custom-404__image {
            max-height: 200px;
        }
        @media (max-width: 768px) {
            .custom-404__extra-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .custom-404--split .custom-404__grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            .custom-404--split .custom-404__content {
                text-align: center;
            }
            .custom-404__title {
                font-size: 4rem;
            }
            .custom-404__subtitle {
                font-size: 1.3rem;
            }
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}
}
