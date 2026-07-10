<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_WooCommerce_Product_Video {

    private static $instance = null;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'customize_register', array( $this, 'register_customizer' ), 30 );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_meta_box' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'inline_css' ), 120 );
        add_action( 'woocommerce_before_single_product_summary', array( $this, 'display_video' ), 25 );
    }

    public function register_customizer( $wp_customize ) {
        $wp_customize->add_section( 'opulentia_wc_product_video', array(
            'title'    => __( 'Product Video', 'opulentia' ),
            'panel'    => 'Opulentia_woocommerce',
            'priority' => 35,
        ) );

        $wp_customize->add_setting( 'wc-product-video-enable', array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'wc-product-video-enable', array(
            'label'   => __( 'Enable Product Video', 'opulentia' ),
            'section' => 'opulentia_wc_product_video',
            'type'    => 'checkbox',
        ) );

        $wp_customize->add_setting( 'wc-product-video-position', array(
            'default'           => 'replace',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'wc-product-video-position', array(
            'label'   => __( 'Video Position', 'opulentia' ),
            'section' => 'opulentia_wc_product_video',
            'type'    => 'select',
            'choices' => array(
                'replace'    => __( 'Replace Featured Image', 'opulentia' ),
                'below'      => __( 'Below Featured Image', 'opulentia' ),
                'thumbnail'  => __( 'As Gallery Thumbnail', 'opulentia' ),
            ),
        ) );

        $wp_customize->add_setting( 'wc-product-video-autoplay', array(
            'default'           => false,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( 'wc-product-video-autoplay', array(
            'label'   => __( 'Autoplay Video', 'opulentia' ),
            'section' => 'opulentia_wc_product_video',
            'type'    => 'checkbox',
        ) );
    }

    public function add_meta_box() {
        add_meta_box(
            'opulentia_product_video',
            __( 'Product Video', 'opulentia' ),
            array( $this, 'render_meta_box' ),
            'product',
            'side',
            'default'
        );
    }

    public function render_meta_box( $post ) {
        wp_nonce_field( 'opulentia_product_video_nonce', 'opulentia_product_video_nonce' );
        $video_url = get_post_meta( $post->ID, '_opulentia_product_video_url', true );
        $video_type = get_post_meta( $post->ID, '_opulentia_product_video_type', true );
        ?>
        <p>
            <label for="opulentia_product_video_url"><?php esc_html_e( 'Video URL:', 'opulentia' ); ?></label>
            <input type="url" name="opulentia_product_video_url" id="opulentia_product_video_url" value="<?php echo esc_attr( $video_url ); ?>" style="width:100%;" placeholder="https://youtube.com/watch?v=...">
        </p>
        <p>
            <label for="opulentia_product_video_type"><?php esc_html_e( 'Video Type:', 'opulentia' ); ?></label>
            <select name="opulentia_product_video_type" id="opulentia_product_video_type" style="width:100%;">
                <option value="youtube" <?php selected( $video_type, 'youtube' ); ?>><?php esc_html_e( 'YouTube', 'opulentia' ); ?></option>
                <option value="vimeo" <?php selected( $video_type, 'vimeo' ); ?>><?php esc_html_e( 'Vimeo', 'opulentia' ); ?></option>
                <option value="self" <?php selected( $video_type, 'self' ); ?>><?php esc_html_e( 'Self-Hosted (MP4)', 'opulentia' ); ?></option>
            </select>
        </p>
        <p class="description"><?php esc_html_e( 'Supports YouTube, Vimeo, or self-hosted MP4 URLs.', 'opulentia' ); ?></p>
        <?php
    }

    public function save_meta_box( $post_id ) {
        if ( ! isset( $_POST['opulentia_product_video_nonce'] ) || ! wp_verify_nonce( $_POST['opulentia_product_video_nonce'], 'opulentia_product_video_nonce' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['opulentia_product_video_url'] ) ) {
            update_post_meta( $post_id, '_opulentia_product_video_url', esc_url_raw( $_POST['opulentia_product_video_url'] ) );
        }
        if ( isset( $_POST['opulentia_product_video_type'] ) ) {
            update_post_meta( $post_id, '_opulentia_product_video_type', sanitize_text_field( $_POST['opulentia_product_video_type'] ) );
        }
    }

    public function display_video() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }
        if ( ! Opulentia_get_option( 'wc-product-video-enable', true ) ) {
            return;
        }
        if ( ! is_singular( 'product' ) ) {
            return;
        }

        $product_id = get_the_ID();
        $video_url  = get_post_meta( $product_id, '_opulentia_product_video_url', true );
        $video_type = get_post_meta( $product_id, '_opulentia_product_video_type', 'youtube' );
        $position   = Opulentia_get_option( 'wc-product-video-position', 'replace' );

        if ( empty( $video_url ) ) {
            return;
        }

        $autoplay = Opulentia_get_option( 'wc-product-video-autoplay', false ) ? '1' : '0';
        $autoplay_param = $autoplay;

        ob_start();
        ?>
        <div class="op-product-video op-product-video--<?php echo esc_attr( $position ); ?>">
            <?php if ( 'youtube' === $video_type ) : ?>
                <div class="op-product-video__embed">
                    <iframe src="https://www.youtube.com/embed/<?php echo esc_attr( $this->get_youtube_id( $video_url ) ); ?>?autoplay=<?php echo esc_attr( $autoplay_param ); ?>&rel=0&showinfo=0" frameborder="0" allowfullscreen allow="autoplay; encrypted-media"></iframe>
                </div>
            <?php elseif ( 'vimeo' === $video_type ) : ?>
                <div class="op-product-video__embed">
                    <iframe src="https://player.vimeo.com/video/<?php echo esc_attr( $this->get_vimeo_id( $video_url ) ); ?>?autoplay=<?php echo esc_attr( $autoplay_param ); ?>" frameborder="0" allowfullscreen allow="autoplay"></iframe>
                </div>
            <?php elseif ( 'self' === $video_type ) : ?>
                <div class="op-product-video__self">
                    <video controls <?php echo $autoplay ? 'autoplay muted' : ''; ?> playsinline>
                        <source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
                    </video>
                </div>
            <?php endif; ?>
        </div>
        <?php
        $video_html = ob_get_clean();

        if ( 'replace' === $position ) {
            remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
            echo $video_html;
        } else {
            echo $video_html;
        }
    }

    private function get_youtube_id( $url ) {
        preg_match( '/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches );
        return isset( $matches[1] ) ? $matches[1] : '';
    }

    private function get_vimeo_id( $url ) {
        preg_match( '/(?:vimeo\.com\/(?:video\/)?)(\d+)/', $url, $matches );
        return isset( $matches[1] ) ? $matches[1] : '';
    }

    public function inline_css() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }
        if ( ! Opulentia_get_option( 'wc-product-video-enable', true ) ) {
            return;
        }

        $css = '
        .op-product-video {
            margin-bottom: 20px;
        }
        .op-product-video__embed {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 8px;
            background: #000;
        }
        .op-product-video__embed iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 8px;
        }
        .op-product-video__self video {
            width: 100%;
            border-radius: 8px;
            display: block;
        }
        .op-product-video--below {
            margin-top: 12px;
        }
        .op-product-video--thumbnail .op-product-video__embed {
            border-radius: 4px;
        }
        ';

        wp_add_inline_style( 'opulentia-style', $css );
    }
}
