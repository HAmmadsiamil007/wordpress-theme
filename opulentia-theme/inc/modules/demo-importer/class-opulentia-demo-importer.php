<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Opulentia_Demo_Importer {

	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'ocdi/register_plugins', array( $this, 'register_ocdi_plugins' ) );
		add_filter( 'ocdi/import_files', array( $this, 'ocdi_import_files' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
	}

	public function init() {
		if ( ! $this->is_ocdi_active() ) {
			add_action( 'admin_notices', array( $this, 'ocdi_recommendation_notice' ) );
		}
	}

	private function is_ocdi_active() {
		return class_exists( 'OCDI\OneClickDemoImport' ) || defined( 'PT_OCDI_VERSION' );
	}

	public function admin_menu() {
		$hook = add_theme_page(
			__( 'Import Demo', 'opulentia' ),
			__( 'Import Demo', 'opulentia' ),
			'manage_options',
			'opulentia-demo-importer',
			array( $this, 'render_admin_page' )
		);
	}

	public function admin_enqueue( $hook ) {
		if ( 'appearance_page_opulentia-demo-importer' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'wp-admin' );
	}

	public function render_admin_page() {
		$demos = $this->get_demos();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Opulentia Demo Importer', 'opulentia' ); ?></h1>
			<p><?php esc_html_e( 'Select a demo to import. This will install sample content, widgets, and customizer settings.', 'opulentia' ); ?></p>

			<?php if ( ! $this->is_ocdi_active() ) : ?>
				<div class="notice notice-warning">
					<p>
						<?php esc_html_e( 'For the best experience, install the free One Click Demo Import plugin.', 'opulentia' ); ?>
						<a href="<?php echo esc_url( admin_url( 'themes.php?page=opulentia-demo-importer&install-ocdi=1' ) ); ?>" class="button button-primary">
							<?php esc_html_e( 'Install OCDI', 'opulentia' ); ?>
						</a>
					</p>
				</div>
			<?php endif; ?>

			<div class="theme-browser" style="margin-top:20px;">
				<div class="themes wp-clearfix">
					<?php foreach ( $demos as $slug => $demo ) : ?>
						<div class="theme" data-demo="<?php echo esc_attr( $slug ); ?>">
							<div class="theme-screenshot">
								<img src="<?php echo esc_url( $demo['preview'] ); ?>" alt="<?php echo esc_attr( $demo['name'] ); ?>">
							</div>
							<div class="theme-id-container">
								<h2 class="theme-name"><?php echo esc_html( $demo['name'] ); ?></h2>
								<div class="theme-actions">
									<a class="button button-primary" href="<?php echo esc_url( $demo['demo_url'] ); ?>" target="_blank">
										<?php esc_html_e( 'Preview', 'opulentia' ); ?>
									</a>
									<a class="button button-primary import-demo-btn" href="<?php echo esc_url( $demo['import_url'] ); ?>" target="_blank">
										<?php esc_html_e( 'Import', 'opulentia' ); ?>
									</a>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<style>
		.theme-browser .theme { cursor: default; }
		.theme-browser .theme .theme-actions { top: auto; bottom: 10px; transform: none; }
		.theme-browser .theme .theme-actions .button { margin: 0 2px; }
		</style>
		<?php
	}

	public function get_demos() {
		$base        = Opulentia_URI . '/inc/modules/demo-importer/demos';
		$import_base = $this->is_ocdi_active() ? admin_url( 'themes.php?page=pt-one-click-demo-import' ) : admin_url( 'themes.php?page=opulentia-demo-importer' );

		return array(
			'business'  => array(
				'name'       => __( 'Business', 'opulentia' ),
				'preview'    => $base . '/business/preview.jpg',
				'demo_url'   => 'https://opulentia-demo.com/business',
				'import_url' => $import_base,
				'ocdi'       => array(
					'import_file_name'             => 'Business',
					'local_import_file'            => Opulentia_DIR . '/inc/modules/demo-importer/demos/business/content.xml',
					'local_import_widget_file'     => Opulentia_DIR . '/inc/modules/demo-importer/demos/business/widgets.json',
					'local_import_customizer_file' => Opulentia_DIR . '/inc/modules/demo-importer/demos/business/customizer.dat',
				),
			),
			'portfolio' => array(
				'name'       => __( 'Portfolio', 'opulentia' ),
				'preview'    => $base . '/portfolio/preview.jpg',
				'demo_url'   => 'https://opulentia-demo.com/portfolio',
				'import_url' => $import_base,
				'ocdi'       => array(
					'import_file_name'             => 'Portfolio',
					'local_import_file'            => Opulentia_DIR . '/inc/modules/demo-importer/demos/portfolio/content.xml',
					'local_import_widget_file'     => Opulentia_DIR . '/inc/modules/demo-importer/demos/portfolio/widgets.json',
					'local_import_customizer_file' => Opulentia_DIR . '/inc/modules/demo-importer/demos/portfolio/customizer.dat',
				),
			),
			'shop'      => array(
				'name'       => __( 'Shop', 'opulentia' ),
				'preview'    => $base . '/shop/preview.jpg',
				'demo_url'   => 'https://opulentia-demo.com/shop',
				'import_url' => $import_base,
				'ocdi'       => array(
					'import_file_name'             => 'Shop',
					'local_import_file'            => Opulentia_DIR . '/inc/modules/demo-importer/demos/shop/content.xml',
					'local_import_widget_file'     => Opulentia_DIR . '/inc/modules/demo-importer/demos/shop/widgets.json',
					'local_import_customizer_file' => Opulentia_DIR . '/inc/modules/demo-importer/demos/shop/customizer.dat',
				),
			),
			'agency'    => array(
				'name'       => __( 'Agency', 'opulentia' ),
				'preview'    => $base . '/agency/preview.jpg',
				'demo_url'   => 'https://opulentia-demo.com/agency',
				'import_url' => $import_base,
				'ocdi'       => array(
					'import_file_name'             => 'Agency',
					'local_import_file'            => Opulentia_DIR . '/inc/modules/demo-importer/demos/agency/content.xml',
					'local_import_widget_file'     => Opulentia_DIR . '/inc/modules/demo-importer/demos/agency/widgets.json',
					'local_import_customizer_file' => Opulentia_DIR . '/inc/modules/demo-importer/demos/agency/customizer.dat',
				),
			),
			'landing'   => array(
				'name'       => __( 'Landing', 'opulentia' ),
				'preview'    => $base . '/landing/preview.jpg',
				'demo_url'   => 'https://opulentia-demo.com/landing',
				'import_url' => $import_base,
				'ocdi'       => array(
					'import_file_name'             => 'Landing',
					'local_import_file'            => Opulentia_DIR . '/inc/modules/demo-importer/demos/landing/content.xml',
					'local_import_widget_file'     => Opulentia_DIR . '/inc/modules/demo-importer/demos/landing/widgets.json',
					'local_import_customizer_file' => Opulentia_DIR . '/inc/modules/demo-importer/demos/landing/customizer.dat',
				),
			),
		);
	}

	public function ocdi_import_files() {
		$demos = $this->get_demos();
		$files = array();

		foreach ( $demos as $slug => $demo ) {
			$ocdi  = $demo['ocdi'];
			$entry = array(
				'import_file_name'             => $ocdi['import_file_name'],
				'local_import_file'            => $ocdi['local_import_file'],
				'local_import_widget_file'     => $ocdi['local_import_widget_file'],
				'local_import_customizer_file' => $ocdi['local_import_customizer_file'],
				'import_preview_image_url'     => $demo['preview'],
				'import_notice'                => __( 'After you import this demo, your site will have the same content as the demo. This process may take a few minutes.', 'opulentia' ),
				'preview_url'                  => $demo['demo_url'],
			);

			if ( is_file( $ocdi['local_import_file'] ) ) {
				$entry['import_file_type'] = 'local';
			} else {
				$entry['import_file_type'] = 'local';
			}

			$files[] = $entry;
		}

		return $files;
	}

	public function register_ocdi_plugins( $plugins ) {
		return $plugins;
	}

	public function ocdi_recommendation_notice() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen && 'appearance_page_opulentia-demo-importer' === $screen->id ) {
			return;
		}
		?>
		<div class="notice notice-info is-dismissible">
			<p>
				<?php esc_html_e( 'Opulentia recommends installing the One Click Demo Import plugin for easy demo content import.', 'opulentia' ); ?>
				<a href="<?php echo esc_url( admin_url( 'themes.php?page=opulentia-demo-importer' ) ); ?>"><?php esc_html_e( 'Go to Demo Importer', 'opulentia' ); ?></a>
			</p>
		</div>
		<?php
	}
}
