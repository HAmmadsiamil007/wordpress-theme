<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_WooCommerce_Recently_Viewed {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
		add_action( 'wp_enqueue_scripts', array( $this, 'track_product' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
		add_shortcode( 'op_recently_viewed', array( $this, 'shortcode' ) );
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'single_product_display' ), 30 );
	}

	public function register_customizer( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_wc_recently_viewed',
			array(
				'title'    => __( 'Recently Viewed Products', 'opulentia' ),
				'panel'    => 'Opulentia_woocommerce',
				'priority' => 30,
			)
		);

		$wp_customize->add_setting(
			'wc-recently-viewed-enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'wc-recently-viewed-enable',
			array(
				'label'   => __( 'Enable Tracking', 'opulentia' ),
				'section' => 'opulentia_wc_recently_viewed',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'wc-recently-viewed-count',
			array(
				'default'           => 6,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'wc-recently-viewed-count',
			array(
				'label'       => __( 'Number of Products', 'opulentia' ),
				'section'     => 'opulentia_wc_recently_viewed',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 2,
					'max'  => 24,
					'step' => 1,
				),
			)
		);

		$wp_customize->add_setting(
			'wc-recently-viewed-columns',
			array(
				'default'           => 4,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'wc-recently-viewed-columns',
			array(
				'label'   => __( 'Columns', 'opulentia' ),
				'section' => 'opulentia_wc_recently_viewed',
				'type'    => 'select',
				'choices' => array(
					2 => 2,
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
				),
			)
		);

		$wp_customize->add_setting(
			'wc-recently-viewed-title',
			array(
				'default'           => __( 'Recently Viewed', 'opulentia' ),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'wc-recently-viewed-title',
			array(
				'label'   => __( 'Section Title', 'opulentia' ),
				'section' => 'opulentia_wc_recently_viewed',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'wc-recently-viewed-single',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'wc-recently-viewed-single',
			array(
				'label'   => __( 'Show on Single Product Page', 'opulentia' ),
				'section' => 'opulentia_wc_recently_viewed',
				'type'    => 'checkbox',
			)
		);
	}

	public function track_product() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		if ( ! Opulentia_get_option( 'wc-recently-viewed-enable', true ) ) {
			return;
		}
		if ( ! is_singular( 'product' ) ) {
			return;
		}

		$product_id = get_the_ID();
		$count      = absint( Opulentia_get_option( 'wc-recently-viewed-count', 6 ) );

		wp_add_inline_script(
			'opulentia-custom',
			'
        (function() {
            var productId = ' . $product_id . ';
            var max = ' . $count . ';
            try {
                var stored = JSON.parse(localStorage.getItem("op_recently_viewed") || "[]");
                stored = stored.filter(function(id) { return id !== productId; });
                stored.unshift(productId);
                if (stored.length > max) stored = stored.slice(0, max);
                localStorage.setItem("op_recently_viewed", JSON.stringify(stored));
                document.cookie = "op_recently_viewed=" + encodeURIComponent(JSON.stringify(stored)) + "; path=/";
            } catch(e) {}
        })();
        '
		);
	}

	public function shortcode( $atts ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return '';
		}

		$atts = shortcode_atts(
			array(
				'count'   => Opulentia_get_option( 'wc-recently-viewed-count', 6 ),
				'columns' => Opulentia_get_option( 'wc-recently-viewed-columns', 4 ),
				'title'   => '',
			),
			$atts
		);

		return $this->render( absint( $atts['count'] ), absint( $atts['columns'] ), $atts['title'] );
	}

	public function render( $count = 6, $columns = 4, $title = '' ) {
		$ids = $this->get_recently_viewed_ids();
		if ( empty( $ids ) ) {
			return '';
		}

		$ids = array_slice( $ids, 0, $count );
		if ( empty( $title ) ) {
			$title = Opulentia_get_option( 'wc-recently-viewed-title', __( 'Recently Viewed', 'opulentia' ) );
		}

		ob_start();
		?>
		<div class="op-recently-viewed">
			<?php if ( $title ) : ?>
				<h3 class="op-recently-viewed__title"><?php echo esc_html( $title ); ?></h3>
			<?php endif; ?>
			<ul class="products columns-<?php echo esc_attr( $columns ); ?>">
				<?php
				$args  = array(
					'post_type'      => 'product',
					'post__in'       => $ids,
					'posts_per_page' => $count,
					'orderby'        => 'post__in',
				);
				$query = new WP_Query( $args );
				while ( $query->have_posts() ) {
					$query->the_post();
					wc_get_template_part( 'content', 'product' );
				}
				wp_reset_postdata();
				?>
			</ul>
		</div>
		<?php
		return ob_get_clean();
	}

	private function get_recently_viewed_ids() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return array();
		}
		if ( ! Opulentia_get_option( 'wc-recently-viewed-enable', true ) ) {
			return array();
		}

		if ( isset( $_COOKIE['op_recently_viewed'] ) ) {
			$ids = json_decode( stripslashes( $_COOKIE['op_recently_viewed'] ), true );
			return is_array( $ids ) ? array_map( 'absint', $ids ) : array();
		}

		return array();
	}

	public function register_widget() {
		register_widget( 'Opulentia_Recently_Viewed_Widget' );
	}

	public function single_product_display() {
		if ( ! is_singular( 'product' ) ) {
			return;
		}
		if ( ! Opulentia_get_option( 'wc-recently-viewed-single', true ) ) {
			return;
		}
		echo $this->render();
	}

	public function inline_css() {
		if ( ! class_exists( 'WooCommerce' ) || ! Opulentia_get_option( 'wc-recently-viewed-enable', true ) ) {
			return;
		}

		$css = '
        .op-recently-viewed {
            margin: 40px 0;
            padding-top: 40px;
            border-top: 1px solid var(--color-border);
        }
        .op-recently-viewed__title {
            font-family: var(--font-heading);
            font-size: 1.3rem;
            color: var(--color-gold);
            margin-bottom: 20px;
            text-align: center;
        }
        ';

		wp_add_inline_style( 'opulentia-style', $css );
	}
}

class Opulentia_Recently_Viewed_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'op_recently_viewed',
			__( 'Recently Viewed Products', 'opulentia' ),
			array(
				'description' => __( 'Display recently viewed products.', 'opulentia' ),
			)
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Recently Viewed', 'opulentia' );
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}
		echo Opulentia_WooCommerce_Recently_Viewed::get_instance()->render();
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Recently Viewed', 'opulentia' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'opulentia' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		return $instance;
	}
}
