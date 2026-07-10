<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Opulentia_Layout_Library {

    private static $instance = null;

    private $templates_dir;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->templates_dir = Opulentia_DIR . '/inc/modules/layout-library/templates';
        add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
        add_action( 'wp_ajax_op_import_layout', array( $this, 'ajax_import_layout' ) );
        add_action( 'wp_ajax_op_export_layout', array( $this, 'ajax_export_layout' ) );
    }

    public function add_admin_page() {
        add_theme_page(
            __( 'Layout Library', 'opulentia' ),
            __( 'Layout Library', 'opulentia' ),
            'manage_options',
            'opulentia-layout-library',
            array( $this, 'render_admin_page' )
        );
    }

    public function admin_enqueue( $hook ) {
        if ( 'appearance_page_opulentia-layout-library' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'opulentia-layout-library',
            Opulentia_URI . '/inc/modules/layout-library/admin.css',
            array(),
            Opulentia_VERSION
        );

        wp_enqueue_script(
            'opulentia-layout-library',
            Opulentia_URI . '/inc/modules/layout-library/admin.js',
            array(),
            Opulentia_VERSION,
            true
        );

        wp_localize_script( 'opulentia-layout-library', 'OpLayoutLib', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'op_layout_lib' ),
            'i18n'    => array(
                'importConfirm' => __( 'Import this layout? It will insert the section into your current page content.', 'opulentia' ),
                'importSuccess' => __( 'Layout imported successfully!', 'opulentia' ),
                'importError'   => __( 'Import failed. Please try again.', 'opulentia' ),
                'exportSuccess' => __( 'Layout exported successfully!', 'opulentia' ),
                'exportError'   => __( 'Export failed. Please try again.', 'opulentia' ),
                'preview'       => __( 'Preview', 'opulentia' ),
                'import'        => __( 'Import', 'opulentia' ),
            ),
        ) );
    }

    public function render_admin_page() {
        $categories = $this->get_categories();
        $templates  = $this->get_all_templates();
        $industries = $this->get_industries();
        ?>
        <div class="wrap op-layout-library">
            <h1><?php esc_html_e( 'Layout Library', 'opulentia' ); ?></h1>
            <p class="description"><?php esc_html_e( 'Pre-built sections for your Opulentia theme. Import them as reusable blocks or template parts.', 'opulentia' ); ?></p>

            <div class="op-layout-filters">
                <select id="op-filter-category" class="op-filter-select">
                    <option value=""><?php esc_html_e( 'All Categories', 'opulentia' ); ?></option>
                    <?php foreach ( $categories as $slug => $label ) : ?>
                        <option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>

                <select id="op-filter-industry" class="op-filter-select">
                    <option value=""><?php esc_html_e( 'All Industries', 'opulentia' ); ?></option>
                    <?php foreach ( $industries as $slug => $label ) : ?>
                        <option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="button" class="button op-export-btn" id="op-export-layout">
                    <span class="dashicons dashicons-download"></span> <?php esc_html_e( 'Export Custom', 'opulentia' ); ?>
                </button>

                <label class="button op-import-btn">
                    <span class="dashicons dashicons-upload"></span> <?php esc_html_e( 'Import JSON', 'opulentia' ); ?>
                    <input type="file" id="op-import-file" accept=".json" style="display:none">
                </label>
            </div>

            <div class="op-layout-grid" id="op-layout-grid">
                <?php foreach ( $templates as $slug => $tmpl ) : ?>
                    <div class="op-layout-card"
                         data-category="<?php echo esc_attr( $tmpl['category'] ); ?>"
                         data-industry="<?php echo esc_attr( $tmpl['industry'] ); ?>">
                        <div class="op-layout-card__preview">
                            <?php if ( ! empty( $tmpl['thumbnail'] ) ) : ?>
                                <img src="<?php echo esc_url( $tmpl['thumbnail'] ); ?>" alt="<?php echo esc_attr( $tmpl['name'] ); ?>">
                            <?php else : ?>
                                <div class="op-layout-card__placeholder">
                                    <span class="dashicons dashicons-layout"></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="op-layout-card__body">
                            <h3><?php echo esc_html( $tmpl['name'] ); ?></h3>
                            <p><?php echo esc_html( $tmpl['description'] ); ?></p>
                            <span class="op-layout-card__badge"><?php echo esc_html( $categories[ $tmpl['category'] ] ?? $tmpl['category'] ); ?></span>
                        </div>
                        <div class="op-layout-card__actions">
                            <button type="button" class="button op-preview-layout" data-slug="<?php echo esc_attr( $slug ); ?>">
                                <?php esc_html_e( 'Preview', 'opulentia' ); ?>
                            </button>
                            <button type="button" class="button button-primary op-import-layout" data-slug="<?php echo esc_attr( $slug ); ?>">
                                <?php esc_html_e( 'Import', 'opulentia' ); ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="op-preview-modal" class="op-modal" style="display:none;">
            <div class="op-modal__backdrop"></div>
            <div class="op-modal__content">
                <button type="button" class="op-modal__close dashicons dashicons-no-alt"></button>
                <div class="op-modal__body"></div>
            </div>
        </div>
        <?php
    }

    private function get_categories() {
        return array(
            'hero'        => __( 'Hero', 'opulentia' ),
            'features'    => __( 'Features', 'opulentia' ),
            'testimonials' => __( 'Testimonials', 'opulentia' ),
            'pricing'     => __( 'Pricing', 'opulentia' ),
            'faq'         => __( 'FAQ', 'opulentia' ),
            'cta'         => __( 'CTA', 'opulentia' ),
            'footer'      => __( 'Footer', 'opulentia' ),
            'header'      => __( 'Header', 'opulentia' ),
            'about'       => __( 'About', 'opulentia' ),
            'team'        => __( 'Team', 'opulentia' ),
            'contact'     => __( 'Contact', 'opulentia' ),
            'portfolio'   => __( 'Portfolio', 'opulentia' ),
            'blog'        => __( 'Blog', 'opulentia' ),
        );
    }

    private function get_industries() {
        return array(
            'business'  => __( 'Business', 'opulentia' ),
            'portfolio' => __( 'Portfolio', 'opulentia' ),
            'ecommerce' => __( 'Ecommerce', 'opulentia' ),
            'landing'   => __( 'Landing Page', 'opulentia' ),
        );
    }

    private function get_all_templates() {
        $templates = array();
        $files     = glob( $this->templates_dir . '/*.json' );

        foreach ( $files as $file ) {
            $data = json_decode( file_get_contents( $file ), true );
            if ( $data && isset( $data['slug'] ) ) {
                $templates[ $data['slug'] ] = $data;
            }
        }

        return $templates;
    }

    public function get_template( $slug ) {
        $file = $this->templates_dir . '/' . basename( $slug ) . '.json';
        if ( ! file_exists( $file ) ) {
            return false;
        }
        return json_decode( file_get_contents( $file ), true );
    }

    public function ajax_import_layout() {
        check_ajax_referer( 'op_layout_lib', 'nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'opulentia' ) ) );
        }

        $slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';

        if ( empty( $slug ) ) {
            wp_send_json_error( array( 'message' => __( 'No layout specified.', 'opulentia' ) ) );
        }

        $template = $this->get_template( $slug );

        if ( ! $template || empty( $template['content'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Layout not found.', 'opulentia' ) ) );
        }

        $post = array(
            'post_title'   => $template['name'] ?? $slug,
            'post_content' => $template['content'],
            'post_status'  => 'publish',
            'post_type'    => 'wp_block',
        );

        $post_id = wp_insert_post( $post );

        if ( is_wp_error( $post_id ) ) {
            wp_send_json_error( array( 'message' => $post_id->get_error_message() ) );
        }

        wp_send_json_success( array(
            'message' => __( 'Layout imported as reusable block.', 'opulentia' ),
            'post_id' => $post_id,
            'edit_url' => admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
        ) );
    }

    public function ajax_export_layout() {
        check_ajax_referer( 'op_layout_lib', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'opulentia' ) ) );
        }

        $name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
        $content = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';

        if ( empty( $name ) || empty( $content ) ) {
            wp_send_json_error( array( 'message' => __( 'Name and content required.', 'opulentia' ) ) );
        }

        $export = array(
            'slug'        => sanitize_title( $name ),
            'name'        => $name,
            'description' => __( 'Custom exported layout', 'opulentia' ),
            'category'    => 'custom',
            'industry'    => 'landing',
            'content'     => $content,
        );

        wp_send_json_success( array(
            'json' => wp_json_encode( $export, JSON_PRETTY_PRINT ),
        ) );
    }

    public function import_custom_json( $json_data ) {
        $data = json_decode( $json_data, true );

        if ( ! $data || empty( $data['content'] ) ) {
            return false;
        }

        $post = array(
            'post_title'   => $data['name'] ?? __( 'Imported Layout', 'opulentia' ),
            'post_content' => $data['content'],
            'post_status'  => 'publish',
            'post_type'    => 'wp_block',
        );

        $post_id = wp_insert_post( $post );

        return $post_id && ! is_wp_error( $post_id ) ? $post_id : false;
    }
}
