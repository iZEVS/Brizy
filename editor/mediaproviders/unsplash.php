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
				'src' => 'https://images.unsplash.com/1/type-away.jpg?q=80&fm=jpg&w=400&fit=max'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://images.unsplash.com/1/type-away.jpg?q=80&fm=jpg&w=400&fit=max'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://images.unsplash.com/1/type-away.jpg?q=80&fm=jpg&w=400&fit=max'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://images.unsplash.com/1/type-away.jpg?q=80&fm=jpg&w=400&fit=max'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://images.unsplash.com/1/type-away.jpg?q=80&fm=jpg&w=400&fit=max'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://images.unsplash.com/1/type-away.jpg?q=80&fm=jpg&w=400&fit=max'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://images.unsplash.com/1/type-away.jpg?q=80&fm=jpg&w=400&fit=max'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://images.unsplash.com/1/type-away.jpg?q=80&fm=jpg&w=400&fit=max'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://images.unsplash.com/1/type-away.jpg?q=80&fm=jpg&w=400&fit=max'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://images.unsplash.com/1/type-away.jpg?q=80&fm=jpg&w=400&fit=max'
			),
			array(
				'id'  => 'type-away',
				'src' => 'https://images.unsplash.com/1/type-away.jpg?q=80&fm=jpg&w=400&fit=max'
			),
		);

		$this->success( $body );
	}

	public function uploadImg() {
		$this->authorize();

		$image = $_POST['src'];

		$get = wp_remote_get( $image );

		$type = wp_remote_retrieve_header( $get, 'content-type' );

		if ( ! $type ) {
			$this->error( 500, esc_html__( 'Unsplash returned an undefined image type', 'brizy' ) );
		}

		$mirror = wp_upload_bits( basename( $image ), '', wp_remote_retrieve_body( $get ) );

		$attachment = array(
			'post_title'     => basename( $image ),
			'post_mime_type' => $type
		);

		$attach_id = wp_insert_attachment( $attachment, $mirror['file'], $parent_id );

		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		$attach_data = wp_generate_attachment_metadata( $attach_id, $mirror['file'] );

		wp_update_attachment_metadata( $attach_id, $attach_data );

		$this->success( $body );
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