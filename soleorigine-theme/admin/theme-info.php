<?php
/**
 * Theme Info Page
 *
 * @package SoleOrigine
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add Theme Info Page
 */
function soleorigine_add_theme_info_page() {
    add_theme_page(
        __( 'SoleOrigine Info', 'soleorigine' ),
        __( 'SoleOrigine', 'soleorigine' ),
        'manage_options',
        'soleorigine-info',
        'soleorigine_theme_info_page'
    );
}
add_action( 'admin_menu', 'soleorigine_add_theme_info_page' );

/**
 * Theme Info Page Content
 */
function soleorigine_theme_info_page() {
    ?>
    <div class="wrap SoleOrigine-admin">
        <h1><?php esc_html_e( 'SoleOrigine Theme', 'soleorigine' ); ?></h1>

        <div class="card">
            <h2><?php esc_html_e( 'Theme Information', 'soleorigine' ); ?></h2>
            <p><?php esc_html_e( 'SoleOrigine is a modern, responsive WordPress theme designed specifically for shoe stores and sneaker brands. Built with performance, accessibility, and user experience in mind.', 'soleorigine' ); ?></p>

            <table class="wp-list-table widefat fixed striped">
                <tbody>
                    <tr>
                        <td><strong><?php esc_html_e( 'Theme Name', 'soleorigine' ); ?></strong></td>
                        <td><?php echo esc_html( wp_get_theme()->get( 'Name' ) ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'Version', 'soleorigine' ); ?></strong></td>
                        <td><?php echo esc_html( wp_get_theme()->get( 'Version' ) ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'Author', 'soleorigine' ); ?></strong></td>
                        <td><?php echo esc_html( wp_get_theme()->get( 'Author' ) ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'Description', 'soleorigine' ); ?></strong></td>
                        <td><?php echo esc_html( wp_get_theme()->get( 'Description' ) ); ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php esc_html_e( 'Text Domain', 'soleorigine' ); ?></strong></td>
                        <td><?php echo esc_html( wp_get_theme()->get( 'TextDomain' ) ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2><?php esc_html_e( 'Features', 'soleorigine' ); ?></h2>
            <ul>
                <li><?php esc_html_e( 'Responsive design for all devices', 'soleorigine' ); ?></li>
                <li><?php esc_html_e( 'Dark, luxury aesthetic', 'soleorigine' ); ?></li>
                <li><?php esc_html_e( 'WooCommerce integration', 'soleorigine' ); ?></li>
                <li><?php esc_html_e( 'Custom post types for collections, styles, and brands', 'soleorigine' ); ?></li>
                <li><?php esc_html_e( 'Customizer settings for easy customization', 'soleorigine' ); ?></li>
                <li><?php esc_html_e( 'Multiple page templates', 'soleorigine' ); ?></li>
                <li><?php esc_html_e( 'Blog with multiple post formats', 'soleorigine' ); ?></li>
                <li><?php esc_html_e( 'SEO optimized', 'soleorigine' ); ?></li>
                <li><?php esc_html_e( 'Translation ready', 'soleorigine' ); ?></li>
                <li><?php esc_html_e( 'Performance optimized', 'soleorigine' ); ?></li>
            </ul>
        </div>

        <div class="card">
            <h2><?php esc_html_e( 'Getting Started', 'soleorigine' ); ?></h2>
            <p><?php esc_html_e( 'To get started with the SoleOrigine theme:', 'soleorigine' ); ?></p>
            <ol>
                <li><?php esc_html_e( 'Install and activate the theme', 'soleorigine' ); ?></li>
                <li><?php esc_html_e( 'Install required plugins (WooCommerce, etc.)', 'soleorigine' ); ?></li>
                <li><?php esc_html_e( 'Configure the theme in Appearance > Customize', 'soleorigine' ); ?></li>
                <li><?php esc_html_e( 'Create your pages (Shop, About, Contact, etc.)', 'soleorigine' ); ?></li>
                <li><?php esc_html_e( 'Add your products', 'soleorigine' ); ?></li>
            </ol>
        </div>

        <div class="card">
            <h2><?php esc_html_e( 'Support', 'soleorigine' ); ?></h2>
            <p><?php esc_html_e( 'For support, please contact us through our website or create a support ticket.', 'soleorigine' ); ?></p>
        </div>
    </div>
    <?php
}

/**
 * Add Theme Settings Link
 */
function soleorigine_add_theme_settings_link( $links ) {
    $settings_link = '<a href="themes.php?page=soleorigine-info">' . esc_html__( 'Theme Info', 'soleorigine' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'theme_action_links', 'soleorigine_add_theme_settings_link' );

/**
 * Add Customizer Link
 */
function soleorigine_add_customizer_link( $links ) {
    $customizer_link = '<a href="' . esc_url( admin_url( 'customize.php' ) ) . '">' . esc_html__( 'Customize', 'soleorigine' ) . '</a>';
    array_unshift( $links, $customizer_link );
    return $links;
}
add_filter( 'theme_action_links', 'soleorigine_add_customizer_link' );
