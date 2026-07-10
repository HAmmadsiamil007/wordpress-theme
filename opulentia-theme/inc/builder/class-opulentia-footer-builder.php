<?php
/**
 * Footer Builder — Full Suite
 *
 * Renders the site footer based on customizer settings.
 * Supports multi-column layouts (2/3/4/5 columns), widget area integration,
 * custom components (brand, social, HTML block, payment icons, copyright),
 * and three rows (above, main, below).
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Footer_Builder class.
 */
class Opulentia_Footer_Builder {

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
	 * Constructor — registers hooks for footer sections.
	 */
	private function __construct() {
		// No hook registrations needed.
		// footer.php calls Opulentia_Footer_Builder::render() directly.
	}

	// -------------------------------------------------------------------------
	// Main Render
	// -------------------------------------------------------------------------

	/**
	 * Render the full footer based on customizer settings.
	 *
	 * Called from footer.php.
	 */
	public static function render() {
		$layout  = self::get_layout();
		$classes = array(
			'site-footer',
			'site-footer--' . $layout,
			self::get_row_visibility_class( 'above' ),
			self::get_row_visibility_class( 'main' ),
			self::get_row_visibility_class( 'below' ),
		);
		?>
		<footer id="colophon" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" role="contentinfo" itemscope="itemscope" itemtype="https://schema.org/WPFooter">
			<div class="container">
				<?php do_action( 'Opulentia_footer_before' ); ?>

				<?php self::render_row( 'above' ); ?>
				<?php self::render_main_footer(); ?>
				<?php self::render_row( 'below' ); ?>

				<?php self::render_footer_bottom(); ?>

				<?php do_action( 'Opulentia_footer_after' ); ?>
			</div>
		</footer>
		<?php
	}

	/**
	 * Get the footer layout (full-width or boxed).
	 *
	 * @return string 'boxed' or 'full-width'.
	 */
	public static function get_layout() {
		$layout = Opulentia_get_option( 'footer-layout', 'boxed' );
		if ( ! in_array( $layout, array( 'boxed', 'full-width' ), true ) ) {
			$layout = 'boxed';
		}
		return $layout;
	}

	/**
	 * Get the footer column count.
	 *
	 * @return int 2, 3, 4, or 5.
	 */
	public static function get_columns() {
		$columns = (int) Opulentia_get_option( 'footer_columns', 4 );
		$columns = max( 2, min( 5, $columns ) );
		return $columns;
	}

	/**
	 * Check if a section/component is enabled.
	 *
	 * @param string $section Section ID.
	 * @return bool
	 */
	private static function section_enabled( $section ) {
		return (bool) Opulentia_get_option( "footer_show_{$section}", true );
	}

	/**
	 * Check if a row should be displayed based on device visibility.
	 *
	 * @param string $row    'above', 'main', 'below'.
	 * @param string $device 'desktop', 'tablet', 'mobile'.
	 * @return bool
	 */
	public static function is_row_visible( $row, $device = 'desktop' ) {
		$setting    = 'footer-row-' . $row . '-visibility';
		$visibility = Opulentia_get_option(
			$setting,
			array(
				'desktop' => true,
				'tablet'  => true,
				'mobile'  => true,
			)
		);
		return isset( $visibility[ $device ] ) ? (bool) $visibility[ $device ] : true;
	}

	/**
	 * Get the CSS class for row visibility.
	 *
	 * @param string $row Row name.
	 * @return string CSS class.
	 */
	private static function get_row_visibility_class( $row ) {
		$classes = array();
		foreach ( array( 'desktop', 'tablet', 'mobile' ) as $device ) {
			if ( ! self::is_row_visible( $row, $device ) ) {
				$classes[] = 'hide-footer-' . $device . '-' . $row;
			}
		}
		return implode( ' ', $classes );
	}

	// -------------------------------------------------------------------------
	// Row Rendering
	// -------------------------------------------------------------------------

	/**
	 * Render a footer row with its configured components.
	 *
	 * @param string $row Row identifier: 'above', 'below' (main is separate).
	 */
	private static function render_row( $row ) {
		$components = self::get_row_components( $row );
		if ( empty( $components ) ) {
			return;
		}

		do_action( 'Opulentia_footer_' . $row . '_before' );
		?>
		<div class="footer-row footer-row--<?php echo esc_attr( $row ); ?>">
			<div class="footer-row__inner">
				<?php
				foreach ( $components as $component ) {
					self::render_component( $component );
				}
				?>
			</div>
		</div>
		<?php
		do_action( 'Opulentia_footer_' . $row . '_after' );
	}

