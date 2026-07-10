<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_GDPR_Cookie_Consent {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( $this, 'render_bar' ) );
		add_filter( 'opulentia_dynamic_css', array( $this, 'dynamic_css' ) );
		add_action( 'wp_ajax_Opulentia_save_consent', array( $this, 'ajax_save_consent' ) );
		add_action( 'wp_ajax_nopriv_Opulentia_save_consent', array( $this, 'ajax_save_consent' ) );
	}

	private function is_enabled() {
		return (bool) get_theme_mod( 'op_gdpr_enable', false );
	}

	public function customize_register( $wp_customize ) {
		$wp_customize->add_panel(
			'opulentia_gdpr',
			array(
				'title'       => __( 'GDPR / Cookie Consent', 'opulentia' ),
				'description' => __( 'Configure the cookie consent bar and GDPR compliance settings.', 'opulentia' ),
				'priority'    => 140,
			)
		);

		// ── Bar Appearance ──
		$wp_customize->add_section(
			'op_gdpr_bar',
			array(
				'title' => __( 'Bar', 'opulentia' ),
				'panel' => 'opulentia_gdpr',
			)
		);

		$wp_customize->add_setting(
			'op_gdpr_enable',
			array(
				'default'           => false,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_gdpr_enable',
			array(
				'label'   => __( 'Enable Cookie Consent Bar', 'opulentia' ),
				'section' => 'op_gdpr_bar',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'op_gdpr_position',
			array(
				'default'           => 'bottom',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_gdpr_position',
			array(
				'label'   => __( 'Bar Position', 'opulentia' ),
				'section' => 'op_gdpr_bar',
				'type'    => 'select',
				'choices' => array(
					'top'    => __( 'Top', 'opulentia' ),
					'bottom' => __( 'Bottom', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'op_gdpr_message',
			array(
				'default'           => __( 'We use cookies to improve your experience. By continuing, you agree to our use of cookies.', 'opulentia' ),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_gdpr_message',
			array(
				'label'   => __( 'Message', 'opulentia' ),
				'section' => 'op_gdpr_bar',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'op_gdpr_accept_text',
			array(
				'default'           => __( 'Accept All', 'opulentia' ),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_gdpr_accept_text',
			array(
				'label'   => __( 'Accept All Button', 'opulentia' ),
				'section' => 'op_gdpr_bar',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'op_gdpr_customize_text',
			array(
				'default'           => __( 'Customize', 'opulentia' ),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_gdpr_customize_text',
			array(
				'label'   => __( 'Customize Button', 'opulentia' ),
				'section' => 'op_gdpr_bar',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'op_gdpr_save_text',
			array(
				'default'           => __( 'Save Preferences', 'opulentia' ),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_gdpr_save_text',
			array(
				'label'   => __( 'Save Button', 'opulentia' ),
				'section' => 'op_gdpr_bar',
				'type'    => 'text',
			)
		);

		// ── Cookie Policy Link ──
		$wp_customize->add_setting(
			'op_gdpr_policy_page',
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_gdpr_policy_page',
			array(
				'label'   => __( 'Cookie Policy Page', 'opulentia' ),
				'section' => 'op_gdpr_bar',
				'type'    => 'dropdown-pages',
			)
		);

		// ── Categories ──
		$wp_customize->add_section(
			'op_gdpr_categories',
			array(
				'title' => __( 'Cookie Categories', 'opulentia' ),
				'panel' => 'opulentia_gdpr',
			)
		);

		$this->register_category( $wp_customize, 'necessary', __( 'Necessary', 'opulentia' ), true, true );
		$this->register_category( $wp_customize, 'analytics', __( 'Analytics', 'opulentia' ), false, false );
		$this->register_category( $wp_customize, 'marketing', __( 'Marketing', 'opulentia' ), false, false );

		// ── Colors ──
		$wp_customize->add_section(
			'op_gdpr_colors',
			array(
				'title' => __( 'Colors', 'opulentia' ),
				'panel' => 'opulentia_gdpr',
			)
		);

		$wp_customize->add_setting(
			'op_gdpr_bg_color',
			array(
				'default'           => '#1a1a1a',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_gdpr_bg_color',
				array(
					'label'   => __( 'Background Color', 'opulentia' ),
					'section' => 'op_gdpr_colors',
				)
			)
		);

		$wp_customize->add_setting(
			'op_gdpr_text_color',
			array(
				'default'           => '#f5f5f5',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_gdpr_text_color',
				array(
					'label'   => __( 'Text Color', 'opulentia' ),
					'section' => 'op_gdpr_colors',
				)
			)
		);

		$wp_customize->add_setting(
			'op_gdpr_accent_color',
			array(
				'default'           => '#c9a96e',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_gdpr_accent_color',
				array(
					'label'   => __( 'Accent / Button Color', 'opulentia' ),
					'section' => 'op_gdpr_colors',
				)
			)
		);

		// ── Cookie Lifespan ──
		$wp_customize->add_section(
			'op_gdpr_advanced',
			array(
				'title' => __( 'Advanced', 'opulentia' ),
				'panel' => 'opulentia_gdpr',
			)
		);

		$wp_customize->add_setting(
			'op_gdpr_cookie_lifespan',
			array(
				'default'           => 365,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_gdpr_cookie_lifespan',
			array(
				'label'       => __( 'Cookie Lifetime (days)', 'opulentia' ),
				'section'     => 'op_gdpr_advanced',
				'type'        => 'number',
				'input_attrs' => array(
					'min' => 1,
					'max' => 3650,
				),
			)
		);
	}

	private function register_category( $wp_customize, $id, $label, $default_checked, $required ) {
		$wp_customize->add_setting(
			'op_gdpr_cat_' . $id . '_title',
			array(
				'default'           => $label,
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_gdpr_cat_' . $id . '_title',
			array(
				'label'   => sprintf( __( 'Category: %s Title', 'opulentia' ), $id ),
				'section' => 'op_gdpr_categories',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'op_gdpr_cat_' . $id . '_desc',
			array(
				'default'           => $this->default_category_desc( $id ),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_gdpr_cat_' . $id . '_desc',
			array(
				'label'   => sprintf( __( 'Category: %s Description', 'opulentia' ), $id ),
				'section' => 'op_gdpr_categories',
				'type'    => 'text',
			)
		);
	}

	private function default_category_desc( $id ) {
		$descs = array(
			'necessary' => __( 'Required for basic site functionality.', 'opulentia' ),
			'analytics' => __( 'Help us improve with anonymous usage data.', 'opulentia' ),
			'marketing' => __( 'Used for personalized ads and content.', 'opulentia' ),
		);
		return isset( $descs[ $id ] ) ? $descs[ $id ] : '';
	}

	public function enqueue_scripts() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		wp_enqueue_script(
			'opulentia-gdpr',
			Opulentia_URI . '/inc/modules/gdpr-cookie-consent/js/gdpr.js',
			array(),
			Opulentia_VERSION,
			true
		);

		wp_localize_script(
			'opulentia-gdpr',
			'OpulentiaGDPR',
			array(
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'Opulentia_gdpr_nonce' ),
				'lifespan'   => get_theme_mod( 'op_gdpr_cookie_lifespan', 365 ),
				'categories' => array(
					'necessary' => array( 'required' => true ),
					'analytics' => array( 'required' => false ),
					'marketing' => array( 'required' => false ),
				),
			)
		);
	}

	public function render_bar() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		$message        = get_theme_mod( 'op_gdpr_message', __( 'We use cookies to improve your experience.', 'opulentia' ) );
		$accept_text    = get_theme_mod( 'op_gdpr_accept_text', __( 'Accept All', 'opulentia' ) );
		$customize_text = get_theme_mod( 'op_gdpr_customize_text', __( 'Customize', 'opulentia' ) );
		$save_text      = get_theme_mod( 'op_gdpr_save_text', __( 'Save Preferences', 'opulentia' ) );
		$position       = get_theme_mod( 'op_gdpr_position', 'bottom' );
		$policy_id      = get_theme_mod( 'op_gdpr_policy_page', 0 );
		$policy_url     = $policy_id ? get_permalink( $policy_id ) : '';
		$cats           = array( 'necessary', 'analytics', 'marketing' );
		?>
		<div id="op-gdpr-bar" class="op-gdpr-bar op-gdpr-bar--<?php echo esc_attr( $position ); ?>" role="dialog" aria-label="<?php esc_attr_e( 'Cookie Consent', 'opulentia' ); ?>">
			<div class="op-gdpr-bar__inner">
				<div class="op-gdpr-bar__text">
					<?php echo esc_html( $message ); ?>
					<?php if ( $policy_url ) : ?>
						<a href="<?php echo esc_url( $policy_url ); ?>" class="op-gdpr-bar__policy"><?php esc_html_e( 'Cookie Policy', 'opulentia' ); ?></a>
					<?php endif; ?>
				</div>
				<div class="op-gdpr-bar__actions">
					<button class="op-gdpr-bar__btn op-gdpr-bar__btn--accept" data-action="accept"><?php echo esc_html( $accept_text ); ?></button>
					<button class="op-gdpr-bar__btn op-gdpr-bar__btn--customize" data-action="customize"><?php echo esc_html( $customize_text ); ?></button>
				</div>
			</div>
			<div class="op-gdpr-bar__preferences" id="op-gdpr-preferences" style="display:none;">
				<?php foreach ( $cats as $cat ) : ?>
					<?php
					$title    = get_theme_mod( 'op_gdpr_cat_' . $cat . '_title', ucfirst( $cat ) );
					$desc     = get_theme_mod( 'op_gdpr_cat_' . $cat . '_desc', $this->default_category_desc( $cat ) );
					$required = ( 'necessary' === $cat );
					?>
					<label class="op-gdpr-bar__cat">
						<input type="checkbox" name="gdpr_<?php echo esc_attr( $cat ); ?>" value="1" 
						<?php
						checked( $required );
						disabled( $required );
						?>
						>
						<span class="op-gdpr-bar__cat-title"><?php echo esc_html( $title ); ?></span>
						<span class="op-gdpr-bar__cat-desc"><?php echo esc_html( $desc ); ?></span>
					</label>
				<?php endforeach; ?>
				<button class="op-gdpr-bar__btn op-gdpr-bar__btn--save" data-action="save"><?php echo esc_html( $save_text ); ?></button>
			</div>
		</div>
		<?php
	}

	public function dynamic_css( $css ) {
		if ( ! $this->is_enabled() ) {
			return $css;
		}

		$bg     = get_theme_mod( 'op_gdpr_bg_color', '#1a1a1a' );
		$text   = get_theme_mod( 'op_gdpr_text_color', '#f5f5f5' );
		$accent = get_theme_mod( 'op_gdpr_accent_color', '#c9a96e' );

		$css .= "
.op-gdpr-bar{position:fixed;left:0;right:0;z-index:99999;background:{$bg};color:{$text};padding:15px 20px;font-size:14px;box-shadow:0 -2px 10px rgba(0,0,0,0.3);transition:transform 0.4s ease,opacity 0.4s ease}
.op-gdpr-bar--bottom{bottom:0;transform:translateY(100%)}
.op-gdpr-bar--top{top:0;transform:translateY(-100%)}
.op-gdpr-bar.op-gdpr-bar--visible{transform:translateY(0)}
.op-gdpr-bar__inner{display:flex;align-items:center;justify-content:space-between;gap:15px;flex-wrap:wrap;max-width:1200px;margin:0 auto}
.op-gdpr-bar__text{flex:1;min-width:200px}
.op-gdpr-bar__policy{margin-left:8px;text-decoration:underline;color:{$accent}}
.op-gdpr-bar__actions{display:flex;gap:8px}
.op-gdpr-bar__btn{padding:8px 16px;border:none;border-radius:4px;cursor:pointer;font-size:13px;font-weight:600}
.op-gdpr-bar__btn--accept{background:{$accent};color:#fff}
.op-gdpr-bar__btn--customize{background:rgba(255,255,255,0.15);color:{$text}}
.op-gdpr-bar__btn--save{background:{$accent};color:#fff;margin-top:10px}
.op-gdpr-bar__preferences{max-width:1200px;margin:10px auto 0;padding-top:10px;border-top:1px solid rgba(255,255,255,0.1)}
.op-gdpr-bar__cat{display:flex;align-items:center;gap:8px;margin-bottom:6px;cursor:pointer}
.op-gdpr-bar__cat input[type=\"checkbox\"]{accent-color:{$accent}}
.op-gdpr-bar__cat-title{font-weight:600;font-size:13px}
.op-gdpr-bar__cat-desc{font-size:12px;opacity:0.7}
";
		return $css;
	}

	public function ajax_save_consent() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'Opulentia_gdpr_nonce' ) ) {
			wp_die( 'Security check', 403 );
		}

		$consent = isset( $_POST['consent'] ) ? json_decode( wp_unslash( $_POST['consent'] ), true ) : array();

		$safe = array();
		$cats = array( 'necessary', 'analytics', 'marketing' );
		foreach ( $cats as $cat ) {
			$safe[ $cat ] = ! empty( $consent[ $cat ] );
		}
		$safe['necessary'] = true;

		setcookie(
			'Opulentia_gdpr_consent',
			wp_json_encode( $safe ),
			time() + ( get_theme_mod( 'op_gdpr_cookie_lifespan', 365 ) * DAY_IN_SECONDS ),
			COOKIEPATH,
			COOKIE_DOMAIN,
			is_ssl(),
			true
		);

		wp_send_json_success( $safe );
	}
}
