<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Popup_Builder {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 25 );
		add_filter( 'opulentia_dynamic_css', array( $this, 'dynamic_css' ) );
		add_action( 'wp_footer', array( $this, 'render_popup' ), 100 );
	}

	private function is_enabled() {
		return (bool) get_theme_mod( 'op_popup_enable', false );
	}

	private function get_popup_type() {
		return get_theme_mod( 'op_popup_type', 'modal' );
	}

	public function customize_register( $wp_customize ) {
		$wp_customize->add_panel(
			'opulentia_popups',
			array(
				'title'       => __( 'Popups', 'opulentia' ),
				'description' => __( 'Global popup configuration with trigger, design, and display conditions.', 'opulentia' ),
				'priority'    => 40,
			)
		);

		// ── Global Toggle ──
		$wp_customize->add_section(
			'op_popup_global',
			array(
				'title'    => __( 'Global Settings', 'opulentia' ),
				'panel'    => 'opulentia_popups',
				'priority' => 1,
			)
		);

		$wp_customize->add_setting(
			'op_popup_enable',
			array(
				'default'           => false,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_enable',
			array(
				'label'       => __( 'Enable Popup', 'opulentia' ),
				'description' => __( 'Globally enable the popup across your site.', 'opulentia' ),
				'section'     => 'op_popup_global',
				'type'        => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'op_popup_type',
			array(
				'default'           => 'modal',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_type',
			array(
				'label'   => __( 'Popup Type', 'opulentia' ),
				'section' => 'op_popup_global',
				'type'    => 'select',
				'choices' => array(
					'modal'        => __( 'Modal (Centered)', 'opulentia' ),
					'notification' => __( 'Notification Bar', 'opulentia' ),
					'slide-in'     => __( 'Slide-In (Corner)', 'opulentia' ),
					'fullscreen'   => __( 'Fullscreen Overlay', 'opulentia' ),
				),
			)
		);

		// ── Content ──
		$wp_customize->add_section(
			'op_popup_content',
			array(
				'title'    => __( 'Content', 'opulentia' ),
				'panel'    => 'opulentia_popups',
				'priority' => 5,
			)
		);

		$wp_customize->add_setting(
			'op_popup_title',
			array(
				'default'           => '',
				'sanitize_callback' => 'wp_kses_post',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_title',
			array(
				'label'   => __( 'Title', 'opulentia' ),
				'section' => 'op_popup_content',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'op_popup_text',
			array(
				'default'           => '',
				'sanitize_callback' => 'wp_kses_post',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_text',
			array(
				'label'   => __( 'Body Text', 'opulentia' ),
				'section' => 'op_popup_content',
				'type'    => 'textarea',
			)
		);

		$wp_customize->add_setting(
			'op_popup_btn_text',
			array(
				'default'           => __( 'Get Started', 'opulentia' ),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_btn_text',
			array(
				'label'   => __( 'Button Text', 'opulentia' ),
				'section' => 'op_popup_content',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'op_popup_btn_url',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_btn_url',
			array(
				'label'   => __( 'Button URL', 'opulentia' ),
				'section' => 'op_popup_content',
				'type'    => 'url',
			)
		);

		// ── Triggers ──
		$wp_customize->add_section(
			'op_popup_triggers',
			array(
				'title'    => __( 'Triggers', 'opulentia' ),
				'panel'    => 'opulentia_popups',
				'priority' => 10,
			)
		);

		$wp_customize->add_setting(
			'op_popup_trigger_type',
			array(
				'default'           => 'time',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_trigger_type',
			array(
				'label'   => __( 'Trigger Event', 'opulentia' ),
				'section' => 'op_popup_triggers',
				'type'    => 'select',
				'choices' => array(
					'time'   => __( 'Time Delay', 'opulentia' ),
					'scroll' => __( 'Scroll Percentage', 'opulentia' ),
					'exit'   => __( 'Exit Intent', 'opulentia' ),
					'click'  => __( 'Click (element)', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'op_popup_delay',
			array(
				'default'           => 3,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_delay',
			array(
				'label'       => __( 'Time Delay (seconds)', 'opulentia' ),
				'section'     => 'op_popup_triggers',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 60,
					'step' => 0.5,
				),
			)
		);

		$wp_customize->add_setting(
			'op_popup_scroll_percent',
			array(
				'default'           => 50,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_scroll_percent',
			array(
				'label'       => __( 'Scroll % (0–100)', 'opulentia' ),
				'section'     => 'op_popup_triggers',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 5,
				),
			)
		);

		$wp_customize->add_setting(
			'op_popup_click_selector',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_click_selector',
			array(
				'label'       => __( 'CSS Click Selector', 'opulentia' ),
				'description' => __( 'CSS selector for elements that trigger the popup on click.', 'opulentia' ),
				'section'     => 'op_popup_triggers',
				'type'        => 'text',
			)
		);

		// ── Frequency ──
		$wp_customize->add_section(
			'op_popup_frequency',
			array(
				'title'    => __( 'Frequency', 'opulentia' ),
				'panel'    => 'opulentia_popups',
				'priority' => 15,
			)
		);

		$wp_customize->add_setting(
			'op_popup_frequency',
			array(
				'default'           => 'session',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_frequency',
			array(
				'label'   => __( 'Show Frequency', 'opulentia' ),
				'section' => 'op_popup_frequency',
				'type'    => 'select',
				'choices' => array(
					'always'  => __( 'Always', 'opulentia' ),
					'session' => __( 'Once Per Session', 'opulentia' ),
					'day'     => __( 'Once Per Day', 'opulentia' ),
					'week'    => __( 'Once Per Week', 'opulentia' ),
				),
			)
		);

		// ── Design ──
		$wp_customize->add_section(
			'op_popup_design',
			array(
				'title'    => __( 'Design', 'opulentia' ),
				'panel'    => 'opulentia_popups',
				'priority' => 20,
			)
		);

		$wp_customize->add_setting(
			'op_popup_bg_color',
			array(
				'default'           => '#1a1a1a',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_popup_bg_color',
				array(
					'label'   => __( 'Background Color', 'opulentia' ),
					'section' => 'op_popup_design',
				)
			)
		);

		$wp_customize->add_setting(
			'op_popup_text_color',
			array(
				'default'           => '#f5f5f5',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_popup_text_color',
				array(
					'label'   => __( 'Text Color', 'opulentia' ),
					'section' => 'op_popup_design',
				)
			)
		);

		$wp_customize->add_setting(
			'op_popup_accent_color',
			array(
				'default'           => '#b8860b',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_popup_accent_color',
				array(
					'label'   => __( 'Accent/Button Color', 'opulentia' ),
					'section' => 'op_popup_design',
				)
			)
		);

		$wp_customize->add_setting(
			'op_popup_overlay_color',
			array(
				'default'           => 'rgba(0,0,0,0.7)',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_overlay_color',
			array(
				'label'       => __( 'Overlay Color', 'opulentia' ),
				'description' => __( 'CSS color value (hex, rgba, or hsla).', 'opulentia' ),
				'section'     => 'op_popup_design',
				'type'        => 'text',
			)
		);

		$wp_customize->add_setting(
			'op_popup_width',
			array(
				'default'           => 500,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_width',
			array(
				'label'       => __( 'Max Width (px)', 'opulentia' ),
				'section'     => 'op_popup_design',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 200,
					'max'  => 1200,
					'step' => 10,
				),
			)
		);

		$wp_customize->add_setting(
			'op_popup_border_radius',
			array(
				'default'           => 8,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_border_radius',
			array(
				'label'       => __( 'Border Radius (px)', 'opulentia' ),
				'section'     => 'op_popup_design',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				),
			)
		);

		// ── Display Conditions ──
		$wp_customize->add_section(
			'op_popup_conditions',
			array(
				'title'    => __( 'Display Conditions', 'opulentia' ),
				'panel'    => 'opulentia_popups',
				'priority' => 25,
			)
		);

		$wp_customize->add_setting(
			'op_popup_show_on',
			array(
				'default'           => 'all',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_show_on',
			array(
				'label'   => __( 'Show On', 'opulentia' ),
				'section' => 'op_popup_conditions',
				'type'    => 'select',
				'choices' => array(
					'all'      => __( 'All Pages', 'opulentia' ),
					'home'     => __( 'Homepage Only', 'opulentia' ),
					'pages'    => __( 'Pages Only', 'opulentia' ),
					'posts'    => __( 'Posts Only', 'opulentia' ),
					'products' => __( 'Products Only', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'op_popup_devices',
			array(
				'default'           => 'all',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_devices',
			array(
				'label'   => __( 'Devices', 'opulentia' ),
				'section' => 'op_popup_conditions',
				'type'    => 'select',
				'choices' => array(
					'all'     => __( 'All Devices', 'opulentia' ),
					'desktop' => __( 'Desktop Only', 'opulentia' ),
					'mobile'  => __( 'Mobile Only', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'op_popup_user_roles',
			array(
				'default'           => 'all',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_popup_user_roles',
			array(
				'label'   => __( 'User Roles', 'opulentia' ),
				'section' => 'op_popup_conditions',
				'type'    => 'select',
				'choices' => array(
					'all'        => __( 'All Users', 'opulentia' ),
					'logged-in'  => __( 'Logged-In Only', 'opulentia' ),
					'logged-out' => __( 'Logged-Out Only', 'opulentia' ),
				),
			)
		);
	}

	public function enqueue_scripts() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		wp_enqueue_script(
			'opulentia-popup',
			Opulentia_URI . '/js/popup.js',
			array( 'gsap-core' ),
			Opulentia_VERSION,
			true
		);

		wp_localize_script(
			'opulentia-popup',
			'OpulentiaPopup',
			array(
				'enable'        => true,
				'type'          => $this->get_popup_type(),
				'trigger'       => get_theme_mod( 'op_popup_trigger_type', 'time' ),
				'delay'         => (float) get_theme_mod( 'op_popup_delay', 3 ),
				'scrollPercent' => (int) get_theme_mod( 'op_popup_scroll_percent', 50 ),
				'clickSelector' => get_theme_mod( 'op_popup_click_selector', '' ),
				'frequency'     => get_theme_mod( 'op_popup_frequency', 'session' ),
				'showOn'        => get_theme_mod( 'op_popup_show_on', 'all' ),
				'devices'       => get_theme_mod( 'op_popup_devices', 'all' ),
				'userRoles'     => get_theme_mod( 'op_popup_user_roles', 'all' ),
				'isLoggedIn'    => is_user_logged_in(),
			)
		);
	}

	public function dynamic_css( $css ) {
		if ( ! $this->is_enabled() ) {
			return $css;
		}

		$bg_color   = get_theme_mod( 'op_popup_bg_color', '#1a1a1a' );
		$text_color = get_theme_mod( 'op_popup_text_color', '#f5f5f5' );
		$accent     = get_theme_mod( 'op_popup_accent_color', '#b8860b' );
		$overlay    = get_theme_mod( 'op_popup_overlay_color', 'rgba(0,0,0,0.7)' );
		$width      = (int) get_theme_mod( 'op_popup_width', 500 );
		$radius     = (int) get_theme_mod( 'op_popup_border_radius', 8 );
		$type       = $this->get_popup_type();

		$position_css = '';
		if ( 'notification' === $type ) {
			$position_css = 'top:0;left:0;right:0;width:100%;max-width:100%;border-radius:0;';
		} elseif ( 'slide-in' === $type ) {
			$position_css = 'bottom:20px;right:20px;';
		} elseif ( 'fullscreen' === $type ) {
			$position_css = 'top:0;left:0;width:100%;height:100%;max-width:100%;max-height:100%;border-radius:0;display:flex;align-items:center;justify-content:center;';
		}

		$css .= '
.op-popup {
    display:none;
    position:fixed;
    z-index:99999;
    background:' . $bg_color . ';
    color:' . $text_color . ';
    border-radius:' . $radius . 'px;
    max-width:' . $width . 'px;
    ' . $position_css . '
    box-shadow:0 10px 40px rgba(0,0,0,0.5);
    font-family:var(--font-body, Inter, sans-serif);
}
.op-popup.is-visible {
    display:block;
}
.op-popup--modal {
    top:50%;left:50%;
    transform:translate(-50%,-50%);
    width:90%;
}
.op-popup--notification {
    top:0;left:0;right:0;width:100%;max-width:100%;border-radius:0;
}
.op-popup--slide-in {
    bottom:20px;right:20px;max-width:360px;
}
.op-popup--fullscreen {
    top:0;left:0;width:100%;height:100%;max-width:100%;max-height:100%;border-radius:0;
    display:flex;align-items:center;justify-content:center;
}
.op-popup__overlay {
    display:none;
    position:fixed;
    top:0;left:0;right:0;bottom:0;
    background:' . $overlay . ';
    z-index:99998;
}
.op-popup__overlay.is-visible {
    display:block;
}
.op-popup__inner {
    padding:30px;
    position:relative;
}
.op-popup--fullscreen .op-popup__inner {
    max-width:' . $width . 'px;
    width:100%;
}
.op-popup__close {
    position:absolute;
    top:10px;right:10px;
    background:none;
    border:none;
    color:' . $text_color . ';
    font-size:24px;
    line-height:1;
    cursor:pointer;
    opacity:0.6;
    transition:opacity 0.2s;
    padding:5px;
}
.op-popup__close:hover {
    opacity:1;
}
.op-popup__title {
    font-family:var(--font-heading, "Playfair Display", serif);
    font-size:24px;
    margin:0 0 10px;
    color:var(--color-gold, #c9a96e);
}
.op-popup__text {
    font-size:15px;
    line-height:1.6;
    margin:0 0 20px;
    opacity:0.9;
}
.op-popup__btn {
    display:inline-block;
    background:' . $accent . ';
    color:#fff;
    padding:12px 28px;
    border-radius:4px;
    text-decoration:none;
    font-size:14px;
    font-weight:600;
    transition:background 0.2s;
    border:none;
    cursor:pointer;
}
.op-popup__btn:hover {
    background:var(--color-accent-hover, #d4a843);
    color:#fff;
}
.op-popup--notification .op-popup__inner {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:20px;
    padding:15px 30px;
}
.op-popup--notification .op-popup__title {
    margin:0;
    font-size:16px;
}
.op-popup--notification .op-popup__text {
    margin:0;
    font-size:14px;
}
.op-popup--notification .op-popup__btn {
    padding:8px 20px;
    font-size:13px;
    white-space:nowrap;
}
.op-popup--slide-in .op-popup__inner {
    padding:25px;
}
.op-popup--slide-in .op-popup__title {
    font-size:20px;
}
.op-popup--slide-in .op-popup__text {
    font-size:14px;
}
';

		return $css;
	}

	public function render_popup() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		$title    = get_theme_mod( 'op_popup_title', '' );
		$text     = get_theme_mod( 'op_popup_text', '' );
		$btn_text = get_theme_mod( 'op_popup_btn_text', '' );
		$btn_url  = get_theme_mod( 'op_popup_btn_url', '' );
		$type     = $this->get_popup_type();

		if ( empty( $title ) && empty( $text ) ) {
			return;
		}

		$type_class    = 'op-popup--' . $type;
		$needs_overlay = in_array( $type, array( 'modal', 'fullscreen' ), true );
		?>
		<?php if ( $needs_overlay ) : ?>
		<div class="op-popup__overlay" id="op-popup-overlay"></div>
		<?php endif; ?>
		<div class="op-popup <?php echo esc_attr( $type_class ); ?>" id="op-popup" role="dialog" aria-modal="<?php echo $needs_overlay ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( $title ?: __( 'Popup', 'opulentia' ) ); ?>">
			<div class="op-popup__inner">
				<button class="op-popup__close" id="op-popup-close" aria-label="<?php esc_attr_e( 'Close popup', 'opulentia' ); ?>" type="button">&times;</button>
				<?php if ( $title ) : ?>
					<h3 class="op-popup__title"><?php echo wp_kses_post( $title ); ?></h3>
				<?php endif; ?>
				<?php if ( $text ) : ?>
					<div class="op-popup__text"><?php echo wp_kses_post( $text ); ?></div>
				<?php endif; ?>
				<?php if ( $btn_text && $btn_url ) : ?>
					<a href="<?php echo esc_url( $btn_url ); ?>" class="op-popup__btn"><?php echo esc_html( $btn_text ); ?></a>
				<?php elseif ( $btn_text ) : ?>
					<button class="op-popup__btn" onclick="document.getElementById('op-popup-close').click()"><?php echo esc_html( $btn_text ); ?></button>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
