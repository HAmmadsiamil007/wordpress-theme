<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Icon_Manager {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'register_icon_cpt' ) );
        add_action( 'init', array( $this, 'register_icon_set_taxonomy' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_meta_box' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
        add_shortcode( 'op_icon', array( $this, 'shortcode' ) );
        add_action( 'wp_ajax_opulentia_save_svg_icon', array( $this, 'ajax_save_svg' ) );
    }

    public function register_icon_cpt() {
        register_post_type( 'op_icon', array(
            'labels' => array(
                'name'          => __( 'Icons', 'opulentia' ),
                'singular_name' => __( 'Icon', 'opulentia' ),
                'add_new_item'  => __( 'Upload SVG Icon', 'opulentia' ),
                'edit_item'     => __( 'Edit Icon', 'opulentia' ),
                'not_found'     => __( 'No icons found.', 'opulentia' ),
            ),
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => 'themes.php',
            'supports'     => array( 'title', 'thumbnail' ),
        ) );
    }

    public function register_icon_set_taxonomy() {
        register_taxonomy( 'op_icon_set', 'op_icon', array(
            'labels' => array(
                'name'          => __( 'Icon Sets', 'opulentia' ),
                'singular_name' => __( 'Icon Set', 'opulentia' ),
                'add_new_item'  => __( 'Add New Set', 'opulentia' ),
                'new_item_name' => __( 'Set Name', 'opulentia' ),
            ),
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'hierarchical' => true,
        ) );
    }

    public function admin_menu() {
        add_submenu_page(
            'themes.php',
            __( 'Icon Manager', 'opulentia' ),
            __( 'Icon Manager', 'opulentia' ),
            'manage_options',
            'opulentia-icons',
            array( $this, 'render_admin_page' )
        );
    }

    public function admin_enqueue( $hook ) {
        if ( 'appearance_page_opulentia-icons' === $hook ) {
            wp_enqueue_style( 'wp-admin' );
        }
    }

    public function render_admin_page() {
        $built_in = $this->get_built_in_icons();
        $custom_icons = get_posts( array(
            'post_type'      => 'op_icon',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ) );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Icon Manager', 'opulentia' ); ?></h1>
            <p><?php esc_html_e( 'Browse, manage, and output icons. Use the shortcode <code>[op_icon name="icon-name"]</code> or PHP function <code>Opulentia_Icon_Manager::get_icon( "name" )</code>.', 'opulentia' ); ?></p>

            <div style="margin:20px 0">
                <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=op_icon' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Upload SVG Icon', 'opulentia' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=op_icon_set&post_type=op_icon' ) ); ?>" class="button"><?php esc_html_e( 'Manage Icon Sets', 'opulentia' ); ?></a>
            </div>

            <h2><?php esc_html_e( 'Custom Uploaded Icons', 'opulentia' ); ?></h2>
            <?php if ( $custom_icons ) : ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:15px;">
                <?php foreach ( $custom_icons as $icon ) : ?>
                    <div style="text-align:center;padding:15px;background:#f0f0f1;border-radius:4px">
                        <div style="font-size:32px;margin-bottom:8px">
                            <?php echo $this->get_svg_from_post( $icon->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                        <span style="font-size:11px;display:block"><?php echo esc_html( $icon->post_title ); ?></span>
                        <code style="font-size:10px">[op_icon name="<?php echo esc_attr( $icon->post_title ); ?>"]</code>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php else : ?>
                <p><?php esc_html_e( 'No custom icons uploaded yet.', 'opulentia' ); ?></p>
            <?php endif; ?>

            <h2 style="margin-top:30px"><?php esc_html_e( 'Built-In Icons', 'opulentia' ); ?></h2>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:15px;">
                <?php foreach ( $built_in as $name => $svg ) : ?>
                    <div style="text-align:center;padding:15px;background:#f0f0f1;border-radius:4px">
                        <div style="font-size:32px;margin-bottom:8px;color:#b8860b"><?php echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                        <span style="font-size:11px;display:block"><?php echo esc_html( $name ); ?></span>
                        <code style="font-size:10px">[op_icon name="<?php echo esc_attr( $name ); ?>"]</code>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    public function add_meta_box() {
        add_meta_box(
            'op_icon_svg_content',
            __( 'SVG Content', 'opulentia' ),
            array( $this, 'render_meta_box' ),
            'op_icon',
            'normal',
            'default'
        );
    }

    public function render_meta_box( $post ) {
        wp_nonce_field( 'op_icon_svg', 'op_icon_svg_nonce' );
        $svg = get_post_meta( $post->ID, '_op_icon_svg', true );
        ?>
        <p>
            <label for="op_icon_svg"><?php esc_html_e( 'Paste SVG markup or upload an SVG file:', 'opulentia' ); ?></label>
        </p>
        <p>
            <textarea name="_op_icon_svg" id="op_icon_svg" rows="6" style="width:100%;font-family:monospace"><?php echo esc_textarea( $svg ); ?></textarea>
        </p>
        <p>
            <button type="button" class="button" id="op-icon-upload-btn"><?php esc_html_e( 'Upload SVG File', 'opulentia' ); ?></button>
        </p>
        <?php if ( $svg ) : ?>
            <div style="padding:20px;background:#f0f0f1;border-radius:4px;text-align:center">
                <p><strong><?php esc_html_e( 'Preview', 'opulentia' ); ?></strong></p>
                <div style="font-size:48px;color:#b8860b"><?php echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
            </div>
        <?php endif; ?>
        <script>
        (function($){
            $('#op-icon-upload-btn').on('click', function(e) {
                e.preventDefault();
                var frame = wp.media({
                    title: '<?php echo esc_js( __( 'Select SVG', 'opulentia' ) ); ?>',
                    library: { type: 'image/svg+xml' },
                    multiple: false
                });
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $.get(attachment.url, function(data) {
                        $('#op_icon_svg').val(data);
                    });
                });
                frame.open();
            });
        })(jQuery);
        </script>
        <?php
    }

    public function save_meta_box( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
        if ( ! isset( $_POST['op_icon_svg_nonce'] ) ) { return; }
        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['op_icon_svg_nonce'] ) ), 'op_icon_svg' ) ) { return; }
        if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

        if ( isset( $_POST['_op_icon_svg'] ) ) {
            update_post_meta( $post_id, '_op_icon_svg', wp_kses( wp_unslash( $_POST['_op_icon_svg'] ), $this->get_svg_kses() ) );
        }
    }

    public function ajax_save_svg() {
        check_ajax_referer( 'opulentia_icon_nonce', 'nonce' );
        $svg = isset( $_POST['svg'] ) ? wp_kses( wp_unslash( $_POST['svg'] ), $this->get_svg_kses() ) : '';
        $name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';

        if ( ! $svg || ! $name ) {
            wp_send_json_error( array( 'message' => __( 'Name and SVG content required.', 'opulentia' ) ) );
        }

        $id = wp_insert_post( array(
            'post_title' => $name,
            'post_type'  => 'op_icon',
            'post_status' => 'publish',
        ) );

        if ( $id ) {
            update_post_meta( $id, '_op_icon_svg', $svg );
            wp_send_json_success( array( 'message' => __( 'Icon saved.', 'opulentia' ) ) );
        }

        wp_send_json_error( array( 'message' => __( 'Could not save icon.', 'opulentia' ) ) );
    }

    private function get_built_in_icons() {
        $icons = array();
        if ( class_exists( 'Opulentia_Icons' ) ) {
            $instance = Opulentia_Icons::get_instance();
            if ( method_exists( $instance, 'get_icons' ) ) {
                $icons = $instance->get_icons();
            }
        }
        return $icons;
    }

    private function get_svg_from_post( $post_id ) {
        $svg = get_post_meta( $post_id, '_op_icon_svg', true );
        if ( ! $svg ) {
            return '<span style="color:#999">?</span>';
        }
        return $svg;
    }

    private function get_svg_kses() {
        return array(
            'svg' => array(
                'xmlns'   => true,
                'viewBox' => true,
                'width'   => true,
                'height'  => true,
                'fill'    => true,
                'stroke'  => true,
                'class'   => true,
                'style'   => true,
                'aria-hidden' => true,
                'role'    => true,
            ),
            'path' => array(
                'd'       => true,
                'fill'    => true,
                'stroke'  => true,
                'stroke-width' => true,
                'stroke-linecap' => true,
                'stroke-linejoin' => true,
                'opacity' => true,
            ),
            'circle' => array(
                'cx' => true, 'cy' => true, 'r' => true,
                'fill' => true, 'stroke' => true,
            ),
            'rect' => array(
                'x' => true, 'y' => true, 'width' => true, 'height' => true,
                'rx' => true, 'ry' => true,
                'fill' => true, 'stroke' => true,
            ),
            'line' => array(
                'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true,
                'stroke' => true, 'stroke-width' => true,
            ),
            'polyline' => array( 'points' => true, 'fill' => true, 'stroke' => true ),
            'polygon' => array( 'points' => true, 'fill' => true, 'stroke' => true ),
            'g' => array( 'fill' => true, 'stroke' => true, 'opacity' => true ),
        );
    }

    public function get_icon( $name, $attrs = array() ) {
        $built_in = $this->get_built_in_icons();
        if ( isset( $built_in[ $name ] ) ) {
            $svg = $built_in[ $name ];
            $class = isset( $attrs['class'] ) ? ' class="' . esc_attr( $attrs['class'] ) . '"' : '';
            $style = isset( $attrs['color'] ) ? ' style="color:' . esc_attr( $attrs['color'] ) . '"' : '';
            return str_replace( '<svg', '<svg' . $class . $style, $svg );
        }

        $icons = get_posts( array(
            'post_type'      => 'op_icon',
            'name'           => sanitize_title( $name ),
            'posts_per_page' => 1,
            'post_status'    => 'publish',
        ) );

        if ( ! empty( $icons ) ) {
            $svg = get_post_meta( $icons[0]->ID, '_op_icon_svg', true );
            if ( $svg ) {
                $class = isset( $attrs['class'] ) ? ' class="' . esc_attr( $attrs['class'] ) . '"' : '';
                $style = isset( $attrs['color'] ) ? ' style="color:' . esc_attr( $attrs['color'] ) . '"' : '';
                return str_replace( '<svg', '<svg' . $class . $style, $svg );
            }
        }

        return '';
    }

    public function shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'name'  => '',
            'class' => '',
            'color' => '',
            'size'  => '',
        ), $atts );

        if ( empty( $atts['name'] ) ) {
            return '';
        }

        $svg = $this->get_icon( $atts['name'], $atts );

        if ( $atts['size'] && $svg ) {
            $size_style = ' style="width:' . esc_attr( $atts['size'] ) . 'px;height:' . esc_attr( $atts['size'] ) . 'px"';
            $svg = str_replace( '<svg', '<svg' . $size_style, $svg );
        }

        return $svg;
    }
}
