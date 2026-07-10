<?php
/**
 * Hook & Filter Registration — Singleton
 *
 * Defines all Opulentia_*_before/after action hooks and
 * Opulentia_* filter hooks used across template files.
 * Mirrors Astra's extensibility pattern: every major template
 * section has before/after hooks for plugin/child-theme injection.
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Opulentia_Hooks class.
 */
class Opulentia_Hooks {

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
     * Constructor — registers all action/filter hooks.
     */
    private function __construct() {
        $this->register_body_hooks();
        $this->register_header_hooks();
        $this->register_content_hooks();
        $this->register_footer_hooks();
        $this->register_sidebar_hooks();
        $this->register_blog_hooks();
        $this->register_page_hooks();
    }

    // -------------------------------------------------------------------------
    // Body Hooks
    // -------------------------------------------------------------------------

    /**
     * Register body-level action hooks.
     */
    private function register_body_hooks() {
        // Before/after #primary wrapper
        add_action( 'Opulentia_body_top', array( $this, 'do_body_top' ) );
        add_action( 'Opulentia_body_bottom', array( $this, 'do_body_bottom' ) );
    }

    /**
     * Body top hook.
     */
    public function do_body_top() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Body bottom hook.
     */
    public function do_body_bottom() {
        // Default: no-op. Override via remove_action/add_action.
    }

    // -------------------------------------------------------------------------
    // Header Hooks
    // -------------------------------------------------------------------------

    /**
     * Register header-level action hooks.
     */
    private function register_header_hooks() {
        add_action( 'Opulentia_header_before', array( $this, 'do_header_before' ) );
        add_action( 'Opulentia_header_after', array( $this, 'do_header_after' ) );
        add_action( 'Opulentia_header_top', array( $this, 'do_header_top' ) );
        add_action( 'Opulentia_header_bottom', array( $this, 'do_header_bottom' ) );

        // Header sub-section hooks
        add_action( 'Opulentia_header_main_before', array( $this, 'do_header_main_before' ) );
        add_action( 'Opulentia_header_main_after', array( $this, 'do_header_main_after' ) );
        add_action( 'Opulentia_header_top_bar_before', array( $this, 'do_header_top_bar_before' ) );
        add_action( 'Opulentia_header_top_bar_after', array( $this, 'do_header_top_bar_after' ) );
        add_action( 'Opulentia_masthead_before', array( $this, 'do_masthead_before' ) );
        add_action( 'Opulentia_masthead_after', array( $this, 'do_masthead_after' ) );
    }

    /**
     * Header before hook.
     */
        public function do_header_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Header after hook.
     */
        public function do_header_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Header top hook.
     */
        public function do_header_top() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Header bottom hook.
     */
        public function do_header_bottom() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Header main before hook.
     */
        public function do_header_main_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Header main after hook.
     */
        public function do_header_main_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Header top bar before hook.
     */
        public function do_header_top_bar_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Header top bar after hook.
     */
        public function do_header_top_bar_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Masthead before hook.
     */
        public function do_masthead_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Masthead after hook.
     */
        public function do_masthead_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    // -------------------------------------------------------------------------
    // Content Hooks
    // -------------------------------------------------------------------------

    /**
     * Register content-level action hooks.
     */
    private function register_content_hooks() {
        add_action( 'Opulentia_primary_content_before', array( $this, 'do_primary_content_before' ) );
        add_action( 'Opulentia_primary_content_after', array( $this, 'do_primary_content_after' ) );

        add_action( 'Opulentia_content_top', array( $this, 'do_content_top' ) );
        add_action( 'Opulentia_content_bottom', array( $this, 'do_content_bottom' ) );

        add_action( 'Opulentia_entry_before', array( $this, 'do_entry_before' ) );
        add_action( 'Opulentia_entry_after', array( $this, 'do_entry_after' ) );
        add_action( 'Opulentia_entry_content_before', array( $this, 'do_entry_content_before' ) );
        add_action( 'Opulentia_entry_content_after', array( $this, 'do_entry_content_after' ) );

        add_action( 'Opulentia_pagination_before', array( $this, 'do_pagination_before' ) );
        add_action( 'Opulentia_pagination_after', array( $this, 'do_pagination_after' ) );
    }

