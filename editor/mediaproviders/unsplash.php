<?php

class Brizy_Editor_MediaProviders_Unsplash {

	const NONCE           = 'brizy-unsplash';
	const AJAX_GET_IMGS   = 'brizy_get_imgs';
	const AJAX_UPLOAD_IMG = 'brizy_upload_img';
	const API_URL         = 'https://brizy.io/';

	/**
	 * @var Brizy_Editor_Project
	 */
	private $project;

	/**
	 * @var Brizy_Editor_Post
	 */
	private $post;

	/**
	 * Brizy_Editor_API constructor.
	 *
	 * @param Brizy_Editor_Project $project
	 * @param Brizy_Editor_Post $post
	 */
	public function __construct( $project, $post ) {

		$this->project = $project;
		$this->post    = $post;

		$this->initialize();
	}

	public function initialize() {
		if ( Brizy_Editor::is_user_allowed() ) {
			add_action( 'wp_ajax_' . self::AJAX_GET_IMGS, array( $this, 'getImgs' ) );
			add_action( 'wp_ajax_' . self::AJAX_UPLOAD_IMG, array( $this, 'uploadImg' ) );
		}
	}

	public function getImgs() {
		$this->authorize();

		$args = array(
			'body' => array(
				'page'  => $_POST['page'],
				'query' => $_POST['query']
			),
		);

		$request = wp_remote_post( self::API_URL, $args );

		if ( is_wp_error( $request ) ) {
			$this->error( 500, $request->get_error_message() );
		}

		if ( 200 !== ( $response_code = wp_remote_retrieve_response_code( $request ) ) ) {
			$this->error( $response_code, 'Error message request: ' . wp_remote_retrieve_response_message( $request ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $request ), true );

		$body = array(
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://i.ytimg.com/vi/T6BjCS0oI6s/hqdefault.jpg'
			),
		);

		$this->success( $body );
	}

	public function uploadImg() {
		$this->authorize();

		// Gives us access to the download_url() and wp_handle_sideload() functions
		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		$url = $_POST['src'];

		// Download file to temp dir
		$temp_file = download_url( $url, 30 );

		if ( is_wp_error( $temp_file ) ) {
			$this->error( 500, 'download_url message: ' . $temp_file->get_error_message() );
		}

		// Array based on $_FILE as seen in PHP file uploads
		$file = array(
			'name'     => basename( $url ),
			'type'     => 'image/png',
			'tmp_name' => $temp_file,
			'error'    => 0,
			'size'     => filesize( $temp_file ),
		);

		$overrides = array(
			'test_form' => false,
			'test_size' => true,
		);

		$results = wp_handle_sideload( $file, $overrides );

		if ( ! empty( $results['error'] ) ) {
			$this->error( 500, 'wp_handle_sideload message: ' . $results['error'] );
		}

		$filename  = $results['file']; // Full path to the file

		$attachment = array(
			'guid'           => $results['url'],
			'post_mime_type' => $results['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		$attach_id = wp_insert_attachment( $attachment, $filename );

		// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		// Generate the metadata for the attachment, and update the database record.
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		if ( ! $attachment = wp_prepare_attachment_for_js( $attach_id ) ) {
			$this->error( 500, 'Prepare attachment for js failed.' );
		}

		echo wp_json_encode( array(
			'success' => true,
			'data'    => $attachment,
		) );

		//$this->success( array( 'message' => esc_html__( 'The image was uploaded successfully!', 'brizy' ) ) );
	}

	protected function error( $code, $message ) {
		wp_send_json_error( array( 'code' => $code, 'message' => $message ), $code );
	}

	protected function success( $data ) {
		wp_send_json_success( $data );
	}

	protected function authorize() {
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], self::NONCE ) ) {
			wp_send_json_error( array( 'code' => 400, 'message' => 'Bad request' ), 400 );
		}
	}
}