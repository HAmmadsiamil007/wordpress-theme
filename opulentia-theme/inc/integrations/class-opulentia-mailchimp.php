<?php
/**
 * Mailchimp API Integration — Singleton
 *
 * Handles Mailchimp audience subscription via the v3 API.
 * Uses native PHP cURL (no Composer dependency).
 *
 * Features:
 * 1. Admin settings page (Appearance > Mailchimp) for API key + list ID
 * 2. Subscribe endpoint with upsert (PUT) behaviour
 * 3. Graceful error handling with WP_Error return and error_log fallback
 * 4. Connection status check
 *
 * @package Opulentia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opulentia_Mailchimp class.
 */
class Opulentia_Mailchimp {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Option keys used for persistent storage.
	 */
	const OPTION_API_KEY      = 'Opulentia_mailchimp_api_key';
	const OPTION_LIST_ID      = 'Opulentia_mailchimp_list_id';
	const OPTION_DOUBLE_OPTIN = 'Opulentia_mailchimp_double_optin';

	/**
	 * Settings page slug.
	 */
	const SETTINGS_PAGE = 'opulentia-mailchimp';

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
	 * Constructor — registers admin hooks.
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	// -------------------------------------------------------------------------
	// 1. Admin Settings Page
	// -------------------------------------------------------------------------

	/**
	 * Register the Mailchimp settings submenu page under Appearance.
	 */
	public function register_settings_page() {
		add_theme_page(
			esc_html__( 'Mailchimp Settings', 'opulentia' ),
			esc_html__( 'Mailchimp', 'opulentia' ),
			'manage_options',
			self::SETTINGS_PAGE,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Render the settings page HTML.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Mailchimp Integration', 'opulentia' ); ?></h1>
			<p><?php esc_html_e( 'Configure your Mailchimp API credentials to enable newsletter signups via the footer subscription form.', 'opulentia' ); ?></p>
			<form action="options.php" method="post">
				<?php
				settings_fields( self::SETTINGS_PAGE );
				do_settings_sections( self::SETTINGS_PAGE );
				submit_button();
				?>
			</form>
			<hr>
			<?php $this->render_connection_status(); ?>
			<hr>
			<h2><?php esc_html_e( 'How to get your credentials', 'opulentia' ); ?></h2>
			<ol>
				<li><?php esc_html_e( 'Log in to your Mailchimp account.', 'opulentia' ); ?></li>
				<li><?php esc_html_e( 'Go to Account > Extras > API keys.', 'opulentia' ); ?></li>
				<li><?php esc_html_e( 'Create a new API key or use an existing one.', 'opulentia' ); ?></li>
				<li><?php esc_html_e( 'Your server prefix is the part after the dash (e.g., us19).', 'opulentia' ); ?></li>
				<li><?php esc_html_e( 'Go to Audience > Manage Audience > Settings > Audience name and defaults.', 'opulentia' ); ?></li>
				<li><?php esc_html_e( 'Copy the Audience ID (e.g., a1b2c3d4e5).', 'opulentia' ); ?></li>
			</ol>
		</div>
		<?php
	}

	/**
	 * Register settings with the WordPress Settings API.
	 */
	public function register_settings() {
		add_settings_section(
			'Opulentia_mailchimp_main',
			esc_html__( 'API Credentials', 'opulentia' ),
			'__return_empty_string',
			self::SETTINGS_PAGE
		);

		// API Key field.
		add_settings_field(
			self::OPTION_API_KEY,
			esc_html__( 'Mailchimp API Key', 'opulentia' ),
			array( $this, 'render_api_key_field' ),
			self::SETTINGS_PAGE,
			'Opulentia_mailchimp_main'
		);
		register_setting(
			self::SETTINGS_PAGE,
			self::OPTION_API_KEY,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);

		// List ID field.
		add_settings_field(
			self::OPTION_LIST_ID,
			esc_html__( 'Audience / List ID', 'opulentia' ),
			array( $this, 'render_list_id_field' ),
			self::SETTINGS_PAGE,
			'Opulentia_mailchimp_main'
		);
		register_setting(
			self::SETTINGS_PAGE,
			self::OPTION_LIST_ID,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);

		// --- Subscription Behaviour ---

		add_settings_section(
			'Opulentia_mailchimp_behaviour',
			esc_html__( 'Subscription Behaviour', 'opulentia' ),
			'__return_empty_string',
			self::SETTINGS_PAGE
		);

		// Double opt-in checkbox.
		add_settings_field(
			self::OPTION_DOUBLE_OPTIN,
			esc_html__( 'Double Opt-in', 'opulentia' ),
			array( $this, 'render_double_optin_field' ),
			self::SETTINGS_PAGE,
			'Opulentia_mailchimp_behaviour'
		);
		register_setting(
			self::SETTINGS_PAGE,
			self::OPTION_DOUBLE_OPTIN,
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default'           => false,
			)
		);
	}

