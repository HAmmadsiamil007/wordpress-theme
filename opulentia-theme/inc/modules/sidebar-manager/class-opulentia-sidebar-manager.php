<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Sidebar_Manager {

	private static $instance = null;
	private $custom_sidebars = array();

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'init' ), 20 );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
		add_filter( 'Opulentia_sidebar_id', array( $this, 'override_sidebar' ), 20 );
	}

	public function init() {
		$this->custom_sidebars = Opulentia_get_option( 'custom-sidebars', array() );
		add_action( 'widgets_init', array( $this, 'register_custom_sidebars' ), 20 );
	}

	public function admin_init() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
	}

	public function register_customizer( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_sidebars',
			array(
				'title'    => __( 'Sidebar Manager', 'opulentia' ),
				'panel'    => 'Opulentia_global_settings',
				'priority' => 110,
			)
		);

		$wp_customize->add_setting(
			'custom-sidebars',
			array(
				'default'           => array(),
				'sanitize_callback' => array( $this, 'sanitize_sidebars' ),
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'custom-sidebars',
				array(
					'label'       => __( 'Custom Sidebars', 'opulentia' ),
					'description' => __( 'Enter sidebar names, one per line. Each creates a new widget area.', 'opulentia' ),
					'section'     => 'opulentia_sidebars',
					'type'        => 'textarea',
					'input_attrs' => array( 'placeholder' => "Sidebar Name 1\nSidebar Name 2" ),
				)
			)
		);

		$wp_customize->add_setting(
			'default-sidebar-post',
			array(
				'default'           => 'sidebar-1',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'default-sidebar-post',
			array(
				'label'   => __( 'Default Post Sidebar', 'opulentia' ),
				'section' => 'opulentia_sidebars',
				'type'    => 'select',
				'choices' => $this->get_sidebar_choices(),
			)
		);

		$wp_customize->add_setting(
			'default-sidebar-page',
			array(
				'default'           => 'sidebar-1',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'default-sidebar-page',
			array(
				'label'   => __( 'Default Page Sidebar', 'opulentia' ),
				'section' => 'opulentia_sidebars',
				'type'    => 'select',
				'choices' => $this->get_sidebar_choices(),
			)
		);

		if ( class_exists( 'WooCommerce' ) ) {
			$wp_customize->add_setting(
				'default-sidebar-product',
				array(
					'default'           => 'sidebar-1',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				)
			);
			$wp_customize->add_control(
				'default-sidebar-product',
				array(
					'label'   => __( 'Default Product Sidebar', 'opulentia' ),
					'section' => 'opulentia_sidebars',
					'type'    => 'select',
					'choices' => $this->get_sidebar_choices(),
				)
			);
		}
	}

	private function get_sidebar_choices() {
		$choices  = array( 'sidebar-1' => __( 'Default Sidebar', 'opulentia' ) );
		$sidebars = Opulentia_get_option( 'custom-sidebars', array() );
		if ( is_array( $sidebars ) ) {
			foreach ( $sidebars as $sidebar ) {
				$slug             = sanitize_title( $sidebar );
				$choices[ $slug ] = $sidebar;
			}
		}
		return $choices;
	}

	public function sanitize_sidebars( $value ) {
		if ( is_string( $value ) ) {
			$lines = explode( "\n", $value );
			$lines = array_map( 'trim', $lines );
			$lines = array_filter( $lines );
			return array_values( $lines );
		}
		return array();
	}

	public function register_custom_sidebars() {
		if ( empty( $this->custom_sidebars ) || ! is_array( $this->custom_sidebars ) ) {
			return;
		}
		foreach ( $this->custom_sidebars as $name ) {
			$slug = sanitize_title( $name );
			register_sidebar(
				array(
					'name'          => $name,
					'id'            => $slug,
					'description'   => sprintf( __( 'Custom widget area: %s', 'opulentia' ), $name ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h3 class="widget__title">',
					'after_title'   => '</h3>',
				)
			);
		}
	}

	public function add_meta_box() {
		$post_types = array( 'post', 'page' );
		if ( class_exists( 'WooCommerce' ) ) {
			$post_types[] = 'product';
		}
		foreach ( $post_types as $type ) {
			add_meta_box(
				'opulentia_sidebar_select',
				__( 'Sidebar', 'opulentia' ),
				array( $this, 'render_meta_box' ),
				$type,
				'side',
				'default'
			);
		}
	}

	public function render_meta_box( $post ) {
		wp_nonce_field( 'opulentia_sidebar_nonce', 'opulentia_sidebar_nonce' );
		$selected = get_post_meta( $post->ID, '_opulentia_sidebar', true );
		$choices  = $this->get_sidebar_choices();
		?>
		<select name="opulentia_sidebar" style="width:100%;">
			<option value=""><?php esc_html_e( '— Default —', 'opulentia' ); ?></option>
			<?php foreach ( $choices as $slug => $label ) : ?>
				<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $selected, $slug ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	public function save_meta_box( $post_id ) {
		if ( ! isset( $_POST['opulentia_sidebar_nonce'] ) || ! wp_verify_nonce( $_POST['opulentia_sidebar_nonce'], 'opulentia_sidebar_nonce' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( isset( $_POST['opulentia_sidebar'] ) ) {
			$sidebar = sanitize_text_field( $_POST['opulentia_sidebar'] );
			update_post_meta( $post_id, '_opulentia_sidebar', $sidebar );
		}
	}

	public function override_sidebar( $sidebar_id ) {
		if ( is_singular() ) {
			$post_id    = get_the_ID();
			$meta_value = get_post_meta( $post_id, '_opulentia_sidebar', true );
			if ( ! empty( $meta_value ) ) {
				return $meta_value;
			}
		}

		if ( is_singular( 'post' ) ) {
			$default = Opulentia_get_option( 'default-sidebar-post', 'sidebar-1' );
			return ! empty( $default ) ? $default : $sidebar_id;
		}
		if ( is_singular( 'page' ) ) {
			$default = Opulentia_get_option( 'default-sidebar-page', 'sidebar-1' );
			return ! empty( $default ) ? $default : $sidebar_id;
		}
		if ( is_singular( 'product' ) && class_exists( 'WooCommerce' ) ) {
			$default = Opulentia_get_option( 'default-sidebar-product', 'sidebar-1' );
			return ! empty( $default ) ? $default : $sidebar_id;
		}

		return $sidebar_id;
	}
}
