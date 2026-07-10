<?php
/**
 * Template Name: Contact Page
 * Description: Contact page with form and information
 *
 * @package Opulentia
 */

get_header();
?>

<main id="primary" class="site-main">
    <section class="page-hero">
        <div class="container">
            <h1 class="page-hero__title"><?php the_title(); ?></h1>
            <p class="page-hero__subtitle"><?php esc_html_e( 'Get in Touch', 'opulentia' ); ?></p>
        </div>
    </section>

    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-info">
                    <h2 class="contact-info__title"><?php esc_html_e( 'Contact Information', 'opulentia' ); ?></h2>
                    <p class="contact-info__text"><?php esc_html_e( 'Have questions about our products or need assistance? We are here to help.', 'opulentia' ); ?></p>

                    <div class="contact-details">
                        <div class="contact-details__item">
                            <div class="contact-details__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            </div>
                            <div class="contact-details__content">
                                <h4 class="contact-details__label"><?php esc_html_e( 'Address', 'opulentia' ); ?></h4>
                                <p class="contact-details__value">Via della Seta 42<br>20121 Milano, Italia</p>
                            </div>
                        </div>

                        <div class="contact-details__item">
                            <div class="contact-details__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                            </div>
                            <div class="contact-details__content">
                                <h4 class="contact-details__label"><?php esc_html_e( 'Phone', 'opulentia' ); ?></h4>
                                <p class="contact-details__value">+39 02 1234 5678</p>
                            </div>
                        </div>

                        <div class="contact-details__item">
                            <div class="contact-details__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                    <polyline points="22,6 12,13 2,6"/>
                                </svg>
                            </div>
                            <div class="contact-details__content">
                                <h4 class="contact-details__label"><?php esc_html_e( 'Email', 'opulentia' ); ?></h4>
                                <p class="contact-details__value">info@opulentia.com</p>
                            </div>
                        </div>

                        <div class="contact-details__item">
                            <div class="contact-details__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12,6 12,12 16,14"/>
                                </svg>
                            </div>
                            <div class="contact-details__content">
                                <h4 class="contact-details__label"><?php esc_html_e( 'Hours', 'opulentia' ); ?></h4>
                                <p class="contact-details__value"><?php esc_html_e( 'Mon - Fri: 9:00 AM - 6:00 PM CET', 'opulentia' ); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contact-form-wrapper">
                    <h2 class="contact-form-wrapper__title"><?php esc_html_e( 'Send a Message', 'opulentia' ); ?></h2>
                    <form class="contact-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                        <input type="hidden" name="action" value="Opulentia_contact_form">
                        <?php Opulentia_Security::nonce_field( 'Opulentia_contact_form', 'Opulentia_contact_nonce' ); ?>
                        <div class="form-group">
                            <label for="name"><?php esc_html_e( 'Full Name', 'opulentia' ); ?></label>
                            <input type="text" id="name" name="name" required placeholder="<?php esc_attr_e( 'Your name', 'opulentia' ); ?>">
                        </div>
                        <div class="form-group">
                            <label for="email"><?php esc_html_e( 'Email Address', 'opulentia' ); ?></label>
                            <input type="email" id="email" name="email" required placeholder="<?php esc_attr_e( 'your@email.com', 'opulentia' ); ?>">
                        </div>
                        <div class="form-group">
                            <label for="subject"><?php esc_html_e( 'Subject', 'opulentia' ); ?></label>
                            <input type="text" id="subject" name="subject" required placeholder="<?php esc_attr_e( 'How can we help?', 'opulentia' ); ?>">
                        </div>
                        <div class="form-group">
                            <label for="message"><?php esc_html_e( 'Message', 'opulentia' ); ?></label>
                            <textarea id="message" name="message" rows="6" required placeholder="<?php esc_attr_e( 'Tell us more...', 'opulentia' ); ?>"></textarea>
                        </div>
                        <button type="submit" class="btn btn--primary btn--full"><?php esc_html_e( 'Send Message', 'opulentia' ); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