    /**
     * Primary content before hook.
     */
        public function do_primary_content_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Primary content after hook.
     */
        public function do_primary_content_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Content top hook.
     */
        public function do_content_top() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Content bottom hook.
     */
        public function do_content_bottom() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Entry before hook.
     */
        public function do_entry_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Entry after hook.
     */
        public function do_entry_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Entry content before hook.
     */
        public function do_entry_content_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Entry content after hook.
     */
        public function do_entry_content_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Pagination before hook.
     */
        public function do_pagination_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Pagination after hook.
     */
        public function do_pagination_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    // -------------------------------------------------------------------------
    // Footer Hooks
    // -------------------------------------------------------------------------

    /**
     * Register footer-level action hooks.
     */
    private function register_footer_hooks() {
        add_action( 'Opulentia_footer_before', array( $this, 'do_footer_before' ) );
        add_action( 'Opulentia_footer_after', array( $this, 'do_footer_after' ) );
        add_action( 'Opulentia_footer_top', array( $this, 'do_footer_top' ) );
        add_action( 'Opulentia_footer_bottom', array( $this, 'do_footer_bottom' ) );

        add_action( 'Opulentia_footer_grid_before', array( $this, 'do_footer_grid_before' ) );
        add_action( 'Opulentia_footer_grid_after', array( $this, 'do_footer_grid_after' ) );
        add_action( 'Opulentia_footer_bottom_bar_before', array( $this, 'do_footer_bottom_bar_before' ) );
        add_action( 'Opulentia_footer_bottom_bar_after', array( $this, 'do_footer_bottom_bar_after' ) );

        add_action( 'Opulentia_newsletter_before', array( $this, 'do_newsletter_before' ) );
        add_action( 'Opulentia_newsletter_after', array( $this, 'do_newsletter_after' ) );
        add_action( 'Opulentia_trust_badges_before', array( $this, 'do_trust_badges_before' ) );
        add_action( 'Opulentia_trust_badges_after', array( $this, 'do_trust_badges_after' ) );

        add_action( 'Opulentia_colophon_before', array( $this, 'do_colophon_before' ) );
        add_action( 'Opulentia_colophon_after', array( $this, 'do_colophon_after' ) );
    }

    /**
     * Footer before hook.
     */
        public function do_footer_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Footer after hook.
     */
        public function do_footer_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Footer top hook.
     */
        public function do_footer_top() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Footer bottom hook.
     */
        public function do_footer_bottom() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Footer grid before hook.
     */
        public function do_footer_grid_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Footer grid after hook.
     */
        public function do_footer_grid_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Footer bottom bar before hook.
     */
        public function do_footer_bottom_bar_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Footer bottom bar after hook.
     */
        public function do_footer_bottom_bar_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Newsletter before hook.
     */
        public function do_newsletter_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Newsletter after hook.
     */
        public function do_newsletter_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Trust badges before hook.
     */
        public function do_trust_badges_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Trust badges after hook.
     */
        public function do_trust_badges_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Colophon before hook.
     */
        public function do_colophon_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Colophon after hook.
     */
        public function do_colophon_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    // -------------------------------------------------------------------------
    // Sidebar Hooks
    // -------------------------------------------------------------------------

    /**
     * Register sidebar-level action hooks.
     */
    private function register_sidebar_hooks() {
        add_action( 'Opulentia_sidebar_before', array( $this, 'do_sidebar_before' ) );
        add_action( 'Opulentia_sidebar_after', array( $this, 'do_sidebar_after' ) );
    }

    /**
     * Sidebar before hook.
     */
        public function do_sidebar_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Sidebar after hook.
     */
        public function do_sidebar_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    // -------------------------------------------------------------------------
    // Blog Hooks
    // -------------------------------------------------------------------------

