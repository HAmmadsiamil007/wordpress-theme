<?php
/**
 * Blog Metabox — Per-Page Layout Override
 *
 * Adds a metabox to posts and pages that allows overriding
 * the global blog layout, image aspect ratio, and featured
 * image visibility on a per-post basis.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Blog_Metabox class.
 */
class Opulentia_Blog_Metabox {

    /**
     * Singleton instance.
     *
     * @var self|null
     */
    private static $instance = null;

    /**
     * Returns the singleton instance.
     *
     * @return self
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor — registers hooks.
     */
    private function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_meta_box' ) );
    }

    /**
     * Add the metabox to post edit screens.
     */
    public function add_meta_box() {
        $screens = apply_filters( 'Opulentia_blog_metabox_screens', array( 'post', 'page' ) );

        foreach ( $screens as $screen ) {
            add_meta_box(
                'Opulentia_blog_settings',
                __( 'Blog / Page Settings', 'opulentia' ),
                array( $this, 'render_meta_box' ),
                $screen,
                'side',
                'default'
            );
        }
    }

    /**
     * Render the metabox contents.
     *
     * @param WP_Post $post The current post object.
     */
    public function render_meta_box( $post ) {
        wp_nonce_field( 'Opulentia_blog_metabox', 'Opulentia_blog_metabox_nonce' );

        $layout        = get_post_meta( $post->ID, '_Opulentia_blog_layout', true );
        $aspect_ratio  = get_post_meta( $post->ID, '_Opulentia_blog_image_aspect_ratio', true );
        $hide_thumb    = get_post_meta( $post->ID, '_Opulentia_blog_hide_featured_image', true );
        ?>

        <p>
            <label for="opulentia-blog-layout"><?php esc_html_e( 'Blog Layout Override', 'opulentia' ); ?></label>
            <select id="opulentia-blog-layout" name="Opulentia_blog_layout" style="width:100%;">
                <option value=""><?php esc_html_e( 'Default (Global Setting)', 'opulentia' ); ?></option>
                <option value="classic" <?php selected( $layout, 'classic' ); ?>><?php esc_html_e( 'Classic', 'opulentia' ); ?></option>
                <option value="grid" <?php selected( $layout, 'grid' ); ?>><?php esc_html_e( 'Grid', 'opulentia' ); ?></option>
                <option value="list" <?php selected( $layout, 'list' ); ?>><?php esc_html_e( 'List', 'opulentia' ); ?></option>
            </select>
        </p>

        <p>
            <label for="opulentia-blog-aspect-ratio"><?php esc_html_e( 'Image Aspect Ratio', 'opulentia' ); ?></label>
            <select id="opulentia-blog-aspect-ratio" name="Opulentia_blog_image_aspect_ratio" style="width:100%;">
                <option value=""><?php esc_html_e( 'Default (Global Setting)', 'opulentia' ); ?></option>
                <option value="16/10" <?php selected( $aspect_ratio, '16/10' ); ?>>16:10</option>
                <option value="16/9" <?php selected( $aspect_ratio, '16/9' ); ?>>16:9</option>
                <option value="4/3" <?php selected( $aspect_ratio, '4/3' ); ?>>4:3</option>
                <option value="3/2" <?php selected( $aspect_ratio, '3/2' ); ?>>3:2</option>
                <option value="1/1" <?php selected( $aspect_ratio, '1/1' ); ?>>1:1 (Square)</option>
                <option value="original" <?php selected( $aspect_ratio, 'original' ); ?>><?php esc_html_e( 'Original (No Crop)', 'opulentia' ); ?></option>
            </select>
        </p>

        <p>
            <label>
                <input type="checkbox" name="Opulentia_blog_hide_featured_image" value="1" <?php checked( $hide_thumb, '1' ); ?> />
                <?php esc_html_e( 'Hide Featured Image', 'opulentia' ); ?>
            </label>
        </p>

        <?php
    }

    /**
     * Save the metabox values.
     *
     * @param int $post_id The post ID.
     */
    public function save_meta_box( $post_id ) {
        // Verify nonce.
        if ( ! isset( $_POST['Opulentia_blog_metabox_nonce'] )
            || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['Opulentia_blog_metabox_nonce'] ) ), 'Opulentia_blog_metabox' ) ) {
            return;
        }

        // Don't save on autosave.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check permissions.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save layout override.
        $layout = isset( $_POST['Opulentia_blog_layout'] ) ? sanitize_text_field( wp_unslash( $_POST['Opulentia_blog_layout'] ) ) : '';
        $valid_layouts = array( '', 'classic', 'grid', 'list' );
        if ( ! in_array( $layout, $valid_layouts, true ) ) {
            $layout = '';
        }

        if ( ! empty( $layout ) ) {
            update_post_meta( $post_id, '_Opulentia_blog_layout', $layout );
        } else {
            delete_post_meta( $post_id, '_Opulentia_blog_layout' );
        }

        // Save aspect ratio override.
        $ratio = isset( $_POST['Opulentia_blog_image_aspect_ratio'] ) ? sanitize_text_field( wp_unslash( $_POST['Opulentia_blog_image_aspect_ratio'] ) ) : '';
        $valid_ratios = array( '', '16/10', '16/9', '4/3', '3/2', '1/1', 'original' );
        if ( ! in_array( $ratio, $valid_ratios, true ) ) {
            $ratio = '';
        }

        if ( ! empty( $ratio ) ) {
            update_post_meta( $post_id, '_Opulentia_blog_image_aspect_ratio', $ratio );
        } else {
            delete_post_meta( $post_id, '_Opulentia_blog_image_aspect_ratio' );
        }

        // Save hide featured image.
        $hide_thumb = isset( $_POST['Opulentia_blog_hide_featured_image'] ) ? '1' : '';
        if ( '1' === $hide_thumb ) {
            update_post_meta( $post_id, '_Opulentia_blog_hide_featured_image', '1' );
        } else {
            delete_post_meta( $post_id, '_Opulentia_blog_hide_featured_image' );
        }
    }
}
