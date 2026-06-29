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
 * Include singleton classes (new architecture)
 */
require SOLEORIGINE_DIR . '/inc/class-soleorigine-after-setup.php';
require SOLEORIGINE_DIR . '/inc/class-soleorigine-enqueue.php';
require SOLEORIGINE_DIR . '/inc/class-soleorigine-icons.php';
require SOLEORIGINE_DIR . '/inc/class-soleorigine-customizer-config.php';

/**
 * Initialize singletons.
 * Hook registration happens inside each private constructor.
 */
SoleOrigine_After_Setup::get_instance();
SoleOrigine_Enqueue::get_instance();
SoleOrigine_Icons::get_instance();
SoleOrigine_Customizer_Config::get_instance();

/**
 * Include legacy flat files (will be migrated to classes).
 */
require SOLEORIGINE_DIR . '/inc/theme-setup.php';
require SOLEORIGINE_DIR . '/inc/enqueue.php';
require SOLEORIGINE_DIR . '/inc/customizer.php';
require SOLEORIGINE_DIR . '/inc/template-tags.php';
require SOLEORIGINE_DIR . '/inc/template-functions.php';
require SOLEORIGINE_DIR . '/inc/widgets.php';
