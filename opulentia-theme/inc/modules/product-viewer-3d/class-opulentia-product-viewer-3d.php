<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Product_Viewer_3D {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'customize_register', array( $this, 'customize_register' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_meta_box' ) );
        add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'render_viewer' ), 10, 2 );
        add_filter( 'body_class', array( $this, 'body_class' ) );
        add_filter( 'opulentia_dynamic_css', array( $this, 'dynamic_css' ) );
    }

    private function is_enabled() {
        return (bool) get_theme_mod( 'op_3d_enable', false );
    }

    private function has_model( $product_id = 0 ) {
        if ( ! $product_id ) {
            $product_id = get_the_ID();
        }
        return (bool) get_post_meta( $product_id, '_op_3d_model_id', true );
    }

    private function get_model_url( $product_id = 0 ) {
        if ( ! $product_id ) {
            $product_id = get_the_ID();
        }
        $attachment_id = (int) get_post_meta( $product_id, '_op_3d_model_id', true );
        if ( $attachment_id ) {
            return wp_get_attachment_url( $attachment_id );
        }
        return get_post_meta( $product_id, '_op_3d_model_url', true );
    }

    public function customize_register( $wp_customize ) {
        $wp_customize->add_section( 'op_3d_viewer', array(
            'title'       => __( '3D Product Viewer', 'opulentia' ),
            'panel'       => 'woocommerce',
            'priority'    => 50,
        ) );

        $wp_customize->add_setting( 'op_3d_enable', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_3d_enable', array(
            'label'   => __( 'Enable 3D Viewer', 'opulentia' ),
            'section' => 'op_3d_viewer',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'op_3d_placement', array(
            'default'           => 'replace',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_3d_placement', array(
            'label'   => __( 'Gallery Placement', 'opulentia' ),
            'section' => 'op_3d_viewer',
            'type'    => 'select',
            'choices' => array(
                'replace'   => __( 'Replace Gallery', 'opulentia' ),
                'alongside' => __( 'Alongside Gallery', 'opulentia' ),
                'lightbox'  => __( 'Lightbox (button)', 'opulentia' ),
            ),
        ) );

        $wp_customize->add_setting( 'op_3d_auto_rotate', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_3d_auto_rotate', array(
            'label'   => __( 'Auto-Rotate', 'opulentia' ),
            'section' => 'op_3d_viewer',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'op_3d_rotate_speed', array(
            'default'           => 2,
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_3d_rotate_speed', array(
            'label'       => __( 'Rotation Speed', 'opulentia' ),
            'section'     => 'op_3d_viewer',
            'type'        => 'number',
            'input_attrs' => array( 'min' => 0, 'max' => 20, 'step' => 1 ),
        ) );

        $wp_customize->add_setting( 'op_3d_zoom_min', array(
            'default'           => 0.5,
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_3d_zoom_min', array(
            'label'       => __( 'Min Zoom', 'opulentia' ),
            'section'     => 'op_3d_viewer',
            'type'        => 'number',
            'input_attrs' => array( 'min' => 0.1, 'max' => 5, 'step' => 0.1 ),
        ) );

        $wp_customize->add_setting( 'op_3d_zoom_max', array(
            'default'           => 3,
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( 'op_3d_zoom_max', array(
            'label'       => __( 'Max Zoom', 'opulentia' ),
            'section'     => 'op_3d_viewer',
            'type'        => 'number',
            'input_attrs' => array( 'min' => 0.1, 'max' => 10, 'step' => 0.1 ),
        ) );

        $wp_customize->add_setting( 'op_3d_bg_color', array(
            'default'           => '#1a1a1a',
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'op_3d_bg_color', array(
            'label'   => __( 'Background Color', 'opulentia' ),
            'section' => 'op_3d_viewer',
        ) ) );
    }

    public function enqueue_scripts() {
        if ( ! $this->is_enabled() || ! function_exists( 'is_product' ) || ! is_product() ) {
            return;
        }

        $product_id = get_the_ID();
        if ( ! $this->has_model( $product_id ) ) {
            return;
        }

        wp_enqueue_script(
            'model-viewer',
            'https://ajax.googleapis.com/ajax/libs/model-viewer/4.1.0/model-viewer.min.js',
            array(),
            '4.1.0',
            true
        );

        wp_enqueue_script(
            'opulentia-3d-viewer',
            Opulentia_URI . '/js/product-viewer-3d.js',
            array( 'model-viewer' ),
            Opulentia_VERSION,
            true
        );

        wp_localize_script( 'opulentia-3d-viewer', 'Opulentia3D', array(
            'modelUrl'   => $this->get_model_url( $product_id ),
            'autoRotate' => (bool) get_theme_mod( 'op_3d_auto_rotate', true ),
            'rotateSpeed' => (float) get_theme_mod( 'op_3d_rotate_speed', 2 ),
            'zoomMin'    => (float) get_theme_mod( 'op_3d_zoom_min', 0.5 ),
            'zoomMax'    => (float) get_theme_mod( 'op_3d_zoom_max', 3 ),
            'bgColor'    => get_theme_mod( 'op_3d_bg_color', '#1a1a1a' ),
            'placement'  => get_theme_mod( 'op_3d_placement', 'replace' ),
        ) );
    }

    public function dynamic_css( $css ) {
        if ( ! $this->is_enabled() ) {
            return $css;
        }

        $bg = get_theme_mod( 'op_3d_bg_color', '#1a1a1a' );

        $css .= '
.op-3d-viewer {
    width:100%;
    height:500px;
    background:' . $bg . ';
    border-radius:8px;
    overflow:hidden;
}
.op-3d-viewer model-viewer {
    width:100%;
    height:100%;
    --poster-color: transparent;
}
.op-3d-viewer--alongside {
    max-width:50%;
    float:left;
    margin-right:20px;
}
.op-3d-viewer__lightbox-btn {
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:10px 20px;
    background:var(--color-accent, #b8860b);
    color:#fff;
    border:none;
    border-radius:4px;
    cursor:pointer;
    font-size:14px;
    margin-top:10px;
}
.op-3d-viewer__lightbox {
    display:none;
    position:fixed;
    inset:0;
    z-index:99999;
    background:rgba(0,0,0,0.9);
    align-items:center;
    justify-content:center;
}
.op-3d-viewer__lightbox.is-open {
    display:flex;
}
.op-3d-viewer__lightbox model-viewer {
    width:90vw;
    height:90vh;
}
.op-3d-viewer__lightbox-close {
    position:absolute;
    top:20px;right:20px;
    background:none;
    border:none;
    color:#fff;
    font-size:30px;
    cursor:pointer;
    z-index:1;
}
';

        return $css;
    }

    public function add_meta_box() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        add_meta_box(
            'opulentia_3d_product',
            __( '3D Model', 'opulentia' ),
            array( $this, 'render_meta_box' ),
            'product',
            'side',
            'default'
        );
    }

    public function render_meta_box( $post ) {
        wp_nonce_field( 'opulentia_3d_product', 'opulentia_3d_product_nonce' );

        $model_id  = (int) get_post_meta( $post->ID, '_op_3d_model_id', true );
        $model_url = get_post_meta( $post->ID, '_op_3d_model_url', true );
        $preview   = $model_id ? wp_get_attachment_url( $model_id ) : '';
        ?>
        <p>
            <label for="op_3d_model_upload"><?php esc_html_e( 'Upload GLB/GLTF Model', 'opulentia' ); ?></label>
            <input type="url" name="_op_3d_model_url" id="op_3d_model_url" value="<?php echo esc_url( $model_url ); ?>" style="width:100%;margin-top:5px" placeholder="<?php esc_attr_e( 'Or paste model URL', 'opulentia' ); ?>">
        </p>
        <p>
            <button type="button" class="button" id="op-3d-upload-btn"><?php esc_html_e( 'Upload Model', 'opulentia' ); ?></button>
            <input type="hidden" name="_op_3d_model_id" id="op_3d_model_id" value="<?php echo esc_attr( $model_id ); ?>">
        </p>
        <?php if ( $preview ) : ?>
            <p style="font-size:12px;color:#666"><?php esc_html_e( 'Model uploaded.', 'opulentia' ); ?></p>
        <?php endif; ?>
        <p style="font-size:12px;color:#666"><?php esc_html_e( 'Supported: .glb, .gltf', 'opulentia' ); ?></p>

        <script>
        (function($){
            $('#op-3d-upload-btn').on('click', function(e) {
                e.preventDefault();
                var frame = wp.media({
                    title: '<?php echo esc_js( __( 'Select 3D Model', 'opulentia' ) ); ?>',
                    library: { type: ['application/octet-stream', 'model/gltf-binary', 'model/gltf+json'] },
                    multiple: false
                });
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#op_3d_model_url').val(attachment.url);
                    $('#op_3d_model_id').val(attachment.id);
                });
                frame.open();
            });
        })(jQuery);
        </script>
        <?php
    }

    public function save_meta_box( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! isset( $_POST['opulentia_3d_product_nonce'] ) ) {
            return;
        }
        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['opulentia_3d_product_nonce'] ) ), 'opulentia_3d_product' ) ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['_op_3d_model_url'] ) ) {
            update_post_meta( $post_id, '_op_3d_model_url', esc_url_raw( wp_unslash( $_POST['_op_3d_model_url'] ) ) );
        }
        if ( isset( $_POST['_op_3d_model_id'] ) ) {
            update_post_meta( $post_id, '_op_3d_model_id', absint( $_POST['_op_3d_model_id'] ) );
        }
    }

    public function render_viewer( $html, $post_thumbnail_id ) {
        if ( ! $this->is_enabled() || ! is_product() ) {
            return $html;
        }

        $product_id = get_the_ID();
        if ( ! $this->has_model( $product_id ) ) {
            return $html;
        }

        $placement = get_theme_mod( 'op_3d_placement', 'replace' );

        if ( 'lightbox' === $placement ) {
            return $html . '<button type="button" class="op-3d-viewer__lightbox-btn" id="op-3d-lightbox-trigger">' . esc_html__( 'View in 3D', 'opulentia' ) . '</button>';
        }

        if ( 'replace' === $placement ) {
            return '<div class="op-3d-viewer" id="op-3d-viewer"><model-viewer id="op-model" loading="eager" reveal="auto"></model-viewer></div>';
        }

        if ( 'alongside' === $placement ) {
            return $html . '<div class="op-3d-viewer op-3d-viewer--alongside" id="op-3d-viewer"><model-viewer id="op-model" loading="eager" reveal="auto"></model-viewer></div>';
        }

        return $html;
    }

    public function body_class( $classes ) {
        if ( $this->is_enabled() && function_exists( 'is_product' ) && is_product() && $this->has_model() ) {
            $classes[] = 'op-has-3d-model';
        }
        return $classes;
    }
}
