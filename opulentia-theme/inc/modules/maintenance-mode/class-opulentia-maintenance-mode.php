<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Maintenance_Mode {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'template_redirect', array( $this, 'maybe_show_maintenance' ), 0 );
		add_filter( 'opulentia_dynamic_css', array( $this, 'dynamic_css' ) );
	}

	private function is_enabled() {
		return (bool) get_theme_mod( 'op_maintenance_enable', false );
	}

	public function customize_register( $wp_customize ) {
		$wp_customize->add_panel(
			'opulentia_maintenance',
			array(
				'title'       => __( 'Maintenance Mode', 'opulentia' ),
				'description' => __( 'Configure maintenance/coming-soon mode for your site.', 'opulentia' ),
				'priority'    => 135,
			)
		);

		// ── General ──
		$wp_customize->add_section(
			'op_maintenance_general',
			array(
				'title' => __( 'General', 'opulentia' ),
				'panel' => 'opulentia_maintenance',
			)
		);

		$wp_customize->add_setting(
			'op_maintenance_enable',
			array(
				'default'           => false,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_maintenance_enable',
			array(
				'label'   => __( 'Enable Maintenance Mode', 'opulentia' ),
				'section' => 'op_maintenance_general',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'op_maintenance_mode_type',
			array(
				'default'           => 'maintenance',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_maintenance_mode_type',
			array(
				'label'   => __( 'Mode', 'opulentia' ),
				'section' => 'op_maintenance_general',
				'type'    => 'select',
				'choices' => array(
					'maintenance' => __( 'Maintenance (503)', 'opulentia' ),
					'coming-soon' => __( 'Coming Soon', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'op_maintenance_heading',
			array(
				'default'           => __( 'We\'ll Be Back Soon', 'opulentia' ),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_maintenance_heading',
			array(
				'label'   => __( 'Heading', 'opulentia' ),
				'section' => 'op_maintenance_general',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'op_maintenance_description',
			array(
				'default'           => __( 'Our site is currently undergoing scheduled maintenance. We appreciate your patience.', 'opulentia' ),
				'sanitize_callback' => 'wp_kses_post',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_maintenance_description',
			array(
				'label'   => __( 'Description', 'opulentia' ),
				'section' => 'op_maintenance_general',
				'type'    => 'textarea',
			)
		);

		// ── Logo ──
		$wp_customize->add_section(
			'op_maintenance_logo',
			array(
				'title' => __( 'Logo', 'opulentia' ),
				'panel' => 'opulentia_maintenance',
			)
		);

		$wp_customize->add_setting(
			'op_maintenance_logo',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'op_maintenance_logo',
				array(
					'label'   => __( 'Custom Logo', 'opulentia' ),
					'section' => 'op_maintenance_logo',
				)
			)
		);

		// ── Countdown ──
		$wp_customize->add_section(
			'op_maintenance_countdown',
			array(
				'title' => __( 'Countdown Timer', 'opulentia' ),
				'panel' => 'opulentia_maintenance',
			)
		);

		$wp_customize->add_setting(
			'op_maintenance_countdown_enable',
			array(
				'default'           => false,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_maintenance_countdown_enable',
			array(
				'label'   => __( 'Enable Countdown', 'opulentia' ),
				'section' => 'op_maintenance_countdown',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'op_maintenance_countdown_date',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_maintenance_countdown_date',
			array(
				'label'       => __( 'Target Date', 'opulentia' ),
				'description' => __( 'Format: YYYY-MM-DD HH:MM', 'opulentia' ),
				'section'     => 'op_maintenance_countdown',
				'type'        => 'text',
			)
		);

		// ── Social ──
		$wp_customize->add_section(
			'op_maintenance_social',
			array(
				'title' => __( 'Social Links', 'opulentia' ),
				'panel' => 'opulentia_maintenance',
			)
		);

		foreach ( array( 'facebook', 'twitter', 'instagram', 'linkedin', 'youtube' ) as $social ) {
			$wp_customize->add_setting(
				'op_maintenance_social_' . $social,
				array(
					'default'           => '',
					'sanitize_callback' => 'esc_url_raw',
					'transport'         => 'refresh',
				)
			);

			$wp_customize->add_control(
				'op_maintenance_social_' . $social,
				array(
					'label'   => ucfirst( $social ),
					'section' => 'op_maintenance_social',
					'type'    => 'url',
				)
			);
		}

		// ── Subscribe ──
		$wp_customize->add_section(
			'op_maintenance_subscribe',
			array(
				'title' => __( 'Subscribe Form', 'opulentia' ),
				'panel' => 'opulentia_maintenance',
			)
		);

		$wp_customize->add_setting(
			'op_maintenance_subscribe_enable',
			array(
				'default'           => false,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_maintenance_subscribe_enable',
			array(
				'label'   => __( 'Enable Subscribe Form', 'opulentia' ),
				'section' => 'op_maintenance_subscribe',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'op_maintenance_subscribe_placeholder',
			array(
				'default'           => __( 'Enter your email', 'opulentia' ),
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_maintenance_subscribe_placeholder',
			array(
				'label'   => __( 'Placeholder Text', 'opulentia' ),
				'section' => 'op_maintenance_subscribe',
				'type'    => 'text',
			)
		);

		// ── Background ──
		$wp_customize->add_section(
			'op_maintenance_background',
			array(
				'title' => __( 'Background', 'opulentia' ),
				'panel' => 'opulentia_maintenance',
			)
		);

		$wp_customize->add_setting(
			'op_maintenance_bg_type',
			array(
				'default'           => 'color',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_maintenance_bg_type',
			array(
				'label'   => __( 'Background Type', 'opulentia' ),
				'section' => 'op_maintenance_background',
				'type'    => 'select',
				'choices' => array(
					'color'    => __( 'Solid Color', 'opulentia' ),
					'gradient' => __( 'Gradient', 'opulentia' ),
					'image'    => __( 'Image', 'opulentia' ),
				),
			)
		);

		$wp_customize->add_setting(
			'op_maintenance_bg_color',
			array(
				'default'           => '#1a1a1a',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'op_maintenance_bg_color',
				array(
					'label'   => __( 'Background Color', 'opulentia' ),
					'section' => 'op_maintenance_background',
				)
			)
		);

		$wp_customize->add_setting(
			'op_maintenance_bg_gradient',
			array(
				'default'           => 'linear-gradient(135deg, #1a1a1a 0%, #333 100%)',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_maintenance_bg_gradient',
			array(
				'label'   => __( 'Gradient CSS', 'opulentia' ),
				'section' => 'op_maintenance_background',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'op_maintenance_bg_image',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'op_maintenance_bg_image',
				array(
					'label'   => __( 'Background Image', 'opulentia' ),
					'section' => 'op_maintenance_background',
				)
			)
		);

		// ── Bypass ──
		$wp_customize->add_section(
			'op_maintenance_bypass',
			array(
				'title' => __( 'Bypass Rules', 'opulentia' ),
				'panel' => 'opulentia_maintenance',
			)
		);

		$wp_customize->add_setting(
			'op_maintenance_bypass_users',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_maintenance_bypass_users',
			array(
				'label'   => __( 'Allow Logged-In Users', 'opulentia' ),
				'section' => 'op_maintenance_bypass',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'op_maintenance_bypass_ips',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'op_maintenance_bypass_ips',
			array(
				'label'       => __( 'Bypass IPs', 'opulentia' ),
				'description' => __( 'Comma-separated IP addresses.', 'opulentia' ),
				'section'     => 'op_maintenance_bypass',
				'type'        => 'text',
			)
		);
	}

	public function maybe_show_maintenance() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		if ( $this->should_bypass() ) {
			return;
		}

		if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		$mode = get_theme_mod( 'op_maintenance_mode_type', 'maintenance' );

		if ( 'maintenance' === $mode ) {
			http_response_code( 503 );
			header( 'Retry-After: 3600' );
		}

		$this->render_maintenance_page();
		exit;
	}

	private function should_bypass() {
		if ( get_theme_mod( 'op_maintenance_bypass_users', true ) && is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
			return true;
		}

		$ips = get_theme_mod( 'op_maintenance_bypass_ips', '' );
		if ( $ips ) {
			$allowed = array_map( 'trim', explode( ',', $ips ) );
			if ( in_array( $_SERVER['REMOTE_ADDR'], $allowed, true ) ) {
				return true;
			}
		}

		return false;
	}

	private function render_maintenance_page() {
		$heading         = get_theme_mod( 'op_maintenance_heading', __( 'We\'ll Be Back Soon', 'opulentia' ) );
		$desc            = get_theme_mod( 'op_maintenance_description', '' );
		$logo            = get_theme_mod( 'op_maintenance_logo', '' );
		$bg_type         = get_theme_mod( 'op_maintenance_bg_type', 'color' );
		$bg_color        = get_theme_mod( 'op_maintenance_bg_color', '#1a1a1a' );
		$bg_grad         = get_theme_mod( 'op_maintenance_bg_gradient', '' );
		$bg_image        = get_theme_mod( 'op_maintenance_bg_image', '' );
		$countdown       = get_theme_mod( 'op_maintenance_countdown_enable', false );
		$countdown_date  = get_theme_mod( 'op_maintenance_countdown_date', '' );
		$subscribe       = get_theme_mod( 'op_maintenance_subscribe_enable', false );
		$sub_placeholder = get_theme_mod( 'op_maintenance_subscribe_placeholder', __( 'Enter your email', 'opulentia' ) );

		$bg_style = '';
		if ( 'gradient' === $bg_type && $bg_grad ) {
			$bg_style = 'background:' . $bg_grad . ';';
		} elseif ( 'image' === $bg_type && $bg_image ) {
			$bg_style = 'background:url(' . esc_url( $bg_image ) . ') center/cover no-repeat;';
		} else {
			$bg_style = 'background:' . $bg_color . ';';
		}

		status_header( 503 );
		nocache_headers();
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title><?php echo esc_html( $heading ); ?> - <?php bloginfo( 'name' ); ?></title>
			<?php wp_print_styles( array( 'Opulentia-theme' ) ); ?>
			<style>
				*{margin:0;padding:0;box-sizing:border-box}
				body{<?php echo $bg_style; ?>color:#f5f5f5;font-family:Inter,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:20px}
				.op-mt__inner{max-width:600px;width:100%}
				.op-mt__logo{max-width:200px;height:auto;margin-bottom:30px}
				.op-mt__heading{font-family:"Playfair Display",serif;font-size:42px;color:#c9a96e;margin-bottom:15px}
				.op-mt__desc{font-size:16px;line-height:1.6;opacity:0.9;margin-bottom:30px}
				.op-mt__countdown{display:flex;gap:15px;justify-content:center;margin-bottom:30px}
				.op-mt__countdown-item{background:rgba(255,255,255,0.1);padding:15px 20px;border-radius:8px;min-width:80px}
				.op-mt__countdown-num{font-size:32px;font-weight:700;color:#c9a96e;display:block}
				.op-mt__countdown-label{font-size:12px;text-transform:uppercase;opacity:0.7}
				.op-mt__social{display:flex;gap:10px;justify-content:center;margin-bottom:30px}
				.op-mt__social a{color:#c9a96e;font-size:20px;text-decoration:none;opacity:0.7;transition:opacity 0.2s}
				.op-mt__social a:hover{opacity:1}
				.op-mt__subscribe{display:flex;gap:10px;max-width:400px;margin:0 auto}
				.op-mt__subscribe input[type="email"]{flex:1;padding:12px 15px;border:1px solid #333;border-radius:4px;background:rgba(255,255,255,0.1);color:#f5f5f5;font-size:14px}
				.op-mt__subscribe button{padding:12px 24px;background:#b8860b;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:14px;font-weight:600}
			</style>
		</head>
		<body>
			<div class="op-mt__inner">
				<?php if ( $logo ) : ?>
					<img class="op-mt__logo" src="<?php echo esc_url( $logo ); ?>" alt="<?php bloginfo( 'name' ); ?>">
				<?php endif; ?>
				<h1 class="op-mt__heading"><?php echo esc_html( $heading ); ?></h1>
				<?php if ( $desc ) : ?>
					<div class="op-mt__desc"><?php echo wp_kses_post( $desc ); ?></div>
				<?php endif; ?>
				<?php if ( $countdown && $countdown_date ) : ?>
					<div class="op-mt__countdown" id="op-mt-countdown" data-target="<?php echo esc_attr( $countdown_date ); ?>">
						<div class="op-mt__countdown-item"><span class="op-mt__countdown-num" id="op-mt-days">00</span><span class="op-mt__countdown-label"><?php esc_html_e( 'Days', 'opulentia' ); ?></span></div>
						<div class="op-mt__countdown-item"><span class="op-mt__countdown-num" id="op-mt-hours">00</span><span class="op-mt__countdown-label"><?php esc_html_e( 'Hours', 'opulentia' ); ?></span></div>
						<div class="op-mt__countdown-item"><span class="op-mt__countdown-num" id="op-mt-mins">00</span><span class="op-mt__countdown-label"><?php esc_html_e( 'Mins', 'opulentia' ); ?></span></div>
						<div class="op-mt__countdown-item"><span class="op-mt__countdown-num" id="op-mt-secs">00</span><span class="op-mt__countdown-label"><?php esc_html_e( 'Secs', 'opulentia' ); ?></span></div>
					</div>
					<script>
					(function(){
						var target = new Date('<?php echo esc_js( $countdown_date ); ?>').getTime();
						function update(){
							var now = Date.now(), diff = Math.max(0, target - now);
							document.getElementById('op-mt-days').textContent = String(Math.floor(diff / 86400000)).padStart(2, '0');
							document.getElementById('op-mt-hours').textContent = String(Math.floor((diff % 86400000) / 3600000)).padStart(2, '0');
							document.getElementById('op-mt-mins').textContent = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
							document.getElementById('op-mt-secs').textContent = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
						}
						update();
						setInterval(update, 1000);
					})();
					</script>
				<?php endif; ?>
				<?php
				$socials    = array( 'facebook', 'twitter', 'instagram', 'linkedin', 'youtube' );
				$has_social = false;
				foreach ( $socials as $s ) {
					if ( get_theme_mod( 'op_maintenance_social_' . $s ) ) {
						$has_social = true;
						break; }
				}
				if ( $has_social ) :
					?>
				<div class="op-mt__social">
					<?php
					foreach ( $socials as $s ) :
						$url = get_theme_mod( 'op_maintenance_social_' . $s ); if ( $url ) :
							?>
						<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( ucfirst( $s ) ); ?></a>
											<?php
											endif;
endforeach;
					?>
				</div>
				<?php endif; ?>
				<?php if ( $subscribe ) : ?>
					<form class="op-mt__subscribe" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
						<input type="hidden" name="action" value="Opulentia_newsletter_signup">
						<?php wp_nonce_field( 'Opulentia_newsletter', 'Opulentia_newsletter_nonce' ); ?>
						<input type="email" name="email" placeholder="<?php echo esc_attr( $sub_placeholder ); ?>" required>
						<button type="submit"><?php esc_html_e( 'Notify Me', 'opulentia' ); ?></button>
					</form>
				<?php endif; ?>
			</div>
		</body>
		</html>
		<?php
	}

	public function dynamic_css( $css ) {
		if ( ! $this->is_enabled() ) {
			return $css;
		}

		$bg_type  = get_theme_mod( 'op_maintenance_bg_type', 'color' );
		$bg_color = get_theme_mod( 'op_maintenance_bg_color', '#1a1a1a' );

		$css .= '
.op-mt__subscribe input[type="email"]::placeholder{color:#999}
.op-mt__subscribe input[type="email"]:focus{outline:none;border-color:#b8860b}
';
		return $css;
	}
}
