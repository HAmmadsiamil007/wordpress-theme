<?php
/**
 * Spacing Module — Singleton
 *
 * Provides responsive spacing controls for:
 * - Container padding (top, bottom) with responsive breakpoints
 * - Section padding for various page sections
 * - Blog/archive post spacing (gap between posts)
 * - Widget spacing
 * - Header/footer spacing
 * - Responsive CSS output via dynamic CSS engine
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Spacing class.
 */
class Opulentia_Spacing {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Returns the singleton instance.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor — registers hooks.
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Initialize hooks.
	 */
	public function init() {
		add_action( 'customize_register', array( $this, 'register_customizer_section' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'output_dynamic_css' ), 110 );
	}

	// -------------------------------------------------------------------------
	// Customizer Section
	// -------------------------------------------------------------------------

	/**
	 * Register customizer section + settings for spacing.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
	 */
	public function register_customizer_section( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_spacing',
			array(
				'title'    => __( 'Spacing', 'opulentia' ),
				'panel'    => 'Opulentia_global_settings',
				'priority' => 45,
			)
		);

		// ---------------------------------------------------------------------
		// Container Padding
		// ---------------------------------------------------------------------

		$this->add_responsive_control(
			$wp_customize,
			'spacing_container_padding_top',
			array(
				'label'       => __( 'Container Top Padding', 'opulentia' ),
				'description' => __( 'Padding top for the main content area.', 'opulentia' ),
				'section'     => 'opulentia_spacing',
				'priority'    => 10,
			),
			80,
			60,
			40
		);

		$this->add_responsive_control(
			$wp_customize,
			'spacing_container_padding_bottom',
			array(
				'label'       => __( 'Container Bottom Padding', 'opulentia' ),
				'description' => __( 'Padding bottom for the main content area.', 'opulentia' ),
				'section'     => 'opulentia_spacing',
				'priority'    => 20,
			),
			80,
			60,
			40
		);

		// ---------------------------------------------------------------------
		// Section Padding
		// ---------------------------------------------------------------------

		$this->add_responsive_control(
			$wp_customize,
			'spacing_section_padding_top',
			array(
				'label'       => __( 'Section Top Padding', 'opulentia' ),
				'description' => __( 'Default padding top for generic sections.', 'opulentia' ),
				'section'     => 'opulentia_spacing',
				'priority'    => 30,
			),
			60,
			40,
			30
		);

		$this->add_responsive_control(
			$wp_customize,
			'spacing_section_padding_bottom',
			array(
				'label'       => __( 'Section Bottom Padding', 'opulentia' ),
				'description' => __( 'Default padding bottom for generic sections.', 'opulentia' ),
				'section'     => 'opulentia_spacing',
				'priority'    => 40,
			),
			60,
			40,
			30
		);

		// ---------------------------------------------------------------------
		// Blog / Archive Post Spacing
		// ---------------------------------------------------------------------

		$this->add_responsive_control(
			$wp_customize,
			'spacing_blog_gap',
			array(
				'label'       => __( 'Blog/Archive Post Gap', 'opulentia' ),
				'description' => __( 'Gap between posts in blog and archive grids.', 'opulentia' ),
				'section'     => 'opulentia_spacing',
				'priority'    => 50,
			),
			32,
			24,
			16
		);

		// ---------------------------------------------------------------------
		// Widget Spacing
		// ---------------------------------------------------------------------

		$wp_customize->add_setting(
			'spacing_widget_margin_bottom',
			array(
				'default'           => 32,
				'sanitize_callback' => 'absint',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			'spacing_widget_margin_bottom',
			array(
				'label'       => __( 'Widget Bottom Margin (px)', 'opulentia' ),
				'description' => __( 'Space below each widget in sidebars.', 'opulentia' ),
				'section'     => 'opulentia_spacing',
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 4,
				),
				'priority'    => 60,
			)
		);