	/**
	 * Render the API key text field.
	 */
	public function render_api_key_field() {
		$value = get_option( self::OPTION_API_KEY, '' );
		?>
		<input type="password"
				name="<?php echo esc_attr( self::OPTION_API_KEY ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				class="regular-text"
				autocomplete="off"
				placeholder="e.g. abc123abc123abc123abc123abc123abc123-us19">
		<p class="description"><?php esc_html_e( 'Your Mailchimp API key. The server prefix (e.g., us19) is extracted automatically.', 'opulentia' ); ?></p>
		<?php
	}

	/**
	 * Render the list ID text field.
	 */
	public function render_list_id_field() {
		$value = get_option( self::OPTION_LIST_ID, '' );
		?>
		<input type="text"
				name="<?php echo esc_attr( self::OPTION_LIST_ID ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				class="regular-text"
				placeholder="e.g. a1b2c3d4e5">
		<p class="description"><?php esc_html_e( 'The unique ID of your Mailchimp audience list. Found in Audience > Settings > Audience name and defaults.', 'opulentia' ); ?></p>
		<?php
	}

	/**
	 * Render the double opt-in checkbox.
	 */
	public function render_double_optin_field() {
		$checked = (bool) get_option( self::OPTION_DOUBLE_OPTIN, false );
		?>
		<input type="hidden"
				name="<?php echo esc_attr( self::OPTION_DOUBLE_OPTIN ); ?>"
				value="0">
		<label>
			<input type="checkbox"
					name="<?php echo esc_attr( self::OPTION_DOUBLE_OPTIN ); ?>"
					value="1"
					<?php checked( $checked ); ?>>
			<?php esc_html_e( 'Send a confirmation email before subscribing new members', 'opulentia' ); ?>
		</label>
		<p class="description">
			<?php esc_html_e( 'When enabled, new subscribers will receive an email asking them to confirm their subscription (recommended for GDPR compliance). Existing subscribers are not affected.', 'opulentia' ); ?>
		</p>
		<?php
	}

	// -------------------------------------------------------------------------
	// 2. Connection Status
	// -------------------------------------------------------------------------

	/**
	 * Render the connection status section.
	 */
	private function render_connection_status() {
		$api_key = get_option( self::OPTION_API_KEY, '' );
		$list_id = get_option( self::OPTION_LIST_ID, '' );

		if ( empty( $api_key ) || empty( $list_id ) ) {
			echo '<p><strong>' . esc_html__( 'Status:', 'opulentia' ) . '</strong> ';
			echo '<span style="color: #d63638;">' . esc_html__( 'Not configured — enter your API key and list ID above.', 'opulentia' ) . '</span></p>';
			return;
		}

		$result = $this->ping();

		if ( is_wp_error( $result ) ) {
			echo '<p><strong>' . esc_html__( 'Status:', 'opulentia' ) . '</strong> ';
			echo '<span style="color: #d63638;">' . esc_html__( 'Connection failed:', 'opulentia' ) . ' ' . esc_html( $result->get_error_message() ) . '</span></p>';
		} else {
			echo '<p><strong>' . esc_html__( 'Status:', 'opulentia' ) . '</strong> ';
			echo '<span style="color: #46b450;">' . esc_html__( 'Connected successfully &mdash; Mailchimp API is reachable.', 'opulentia' ) . '</span></p>';
		}
	}

	// -------------------------------------------------------------------------
	// 3. Mailchimp API — Subscribe
	// -------------------------------------------------------------------------

