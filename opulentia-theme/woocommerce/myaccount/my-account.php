<?php
/**
 * My Account Page Template
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

wc_print_notices();
?>

<div class="woocommerce-myaccount">
    <div class="page-header">
        <h1 class="page-header__title"><?php esc_html_e( 'My Account', 'opulentia' ); ?></h1>
        <p class="page-header__subtitle"><?php esc_html_e( 'Manage your account settings', 'opulentia' ); ?></p>
    </div>

    <?php if ( is_user_logged_in() ) : ?>
        <div class="myaccount-content">
            <nav class="woocommerce-MyAccount-navigation">
                <ul>
                    <?php
                    $navigation = WC()->account->get_navigation_items();

                    foreach ( $navigation as $key => $item ) :
                        $class = array();

                        if ( wc_is_current_account_menu_item( $item['endpoint'] ) ) {
                            $class[] = 'is-active';
                        }

                        if ( empty( $item['endpoint'] ) ) {
                            $class[] = 'woocommerce-MyAccount-navigation-link--home';
                        }
                    ?>
                        <li class="<?php echo esc_attr( implode( ' ', $class ) ); ?>">
                            <a href="<?php echo esc_url( wc_get_account_endpoint_url( $item['endpoint'] ) ); ?>">
                                <?php echo esc_html( $item['title'] ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <div class="woocommerce-MyAccount-content">
                <?php
                /**
                 * My Account content.
                 *
                 * @hooked WC_Shortcode_Account_Dashboard::output
                 */
                do_action( 'woocommerce_account_content' );
                ?>
            </div>
        </div>
    <?php else : ?>
        <div class="woocommerce-notices-wrapper">
            <div class="woocommerce-info">
                <?php
                printf(
                    /* translators: %s: login URL */
                    esc_html__( 'Please %s to access your account.', 'opulentia' ),
                    '<a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">'
                ); ?>
                    <?php esc_html_e( 'log in', 'opulentia' ); ?>
                </a>
            </div>
        </div>

        <div class="woocommerce-row">
            <div class="woocommerce-col-half">
                <div class="woocommerce-form-login">
                    <h2><?php esc_html_e( 'Login', 'opulentia' ); ?></h2>

                    <?php woocommerce_login_form(); ?>
                </div>
            </div>

            <div class="woocommerce-col-half">
                <div class="woocommerce-form-register">
                    <h2><?php esc_html_e( 'Register', 'opulentia' ); ?></h2>

                    <?php woocommerce_register_form(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