		// ---------------------------------------------------------------------
		// Header Spacing
		// ---------------------------------------------------------------------

		$this->add_responsive_control(
			$wp_customize,
			'spacing_header_padding_top',
			array(
				'label'       => __( 'Header Top Padding', 'opulentia' ),
				'description' => __( 'Padding top for the main header area.', 'opulentia' ),
				'section'     => 'opulentia_spacing',
				'priority'    => 70,
			),
			20,
			16,
			12
		);

		$this->add_responsive_control(
			$wp_customize,
			'spacing_header_padding_bottom',
			array(
				'label'       => __( 'Header Bottom Padding', 'opulentia' ),
				'description' => __( 'Padding bottom for the main header area.', 'opulentia' ),
				'section'     => 'opulentia_spacing',
				'priority'    => 80,
			),
			20,
			16,
			12
		);

		// ---------------------------------------------------------------------
		// Footer Spacing
		// ---------------------------------------------------------------------

		$this->add_responsive_control(
			$wp_customize,
			'spacing_footer_padding_top',
			array(
				'label'       => __( 'Footer Top Padding', 'opulentia' ),
				'description' => __( 'Padding top for the footer area.', 'opulentia' ),
				'section'     => 'opulentia_spacing',
				'priority'    => 90,
			),
			48,
			32,
			24
		);

