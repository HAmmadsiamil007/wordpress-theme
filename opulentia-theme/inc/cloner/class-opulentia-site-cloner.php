<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

class Opulentia_Site_Cloner {
	private static $instance = null;
	private $capture;
	private $analyzer;
	private $tokens;
	private $applier;

	const UPLOAD_DIR = 'opulentia-cloner';

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'wp_ajax_opulentia_cloner_capture', array( $this, 'ajax_capture' ) );
		add_action( 'wp_ajax_opulentia_cloner_analyze', array( $this, 'ajax_analyze' ) );
		add_action( 'wp_ajax_opulentia_cloner_apply', array( $this, 'ajax_apply' ) );
		add_action( 'wp_ajax_opulentia_cloner_preview', array( $this, 'ajax_preview' ) );
		add_action( 'wp_ajax_opulentia_cloner_dembrandt', array( $this, 'ajax_dembrandt_import' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'preview_styles' ) );

		$this->load_dependencies();
	}

	private function load_dependencies() {
		$dir = Opulentia_DIR . '/inc/cloner/';
		require_once $dir . 'class-opulentia-cloner-capture.php';
		require_once $dir . 'class-opulentia-cloner-analyzer.php';
		require_once $dir . 'class-opulentia-cloner-tokens.php';
		require_once $dir . 'class-opulentia-cloner-applier.php';
		require_once $dir . 'class-opulentia-cloner-importer.php';
		$this->capture  = Opulentia_Cloner_Capture::get_instance();
		$this->analyzer = Opulentia_Cloner_Analyzer::get_instance();
		$this->tokens   = Opulentia_Cloner_Tokens::get_instance();
		$this->applier  = Opulentia_Cloner_Applier::get_instance();
	}

	public function prepare_upload_dir() {
		$upload_dir = wp_upload_dir();
		$dir        = $upload_dir['basedir'] . '/' . self::UPLOAD_DIR;
		if ( ! file_exists( $dir ) ) {
			wp_mkdir_p( $dir );
		}
		return $dir;
	}

	public function get_upload_url() {
		$upload_dir = wp_upload_dir();
		return $upload_dir['baseurl'] . '/' . self::UPLOAD_DIR;
	}

	public function ajax_capture() {
		check_ajax_referer( 'opulentia_cloner_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( -1 ); }

		$url = esc_url_raw( $_POST['url'] ?? '' );
		if ( empty( $url ) ) {
			wp_send_json_error( array( 'message' => __( 'URL is required.', 'opulentia' ) ) );
		}

		$session_id = uniqid( 'clone_' );
		$dir        = $this->prepare_upload_dir() . '/' . $session_id;
		wp_mkdir_p( $dir );

		$result = $this->capture->capture( $url, $session_id );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		update_option(
			'opulentia_cloner_session',
			array(
				'session_id' => $session_id,
				'url'        => $url,
				'status'     => 'captured',
				'data'       => $result,
			)
		);

		wp_send_json_success(
			array(
				'session_id' => $session_id,
				'screenshot' => $result['screenshot'] ?? '',
				'message'    => __( 'Site captured successfully. Ready for analysis.', 'opulentia' ),
			)
		);
	}

	public function ajax_analyze() {
		check_ajax_referer( 'opulentia_cloner_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( -1 ); }

		$session = get_option( 'opulentia_cloner_session', array() );
		if ( empty( $session['session_id'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No session found. Capture a site first.', 'opulentia' ) ) );
		}

		$analysis = $this->analyzer->analyze( $session );
		if ( is_wp_error( $analysis ) ) {
			wp_send_json_error( array( 'message' => $analysis->get_error_message() ) );
		}

		$design_md            = $this->tokens->generate_design_md( $analysis );
		$session['status']    = 'analyzed';
		$session['analysis']  = $analysis;
		$session['design_md'] = $design_md;
		update_option( 'opulentia_cloner_session', $session );

		wp_send_json_success(
			array(
				'analysis'  => $analysis,
				'design_md' => $design_md,
				'message'   => __( 'Design analysis complete.', 'opulentia' ),
			)
		);
	}

	public function ajax_apply() {
		check_ajax_referer( 'opulentia_cloner_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( -1 ); }

		$session = get_option( 'opulentia_cloner_session', array() );
		if ( empty( $session['analysis'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No analysis found. Analyze a site first.', 'opulentia' ) ) );
		}

		$overrides = isset( $_POST['overrides'] ) ? json_decode( wp_unslash( $_POST['overrides'] ), true ) : array();
		$result    = $this->applier->apply( $session['analysis'], $overrides );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		$session['status'] = 'applied';
		update_option( 'opulentia_cloner_session', $session );

		wp_send_json_success(
			array(
				'message' => __( 'Design applied successfully.', 'opulentia' ),
				'changes' => $result,
			)
		);
	}

	public function ajax_preview() {
		check_ajax_referer( 'opulentia_cloner_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( -1 ); }

		$session = get_option( 'opulentia_cloner_session', array() );
		if ( empty( $session['analysis'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No analysis to preview.', 'opulentia' ) ) );
		}

		wp_send_json_success(
			array(
				'preview_url' => add_query_arg( 'opulentia_cloner_preview', '1', home_url() ),
			)
		);
	}

	public function ajax_dembrandt_import() {
		check_ajax_referer( 'opulentia_cloner_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( -1 ); }

		$raw = isset( $_POST['dembrandt_json'] ) ? trim( wp_unslash( $_POST['dembrandt_json'] ) ) : '';
		if ( empty( $raw ) ) {
			wp_send_json_error( array( 'message' => __( 'No Dembrandt JSON data received.', 'opulentia' ) ) );
		}

		$dembrandt_data = json_decode( $raw, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			wp_send_json_error( array( 'message' => 'Invalid JSON: ' . json_last_error_msg() ) );
		}

		$analysis   = $this->tokens->from_dembrandt_json( $dembrandt_data );
		$design_md  = $this->tokens->generate_design_md( $analysis );
		$theme_mods = $this->tokens->analysis_to_theme_mods( $analysis );

		$session               = get_option( 'opulentia_cloner_session', array() );
		$session['status']     = 'analyzed';
		$session['analysis']   = $analysis;
		$session['design_md']  = $design_md;
		$session['theme_mods'] = $theme_mods;
		$session['source']     = 'dembrandt';
		update_option( 'opulentia_cloner_session', $session );

		wp_send_json_success(
			array(
				'message'    => __( 'Dembrandt data imported successfully. Ready to apply.', 'opulentia' ),
				'theme_mods' => $theme_mods,
				'design_md'  => $design_md,
			)
		);
	}

	public function preview_styles() {
		if ( empty( $_GET['opulentia_cloner_preview'] ) ) {
			return; }

		$session = get_option( 'opulentia_cloner_session', array() );
		if ( empty( $session['analysis'] ) ) {
			return; }

		$css = $this->applier->generate_preview_css( $session['analysis'] );
		wp_add_inline_style( 'opulentia-style', $css );
	}
}

Opulentia_Site_Cloner::get_instance();
