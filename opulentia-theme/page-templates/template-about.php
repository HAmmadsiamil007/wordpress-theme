<?php
/**
 * Template Name: About Page
 * Description: About page template with hero, heritage section, and team
 *
 * @package Opulentia
 */

get_header();
?>

<main id="primary" class="site-main">
	<section class="page-hero">
		<div class="container">
			<h1 class="page-hero__title"><?php the_title(); ?></h1>
			<p class="page-hero__subtitle"><?php esc_html_e( 'The Story Behind the Craft', 'opulentia' ); ?></p>
		</div>
	</section>

	<?php get_template_part( 'template-parts/about' ); ?>

	<section class="values-section">
		<div class="container">
			<div class="section-header">
				<p class="section-header__subtitle"><?php esc_html_e( 'Our Values', 'opulentia' ); ?></p>
				<h2 class="section-header__title"><?php esc_html_e( 'What Defines Us', 'opulentia' ); ?></h2>
			</div>

			<div class="values-grid">
				<div class="value-item">
					<div class="value-item__icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
							<path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
							<path d="M14.31 8l5.74 9.94M9.69 8h11.48M7.38 12l5.74-9.94M9.69 16L3.95 6.06M14.31 16H2.83M16.62 12l-5.74 9.94"/>
						</svg>
					</div>
					<h3 class="value-item__title"><?php esc_html_e( 'Craftsmanship', 'opulentia' ); ?></h3>
					<p class="value-item__text"><?php esc_html_e( 'Every stitch, every cut, every detail reflects our commitment to perfection. Our artisans have perfected their craft over decades.', 'opulentia' ); ?></p>
				</div>

				<div class="value-item">
					<div class="value-item__icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
							<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
						</svg>
					</div>
					<h3 class="value-item__title"><?php esc_html_e( 'Innovation', 'opulentia' ); ?></h3>
					<p class="value-item__text"><?php esc_html_e( 'While honoring tradition, we continuously push boundaries in design, materials, and sustainable practices.', 'opulentia' ); ?></p>
				</div>

				<div class="value-item">
					<div class="value-item__icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
							<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
						</svg>
					</div>
					<h3 class="value-item__title"><?php esc_html_e( 'Passion', 'opulentia' ); ?></h3>
					<p class="value-item__text"><?php esc_html_e( 'Our love for exceptional footwear drives everything we do, from design to the final polish.', 'opulentia' ); ?></p>
				</div>

				<div class="value-item">
					<div class="value-item__icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
							<circle cx="12" cy="12" r="10"/>
							<path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
						</svg>
					</div>
					<h3 class="value-item__title"><?php esc_html_e( 'Sustainability', 'opulentia' ); ?></h3>
					<p class="value-item__text"><?php esc_html_e( 'We are committed to ethical sourcing and environmentally responsible manufacturing processes.', 'opulentia' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/features' ); ?>
</main>

<?php
get_footer();