	/**
	 * Get the list of enabled components for a given row.
	 *
	 * @param string $row Row name.
	 * @return array Component IDs.
	 */
	private static function get_row_components( $row ) {
		$setting    = 'footer-row-' . $row . '-components';
		$defaults   = array(
			'above' => array( 'newsletter', 'trust-badges' ),
			'below' => array(),
		);
		$components = Opulentia_get_option( $setting, isset( $defaults[ $row ] ) ? $defaults[ $row ] : array() );
		return is_array( $components ) ? $components : array();
	}

	// -------------------------------------------------------------------------
	// Main Footer Area (Widget Grid)
	// -------------------------------------------------------------------------

	/**
	 * Render the main footer area with widget columns.
	 */
	private static function render_main_footer() {
		$columns = self::get_columns();
		$classes = array(
			'footer-widget-grid',
			'footer-widget-grid--' . $columns . '-cols',
		);

		do_action( 'Opulentia_footer_grid_before' );
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php
			// Column 1 is always the brand column (if enabled).
			if ( self::section_enabled( 'brand' ) ) {
				echo '<div class="footer-widget-area footer-widget-area--brand">';
				self::render_brand_column();
				echo '</div>';
			}

			// Render remaining widget areas (column 2 through N).
			$widget_columns = $columns - ( self::section_enabled( 'brand' ) ? 1 : 0 );
			for ( $i = 1; $i <= $widget_columns; $i++ ) {
				$sidebar_id          = 'footer-' . $i;
				$widget_area_visible = (bool) Opulentia_get_option( "footer_show_widget_area_{$i}", true );
				if ( ! $widget_area_visible ) {
					continue;
				}
				?>
				<div class="footer-widget-area footer-widget-area--<?php echo esc_attr( $i ); ?>">
					<?php if ( is_active_sidebar( $sidebar_id ) ) : ?>
						<?php dynamic_sidebar( $sidebar_id ); ?>
					<?php else : ?>
						<?php self::render_column_fallback( $i, $columns ); ?>
					<?php endif; ?>
				</div>
				<?php
			}
			?>
		</div>
		<?php
		do_action( 'Opulentia_footer_grid_after' );
	}

