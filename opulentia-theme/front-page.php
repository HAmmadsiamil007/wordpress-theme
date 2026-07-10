<?php
/**
 * The front page template - Premium Luxury Design
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<?php do_action( 'Opulentia_front_page_before' ); ?>
<?php do_action( 'Opulentia_content_top' ); ?>

<!-- Hero Section -->
<section class="hero" id="hero">
	<?php
	$hero_bg = get_theme_mod( 'hero_background', '' );
	if ( $hero_bg ) :
		?>
		<img class="hero__background" src="<?php echo esc_url( $hero_bg ); ?>" alt="" loading="eager">
	<?php endif; ?>

	<div class="hero__overlay"></div>

	<div class="hero__content">
		<div class="hero__logo">
			<svg class="hero__logo-icon" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
				<circle cx="50" cy="50" r="48" stroke="#c9a96e" stroke-width="1.5"/>
				<text x="50" y="55" font-family="Playfair Display, serif" font-size="24" fill="#c9a96e" text-anchor="middle" font-weight="600">SO</text>
			</svg>
			<span class="hero__logo-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
			<span class="hero__logo-tagline"><?php esc_html_e( 'CRAFTED FROM ORIGIN. MADE TO LAST.', 'opulentia' ); ?></span>
		</div>

		<h1 class="hero__title">
			<?php echo esc_html( get_theme_mod( 'hero_title', 'TIMELESS ELEGANCE. UNMATCHED COMFORT.' ) ); ?>
		</h1>

		<p class="hero__subtitle">
			<?php echo esc_html( get_theme_mod( 'hero_subtitle', 'Premium handcrafted shoes designed for the modern gentleman.' ) ); ?>
		</p>

		<div class="hero__buttons">
			<a href="<?php echo esc_url( get_theme_mod( 'hero_button_1_url', '#collection' ) ); ?>" class="btn btn--primary">
				<?php echo esc_html( get_theme_mod( 'hero_button_1_text', 'EXPLORE COLLECTION' ) ); ?>
			</a>
			<a href="<?php echo esc_url( get_theme_mod( 'hero_button_2_url', '#about' ) ); ?>" class="btn btn--outline">
				<?php echo esc_html( get_theme_mod( 'hero_button_2_text', 'OUR STORY' ) ); ?>
			</a>
		</div>

		<div class="hero__scroll-indicator">
			<span><?php esc_html_e( 'SCROLL TO EXPLORE', 'opulentia' ); ?></span>
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<path d="M12 5v14M5 12l7 7 7-7"/>
			</svg>
		</div>
	</div>
</section>

<!-- Features Bar -->
<section class="features-bar">
	<div class="container">
		<div class="features-bar__grid">
			<div class="feature-item">
				<svg class="feature-item__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
					<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
					<line x1="7" y1="7" x2="7.01" y2="7"/>
				</svg>
				<div class="feature-item__content">
					<h3 class="feature-item__title"><?php esc_html_e( 'Premium Materials', 'opulentia' ); ?></h3>
					<p class="feature-item__text"><?php esc_html_e( 'Finest quality leather sourced ethically.', 'opulentia' ); ?></p>
				</div>
			</div>

			<div class="feature-item">
				<svg class="feature-item__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
					<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
				</svg>
				<div class="feature-item__content">
					<h3 class="feature-item__title"><?php esc_html_e( 'Expert Craftsmanship', 'opulentia' ); ?></h3>
					<p class="feature-item__text"><?php esc_html_e( 'Handcrafted by skilled artisans.', 'opulentia' ); ?></p>
				</div>
			</div>

			<div class="feature-item">
				<svg class="feature-item__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
					<circle cx="12" cy="12" r="10"/>
					<polyline points="12 6 12 12 16 14"/>
				</svg>
				<div class="feature-item__content">
					<h3 class="feature-item__title"><?php esc_html_e( 'Timeless Designs', 'opulentia' ); ?></h3>
					<p class="feature-item__text"><?php esc_html_e( 'Classic styles for every occasion.', 'opulentia' ); ?></p>
				</div>
			</div>

			<div class="feature-item">
				<svg class="feature-item__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
					<circle cx="12" cy="12" r="10"/>
					<path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
				</svg>
				<div class="feature-item__content">
					<h3 class="feature-item__title"><?php esc_html_e( 'Worldwide Shipping', 'opulentia' ); ?></h3>
					<p class="feature-item__text"><?php esc_html_e( 'Delivered to your doorstep anywhere.', 'opulentia' ); ?></p>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Collection Section -->
<section class="collection-section" id="collection">
	<div class="container">
		<div class="section-header">
			<p class="section-subtitle"><?php esc_html_e( 'Our Collection', 'opulentia' ); ?></p>
			<div class="gold-line"></div>
			<h2 class="section-title"><?php esc_html_e( 'CLASSIC. REFINED. ICONIC.', 'opulentia' ); ?></h2>
			<p class="section-description">
				<?php esc_html_e( 'Discover our range of meticulously handcrafted shoes, made for those who value quality and sophistication.', 'opulentia' ); ?>
			</p>
		</div>

		<div class="product-grid">
			<?php
			if ( class_exists( 'WooCommerce' ) ) :
				$args = array(
					'post_type'      => 'product',
					'posts_per_page' => 4,
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
				);

				$products = new WP_Query( $args );

				if ( $products->have_posts() ) :
					while ( $products->have_posts() ) :
						$products->the_post();
						Opulentia_product_card( get_the_ID() );
					endwhile;
					wp_reset_postdata();
				endif;
			else :
				// Fallback products for demo purposes
				$demo_products = array(
					array(
						'name'        => __( 'CLASSIC OXFORD', 'opulentia' ),
						'description' => __( 'Dark Brown Calf Leather', 'opulentia' ),
						'price'       => 'PKR 48,500',
						'image'       => 'https://images.unsplash.com/photo-1614252369475-531eba835eb1?w=400&h=400&fit=crop',
					),
					array(
						'name'        => __( 'CAP TOE DERBY', 'opulentia' ),
						'description' => __( 'Dark Brown Calf Leather', 'opulentia' ),
						'price'       => 'PKR 46,500',
						'image'       => 'https://images.unsplash.com/photo-1608256246200-53e635b5b65f?w=400&h=400&fit=crop',
					),
					array(
						'name'        => __( 'PENNY LOAFER', 'opulentia' ),
						'description' => __( 'Tan Brown Leather', 'opulentia' ),
						'price'       => 'PKR 43,500',
						'image'       => 'https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=400&h=400&fit=crop',
					),
					array(
						'name'        => __( 'MONK STRAP', 'opulentia' ),
						'description' => __( 'Dark Brown Calf Leather', 'opulentia' ),
						'price'       => 'PKR 47,500',
						'image'       => 'https://images.unsplash.com/photo-1611074022320-4bd919e44846?w=400&h=400&fit=crop',
					),
				);

				foreach ( $demo_products as $product ) :
					?>
					<div class="product-card">
						<div class="product-card__image">
							<img src="<?php echo esc_url( $product['image'] ); ?>" alt="<?php echo esc_attr( $product['name'] ); ?>" loading="lazy">
						</div>
						<div class="product-card__content">
							<h3 class="product-card__title"><?php echo esc_html( $product['name'] ); ?></h3>
							<p class="product-card__description"><?php echo esc_html( $product['description'] ); ?></p>
							<div class="product-card__price"><?php echo esc_html( $product['price'] ); ?></div>
							<a href="#" class="product-card__link">
								<?php esc_html_e( 'Shop Now', 'opulentia' ); ?>
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<path d="M5 12h14M12 5l7 7-7 7"/>
								</svg>
							</a>
						</div>
					</div>
					<?php
				endforeach;
			endif;
			?>
		</div>

		<div class="text-center">
			<a href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/collection' ) ); ?>" class="btn btn--outline-dark">
				<?php esc_html_e( 'View All Collection', 'opulentia' ); ?>
			</a>
		</div>
	</div>
</section>

<!-- Elite Collection Section -->
<section class="elite-collection">
	<div class="container">
		<div class="section-header">
			<p class="section-subtitle"><?php esc_html_e( 'Exclusive Selection', 'opulentia' ); ?></p>
			<div class="gold-line"></div>
			<h2 class="section-title"><?php esc_html_e( 'THE ELITE COLLECTION', 'opulentia' ); ?></h2>
			<p class="section-description">
				<?php esc_html_e( 'Handpicked premium designs for the discerning gentleman who accepts nothing less than perfection.', 'opulentia' ); ?>
			</p>
		</div>

		<div class="elite-collection__grid">
			<div class="elite-item">
				<div class="elite-item__number">01</div>
				<div class="elite-item__image">
					<img src="https://images.unsplash.com/photo-1614252369475-531eba835eb1?w=600&h=800&fit=crop" alt="Oxford Derby" loading="lazy">
				</div>
				<div class="elite-item__content">
					<h3 class="elite-item__title">OXFORD DERBY</h3>
					<p class="elite-item__text">The cornerstone of formal footwear, reimagined with modern comfort.</p>
					<span class="elite-item__price">PKR 52,000</span>
				</div>
			</div>

			<div class="elite-item">
				<div class="elite-item__number">02</div>
				<div class="elite-item__image">
					<img src="https://images.unsplash.com/photo-1608256246200-53e635b5b65f?w=600&h=800&fit=crop" alt="Chelsea Boot" loading="lazy">
				</div>
				<div class="elite-item__content">
					<h3 class="elite-item__title">CHELSEA BOOT</h3>
					<p class="elite-item__text">Versatile elegance that transitions from boardroom to evening.</p>
					<span class="elite-item__price">PKR 55,000</span>
				</div>
			</div>

			<div class="elite-item">
				<div class="elite-item__number">03</div>
				<div class="elite-item__image">
					<img src="https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=600&h=800&fit=crop" alt="Italian Loafer" loading="lazy">
				</div>
				<div class="elite-item__content">
					<h3 class="elite-item__title">ITALIAN LOAFER</h3>
					<p class="elite-item__text">Effortless sophistication with hand-stitched Italian leather.</p>
					<span class="elite-item__price">PKR 48,000</span>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Brand Story Section -->
<section class="brand-story" id="about">
	<div class="container">
		<div class="brand-story__grid">
			<div class="brand-story__image">
				<?php
				$about_image = get_theme_mod( 'about_image', '' );
				if ( $about_image ) :
					?>
					<img src="<?php echo esc_url( $about_image ); ?>" alt="<?php esc_attr_e( 'Craftsmanship', 'opulentia' ); ?>" loading="lazy">
				<?php else : ?>
					<img src="https://images.unsplash.com/photo-1473188588951-666fce8e7c68?w=800&h=1000&fit=crop" alt="Artisan Craftsmanship" loading="lazy">
				<?php endif; ?>
				<div class="brand-story__image-overlay">
					<span class="brand-story__year">EST. 2019</span>
				</div>
			</div>

			<div class="brand-story__content">
				<p class="brand-story__subtitle">
					<?php echo esc_html( get_theme_mod( 'about_subtitle', 'OUR HERITAGE' ) ); ?>
				</p>

				<h2 class="brand-story__title">
					<?php echo esc_html( get_theme_mod( 'about_title', 'BUILT FROM HERITAGE. PERFECTED OVER TIME.' ) ); ?>
				</h2>

				<p class="brand-story__text">
					<?php echo esc_html( get_theme_mod( 'about_text', 'At Opulentia, every pair is a testament to true craftsmanship. From the finest materials to the smallest details, we create shoes that stand the test of time.' ) ); ?>
				</p>

				<div class="brand-story__stats">
					<div class="brand-story__stat">
						<span class="brand-story__stat-number">500+</span>
						<span class="brand-story__stat-label">Happy Clients</span>
					</div>
					<div class="brand-story__stat">
						<span class="brand-story__stat-number">100%</span>
						<span class="brand-story__stat-label">Italian Leather</span>
					</div>
					<div class="brand-story__stat">
						<span class="brand-story__stat-number">50+</span>
						<span class="brand-story__stat-label">Unique Designs</span>
					</div>
				</div>

				<a href="<?php echo esc_url( home_url( '/about-us' ) ); ?>" class="btn btn--primary">
					<?php esc_html_e( 'Discover Our Story', 'opulentia' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>

<!-- Testimonials Section -->
<section class="testimonials">
	<div class="container">
		<div class="section-header">
			<p class="section-subtitle"><?php esc_html_e( 'Client Stories', 'opulentia' ); ?></p>
			<div class="gold-line"></div>
			<h2 class="section-title"><?php esc_html_e( 'WHAT OUR CLIENTS SAY', 'opulentia' ); ?></h2>
		</div>

		<div class="testimonials__grid">
			<div class="testimonial-card">
				<div class="testimonial-card__stars">
					<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
					<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
					<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
					<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
					<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
				</div>
				<p class="testimonial-card__text">
					"The attention to detail is remarkable. These shoes are not just footwear; they're a statement of sophistication."
				</p>
				<div class="testimonial-card__author">
					<div class="testimonial-card__avatar">AH</div>
					<div class="testimonial-card__info">
						<h4 class="testimonial-card__name">Ahmed Hassan</h4>
						<p class="testimonial-card__role">Business Executive</p>
					</div>
				</div>
			</div>

			<div class="testimonial-card">
				<div class="testimonial-card__stars">
					<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
					<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
					<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
					<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
					<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
				</div>
				<p class="testimonial-card__text">
					"I've never worn shoes this comfortable. The leather quality is exceptional and they look even better in person."
				</p>
				<div class="testimonial-card__author">
					<div class="testimonial-card__avatar">FK</div>
					<div class="testimonial-card__info">
						<h4 class="testimonial-card__name">Faisal Khan</h4>
						<p class="testimonial-card__role">Entrepreneur</p>
					</div>
				</div>
			</div>

			<div class="testimonial-card">
				<div class="testimonial-card__stars">
					<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
					<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
					<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
					<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
					<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
				</div>
				<p class="testimonial-card__text">
					"Worth every penny. The craftsmanship is Italian at its finest. I've received countless compliments."
				</p>
				<div class="testimonial-card__author">
					<div class="testimonial-card__avatar">MS</div>
					<div class="testimonial-card__info">
						<h4 class="testimonial-card__name">Muhammad Shah</h4>
						<p class="testimonial-card__role">Architect</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- Instagram Feed Section -->
<section class="instagram-feed">
	<div class="container">
		<div class="section-header">
			<p class="section-subtitle"><?php esc_html_e( 'Follow Us', 'opulentia' ); ?></p>
			<h2 class="section-title"><?php esc_html_e( '@Opulentia', 'opulentia' ); ?></h2>
		</div>

		<div class="instagram-feed__grid">
			<a href="#" class="instagram-item">
				<img src="https://images.unsplash.com/photo-1614252369475-531eba835eb1?w=400&h=400&fit=crop" alt="Instagram Post" loading="lazy">
				<div class="instagram-item__overlay">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
						<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
						<line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
					</svg>
				</div>
			</a>
			<a href="#" class="instagram-item">
				<img src="https://images.unsplash.com/photo-1608256246200-53e635b5b65f?w=400&h=400&fit=crop" alt="Instagram Post" loading="lazy">
				<div class="instagram-item__overlay">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
						<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
						<line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
					</svg>
				</div>
			</a>
			<a href="#" class="instagram-item">
				<img src="https://images.unsplash.com/photo-1603808033192-082d6919d3e1?w=400&h=400&fit=crop" alt="Instagram Post" loading="lazy">
				<div class="instagram-item__overlay">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
						<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
						<line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
					</svg>
				</div>
			</a>
			<a href="#" class="instagram-item">
				<img src="https://images.unsplash.com/photo-1611074022320-4bd919e44846?w=400&h=400&fit=crop" alt="Instagram Post" loading="lazy">
				<div class="instagram-item__overlay">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
						<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
						<line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
					</svg>
				</div>
			</a>
			<a href="#" class="instagram-item">
				<img src="https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=400&h=400&fit=crop" alt="Instagram Post" loading="lazy">
				<div class="instagram-item__overlay">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
						<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
						<line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
					</svg>
				</div>
			</a>
			<a href="#" class="instagram-item">
				<img src="https://images.unsplash.com/photo-1473188588951-666fce8e7c68?w=400&h=400&fit=crop" alt="Instagram Post" loading="lazy">
				<div class="instagram-item__overlay">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
						<path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
						<line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
					</svg>
				</div>
			</a>
		</div>
	</div>
</section>

<?php do_action( 'Opulentia_content_bottom' ); ?>
<?php do_action( 'Opulentia_front_page_after' ); ?>

<?php get_footer(); ?>
