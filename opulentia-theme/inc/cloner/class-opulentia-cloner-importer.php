<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

class Opulentia_Cloner_Importer {
	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function import_content( $source_url, $options = array() ) {
		$defaults = array(
			'import_posts' => true,
			'import_pages' => true,
			'import_menus' => false,
			'import_media' => true,
			'import_theme' => false,
			'max_items'    => 20,
		);
		$options  = wp_parse_args( $options, $defaults );

		$results = array(
			'posts'    => array(),
			'pages'    => array(),
			'media'    => array(),
			'menus'    => array(),
			'errors'   => array(),
			'imported' => 0,
		);

		if ( $options['import_posts'] || $options['import_pages'] ) {
			$items = $this->fetch_wp_rest( $source_url, 'wp/v2/posts', $options['max_items'] );
			$type  = 'posts';
			if ( ! is_wp_error( $items ) ) {
				foreach ( $items as $item ) {
					$result = $this->import_single_post( $item, $source_url, $options );
					if ( is_wp_error( $result ) ) {
						$results['errors'][] = $result->get_error_message();
					} else {
						$results[ $type ][] = $result;
						++$results['imported'];
					}
				}
			}

			$pages = $this->fetch_wp_rest( $source_url, 'wp/v2/pages', $options['max_items'] );
			$type  = 'pages';
			if ( ! is_wp_error( $pages ) ) {
				foreach ( $pages as $page ) {
					$result = $this->import_single_post( $page, $source_url, $options );
					if ( is_wp_error( $result ) ) {
						$results['errors'][] = $result->get_error_message();
					} else {
						$results[ $type ][] = $result;
						++$results['imported'];
					}
				}
			}
		}

		return $results;
	}

	private function fetch_wp_rest( $base_url, $endpoint, $per_page = 20 ) {
		$url = trailingslashit( $base_url ) . 'wp-json/' . ltrim( $endpoint, '/' );
		$url = add_query_arg(
			array(
				'per_page' => $per_page,
				'_embed'   => '1',
			),
			$url
		);

		$response = wp_remote_get( $url, array( 'timeout' => 30 ) );
		if ( is_wp_error( $response ) ) {
			return $response; }

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code !== 200 ) {
			return new WP_Error( 'rest_error', "REST API returned {$code} for {$url}" );
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		return is_array( $data ) ? $data : array();
	}

	private function import_single_post( $item, $source_url, $options ) {
		if ( empty( $item['slug'] ) || empty( $item['title']['rendered'] ) ) {
			return new WP_Error( 'invalid_item', __( 'Invalid item data.', 'opulentia' ) );
		}

		$existing = get_page_by_path( $item['slug'], OBJECT, $item['type'] ?? 'post' );
		if ( $existing ) {
			return $existing->ID;
		}

		$post_data = array(
			'post_title'   => wp_strip_all_tags( $item['title']['rendered'] ),
			'post_content' => $this->rewrite_media_urls( $item['content']['rendered'] ?? '', $source_url ),
			'post_status'  => 'draft',
			'post_type'    => $item['type'] ?? 'post',
			'post_excerpt' => $item['excerpt']['rendered'] ?? '',
			'post_name'    => $item['slug'],
		);

		if ( ! empty( $item['date'] ) ) {
			$post_data['post_date'] = $item['date'];
		}

		$post_id = wp_insert_post( $post_data );
		if ( is_wp_error( $post_id ) ) {
			return $post_id; }

		if ( ! empty( $item['_embedded']['wp:featuredmedia'][0]['source_url'] ) ) {
			$image_url = $item['_embedded']['wp:featuredmedia'][0]['source_url'];
			$image_id  = $this->sideload_image( $image_url );
			if ( $image_id && ! is_wp_error( $image_id ) ) {
				set_post_thumbnail( $post_id, $image_id );
			}
		}

		return $post_id;
	}

	private function rewrite_media_urls( $content, $source_url ) {
		$source_host = parse_url( $source_url, PHP_URL_HOST );
		$content     = preg_replace(
			'/(src|href)=["\']https?:\/\/' . preg_quote( $source_host, '/' ) . '/i',
			'$1="' . home_url(),
			$content
		);
		return $content;
	}

	public function sideload_image( $url ) {
		if ( ! function_exists( 'media_sideload_image' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		$tmp = download_url( $url );
		if ( is_wp_error( $tmp ) ) {
			return $tmp; }

		$file_name = basename( parse_url( $url, PHP_URL_PATH ) );
		$file      = array(
			'name'     => $file_name,
			'tmp_name' => $tmp,
		);

		$id = media_handle_sideload( $file, 0 );
		if ( is_wp_error( $id ) ) {
			@unlink( $tmp );
		}

		return $id;
	}
}
