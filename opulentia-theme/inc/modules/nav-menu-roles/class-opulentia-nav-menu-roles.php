<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Nav_Menu_Roles {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
        add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_css' ), 120 );
        add_filter( 'wp_setup_nav_menu_item', array( $this, 'setup_nav_menu_item' ) );
        add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'menu_item_fields' ), 10, 4 );
        add_action( 'wp_update_nav_menu_item', array( $this, 'save_menu_item_fields' ), 10, 3 );
        add_filter( 'wp_nav_menu_objects', array( $this, 'filter_menu_objects' ), 10, 2 );
    }

    private function is_enabled() {
        return (bool) Opulentia_get_option( 'enable-nav-menu-roles', true );
    }

    public function register_customizer( $wp_customize ) {
        $wp_customize->add_section( 'opulentia_nav_menu_roles', array(
            'title'    => __( 'Nav Menu Roles', 'opulentia' ),
            'panel'    => 'Opulentia_global_settings',
            'priority' => 210,
        ) );

        $wp_customize->add_setting( 'enable-nav-menu-roles', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'enable-nav-menu-roles', array(
            'label'   => __( 'Enable Role Restriction', 'opulentia' ),
            'section' => 'opulentia_nav_menu_roles',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'nav-menu-roles-fallback', array(
            'default'           => 'hide',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'nav-menu-roles-fallback', array(
            'label'   => __( 'Fallback for Restricted Items', 'opulentia' ),
            'section' => 'opulentia_nav_menu_roles',
            'type'    => 'select',
            'choices' => array(
                'hide' => __( 'Hide', 'opulentia' ),
                'show' => __( 'Show (greyed out)', 'opulentia' ),
            ),
        ) );
    }

    public function setup_nav_menu_item( $menu_item ) {
        $menu_item->op_role_restriction = get_post_meta( $menu_item->ID, '_Opulentia_menu_role_restriction', true );
        if ( empty( $menu_item->op_role_restriction ) ) {
            $menu_item->op_role_restriction = 'all';
        }
        return $menu_item;
    }

    public function menu_item_fields( $item_id, $item, $depth, $args ) {
        if ( ! $this->is_enabled() ) {
            return;
        }

        $restriction = get_post_meta( $item_id, '_Opulentia_menu_role_restriction', true );
        if ( empty( $restriction ) ) {
            $restriction = 'all';
        }

        wp_nonce_field( 'opulentia_menu_role_nonce', '_opulentia_menu_role_nonce' );
        ?>
        <div class="opulentia-menu-role-fields description-wide" style="margin:6px 0;padding:6px 0;border-top:1px solid #e0e0e0;">
            <p style="margin:0 0 4px;font-weight:600;color:#333;">
                <?php esc_html_e( 'Opulentia Role Restriction', 'opulentia' ); ?>
            </p>
            <label for="edit-menu-item-role-<?php echo esc_attr( $item_id ); ?>" style="display:block;margin:4px 0;">
                <?php esc_html_e( 'Display this menu item to:', 'opulentia' ); ?>
            </label>
            <select id="edit-menu-item-role-<?php echo esc_attr( $item_id ); ?>"
                    name="menu-item-role-restriction[<?php echo esc_attr( $item_id ); ?>]"
                    class="widefat">
                <option value="all" <?php selected( $restriction, 'all' ); ?>>
                    <?php esc_html_e( 'All Users', 'opulentia' ); ?>
                </option>
                <option value="logged_in" <?php selected( $restriction, 'logged_in' ); ?>>
                    <?php esc_html_e( 'Logged In', 'opulentia' ); ?>
                </option>
                <option value="logged_out" <?php selected( $restriction, 'logged_out' ); ?>>
                    <?php esc_html_e( 'Logged Out (Guests)', 'opulentia' ); ?>
                </option>
                <?php foreach ( wp_roles()->get_names() as $role_key => $role_name ) : ?>
                    <option value="<?php echo esc_attr( $role_key ); ?>" <?php selected( $restriction, $role_key ); ?>>
                        <?php echo esc_html( translate_user_role( $role_name ) ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php
    }

    public function save_menu_item_fields( $menu_id, $menu_item_db_id, $args ) {
        if ( ! isset( $_POST['_opulentia_menu_role_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_opulentia_menu_role_nonce'] ) ), 'opulentia_menu_role_nonce' ) ) {
            return;
        }

        if ( ! current_user_can( 'edit_theme_options' ) ) {
            return;
        }

        if ( isset( $_POST['menu-item-role-restriction'][ $menu_item_db_id ] ) ) {
            $restriction = sanitize_text_field( wp_unslash( $_POST['menu-item-role-restriction'][ $menu_item_db_id ] ) );
            if ( in_array( $restriction, array_merge( array( 'all', 'logged_in', 'logged_out' ), array_keys( wp_roles()->get_names() ) ), true ) ) {
                update_post_meta( $menu_item_db_id, '_Opulentia_menu_role_restriction', $restriction );
            }
        }
    }

    public function filter_menu_objects( $items, $args ) {
        if ( ! $this->is_enabled() ) {
            return $items;
        }

        $fallback = Opulentia_get_option( 'nav-menu-roles-fallback', 'hide' );

        foreach ( $items as $key => $item ) {
            if ( ! isset( $item->op_role_restriction ) ) {
                $item->op_role_restriction = get_post_meta( $item->ID, '_Opulentia_menu_role_restriction', true );
                if ( empty( $item->op_role_restriction ) ) {
                    $item->op_role_restriction = 'all';
                }
            }

            $visible = $this->is_item_visible( $item );

            if ( ! $visible ) {
                if ( 'hide' === $fallback ) {
                    unset( $items[ $key ] );
                } else {
                    $item->classes[] = 'op-menu-item-restricted';
                }
            }
        }

        return $items;
    }

    private function is_item_visible( $item ) {
        $restriction = $item->op_role_restriction;

        if ( 'all' === $restriction ) {
            return true;
        }

        if ( 'logged_in' === $restriction ) {
            return is_user_logged_in();
        }

        if ( 'logged_out' === $restriction ) {
            return ! is_user_logged_in();
        }

        if ( is_user_logged_in() ) {
            $user = wp_get_current_user();
            if ( in_array( $restriction, (array) $user->roles, true ) ) {
                return true;
            }
        }

        return false;
    }

    public function inline_css() {
        if ( ! $this->is_enabled() ) {
            return;
        }

        $fallback = Opulentia_get_option( 'nav-menu-roles-fallback', 'hide' );

        if ( 'show' === $fallback ) {
            $css = '
            .op-menu-item-restricted {
                opacity: 0.4;
                cursor: not-allowed;
                pointer-events: none;
            }
            .op-menu-item-restricted a {
                color: var(--color-text-muted, #777) !important;
                border-color: var(--color-border, #333) !important;
            }
            ';
            wp_add_inline_style( 'opulentia-style', $css );
        }
    }

    public function admin_css( $hook ) {
        if ( 'nav-menus.php' !== $hook ) {
            return;
        }
        $css = '
        .opulentia-menu-role-fields select {
            margin-top: 4px;
        }
        .opulentia-menu-role-fields {
            font-size: 12px;
        }
        ';
        wp_add_inline_style( 'wp-admin', $css );
    }
}