    /**
     * Register blog/archive-level action hooks.
     */
    private function register_blog_hooks() {
        add_action( 'Opulentia_blog_before', array( $this, 'do_blog_before' ) );
        add_action( 'Opulentia_blog_after', array( $this, 'do_blog_after' ) );
        add_action( 'Opulentia_blog_header_before', array( $this, 'do_blog_header_before' ) );
        add_action( 'Opulentia_blog_header_after', array( $this, 'do_blog_header_after' ) );

        add_action( 'Opulentia_single_before', array( $this, 'do_single_before' ) );
        add_action( 'Opulentia_single_after', array( $this, 'do_single_after' ) );
        add_action( 'Opulentia_single_header_before', array( $this, 'do_single_header_before' ) );
        add_action( 'Opulentia_single_header_after', array( $this, 'do_single_header_after' ) );
        add_action( 'Opulentia_single_footer_before', array( $this, 'do_single_footer_before' ) );
        add_action( 'Opulentia_single_footer_after', array( $this, 'do_single_footer_after' ) );

        add_action( 'Opulentia_archive_before', array( $this, 'do_archive_before' ) );
        add_action( 'Opulentia_archive_after', array( $this, 'do_archive_after' ) );
        add_action( 'Opulentia_archive_header_before', array( $this, 'do_archive_header_before' ) );
        add_action( 'Opulentia_archive_header_after', array( $this, 'do_archive_header_after' ) );

        add_action( 'Opulentia_search_before', array( $this, 'do_search_before' ) );
        add_action( 'Opulentia_search_after', array( $this, 'do_search_after' ) );
        add_action( 'Opulentia_search_header_before', array( $this, 'do_search_header_before' ) );
        add_action( 'Opulentia_search_header_after', array( $this, 'do_search_header_after' ) );

        add_action( 'Opulentia_404_before', array( $this, 'do_404_before' ) );
        add_action( 'Opulentia_404_after', array( $this, 'do_404_after' ) );
    }

    /**
     * Blog before hook.
     */
        public function do_blog_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Blog after hook.
     */
        public function do_blog_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Blog header before hook.
     */
        public function do_blog_header_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Blog header after hook.
     */
        public function do_blog_header_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Single before hook.
     */
        public function do_single_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Single after hook.
     */
        public function do_single_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Single header before hook.
     */
        public function do_single_header_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Single header after hook.
     */
        public function do_single_header_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Single footer before hook.
     */
        public function do_single_footer_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Single footer after hook.
     */
        public function do_single_footer_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Archive before hook.
     */
        public function do_archive_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Archive after hook.
     */
        public function do_archive_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Archive header before hook.
     */
        public function do_archive_header_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Archive header after hook.
     */
        public function do_archive_header_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Search before hook.
     */
        public function do_search_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Search after hook.
     */
        public function do_search_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Search header before hook.
     */
        public function do_search_header_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Search header after hook.
     */
        public function do_search_header_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * 404 before hook.
     */
    public function do_404_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * 404 after hook.
     */
    public function do_404_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    // -------------------------------------------------------------------------
    // Page Hooks
    // -------------------------------------------------------------------------

    /**
     * Register page-level action hooks.
     */
    private function register_page_hooks() {
        add_action( 'Opulentia_page_before', array( $this, 'do_page_before' ) );
        add_action( 'Opulentia_page_after', array( $this, 'do_page_after' ) );
        add_action( 'Opulentia_page_header_before', array( $this, 'do_page_header_before' ) );
        add_action( 'Opulentia_page_header_after', array( $this, 'do_page_header_after' ) );
        add_action( 'Opulentia_front_page_before', array( $this, 'do_front_page_before' ) );
        add_action( 'Opulentia_front_page_after', array( $this, 'do_front_page_after' ) );
    }

    /**
     * Page before hook.
     */
        public function do_page_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Page after hook.
     */
        public function do_page_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Page header before hook.
     */
        public function do_page_header_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Page header after hook.
     */
        public function do_page_header_after() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Front page before hook.
     */
        public function do_front_page_before() {
        // Default: no-op. Override via remove_action/add_action.
    }

    /**
     * Front page after hook.
     */
        public function do_front_page_after() {
        // Default: no-op. Override via remove_action/add_action.
    }
}
