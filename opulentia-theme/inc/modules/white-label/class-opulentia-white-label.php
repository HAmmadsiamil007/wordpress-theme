<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_White_Label {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 999 );
        add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 999 );
        add_action( 'wp_before_admin_bar_render', array( $this, 'admin_bar_render' ), 999 );
        add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 999 );
        add_filter( 'update_footer', array( $this, 'update_footer' ), 999 );
        add_filter( 'all_plugins', array( $this, 'filter_plugins' ), 999 );
        add_action( 'admin_head', array( $this, 'admin_css' ), 999 );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    private function is_client_mode() {
        return (bool) get_option( 'op_white_label_client_mode', false );
    }

    private function get_brand_name() {
        $name = get_option( 'op_white_label_brand_name', '' );
        return $name ?: 'Opulentia';
    }

    private function get_brand_author() {
        return get_option( 'op_white_label_brand_author', '' );
    }

    private function get_brand_uri() {
        return get_option( 'op_white_label_brand_uri', '' );
    }

    public function register_settings() {
        add_settings_section( 'op_white_label', __( 'White Label Settings', 'opulentia' ), null, 'general' );

        register_setting( 'general', 'op_white_label_client_mode', array( 'type' => 'boolean', 'default' => false ) );
        register_setting( 'general', 'op_white_label_brand_name', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'general', 'op_white_label_brand_author', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'general', 'op_white_label_brand_uri', array( 'type' => 'string', 'sanitize_callback' => 'esc_url_raw' ) );
        register_setting( 'general', 'op_white_label_footer_text', array( 'type' => 'string', 'sanitize_callback' => 'wp_kses_post' ) );
        register_setting( 'general', 'op_white_label_dashboard_icon', array( 'type' => 'string', 'sanitize_callback' => 'esc_url_raw' ) );
        register_setting( 'general', 'op_white_label_hide_theme_page', array( 'type' => 'boolean', 'default' => false ) );

        add_settings_field( 'op_white_label_client_mode', __( 'Client Mode', 'opulentia' ), array( $this, 'render_field_client_mode' ), 'general', 'op_white_label' );
        add_settings_field( 'op_white_label_brand_name', __( 'Brand Name', 'opulentia' ), array( $this, 'render_field_text' ), 'general', 'op_white_label', array( 'id' => 'op_white_label_brand_name' ) );
        add_settings_field( 'op_white_label_brand_author', __( 'Brand Author', 'opulentia' ), array( $this, 'render_field_text' ), 'general', 'op_white_label', array( 'id' => 'op_white_label_brand_author' ) );
        add_settings_field( 'op_white_label_brand_uri', __( 'Brand URI', 'opulentia' ), array( $this, 'render_field_text' ), 'general', 'op_white_label', array( 'id' => 'op_white_label_brand_uri', 'type' => 'url' ) );
        add_settings_field( 'op_white_label_footer_text', __( 'Admin Footer Text', 'opulentia' ), array( $this, 'render_field_textarea' ), 'general', 'op_white_label', array( 'id' => 'op_white_label_footer_text' ) );
        add_settings_field( 'op_white_label_dashboard_icon', __( 'Dashboard Icon URL', 'opulentia' ), array( $this, 'render_field_text' ), 'general', 'op_white_label', array( 'id' => 'op_white_label_dashboard_icon', 'type' => 'url' ) );
        add_settings_field( 'op_white_label_hide_theme_page', __( 'Hide Theme Info Page', 'opulentia' ), array( $this, 'render_field_client_mode' ), 'general', 'op_white_label', array( 'id' => 'op_white_label_hide_theme_page' ) );
    }

    public function render_field_client_mode( $args ) {
        $id    = $args['id'] ?? 'op_white_label_client_mode';
        $value = get_option( $id, false );
        echo '<input type="checkbox" name="' . esc_attr( $id ) . '" value="1" ' . checked( $value, true, false ) . '>';
        echo '<p class="description">' . esc_html__( 'Enable to replace all "Opulentia" branding, hide theme update notices, and streamline the admin for clients.', 'opulentia' ) . '</p>';
    }

    public function render_field_text( $args ) {
        $id    = $args['id'];
        $type  = $args['type'] ?? 'text';
        $value = get_option( $id, '' );
        echo '<input type="' . esc_attr( $type ) . '" name="' . esc_attr( $id ) . '" value="' . esc_attr( $value ) . '" class="regular-text">';
    }

    public function render_field_textarea( $args ) {
        $id    = $args['id'];
        $value = get_option( $id, '' );
        echo '<textarea name="' . esc_attr( $id ) . '" class="large-text" rows="2">' . esc_textarea( $value ) . '</textarea>';
    }

    public function admin_menu() {
        if ( get_option( 'op_white_label_hide_theme_page' ) ) {
            remove_submenu_page( 'themes.php', 'opulentia-theme' );
        }
    }

    public function admin_bar_menu( $wp_admin_bar ) {
        if ( ! $this->is_client_mode() ) {
            return;
        }

        $nodes = $wp_admin_bar->get_nodes();
        if ( $nodes ) {
            foreach ( $nodes as $node ) {
                if ( isset( $node->title ) && stripos( $node->title, 'opulentia' ) !== false ) {
                    $wp_admin_bar->remove_node( $node->id );
                }
            }
        }
    }

    public function admin_bar_render() {
        if ( $this->is_client_mode() ) {
            global $wp_admin_bar;
            $wp_admin_bar->remove_node( 'wp-logo' );
        }
    }

    public function admin_footer_text() {
        $custom = get_option( 'op_white_label_footer_text', '' );
        if ( $custom ) {
            return $custom;
        }
        if ( $this->is_client_mode() ) {
            return '';
        }
        return __( 'Thank you for creating with Opulentia.', 'opulentia' );
    }

    public function update_footer() {
        if ( $this->is_client_mode() ) {
            return '';
        }
        return '';
    }

    public function filter_plugins( $plugins ) {
        if ( ! $this->is_client_mode() ) {
            return $plugins;
        }

        $brand_name  = $this->get_brand_name();
        $brand_author = $this->get_brand_author();
        $brand_uri   = $this->get_brand_uri();

        foreach ( $plugins as $file => $data ) {
            if ( strpos( $file, 'opulentia' ) !== false ) {
                if ( $brand_name ) {
                    $plugins[ $file ]['Name']        = $brand_name;
                    $plugins[ $file ]['Description']  = str_replace( 'Opulentia', $brand_name, $data['Description'] );
                }
                if ( $brand_author ) {
                    $plugins[ $file ]['Author']     = $brand_author;
                    $plugins[ $file ]['AuthorName'] = $brand_author;
                }
                if ( $brand_uri ) {
                    $plugins[ $file ]['PluginURI']  = $brand_uri;
                    $plugins[ $file ]['AuthorURI']  = $brand_uri;
                }
            }
        }

        return $plugins;
    }

    public function admin_css() {
        $icon = get_option( 'op_white_label_dashboard_icon', '' );
        ?>
        <style>
        <?php if ( $icon ) : ?>
        #adminmenu .toplevel_page_opulentia-dashboard .wp-menu-image img,
        #adminmenu .menu-icon-opulentia .wp-menu-image img {
            content: url('<?php echo esc_url( $icon ); ?>') !important;
        }
        <?php endif; ?>
        <?php if ( $this->is_client_mode() ) : ?>
        .update-nag, .theme-update-message, .update-available { display: none !important; }
        .notice.theme-notice, .notice.opulentia-notice { display: none !important; }
        <?php endif; ?>
        </style>
        <?php
    }

    public function enqueue_assets() {
        if ( isset( $_GET['page'] ) && 'opulentia-theme' === $_GET['page'] && $this->is_client_mode() ) {
            wp_safe_redirect( admin_url() );
            exit;
        }
    }
}
