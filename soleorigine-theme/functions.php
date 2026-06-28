<?php
/**
 * SoleOrigine Theme Functions
 *
 * @package SoleOrigine
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Define constants
 */
define( 'SOLEORIGINE_VERSION', '1.0.0' );
define( 'SOLEORIGINE_DIR', get_template_directory() );
define( 'SOLEORIGINE_URI', get_template_directory_uri() );

/**
 * Include theme files
 */
require SOLEORIGINE_DIR . '/inc/theme-setup.php';
require SOLEORIGINE_DIR . '/inc/enqueue.php';
require SOLEORIGINE_DIR . '/inc/customizer.php';
require SOLEORIGINE_DIR . '/inc/template-tags.php';
require SOLEORIGINE_DIR . '/inc/template-functions.php';
require SOLEORIGINE_DIR . '/inc/widgets.php';
