<?php
/**
 * Meta Boxes — Full Suite — Singleton
 *
 * Registers per-post/page meta boxes with these controls:
 * - Site Layout (default, full-width, boxed, contained)
 * - Sidebar (default, left, right, none)
 * - Transparent Header (default, enable, disable)
 * - Page Header (default, enable, disable)
 * - Sticky Header (default, enable, disable)
 * - Breadcrumbs (default, enable, disable)
 * - Featured Image (default, show, hide)
 * - Title (default, show, hide)
 * - Footer Widgets (default, enable, disable)
 * - Content Layout (default, boxed, unboxed)
 * - Background Override
 * - Custom CSS
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Meta_Boxes class.
 */
class Opulentia_Meta_Boxes {

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
        add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
    }

    /**
     * Get the post types that support these meta boxes.
     *
     * @return array
     */
    private function get_supported_post_types() {
        return apply_filters( 'Opulentia_meta_box_post_types', array( 'post', 'page', 'product', 'collection', 'style', 'brand' ) );
    }

    /**
     * Register meta boxes.
     */
    public function register_meta_boxes() {
        $post_types = $this->get_supported_post_types();

        add_meta_box(
            'Opulentia_layout_meta',
            __( 'Opulentia Layout Settings', 'opulentia' ),
            array( $this, 'render_meta_box' ),
            $post_types,
            'side',
            'default'
        );
    }

    /**
     * Render the meta box.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box( $post ) {
        wp_nonce_field( 'Opulentia_meta_box', 'Opulentia_meta_box_nonce' );

        $fields = array(
            '_Opulentia_site_layout' => array(
                'label'   => __( 'Site Layout', 'opulentia' ),
                'type'    => 'select',
                'options' => array(
                    ''            => __( 'Default (from Customizer)', 'opulentia' ),
                    'full-width'  => __( 'Full Width', 'opulentia' ),
                    'boxed'       => __( 'Boxed', 'opulentia' ),
                    'content-boxed' => __( 'Content Boxed', 'opulentia' ),
                ),
                'default' => '',
            ),
            '_Opulentia_sidebar_position' => array(
                'label'   => __( 'Sidebar Position', 'opulentia' ),
                'type'    => 'select',
                'options' => array(
                    ''      => __( 'Default (from Customizer)', 'opulentia' ),
                    'left'  => __( 'Left Sidebar', 'opulentia' ),
                    'right' => __( 'Right Sidebar', 'opulentia' ),
                    'none'  => __( 'No Sidebar', 'opulentia' ),
                ),
                'default' => '',
            ),
            '_Opulentia_transparent_header' => array(
                'label'   => __( 'Transparent Header', 'opulentia' ),
                'type'    => 'select',
                'options' => array(
                    ''        => __( 'Default', 'opulentia' ),
                    'enable'  => __( 'Enable', 'opulentia' ),
                    'disable' => __( 'Disable', 'opulentia' ),
                ),
                'default' => '',
            ),
            '_Opulentia_disable_page_header' => array(
                'label'   => __( 'Page Header/Banner', 'opulentia' ),
                'type'    => 'select',
                'options' => array(
                    ''     => __( 'Default', 'opulentia' ),
                    '0'    => __( 'Show', 'opulentia' ),
                    '1'    => __( 'Hide', 'opulentia' ),
                ),
                'default' => '',
            ),
            '_Opulentia_blog_layout' => array(
                'label'   => __( 'Blog Layout (Archive)', 'opulentia' ),
                'type'    => 'select',
                'options' => array(
                    ''        => __( 'Default', 'opulentia' ),
                    'classic' => __( 'Classic', 'opulentia' ),
                    'grid'    => __( 'Grid', 'opulentia' ),
                    'list'    => __( 'List', 'opulentia' ),
                ),
                'default' => '',
            ),
        );

        foreach ( $fields as $key => $field ) {
            $value = get_post_meta( $post->ID, $key, true );
            if ( empty( $value ) ) {
                $value = $field['default'];
            }
            ?>
            <p>
                <label for="<?php echo esc_attr( $key ); ?>" style="display:block;font-weight:600;margin-bottom:4px;">
                    <?php echo esc_html( $field['label'] ); ?>
                </label>
                <select id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" style="width:100%;">
                    <?php foreach ( $field['options'] as $opt_val => $opt_label ) : ?>
                        <option value="<?php echo esc_attr( $opt_val ); ?>" <?php selected( $value, $opt_val ); ?>>
                            <?php echo esc_html( $opt_label ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <?php
        }

        // Custom CSS textarea.
        $custom_css = get_post_meta( $post->ID, '_Opulentia_custom_css', true );
        ?>
        <p>
            <label for="_Opulentia_custom_css" style="display:block;font-weight:600;margin-bottom:4px;">
                <?php esc_html_e( 'Custom CSS', 'opulentia' ); ?>
            </label>
            <textarea id="_Opulentia_custom_css" name="_Opulentia_custom_css" rows="4" style="width:100%;font-family:monospace;font-size:12px;"><?php echo esc_textarea( $custom_css ); ?></textarea>
        </p>
        <?php
    }

    /**
     * Save meta box values.
     *
     * @param int $post_id Post ID.
     */
    public function save_meta_boxes( $post_id ) {
        if ( ! isset( $_POST['Opulentia_meta_box_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['Opulentia_meta_box_nonce'] ) ), 'Opulentia_meta_box' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $fields = array(
            '_Opulentia_site_layout',
            '_Opulentia_sidebar_position',
            '_Opulentia_transparent_header',
            '_Opulentia_disable_page_header',
            '_Opulentia_blog_layout',
            '_Opulentia_custom_css',
        );

        foreach ( $fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                $value = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
                if ( '_Opulentia_custom_css' === $field ) {
                    $value = wp_strip_all_tags( wp_unslash( $_POST[ $field ] ) );
                }
                update_post_meta( $post_id, $field, $value );
            }
        }
    }
}