	/**
	 * Subscribe an email address to the configured Mailchimp audience list.
	 *
	 * Uses PUT (upsert) to /lists/{list_id}/members/{subscriber_hash}
	 * so existing subscribers are updated rather than rejected.
	 *
	 * @param string $email        Subscriber email address.
	 * @param array  $merge_fields Optional. Merge field key/value pairs (e.g. FNAME, LNAME).
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function subscribe( $email, $merge_fields = array() ) {
		$api_key = get_option( self::OPTION_API_KEY, '' );
		$list_id = get_option( self::OPTION_LIST_ID, '' );

		if ( empty( $api_key ) || empty( $list_id ) ) {
			return new \WP_Error(
				'mailchimp_not_configured',
				__( 'Mailchimp is not configured. Please set your API key and list ID in Appearance > Mailchimp.', 'opulentia' )
			);
		}

		// Validate email before sending.
		if ( ! is_email( $email ) ) {
			return new \WP_Error(
				'invalid_email',
				__( 'Invalid email address.', 'opulentia' )
			);
		}

		// Build the API endpoint URL.
		$server_prefix   = $this->get_server_prefix( $api_key );
		$subscriber_hash = md5( strtolower( $email ) );
		$url             = "https://{$server_prefix}.api.mailchimp.com/3.0/lists/{$list_id}/members/{$subscriber_hash}";

		// Build the request body.
		$double_optin = (bool) get_option( self::OPTION_DOUBLE_OPTIN, false );

		$body = array(
			'email_address' => $email,
		);

		if ( $double_optin ) {
			// Pending — subscriber must confirm via email.
			$body['status_if_new'] = 'pending';
		} else {
			// Immediate subscription (single opt-in).
			$body['status_if_new'] = 'subscribed';
			$body['status']        = 'subscribed';
		}

		if ( ! empty( $merge_fields ) ) {
			$body['merge_fields'] = $merge_fields;
		}

		$json_body = wp_json_encode( $body );
		if ( false === $json_body ) {
			return new \WP_Error(
				'json_encode_failed',
				__( 'Failed to encode request data.', 'opulentia' )
			);
		}

		// Make the API request.
		$response = wp_remote_request(
			$url,
			array(
				'method'  => 'PUT',
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
				),
				'body'    => $json_body,
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			$this->log_error( 'HTTP request failed: ' . $error_message );

			return new \WP_Error(
				'mailchimp_http_error',
				__( 'Could not connect to Mailchimp. Please try again later.', 'opulentia' ),
				array( 'original_error' => $error_message )
			);
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body_raw    = wp_remote_retrieve_body( $response );
		$data        = json_decode( $body_raw, true );

		// 200 = member updated; 201 = member created. Both are success.
		if ( in_array( $status_code, array( 200, 201 ), true ) ) {
			return true;
		}

		// Handle API-level errors.
		$error_detail = isset( $data['detail'] ) ? $data['detail'] : '';

		$this->log_error(
			sprintf(
				'API error [%d]: %s — URL: %s',
				$status_code,
				$error_detail,
				$url
			)
		);

		return new \WP_Error(
			'mailchimp_api_error',
			__( 'Subscription failed. Please try again later.', 'opulentia' ),
			array(
				'status_code' => $status_code,
				'detail'      => $error_detail,
			)
		);
	}

	/**
	 * Check connectivity by pinging the Mailchimp API.
	 *
	 * @return true|\WP_Error True if reachable, WP_Error on failure.
	 */
	public function ping() {
		$api_key       = get_option( self::OPTION_API_KEY, '' );
		$server_prefix = $this->get_server_prefix( $api_key );

		if ( empty( $server_prefix ) ) {
			return new \WP_Error(
				'invalid_api_key',
				__( 'API key format is invalid. It should end with a dash and server prefix (e.g., -us19).', 'opulentia' )
			);
		}

		$url = "https://{$server_prefix}.api.mailchimp.com/3.0/";

		$response = wp_remote_get(
			$url,
			array(
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
				),
				'timeout' => 10,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = wp_remote_retrieve_response_code( $response );

		// Mailchimp returns 200 for a successful ping with valid auth.
		if ( 200 === $status_code ) {
			return true;
		}

		$body   = json_decode( wp_remote_retrieve_body( $response ), true );
		$detail = isset( $body['detail'] ) ? $body['detail'] : '';

		return new \WP_Error(
			'mailchimp_ping_failed',
			sprintf(
				/* translators: %d: HTTP status code, %s: error detail from Mailchimp */
				__( 'API returned HTTP %1$d: %2$s', 'opulentia' ),
				$status_code,
				$detail
			)
		);
	}

	// -------------------------------------------------------------------------
	// 4. Helpers
	// -------------------------------------------------------------------------

	/**
	 * Extract the Mailchimp server prefix from an API key.
	 *
	 * The server prefix is the part after the dash (e.g., "us19" from "abc-us19").
	 *
	 * @param string $api_key The full Mailchimp API key.
	 * @return string Server prefix (e.g. "us19") or empty string if invalid.
	 */
	private function get_server_prefix( $api_key ) {
		if ( empty( $api_key ) || false === strpos( $api_key, '-' ) ) {
			return '';
		}

		$parts = explode( '-', $api_key, 2 );

		return isset( $parts[1] ) ? $parts[1] : '';
	}

	/**
	 * Log a Mailchimp error to the WordPress error log.
	 *
	 * @param string $message The error message to log.
	 */
	private function log_error( $message ) {
		error_log(
			sprintf(
				'[Opulentia Mailchimp] %s',
				$message
			)
		);
	}
}
