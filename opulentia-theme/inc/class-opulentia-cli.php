<?php
/**
 * WP-CLI Commands for Opulentia Theme
 *
 * @package Opulentia
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Manage Opulentia theme settings, modules, and tools.
 */
class Opulentia_CLI extends WP_CLI_Command {

	/**
	 * Get a theme option value.
	 *
	 * ## OPTIONS
	 *
	 * <key>
	 * : The option key to retrieve.
	 *
	 * [--format=<format>]
	 * : Output format. Accepts: raw, json. Default: raw.
	 *
	 * ## EXAMPLES
	 *
	 *     wp opulentia option get container_width
	 *     wp opulentia option get --format=json
	 */
	public function option( $args, $assoc_args ) {
		$key    = $args[0] ?? '';
		$format = $assoc_args['format'] ?? 'raw';

		if ( empty( $key ) ) {
			$options = opulentia_get_options();
			if ( 'json' === $format ) {
				WP_CLI::line( wp_json_encode( $options, JSON_PRETTY_PRINT ) );
			} else {
				foreach ( $options as $k => $v ) {
					WP_CLI::line( "$k: " . ( is_scalar( $v ) ? $v : wp_json_encode( $v ) ) );
				}
			}
			return;
		}

		$value = opulentia_get_option( $key, '__NOT_FOUND__' );

		if ( '__NOT_FOUND__' === $value ) {
			WP_CLI::error( "Option '$key' not found." );
		}

		if ( 'json' === $format ) {
			WP_CLI::line( wp_json_encode( array( $key => $value ), JSON_PRETTY_PRINT ) );
		} else {
			WP_CLI::success( "$key: " . ( is_scalar( $value ) ? $value : wp_json_encode( $value ) ) );
		}
	}

	/**
	 * Set a theme option value.
	 *
	 * ## OPTIONS
	 *
	 * <key>
	 * : The option key to set.
	 *
	 * <value>
	 * : The value to set.
	 *
	 * ## EXAMPLES
	 *
	 *     wp opulentia option set container_width 1280
	 */
	public function set( $args, $assoc_args ) {
		list( $key, $value ) = $args;

		$numeric = is_numeric( $value );
		$parsed  = $numeric ? $value + 0 : $value;

		opulentia_update_option( $key, $parsed );

		WP_CLI::success( "Option '$key' set to " . ( is_scalar( $parsed ) ? $parsed : wp_json_encode( $parsed ) ) );
	}

	/**
	 * List all registered modules and their status.
	 *
	 * ## OPTIONS
	 *
	 * [--format=<format>]
	 * : Output format. Accepts: table, json, csv. Default: table.
	 *
	 * ## EXAMPLES
	 *
	 *     wp opulentia module list
	 *     wp opulentia module list --format=json
	 */
	public function module( $args, $assoc_args ) {
		$subcommand = $args[0] ?? 'list';

		if ( 'list' === $subcommand ) {
			$this->_module_list( $assoc_args );
		} elseif ( 'enable' === $subcommand ) {
			$this->_module_enable( $args[1] ?? '' );
		} elseif ( 'disable' === $subcommand ) {
			$this->_module_disable( $args[1] ?? '' );
		} else {
			WP_CLI::error( "Unknown subcommand: $subcommand. Use list, enable, or disable." );
		}
	}

	/**
	 * List modules.
	 */
	private function _module_list( $assoc_args ) {
		$format  = $assoc_args['format'] ?? 'table';
		$modules = Opulentia_Modules::get_instance()->get_modules();

		if ( empty( $modules ) ) {
			WP_CLI::warning( 'No modules registered.' );
			return;
		}

		$rows = array();
		foreach ( $modules as $slug => $mod ) {
			$rows[] = array(
				'Slug'         => $slug,
				'Name'         => $mod['name'] ?? $slug,
				'Category'     => $mod['category'] ?? '',
				'Default'      => ! empty( $mod['default'] ) ? 'On' : 'Off',
				'Dependencies' => implode( ', ', $mod['dependencies'] ?? array() ),
			);
		}

		if ( 'json' === $format ) {
			WP_CLI::line( wp_json_encode( $rows, JSON_PRETTY_PRINT ) );
		} else {
			WP_CLI\Utils\format_items( $format, $rows, array( 'Slug', 'Name', 'Category', 'Default', 'Dependencies' ) );
		}
	}

	/**
	 * Enable a module.
	 */
	private function _module_enable( $slug ) {
		if ( empty( $slug ) ) {
			WP_CLI::error( 'Please specify a module slug.' );
		}

		$modules = Opulentia_Modules::get_instance();

		if ( ! $modules->module_exists( $slug ) ) {
			WP_CLI::error( "Module '$slug' not found." );
		}

		$modules->enable_module( $slug );
		WP_CLI::success( "Module '$slug' enabled." );
	}

	/**
	 * Disable a module.
	 */
	private function _module_disable( $slug ) {
		if ( empty( $slug ) ) {
			WP_CLI::error( 'Please specify a module slug.' );
		}

		$modules = Opulentia_Modules::get_instance();

		if ( ! $modules->module_exists( $slug ) ) {
			WP_CLI::error( "Module '$slug' not found." );
		}

		$modules->disable_module( $slug );
		WP_CLI::success( "Module '$slug' disabled." );
	}

	/**
	 * Clone a website design into Opulentia.
	 *
	 * ## OPTIONS
	 *
	 * <url>
	 * : The URL of the website to clone.
	 *
	 * [--dry-run]
	 * : Preview the analysis without applying changes.
	 *
	 * ## EXAMPLES
	 *
	 *     wp opulentia cloner run https://example.com
	 *     wp opulentia cloner run https://example.com --dry-run
	 */
	public function cloner( $args, $assoc_args ) {
		$subcommand = $args[0] ?? 'run';

		if ( 'run' !== $subcommand ) {
			WP_CLI::error( "Unknown subcommand: $subcommand. Use 'run'." );
		}

		$url    = $args[1] ?? '';
		$dryrun = ! empty( $assoc_args['dry-run'] );

		if ( empty( $url ) ) {
			WP_CLI::error( 'Please provide a URL.' );
		}

		WP_CLI::line( "Analyzing: $url" . ( $dryrun ? ' (dry run)' : '' ) );

		if ( ! class_exists( 'Opulentia_Site_Cloner' ) ) {
			WP_CLI::error( 'Site Cloner module is not available.' );
		}

		$cloner = Opulentia_Site_Cloner::get_instance();

		try {
			$result = $cloner->clone_from_url( $url, array( 'dry_run' => $dryrun ) );
			WP_CLI::success( 'Analysis complete.' );
			WP_CLI::line( wp_json_encode( $result, JSON_PRETTY_PRINT ) );
		} catch ( Exception $e ) {
			WP_CLI::error( 'Cloning failed: ' . $e->getMessage() );
		}
	}
}

WP_CLI::add_command( 'opulentia', 'Opulentia_CLI' );
