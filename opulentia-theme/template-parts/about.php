<?php
/**
 * Template part for displaying about section
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$about_title    = get_theme_mod( 'about_title', __( 'Our Heritage', 'opulentia' ) );
$about_subtitle = get_theme_mod( 'about_subtitle', __( 'A Legacy of Excellence', 'opulentia' ) );
$about_text     = get_theme_mod( 'about_text', __( 'Born from a passion for exceptional footwear, Opulentia represents the pinnacle of Italian craftsmanship. Each pair is meticulously handcrafted using time-honored techniques passed down through generations.', 'opulentia' ) );
$about_image    = get_theme_mod( 'about_image', '' );
?>

<section class="about-section">
	<div class="container">
		<div class="about-grid">
			<div class="about-content">
				<p class="about-content__subtitle"><?php echo esc_html( $about_subtitle ); ?></p>
				<h2 class="about-content__title"><?php echo esc_html( $about_title ); ?></h2>
				<p class="about-content__text"><?php echo esc_html( $about_text ); ?></p>

				<div class="about-features">
					<div class="about-feature">
						<div class="about-feature__icon">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
								<path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
								<path d="M9 12l2 2 4-4"/>
							</svg>
						</div>
						<div class="about-feature__content">
							<h4 class="about-feature__title"><?php esc_html_e( 'Since 1985', 'opulentia' ); ?></h4>
							<p class="about-feature__text"><?php esc_html_e( 'Three decades of excellence', 'opulentia' ); ?></p>
						</div>
					</div>

					<div class="about-feature">
						<div class="about-feature__icon">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
								<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
							</svg>
						</div>
						<div class="about-feature__content">
							<h4 class="about-feature__title"><?php esc_html_e( 'Handmade', 'opulentia' ); ?></h4>
							<p class="about-feature__text"><?php esc_html_e( 'Crafted with precision', 'opulentia' ); ?></p>
						</div>
					</div>

					<div class="about-feature">
						<div class="about-feature__icon">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
								<circle cx="12" cy="12" r="10"/>
								<path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
							</svg>
						</div>
						<div class="about-feature__content">
							<h4 class="about-feature__title"><?php esc_html_e( 'Italian Design', 'opulentia' ); ?></h4>
							<p class="about-feature__text"><?php esc_html_e( 'Authentic Italian style', 'opulentia' ); ?></p>
						</div>
					</div>

					<div class="about-feature">
						<div class="about-feature__icon">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
								<path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
							</svg>
						</div>
						<div class="about-feature__content">
							<h4 class="about-feature__title"><?php esc_html_e( 'Free Shipping', 'opulentia' ); ?></h4>
							<p class="about-feature__text"><?php esc_html_e( 'Worldwide delivery', 'opulentia' ); ?></p>
						</div>
					</div>
				</div>
			</div>

			<div class="about-image">
				<?php if ( $about_image ) : ?>
					<img src="<?php echo esc_url( $about_image ); ?>" alt="<?php echo esc_attr( $about_title ); ?>">
				<?php else : ?>
					<svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect width="400" height="300" fill="#1a1a1a"/>
						<path d="M50 250 Q200 100 350 250" stroke="#8B4513" stroke-width="3" fill="none"/>
						<ellipse cx="200" cy="260" rx="150" ry="20" fill="#8B4513" opacity="0.3"/>
						<path d="M80 230 Q200 120 320 230" fill="#A0522D"/>
						<path d="M120 200 Q200 140 280 200" fill="#8B4513"/>
					</svg>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