		$this->add_responsive_control(
			$wp_customize,
			'spacing_footer_padding_bottom',
			array(
				'label'       => __( 'Footer Bottom Padding', 'opulentia' ),
				'description' => __( 'Padding bottom for the footer area.', 'opulentia' ),
				'section'     => 'opulentia_spacing',
				'priority'    => 100,
			),
			48,
			32,
			24
		);
	}

	/**
	 * Add a responsive spacing control with desktop, tablet, and mobile fields.
	 *
	 * Registers three settings per device and renders them inline in the same section row.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager.
	 * @param string               $id           Base setting ID.
	 * @param array                $args         Control args (label, description, section, priority).
	 * @param int                  $desktop      Default desktop value.
	 * @param int                  $tablet       Default tablet value.
	 * @param int                  $mobile       Default mobile value.
	 */
	private function add_responsive_control( $wp_customize, $id, $args, $desktop = 0, $tablet = 0, $mobile = 0 ) {
		$devices = array(
			'desktop' => $desktop,
			'tablet'  => $tablet,
			'mobile'  => $mobile,
		);

		foreach ( $devices as $device => $default ) {
			$setting_id = $id . '_' . $device;
			$wp_customize->add_setting(
				$setting_id,
				array(
					'default'           => $default,
					'sanitize_callback' => 'absint',
					'transport'         => 'postMessage',
				)
			);
		}

		$wp_customize->add_control(
			new Opulentia_Responsive_Spacing_Control(
				$wp_customize,
				$id . '_ctrl',
				array(
					'label'       => $args['label'],
					'description' => $args['description'],
					'section'     => $args['section'],
					'settings'    => array(
						'desktop' => $id . '_desktop',
						'tablet'  => $id . '_tablet',
						'mobile'  => $id . '_mobile',
					),
					'priority'    => $args['priority'],
				)
			)
		);
	}

	/**
	 * Get a responsive spacing value.
	 *
	 * @param  string $id     Setting base ID.
	 * @param  string $device 'desktop', 'tablet', or 'mobile'.
	 * @return int
	 */
	private function get_responsive_value( $id, $device = 'desktop' ) {
		$setting  = $id . '_' . $device;
		$defaults = array(
			'spacing_container_padding_top_desktop'    => 80,
			'spacing_container_padding_top_tablet'     => 60,
			'spacing_container_padding_top_mobile'     => 40,
			'spacing_container_padding_bottom_desktop' => 80,
			'spacing_container_padding_bottom_tablet'  => 60,
			'spacing_container_padding_bottom_mobile'  => 40,
			'spacing_section_padding_top_desktop'      => 60,
			'spacing_section_padding_top_tablet'       => 40,
			'spacing_section_padding_top_mobile'       => 30,
			'spacing_section_padding_bottom_desktop'   => 60,
			'spacing_section_padding_bottom_tablet'    => 40,
			'spacing_section_padding_bottom_mobile'    => 30,
			'spacing_blog_gap_desktop'                 => 32,
			'spacing_blog_gap_tablet'                  => 24,
			'spacing_blog_gap_mobile'                  => 16,
			'spacing_header_padding_top_desktop'       => 20,
			'spacing_header_padding_top_tablet'        => 16,
			'spacing_header_padding_top_mobile'        => 12,
			'spacing_header_padding_bottom_desktop'    => 20,
			'spacing_header_padding_bottom_tablet'     => 16,
			'spacing_header_padding_bottom_mobile'     => 12,
			'spacing_footer_padding_top_desktop'       => 48,
			'spacing_footer_padding_top_tablet'        => 32,
			'spacing_footer_padding_top_mobile'        => 24,
			'spacing_footer_padding_bottom_desktop'    => 48,
			'spacing_footer_padding_bottom_tablet'     => 32,
			'spacing_footer_padding_bottom_mobile'     => 24,
		);
		$default  = isset( $defaults[ $setting ] ) ? $defaults[ $setting ] : 0;
		return (int) get_theme_mod( $setting, $default );
	}

	// -------------------------------------------------------------------------
	// Dynamic CSS
	// -------------------------------------------------------------------------

	/**
	 * Generate responsive spacing CSS from customizer settings.
	 *
	 * @return string
	 */
	public function get_spacing_css() {
		$css = '';

		// --- Container Padding ---
		$container_top_dt     = $this->get_responsive_value( 'spacing_container_padding_top', 'desktop' );
		$container_top_tab    = $this->get_responsive_value( 'spacing_container_padding_top', 'tablet' );
		$container_top_mob    = $this->get_responsive_value( 'spacing_container_padding_top', 'mobile' );
		$container_bottom_dt  = $this->get_responsive_value( 'spacing_container_padding_bottom', 'desktop' );
		$container_bottom_tab = $this->get_responsive_value( 'spacing_container_padding_bottom', 'tablet' );
		$container_bottom_mob = $this->get_responsive_value( 'spacing_container_padding_bottom', 'mobile' );

		$container_selector = '.site-main, .content-sidebar-layout, .page-header + .site-main';

		if ( 80 !== $container_top_dt || 80 !== $container_bottom_dt ) {
			$css .= "{$container_selector} {\n";
			if ( 80 !== $container_top_dt ) {
				$css .= "    padding-top: {$container_top_dt}px;\n";
			}
			if ( 80 !== $container_bottom_dt ) {
				$css .= "    padding-bottom: {$container_bottom_dt}px;\n";
			}
			$css .= "}\n\n";
		}

		if ( 60 !== $container_top_tab || 60 !== $container_bottom_tab ) {
			$css .= '@media (max-width: ' . Opulentia_get_tablet_breakpoint() . "px) {\n";
			$css .= "    {$container_selector} {\n";
			if ( 60 !== $container_top_tab ) {
				$css .= "        padding-top: {$container_top_tab}px;\n";
			}
			if ( 60 !== $container_bottom_tab ) {
				$css .= "        padding-bottom: {$container_bottom_tab}px;\n";
			}
			$css .= "    }\n";
			$css .= "}\n\n";
		}

		if ( 40 !== $container_top_mob || 40 !== $container_bottom_mob ) {
			$css .= '@media (max-width: ' . Opulentia_get_mobile_breakpoint() . "px) {\n";
			$css .= "    {$container_selector} {\n";
			if ( 40 !== $container_top_mob ) {
				$css .= "        padding-top: {$container_top_mob}px;\n";
			}
			if ( 40 !== $container_bottom_mob ) {
				$css .= "        padding-bottom: {$container_bottom_mob}px;\n";
			}
			$css .= "    }\n";
			$css .= "}\n\n";
		}

		// --- Section Padding ---
		$section_top_dt     = $this->get_responsive_value( 'spacing_section_padding_top', 'desktop' );
		$section_top_tab    = $this->get_responsive_value( 'spacing_section_padding_top', 'tablet' );
		$section_top_mob    = $this->get_responsive_value( 'spacing_section_padding_top', 'mobile' );
		$section_bottom_dt  = $this->get_responsive_value( 'spacing_section_padding_bottom', 'desktop' );
		$section_bottom_tab = $this->get_responsive_value( 'spacing_section_padding_bottom', 'tablet' );
		$section_bottom_mob = $this->get_responsive_value( 'spacing_section_padding_bottom', 'mobile' );

		$section_selector = '.site-section, .opulentia-section, [class*="__section"], .section';

		if ( 60 !== $section_top_dt || 60 !== $section_bottom_dt ) {
			$css .= "{$section_selector} {\n";
			if ( 60 !== $section_top_dt ) {
				$css .= "    padding-top: {$section_top_dt}px;\n";
			}
			if ( 60 !== $section_bottom_dt ) {
				$css .= "    padding-bottom: {$section_bottom_dt}px;\n";
			}
			$css .= "}\n\n";
		}

		if ( 40 !== $section_top_tab || 40 !== $section_bottom_tab ) {
			$css .= '@media (max-width: ' . Opulentia_get_tablet_breakpoint() . "px) {\n";
			$css .= "    {$section_selector} {\n";
			if ( 40 !== $section_top_tab ) {
				$css .= "        padding-top: {$section_top_tab}px;\n";
			}
			if ( 40 !== $section_bottom_tab ) {
				$css .= "        padding-bottom: {$section_bottom_tab}px;\n";
			}
			$css .= "    }\n";
			$css .= "}\n\n";
		}

		if ( 30 !== $section_top_mob || 30 !== $section_bottom_mob ) {
			$css .= '@media (max-width: ' . Opulentia_get_mobile_breakpoint() . "px) {\n";
			$css .= "    {$section_selector} {\n";
			if ( 30 !== $section_top_mob ) {
				$css .= "        padding-top: {$section_top_mob}px;\n";
			}
			if ( 30 !== $section_bottom_mob ) {
				$css .= "        padding-bottom: {$section_bottom_mob}px;\n";
			}
			$css .= "    }\n";
			$css .= "}\n\n";
		}

		// --- Blog / Archive Post Gap ---
		$blog_gap_dt  = $this->get_responsive_value( 'spacing_blog_gap', 'desktop' );
		$blog_gap_tab = $this->get_responsive_value( 'spacing_blog_gap', 'tablet' );
		$blog_gap_mob = $this->get_responsive_value( 'spacing_blog_gap', 'mobile' );

		$blog_selector = '.blog-grid, .archive-grid, .posts-grid, [class*="__posts"]';

		if ( 32 !== $blog_gap_dt ) {
			$css .= "{$blog_selector} {\n";
			$css .= "    gap: {$blog_gap_dt}px;\n";
			$css .= "}\n\n";
		}

		if ( 24 !== $blog_gap_tab ) {
			$css .= '@media (max-width: ' . Opulentia_get_tablet_breakpoint() . "px) {\n";
			$css .= "    {$blog_selector} {\n";
			$css .= "        gap: {$blog_gap_tab}px;\n";
			$css .= "    }\n";
			$css .= "}\n\n";
		}

		if ( 16 !== $blog_gap_mob ) {
			$css .= '@media (max-width: ' . Opulentia_get_mobile_breakpoint() . "px) {\n";
			$css .= "    {$blog_selector} {\n";
			$css .= "        gap: {$blog_gap_mob}px;\n";
			$css .= "    }\n";
			$css .= "}\n\n";
		}

		// --- Widget Spacing ---
		$widget_margin = (int) get_theme_mod( 'spacing_widget_margin_bottom', 32 );
		if ( 32 !== $widget_margin ) {
			$css .= ".widget {\n";
			$css .= "    margin-bottom: {$widget_margin}px;\n";
			$css .= "}\n\n";
		}

		// --- Header Padding ---
		$header_top_dt     = $this->get_responsive_value( 'spacing_header_padding_top', 'desktop' );
		$header_top_tab    = $this->get_responsive_value( 'spacing_header_padding_top', 'tablet' );
		$header_top_mob    = $this->get_responsive_value( 'spacing_header_padding_top', 'mobile' );
		$header_bottom_dt  = $this->get_responsive_value( 'spacing_header_padding_bottom', 'desktop' );
		$header_bottom_tab = $this->get_responsive_value( 'spacing_header_padding_bottom', 'tablet' );
		$header_bottom_mob = $this->get_responsive_value( 'spacing_header_padding_bottom', 'mobile' );

		$header_selector = '.site-header, .site-header__inner, .header-main';

		if ( 20 !== $header_top_dt || 20 !== $header_bottom_dt ) {
			$css .= "{$header_selector} {\n";
			if ( 20 !== $header_top_dt ) {
				$css .= "    padding-top: {$header_top_dt}px;\n";
			}
			if ( 20 !== $header_bottom_dt ) {
				$css .= "    padding-bottom: {$header_bottom_dt}px;\n";
			}
			$css .= "}\n\n";
		}

		if ( 16 !== $header_top_tab || 16 !== $header_bottom_tab ) {
			$css .= '@media (max-width: ' . Opulentia_get_tablet_breakpoint() . "px) {\n";
			$css .= "    {$header_selector} {\n";
			if ( 16 !== $header_top_tab ) {
				$css .= "        padding-top: {$header_top_tab}px;\n";
			}
			if ( 16 !== $header_bottom_tab ) {
				$css .= "        padding-bottom: {$header_bottom_tab}px;\n";
			}
			$css .= "    }\n";
			$css .= "}\n\n";
		}

		if ( 12 !== $header_top_mob || 12 !== $header_bottom_mob ) {
			$css .= '@media (max-width: ' . Opulentia_get_mobile_breakpoint() . "px) {\n";
			$css .= "    {$header_selector} {\n";
			if ( 12 !== $header_top_mob ) {
				$css .= "        padding-top: {$header_top_mob}px;\n";
			}
			if ( 12 !== $header_bottom_mob ) {
				$css .= "        padding-bottom: {$header_bottom_mob}px;\n";
			}
			$css .= "    }\n";
			$css .= "}\n\n";
		}

		// --- Footer Padding ---
		$footer_top_dt     = $this->get_responsive_value( 'spacing_footer_padding_top', 'desktop' );
		$footer_top_tab    = $this->get_responsive_value( 'spacing_footer_padding_top', 'tablet' );
		$footer_top_mob    = $this->get_responsive_value( 'spacing_footer_padding_top', 'mobile' );
		$footer_bottom_dt  = $this->get_responsive_value( 'spacing_footer_padding_bottom', 'desktop' );
		$footer_bottom_tab = $this->get_responsive_value( 'spacing_footer_padding_bottom', 'tablet' );
		$footer_bottom_mob = $this->get_responsive_value( 'spacing_footer_padding_bottom', 'mobile' );

		$footer_selector = '.site-footer, .site-footer__inner, .footer-main';

		if ( 48 !== $footer_top_dt || 48 !== $footer_bottom_dt ) {
			$css .= "{$footer_selector} {\n";
			if ( 48 !== $footer_top_dt ) {
				$css .= "    padding-top: {$footer_top_dt}px;\n";
			}
			if ( 48 !== $footer_bottom_dt ) {
				$css .= "    padding-bottom: {$footer_bottom_dt}px;\n";
			}
			$css .= "}\n\n";
		}

		if ( 32 !== $footer_top_tab || 32 !== $footer_bottom_tab ) {
			$css .= '@media (max-width: ' . Opulentia_get_tablet_breakpoint() . "px) {\n";
			$css .= "    {$footer_selector} {\n";
			if ( 32 !== $footer_top_tab ) {
				$css .= "        padding-top: {$footer_top_tab}px;\n";
			}
			if ( 32 !== $footer_bottom_tab ) {
				$css .= "        padding-bottom: {$footer_bottom_tab}px;\n";
			}
			$css .= "    }\n";
			$css .= "}\n\n";
		}

		if ( 24 !== $footer_top_mob || 24 !== $footer_bottom_mob ) {
			$css .= '@media (max-width: ' . Opulentia_get_mobile_breakpoint() . "px) {\n";
			$css .= "    {$footer_selector} {\n";
			if ( 24 !== $footer_top_mob ) {
				$css .= "        padding-top: {$footer_top_mob}px;\n";
			}
			if ( 24 !== $footer_bottom_mob ) {
				$css .= "        padding-bottom: {$footer_bottom_mob}px;\n";
			}
			$css .= "    }\n";
			$css .= "}\n\n";
		}

		return $css;
	}

	/**
	 * Output dynamic spacing CSS.
	 */
	public function output_dynamic_css() {
		$css = $this->get_spacing_css();

		if ( empty( $css ) ) {
			return;
		}

		wp_add_inline_style( 'opulentia-style', $css );
	}
}

