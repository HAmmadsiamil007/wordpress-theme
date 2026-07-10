<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

class Opulentia_Cloner_Capture {
	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function capture( $url, $session_id ) {
		$dir = $this->get_session_dir( $session_id );

		$result = array(
			'url'        => $url,
			'screenshot' => '',
			'styles'     => array(),
			'html'       => '',
		);

		$result['styles'] = $this->extract_default_styles( $url );

		$screenshot = $this->capture_screenshot_via_api( $url, $dir );
		if ( ! is_wp_error( $screenshot ) ) {
			$result['screenshot'] = $screenshot;
		}

		$result['html'] = $this->fetch_html( $url );

		file_put_contents(
			$dir . '/capture-data.json',
			wp_json_encode( $result, JSON_PRETTY_PRINT )
		);

		return $result;
	}

	public function get_session_dir( $session_id ) {
		$cloner = Opulentia_Site_Cloner::get_instance();
		$dir    = $cloner->prepare_upload_dir() . '/' . $session_id;
		return $dir;
	}

	private function capture_screenshot_via_api( $url, $dir ) {
		$response = wp_remote_post(
			admin_url( 'admin-ajax.php?action=opulentia_remote_screenshot' ),
			array(
				'timeout' => 30,
				'body'    => array(
					'url'  => $url,
					'type' => 'fullpage',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response; }

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! empty( $data['data']['screenshot'] ) ) {
			$screenshot_data = base64_decode( $data['data']['screenshot'] );
			if ( $screenshot_data ) {
				$file = $dir . '/screenshot.png';
				file_put_contents( $file, $screenshot_data );
				$cloner = Opulentia_Site_Cloner::get_instance();
				return $cloner->get_upload_url() . '/' . basename( $dir ) . '/screenshot.png';
			}
		}

		return new WP_Error( 'capture_failed', __( 'Could not capture screenshot.', 'opulentia' ) );
	}

	public function store_screenshot_data( $base64_data, $session_id ) {
		$dir             = $this->get_session_dir( $session_id );
		$screenshot_data = base64_decode( $base64_data );
		if ( ! $screenshot_data ) {
			return new WP_Error( 'invalid_data', __( 'Invalid screenshot data.', 'opulentia' ) );
		}
		$file = $dir . '/screenshot.png';
		file_put_contents( $file, $screenshot_data );
		$cloner = Opulentia_Site_Cloner::get_instance();
		return $cloner->get_upload_url() . '/' . basename( $dir ) . '/screenshot.png';
	}

	private function extract_default_styles( $url ) {
		$response = wp_remote_get( $url, array( 'timeout' => 15 ) );
		if ( is_wp_error( $response ) ) {
			return array(); }

		$html   = wp_remote_retrieve_body( $response );
		$styles = array(
			'colors' => array(),
			'fonts'  => array(),
			'links'  => array(),
		);

		preg_match_all( '/<link[^>]+rel=["\']stylesheet["\'][^>]*href=["\']([^"\']+)["\']/i', $html, $css_links );
		$styles['links'] = $css_links[1] ?? array();

		preg_match_all( '/--[\w-]+:\s*[^;]+;/', $html, $css_vars );
		foreach ( $css_vars[0] as $var ) {
			if ( preg_match( '/color|background/i', $var ) ) {
				$styles['colors'][] = trim( $var );
			}
		}

		return $styles;
	}

	private function fetch_html( $url ) {
		$response = wp_remote_get( $url, array( 'timeout' => 15 ) );
		if ( is_wp_error( $response ) ) {
			return ''; }
		return wp_remote_retrieve_body( $response );
	}
}
