<?php
/**
 * Free Shipping Progress Bar
 *
 * Displays a progress bar on cart/checkout/mini-cart showing
 * customers how much more they need for free shipping.
 *
 * @package Opulentia
 * @subpackage Modules
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Opulentia_Free_Shipping_Bar
 */
class Opulentia_Free_Shipping_Bar {

	/**
	 * Singleton instance.
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return self
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_inline_css' ) );
		add_action( 'woocommerce_before_cart', array( $this, 'render_shipping_bar' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'render_shipping_bar' ) );
	}

	/**
	 * Init hook.
	 */
	public function init() {
		if ( ! $this->is_woocommerce_active() ) {
			return;
		}

		if ( Opulentia_get_option( 'shipping_bar_show_mini', true ) ) {
			add_action( 'woocommerce_before_mini_cart_contents', array( $this, 'render_mini_cart_bar' ) );
		}
	}

	/**
	 * Check if WooCommerce is active.
	 *
	 * @return bool
	 */
	private function is_woocommerce_active() {
		return function_exists( 'WC' );
	}

	/**
	 * Register customizer controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager.
	 */
	public function customize_register( $wp_customize ) {
		$wp_customize->add_section(
			'opulentia_free_shipping',
			array(
				'title'    => esc_html__( 'Free Shipping Bar', 'opulentia' ),
				'panel'    => 'opulentia_global_settings',
				'priority' => 60,
			)
		);

		$wp_customize->add_setting(
			'shipping_bar_enable',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'type'              => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			'shipping_bar_enable',
			array(
				'label'   => esc_html__( 'Enable free shipping bar', 'opulentia' ),
				'section' => 'opulentia_free_shipping',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'shipping_bar_fallback_amount',
			array(
				'default'           => 100,
				'sanitize_callback' => 'absint',
				'type'              => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			'shipping_bar_fallback_amount',
			array(
				'label'       => esc_html__( 'Fallback free shipping amount', 'opulentia' ),
				'section'     => 'opulentia_free_shipping',
				'type'        => 'number',
				'input_attrs'  => array(
					'min'  => 0,
					'step' => 1,
				),
			)
		);

		$wp_customize->add_setting(
			'shipping_bar_progress_text',
			array(
				'default'           => 'Add {amount} more for free shipping!',
				'sanitize_callback' => 'wp_kses_post',
				'type'              => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			'shipping_bar_progress_text',
			array(
				'label'   => esc_html__( 'Progress message', 'opulentia' ),
				'section' => 'opulentia_free_shipping',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'shipping_bar_success_text',
			array(
				'default'           => 'Congratulations! You\'ve earned free shipping!',
				'sanitize_callback' => 'wp_kses_post',
				'type'              => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			'shipping_bar_success_text',
			array(
				'label'   => esc_html__( 'Success message', 'opulentia' ),
				'section' => 'opulentia_free_shipping',
				'type'    => 'text',
			)
		);

		$wp_customize->add_setting(
			'shipping_bar_bg',
			array(
				'default'           => '#b8860b',
				'sanitize_callback' => 'sanitize_hex_color',
				'type'              => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'shipping_bar_bg',
				array(
					'label'   => esc_html__( 'Bar fill color', 'opulentia' ),
					'section' => 'opulentia_free_shipping',
				)
			)
		);

		$wp_customize->add_setting(
			'shipping_bar_track',
			array(
				'default'           => '#333333',
				'sanitize_callback' => 'sanitize_hex_color',
				'type'              => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'shipping_bar_track',
				array(
					'label'   => esc_html__( 'Track color', 'opulentia' ),
					'section' => 'opulentia_free_shipping',
				)
			)
		);

		$wp_customize->add_setting(
			'shipping_bar_text_color',
			array(
				'default'           => '#f5f5f5',
				'sanitize_callback' => 'sanitize_hex_color',
				'type'              => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'shipping_bar_text_color',
				array(
					'label'   => esc_html__( 'Text color', 'opulentia' ),
					'section' => 'opulentia_free_shipping',
				)
			)
		);

		$wp_customize->add_setting(
			'shipping_bar_height',
			array(
				'default'           => 8,
				'sanitize_callback' => 'absint',
				'type'              => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			'shipping_bar_height',
			array(
				'label'       => esc_html__( 'Bar height (px)', 'opulentia' ),
				'section'     => 'opulentia_free_shipping',
				'type'        => 'range',
				'input_attrs' => array(
					'min'  => 4,
					'max'  => 20,
					'step' => 1,
				),
			)
		);

		$wp_customize->add_setting(
			'shipping_bar_show_cart',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'type'              => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			'shipping_bar_show_cart',
			array(
				'label'   => esc_html__( 'Show on cart page', 'opulentia' ),
				'section' => 'opulentia_free_shipping',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'shipping_bar_show_checkout',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'type'              => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			'shipping_bar_show_checkout',
			array(
				'label'   => esc_html__( 'Show on checkout page', 'opulentia' ),
				'section' => 'opulentia_free_shipping',
				'type'    => 'checkbox',
			)
		);

		$wp_customize->add_setting(
			'shipping_bar_show_mini',
			array(
				'default'           => true,
				'sanitize_callback' => 'wp_validate_boolean',
				'type'              => 'theme_mod',
			)
		);

		$wp_customize->add_control(
			'shipping_bar_show_mini',
			array(
				'label'   => esc_html__( 'Show in mini-cart', 'opulentia' ),
				'section' => 'opulentia_free_shipping',
				'type'    => 'checkbox',
			)
		);
	}

	/**
	 * Enqueue inline CSS.
	 */
	public function enqueue_inline_css() {
		if ( ! $this->is_woocommerce_active() ) {
			return;
		}

		if ( ! Opulentia_get_option( 'shipping_bar_enable', true ) ) {
			return;
		}

		$css = '
.op-free-shipping-bar {
    padding: 16px;
    margin: 10px 0;
    border-radius: 8px;
    background: var(--color-secondary-dark, #111);
    border: 1px solid var(--color-border, #333);
    text-align: center;
}
.op-free-shipping-bar__message {
    font-family: var(--font-body, Inter);
    font-size: 0.85rem;
    color: var(--bar-text, #f5f5f5);
    margin-bottom: 10px;
}
.op-free-shipping-bar__track {
    height: var(--bar-height, 8px);
    background: var(--bar-track, #333);
    border-radius: 10px;
    overflow: hidden;
}
.op-free-shipping-bar__fill {
    height: 100%;
    background: var(--bar-bg, #b8860b);
    border-radius: 10px;
    transition: width 0.5s ease;
}
.op-free-shipping-bar--mini {
    padding: 10px;
    margin: 5px 0;
}
.op-free-shipping-bar--mini .op-free-shipping-bar__message {
    font-size: 0.75rem;
}
.op-free-shipping-bar--mini .op-free-shipping-bar__track {
    height: 4px;
}';

		wp_add_inline_style( 'opulentia-main', wp_strip_all_tags( $css ) );
	}

	/**
	 * Get the free shipping threshold amount.
	 *
	 * @return float
	 */
	private function get_free_shipping_threshold() {
		$threshold = floatval( Opulentia_get_option( 'shipping_bar_fallback_amount', 100 ) );

		if ( ! function_exists( 'WC' ) || ! WC()->session ) {
			return $threshold;
		}

		$zones = WC_Shipping_Zones::get_zones();

		foreach ( $zones as $zone_id => $zone ) {
			$shipping_methods = $zone['shipping_methods'];

			foreach ( $shipping_methods as $method ) {
				if ( 'free_shipping' === $method->id ) {
					$opts = $method->instance_settings;

					if ( ! empty( $opts['requires'] ) && in_array( $opts['requires'], array( 'min_amount', 'either', 'both' ), true ) ) {
						$min_amount = ! empty( $opts['min_amount'] ) ? floatval( $opts['min_amount'] ) : $threshold;
						return $min_amount;
					}
				}
			}
		}

		return $threshold;
	}

	/**
	 * Render the free shipping progress bar on cart/checkout pages.
	 */
	public function render_shipping_bar() {
		if ( ! $this->is_woocommerce_active() || ! WC()->cart ) {
			return;
		}

		if ( WC()->cart->is_empty() ) {
			return;
		}

		if ( ! Opulentia_get_option( 'shipping_bar_enable', true ) ) {
			return;
		}

		$threshold = $this->get_free_shipping_threshold();
		$total     = WC()->cart->get_subtotal();

		if ( $total >= $threshold ) {
			$percent = 100;
			$message = Opulentia_get_option( 'shipping_bar_success_text', 'Congratulations! You\'ve earned free shipping!' );
		} else {
			$percent = min( ( $total / $threshold ) * 100, 99 );
			$needed  = wc_price( $threshold - $total );
			$message = str_replace(
				'{amount}',
				$needed,
				Opulentia_get_option( 'shipping_bar_progress_text', 'Add {amount} more for free shipping!' )
			);
		}

		$bg_color    = Opulentia_get_option( 'shipping_bar_bg', '#b8860b' );
		$track_color = Opulentia_get_option( 'shipping_bar_track', '#333' );
		$text_color  = Opulentia_get_option( 'shipping_bar_text_color', '#f5f5f5' );
		$height      = intval( Opulentia_get_option( 'shipping_bar_height', 8 ) );
		$show_cart   = Opulentia_get_option( 'shipping_bar_show_cart', true );
		$show_check  = Opulentia_get_option( 'shipping_bar_show_checkout', true );

		$current_filter = current_filter();

		if ( 'woocommerce_before_cart' === $current_filter && ! $show_cart ) {
			return;
		}

		if ( 'woocommerce_before_checkout_form' === $current_filter && ! $show_check ) {
			return;
		}

		echo '<div class="op-free-shipping-bar" style="--bar-bg:' . esc_attr( $bg_color ) . ';--bar-track:' . esc_attr( $track_color ) . ';--bar-text:' . esc_attr( $text_color ) . ';--bar-height:' . esc_attr( $height ) . 'px">';
		echo '<div class="op-free-shipping-bar__message">' . wp_kses_post( $message ) . '</div>';
		echo '<div class="op-free-shipping-bar__track"><div class="op-free-shipping-bar__fill" style="width:' . floatval( $percent ) . '%"></div></div>';
		echo '</div>';
	}

	/**
	 * Render a slim progress bar for the mini-cart.
	 */
	public function render_mini_cart_bar() {
		if ( ! $this->is_woocommerce_active() || ! WC()->cart ) {
			return;
		}

		if ( WC()->cart->is_empty() ) {
			return;
		}

		if ( ! Opulentia_get_option( 'shipping_bar_enable', true ) ) {
			return;
		}

		$threshold = $this->get_free_shipping_threshold();
		$total     = WC()->cart->get_subtotal();

		if ( $total >= $threshold ) {
			$percent = 100;
			$message = Opulentia_get_option( 'shipping_bar_success_text', 'Congratulations! You\'ve earned free shipping!' );
		} else {
			$percent = min( ( $total / $threshold ) * 100, 99 );
			$needed  = wc_price( $threshold - $total );
			$message = str_replace(
				'{amount}',
				$needed,
				Opulentia_get_option( 'shipping_bar_progress_text', 'Add {amount} more for free shipping!' )
			);
		}

		$bg_color    = Opulentia_get_option( 'shipping_bar_bg', '#b8860b' );
		$track_color = Opulentia_get_option( 'shipping_bar_track', '#333' );
		$text_color  = Opulentia_get_option( 'shipping_bar_text_color', '#f5f5f5' );

		echo '<div class="op-free-shipping-bar op-free-shipping-bar--mini" style="--bar-bg:' . esc_attr( $bg_color ) . ';--bar-track:' . esc_attr( $track_color ) . ';--bar-text:' . esc_attr( $text_color ) . '">';
		echo '<div class="op-free-shipping-bar__message">' . wp_kses_post( $message ) . '</div>';
		echo '<div class="op-free-shipping-bar__track"><div class="op-free-shipping-bar__fill" style="width:' . floatval( $percent ) . '%"></div></div>';
		echo '</div>';
	}
}