// -----------------------------------------------------------------------------
// Customizer Control — Responsive Spacing
// -----------------------------------------------------------------------------

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Opulentia_Responsive_Spacing_Control' ) ) {

	/**
	 * Customizer control for responsive spacing values.
	 *
	 * Renders three number inputs (desktop, tablet, mobile) in a single control row
	 * with device icon tabs.
	 */
	class Opulentia_Responsive_Spacing_Control extends WP_Customize_Control {

		/**
		 * Control type.
		 *
		 * @var string
		 */
		public $type = 'opulentia-responsive-spacing';

		/**
		 * Render the control content.
		 */
		public function render_content() {
			$devices = array(
				'desktop' => __( 'Desktop', 'opulentia' ),
				'tablet'  => __( 'Tablet', 'opulentia' ),
				'mobile'  => __( 'Mobile', 'opulentia' ),
			);

			$device_icons = array(
				'desktop' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
				'tablet'  => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12" y2="18"/></svg>',
				'mobile'  => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="7" y="2" width="10" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12" y2="18"/></svg>',
			);

			if ( empty( $this->settings ) ) {
				return;
			}

			$settings = $this->settings;
			?>
			<label>
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>
			</label>
			<div class="opulentia-responsive-spacing-wrapper" style="display: flex; gap: 8px; margin-top: 8px;">
				<?php foreach ( $devices as $device_key => $device_label ) : ?>
					<?php
					if ( ! isset( $settings[ $device_key ] ) ) {
						continue;
					}
					$setting = $settings[ $device_key ];
					$value   = $this->value( $device_key );
					?>
					<div class="opulentia-responsive-spacing-device" style="flex: 1; text-align: center;">
						<label style="display: block; margin-bottom: 4px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #888;">
							<?php if ( isset( $device_icons[ $device_key ] ) ) : ?>
								<span style="display: inline-block; vertical-align: middle;"><?php echo $device_icons[ $device_key ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<?php endif; ?>
						</label>
						<input type="number"
								style="width: 100%; text-align: center;"
								min="0" max="200" step="1"
								value="<?php echo esc_attr( $value ); ?>"
								data-device="<?php echo esc_attr( $device_key ); ?>"
								data-customize-setting-link="<?php echo esc_attr( $setting->id ); ?>" />
					</div>
				<?php endforeach; ?>
			</div>
			<?php
		}
	}
}