	/**
	 * Render fallback content when a widget area has no active widgets.
	 *
	 * @param int $index  Column index (1-based).
	 * @param int $total  Total columns.
	 */
	private static function render_column_fallback( $index, $total ) {
		$fallbacks   = self::get_column_fallbacks();
		$column_name = '';

		// Determine which fallback to show based on column index.
		// Brand takes column 1, so fallbacks start at 2.
		$brand_enabled = self::section_enabled( 'brand' );
		$fb_index      = $brand_enabled ? $index + 1 : $index;

		if ( isset( $fallbacks[ $fb_index ] ) ) {
			$column = $fallbacks[ $fb_index ];
			?>
			<aside class="widget">
				<h3 class="widget__title"><?php echo esc_html( $column['title'] ); ?></h3>
				<?php if ( ! empty( $column['content'] ) ) : ?>
					<?php echo wp_kses_post( $column['content'] ); ?>
				<?php elseif ( ! empty( $column['links'] ) ) : ?>
					<ul>
						<?php foreach ( $column['links'] as $link ) : ?>
							<li><a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['text'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</aside>
			<?php
		} else {
			// Empty column placeholder.
			?>
			<aside class="widget">
				<h3 class="widget__title"><?php esc_html_e( 'Widget Area', 'opulentia' ); ?></h3>
				<p style="font-size:0.875rem;color:var(--color-text-muted);">
					<?php esc_html_e( 'Add widgets in Appearance → Widgets.', 'opulentia' ); ?>
				</p>
			</aside>
			<?php
		}
	}

	/**
	 * Get fallback column data (hardcoded default content).
	 *
	 * @return array Column definitions keyed by position.
	 */
	private static function get_column_fallbacks() {
		return array(
			2 => array(
				'title' => __( 'Quick Links', 'opulentia' ),
				'links' => array(
					array(
						'text' => __( 'Home', 'opulentia' ),
						'url'  => home_url( '/' ),
					),
					array(
						'text' => __( 'Collection', 'opulentia' ),
						'url'  => home_url( '/collection' ),
					),
					array(
						'text' => __( 'About Us', 'opulentia' ),
						'url'  => home_url( '/about-us' ),
					),
					array(
						'text' => __( 'Craftsmanship', 'opulentia' ),
						'url'  => home_url( '/craftsmanship' ),
					),
					array(
						'text' => __( 'Contact', 'opulentia' ),
						'url'  => home_url( '/contact' ),
					),
				),
			),
			3 => array(
				'title' => __( 'Customer Service', 'opulentia' ),
				'links' => array(
					array(
						'text' => __( 'Shipping & Delivery', 'opulentia' ),
						'url'  => '#',
					),
					array(
						'text' => __( 'Returns & Exchanges', 'opulentia' ),
						'url'  => '#',
					),
					array(
						'text' => __( 'Size Guide', 'opulentia' ),
						'url'  => '#',
					),
					array(
						'text' => __( 'FAQs', 'opulentia' ),
						'url'  => '#',
					),
					array(
						'text' => __( 'Contact Us', 'opulentia' ),
						'url'  => '#',
					),
				),
			),
			4 => array(
				'title'   => __( 'Contact Info', 'opulentia' ),
				'content' => '
                    <ul class="footer-contact-list">
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>' . esc_html__( '123 Craftsmanship Street, Leather District', 'opulentia' ) . '</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span>+92 300 1234567</span>
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <span>info@opulentia.com</span>
                        </li>
                    </ul>
                ',
			),
			5 => array(
				'title'   => __( 'Follow Us', 'opulentia' ),
				'content' => '
                    <p style="font-size:0.9375rem;color:var(--color-text-muted);margin-bottom:1rem;">' . esc_html__( 'Stay connected for exclusive updates and style inspiration.', 'opulentia' ) . '</p>
                    <div class="footer-social">' . self::get_social_links_html() . '</div>
                ',
			),
		);
	}

	// -------------------------------------------------------------------------
	// Brand Column
	// -------------------------------------------------------------------------

	/**
	 * Render the brand column (logo, description, social).
	 */
	private static function render_brand_column() {
		?>
		<aside class="widget">
			<div class="footer-brand">
				<div class="footer-brand__logo">
					<svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
						<circle cx="50" cy="50" r="48" stroke="#c9a96e" stroke-width="1.5"/>
						<text x="50" y="55" font-family="Playfair Display, serif" font-size="24" fill="#c9a96e" text-anchor="middle" font-weight="600">SO</text>
					</svg>
					<span class="footer-brand__name"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
				</div>
				<p class="footer-brand__text">
					<?php esc_html_e( 'Premium handcrafted leather shoes designed for the modern gentleman. Timeless elegance, unmatched comfort.', 'opulentia' ); ?>
				</p>
				<?php if ( self::section_enabled( 'social' ) ) : ?>
					<div class="footer-social">
						<?php echo self::get_social_links_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>
				<?php if ( self::section_enabled( 'html_block' ) ) : ?>
					<div class="footer-html-block">
						<?php echo wp_kses_post( Opulentia_get_option( 'footer-html-block', '' ) ); ?>
					</div>
				<?php endif; ?>
			</div>
		</aside>
		<?php
	}

	// -------------------------------------------------------------------------
	// Social Links
	// -------------------------------------------------------------------------

	/**
	 * Get social links HTML string.
	 *
	 * @return string Social links HTML.
	 */
	private static function get_social_links_html() {
		$socials = array(
			'facebook'  => array(
				'url'   => get_theme_mod( 'social_facebook', '' ),
				'icon'  => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
				'label' => 'Facebook',
			),
			'instagram' => array(
				'url'   => get_theme_mod( 'social_instagram', '' ),
				'icon'  => '<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>',
				'label' => 'Instagram',
			),
			'twitter'   => array(
				'url'   => get_theme_mod( 'social_twitter', '' ),
				'icon'  => '<path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>',
				'label' => 'Twitter',
			),
			'pinterest' => array(
				'url'   => get_theme_mod( 'social_pinterest', '' ),
				'icon'  => '<path d="M12 0C5.37 0 0 5.37 0 12c0 5.08 3.15 9.42 7.59 11.18-.1-.94-.2-2.39.04-3.42.22-.93 1.41-5.97 1.41-5.97s-.36-.72-.36-1.78c0-1.66.96-2.9 2.16-2.9 1.02 0 1.51.77 1.51 1.68 0 1.03-.65 2.56-.99 3.98-.28 1.19.6 2.16 1.77 2.16 2.13 0 3.76-2.24 3.76-5.49 0-2.87-2.06-4.87-5-4.87-3.41 0-5.41 2.56-5.41 5.21 0 1.03.4 2.13.89 2.73a.36.36 0 0 1 .08.34c-.09.37-.29 1.19-.33 1.36-.05.22-.17.27-.4.16-1.49-.69-2.42-2.88-2.42-4.63 0-3.77 2.74-7.24 7.89-7.24 4.14 0 7.36 2.95 7.36 6.89 0 4.11-2.59 7.42-6.19 7.42-1.21 0-2.35-.63-2.74-1.37l-.75 2.85c-.27 1.04-1 2.35-1.49 3.14C9.57 23.81 10.75 24 12 24c6.63 0 12-5.37 12-12S18.63 0 12 0z"/>',
				'label' => 'Pinterest',
			),
			'youtube'   => array(
				'url'   => get_theme_mod( 'social_youtube', '' ),
				'icon'  => '<path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>',
				'label' => 'YouTube',
			),
		);

		$html = '';
		foreach ( $socials as $name => $data ) {
			if ( empty( $data['url'] ) ) {
				continue;
			}
			$html .= sprintf(
				'<a href="%s" target="_blank" rel="noopener noreferrer" class="footer-social__link" aria-label="%s">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">%s</svg>
                </a>',
				esc_url( $data['url'] ),
				esc_attr( $data['label'] ),
				$data['icon'] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
		}

		return $html;
	}

	// -------------------------------------------------------------------------
	// Component Renderers
	// -------------------------------------------------------------------------

	/**
	 * Render a component by ID.
	 *
	 * @param string $component Component ID.
	 */
	private static function render_component( $component ) {
		switch ( $component ) {
			case 'newsletter':
				self::render_newsletter();
				break;
			case 'trust-badges':
				self::render_trust_badges();
				break;
			case 'html-block':
				self::render_html_block();
				break;
			case 'copyright':
				self::render_copyright();
				break;
			case 'payment-icons':
				self::render_payment_icons();
				break;
			case 'social-icons':
				self::render_social_icons();
				break;
		}
	}

	/**
	 * Render the newsletter signup section.
	 */
	private static function render_newsletter() {
		if ( ! self::section_enabled( 'newsletter' ) ) {
			return;
		}
		?>
		<section class="footer-newsletter">
			<div class="footer-newsletter__content">
				<div class="footer-newsletter__text">
					<h3 class="footer-newsletter__title"><?php esc_html_e( 'JOIN OUR EXCLUSIVE LIST', 'opulentia' ); ?></h3>
					<p class="footer-newsletter__subtitle"><?php esc_html_e( 'Subscribe for early access to new collections, exclusive offers, and style insights.', 'opulentia' ); ?></p>
				</div>
				<div class="footer-newsletter__form">
					<form class="footer-newsletter__form-inner" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
						<input type="hidden" name="action" value="Opulentia_newsletter_signup">
						<?php Opulentia_Security::nonce_field( 'Opulentia_newsletter', 'Opulentia_newsletter_nonce' ); ?>
						<div class="footer-newsletter__fields">
							<input type="text" name="fname" class="footer-newsletter__input" placeholder="<?php esc_attr_e( 'First name', 'opulentia' ); ?>">
							<input type="email" name="email" class="footer-newsletter__input" placeholder="<?php esc_attr_e( 'Enter your email', 'opulentia' ); ?>" required>
						</div>
						<button type="submit" class="btn btn--primary btn--small"><?php esc_html_e( 'SUBSCRIBE', 'opulentia' ); ?></button>
					</form>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Render the trust badges section.
	 */
	private static function render_trust_badges() {
		if ( ! self::section_enabled( 'trust_badges' ) ) {
			return;
		}
		?>
		<section class="footer-trust-badges">
			<div class="footer-trust-badges__grid">
				<div class="footer-trust-badge">
					<svg class="footer-trust-badge__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
						<rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
					</svg>
					<div class="footer-trust-badge__content">
						<h4 class="footer-trust-badge__title"><?php esc_html_e( 'Secure Payment', 'opulentia' ); ?></h4>
						<p class="footer-trust-badge__text"><?php esc_html_e( '100% secure & trusted checkout.', 'opulentia' ); ?></p>
					</div>
				</div>
				<div class="footer-trust-badge">
					<svg class="footer-trust-badge__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
						<polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
					</svg>
					<div class="footer-trust-badge__content">
						<h4 class="footer-trust-badge__title"><?php esc_html_e( 'Easy Returns', 'opulentia' ); ?></h4>
						<p class="footer-trust-badge__text"><?php esc_html_e( 'Hassle-free returns within 14 days.', 'opulentia' ); ?></p>
					</div>
				</div>
				<div class="footer-trust-badge">
					<svg class="footer-trust-badge__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
						<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
					</svg>
					<div class="footer-trust-badge__content">
						<h4 class="footer-trust-badge__title"><?php esc_html_e( 'Customer Support', 'opulentia' ); ?></h4>
						<p class="footer-trust-badge__text"><?php esc_html_e( '24/7 support for all your queries.', 'opulentia' ); ?></p>
					</div>
				</div>
				<div class="footer-trust-badge">
					<svg class="footer-trust-badge__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
						<path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"/><path d="M4 6v12c0 1.1.9 2 2 2h14v-4"/><path d="M18 12a2 2 0 0 0-2 2c0 1.1.9 2 2 2h4v-4h-4z"/>
					</svg>
					<div class="footer-trust-badge__content">
						<h4 class="footer-trust-badge__title"><?php esc_html_e( 'Exclusive Offers', 'opulentia' ); ?></h4>
						<p class="footer-trust-badge__text"><?php esc_html_e( 'Sign up to get special discounts.', 'opulentia' ); ?></p>
					</div>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Render the HTML block component.
	 */
	private static function render_html_block() {
		$html = Opulentia_get_option( 'footer-html-block', '' );
		if ( empty( $html ) ) {
			return;
		}
		?>
		<div class="footer-html-block footer-html-block--row">
			<?php echo wp_kses_post( $html ); ?>
		</div>
		<?php
	}

	/**
	 * Render copyright text.
	 */
	private static function render_copyright() {
		?>
		<p class="footer-copyright">
			<?php echo wp_kses_post( Opulentia_get_option( 'footer_copyright', sprintf( __( '&copy; %d Opulentia. All rights reserved.', 'opulentia' ), gmdate( 'Y' ) ) ) ); ?>
		</p>
		<?php
	}

	/**
	 * Render payment icons.
	 */
	private static function render_payment_icons() {
		if ( ! self::section_enabled( 'payment_icons' ) ) {
			return;
		}
		?>
		<div class="footer-payments">
			<span class="footer-payments__label"><?php esc_html_e( 'We Accept:', 'opulentia' ); ?></span>
			<div class="footer-payments__icons">
				<span class="payment-icon">VISA</span>
				<span class="payment-icon">MC</span>
				<span class="payment-icon">AMEX</span>
				<span class="payment-icon">COD</span>
			</div>
		</div>
		<?php
	}

	/**
	 * Render social icons as a standalone row component.
	 */
	private static function render_social_icons() {
		$html = self::get_social_links_html();
		if ( empty( $html ) ) {
			return;
		}
		?>
		<div class="footer-social footer-social--row">
			<?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php
	}

	// -------------------------------------------------------------------------
	// Footer Bottom
	// -------------------------------------------------------------------------

	/**
	 * Render the footer bottom bar (copyright & payment icons).
	 */
	private static function render_footer_bottom() {
		do_action( 'Opulentia_footer_bottom_bar_before' );
		?>
		<div class="footer-bottom">
			<?php self::render_copyright(); ?>
			<?php self::render_payment_icons(); ?>
		</div>
		<?php
		do_action( 'Opulentia_footer_bottom_bar_after' );
	}
}
