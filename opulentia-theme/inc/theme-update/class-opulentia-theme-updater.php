<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OPULENTIA_DB_VERSION', '2.0.0' );

class Opulentia_Theme_Updater {

	private static $instance = null;
	private $option_key      = 'opulentia_db_version';

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_init', array( $this, 'check_version' ) );
		add_action( 'switch_theme', array( $this, 'clear_version_on_switch' ) );
	}

	public function check_version() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$stored_version = get_option( $this->option_key, '0.0.0' );

		if ( version_compare( $stored_version, OPULENTIA_DB_VERSION, '>=' ) ) {
			return;
		}

		$this->run_updates( $stored_version );

		update_option( $this->option_key, OPULENTIA_DB_VERSION );
	}

	private function run_updates( $from_version ) {
		$updates    = $this->get_update_callbacks();
		$update_log = array();

		foreach ( $updates as $version => $callback ) {
			if ( version_compare( $from_version, $version, '<' ) ) {
				if ( function_exists( $callback ) ) {
					$result       = call_user_func( $callback );
					$update_log[] = array(
						'version'  => $version,
						'callback' => $callback,
						'success'  => false !== $result,
					);
				}
			}
		}

		if ( ! empty( $update_log ) ) {
			update_option( 'opulentia_update_log', $update_log, false );
		}
	}

	private function get_update_callbacks() {
		return array(
			'2.0.0' => 'opulentia_update_2_0_0',
		);
	}

	public function clear_version_on_switch() {
		delete_option( $this->option_key );
	}

	public function get_stored_version() {
		return get_option( $this->option_key, '0.0.0' );
	}
}

Opulentia_Theme_Updater::get_instance();
