<?php
/**
 * The footer template - Premium Luxury Design
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<!-- Newsletter Section -->
<section class="newsletter-section">
    <div class="container">
        <div class="newsletter-content">
            <div class="newsletter-text">
                <h3 class="newsletter-title"><?php esc_html_e( 'JOIN OUR EXCLUSIVE LIST', 'soleorigine' ); ?></h3>
                <p class="newsletter-subtitle"><?php esc_html_e( 'Subscribe for early access to new collections, exclusive offers, and style insights.', 'soleorigine' ); ?></p>
            </div>
            <div class="newsletter-form">
                <form class="newsletter-form__inner" action="#" method="post">
                    <input type="email" name="email" placeholder="<?php esc_attr_e( 'Enter your email address', 'soleorigine' ); ?>" required>
                    <button type="submit" class="btn btn--primary"><?php esc_html_e( 'SUBSCRIBE', 'soleorigine' ); ?></button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Trust Badges -->
<section class="trust-badges">
    <div class="container">
        <div class="trust-badges__grid">
            <div class="trust-badge">
                <svg class="trust-badge__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
                <div class="trust-badge__content">
                    <h4 class="trust-badge__title"><?php esc_html_e( 'Secure Payment', 'soleorigine' ); ?></h4>
                    <p class="trust-badge__text"><?php esc_html_e( '100% secure & trusted checkout.', 'soleorigine' ); ?></p>
                </div>
            </div>

            <div class="trust-badge">
                <svg class="trust-badge__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <polyline points="1 4 1 10 7 10"/>
                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>
                </svg>
                <div class="trust-badge__content">
                    <h4 class="trust-badge__title"><?php esc_html_e( 'Easy Returns', 'soleorigine' ); ?></h4>
                    <p class="trust-badge__text"><?php esc_html_e( 'Hassle-free returns within 14 days.', 'soleorigine' ); ?></p>
                </div>
            </div>

            <div class="trust-badge">
                <svg class="trust-badge__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <div class="trust-badge__content">
                    <h4 class="trust-badge__title"><?php esc_html_e( 'Customer Support', 'soleorigine' ); ?></h4>
                    <p class="trust-badge__text"><?php esc_html_e( '24/7 support for all your queries.', 'soleorigine' ); ?></p>
                </div>
            </div>

            <div class="trust-badge">
                <svg class="trust-badge__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"/>
                    <path d="M4 6v12c0 1.1.9 2 2 2h14v-4"/>
                    <path d="M18 12a2 2 0 0 0-2 2c0 1.1.9 2 2 2h4v-4h-4z"/>
                </svg>
                <div class="trust-badge__content">
                    <h4 class="trust-badge__title"><?php esc_html_e( 'Exclusive Offers', 'soleorigine' ); ?></h4>
                    <p class="trust-badge__text"><?php esc_html_e( 'Sign up to get special discounts.', 'soleorigine' ); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer-grid">
            <!-- Brand Column -->
            <div class="footer-brand">
                <div class="footer-brand__logo">
                    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="48" stroke="#c9a96e" stroke-width="1.5"/>
                        <text x="50" y="55" font-family="Playfair Display, serif" font-size="24" fill="#c9a96e" text-anchor="middle" font-weight="600">SO</text>
                    </svg>
                    <span class="footer-brand__name"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
                </div>
                <p class="footer-brand__text">
                    <?php esc_html_e( 'Premium handcrafted leather shoes designed for the modern gentleman. Timeless elegance, unmatched comfort.', 'soleorigine' ); ?>
                </p>
                <div class="footer-social">
                    <?php
                    $social_links = array(
                        'facebook'  => '#',
                        'instagram' => '#',
                        'twitter'   => '#',
                        'pinterest' => '#',
                    );

                    $social_icons = array(
                        'facebook'  => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
                        'instagram' => '<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>',
                        'twitter'   => '<path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>',
                        'pinterest' => '<path d="M12 0C5.37 0 0 5.37 0 12c0 5.08 3.15 9.42 7.59 11.18-.1-.94-.2-2.39.04-3.42.22-.93 1.41-5.97 1.41-5.97s-.36-.72-.36-1.78c0-1.66.96-2.9 2.16-2.9 1.02 0 1.51.77 1.51 1.68 0 1.03-.65 2.56-.99 3.98-.28 1.19.6 2.16 1.77 2.16 2.13 0 3.76-2.24 3.76-5.49 0-2.87-2.06-4.87-5-4.87-3.41 0-5.41 2.56-5.41 5.21 0 1.03.4 2.13.89 2.73a.36.36 0 0 1 .08.34c-.09.37-.29 1.19-.33 1.36-.05.22-.17.27-.4.16-1.49-.69-2.42-2.88-2.42-4.63 0-3.77 2.74-7.24 7.89-7.24 4.14 0 7.36 2.95 7.36 6.89 0 4.11-2.59 7.42-6.19 7.42-1.21 0-2.35-.63-2.74-1.37l-.75 2.85c-.27 1.04-1 2.35-1.49 3.14C9.57 23.81 10.75 24 12 24c6.63 0 12-5.37 12-12S18.63 0 12 0z"/>',
                    );

                    foreach ( $social_links as $social => $url ) :
                    ?>
                        <a href="<?php echo esc_url( $url ); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="<?php echo esc_attr( ucfirst( $social ) ); ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <?php echo $social_icons[ $social ]; ?>
                            </svg>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-column">
                <h4 class="footer-column__title"><?php esc_html_e( 'Quick Links', 'soleorigine' ); ?></h4>
                <ul class="footer-links">
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'soleorigine' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/collection' ) ); ?>"><?php esc_html_e( 'Collection', 'soleorigine' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/about-us' ) ); ?>"><?php esc_html_e( 'About Us', 'soleorigine' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/craftsmanship' ) ); ?>"><?php esc_html_e( 'Craftsmanship', 'soleorigine' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>"><?php esc_html_e( 'Contact', 'soleorigine' ); ?></a></li>
                </ul>
            </div>

            <!-- Customer Service -->
            <div class="footer-column">
                <h4 class="footer-column__title"><?php esc_html_e( 'Customer Service', 'soleorigine' ); ?></h4>
                <ul class="footer-links">
                    <li><a href="#"><?php esc_html_e( 'Shipping & Delivery', 'soleorigine' ); ?></a></li>
                    <li><a href="#"><?php esc_html_e( 'Returns & Exchanges', 'soleorigine' ); ?></a></li>
                    <li><a href="#"><?php esc_html_e( 'Size Guide', 'soleorigine' ); ?></a></li>
                    <li><a href="#"><?php esc_html_e( 'FAQs', 'soleorigine' ); ?></a></li>
                    <li><a href="#"><?php esc_html_e( 'Contact Us', 'soleorigine' ); ?></a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-column">
                <h4 class="footer-column__title"><?php esc_html_e( 'Contact Info', 'soleorigine' ); ?></h4>
                <ul class="footer-links footer-links--contact">
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span><?php esc_html_e( '123 Craftsmanship Street, Leather District', 'soleorigine' ); ?></span>
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                        <span>+92 300 1234567</span>
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <span>info@soleorigine.com</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <p class="footer-copyright">
                <?php
                echo wp_kses_post( get_theme_mod(
                    'footer_copyright',
                    sprintf( __( '&copy; %d SoleOrigine. All rights reserved.', 'soleorigine' ), date( 'Y' ) )
                ) ); ?>
            </p>
            <div class="footer-payments">
                <span class="footer-payments__label"><?php esc_html_e( 'We Accept:', 'soleorigine' ); ?></span>
                <div class="footer-payments__icons">
                    <span class="payment-icon">VISA</span>
                    <span class="payment-icon">MC</span>
                    <span class="payment-icon">AMEX</span>
                    <span class="payment-icon">COD</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top -->
<button class="back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'soleorigine' ); ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 15l-6-6-6 6"/>
    </svg>
</button>

</div><!-- #primary -->

<?php wp_footer(); ?>

</body>
</html>
