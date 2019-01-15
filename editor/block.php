<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 1/18/19
 * Time: 12:20 PM
 */


class Brizy_Editor_Block extends Brizy_Editor_Post {

	/**
	 * @param $apost
	 *
	 * @return Brizy_Editor_Block|null
	 * @throws Brizy_Editor_Exceptions_NotFound
	 */
	public static function get( $apost, $uid = null ) {

		$wp_post_id = $apost;
		if ( $apost instanceof WP_Post ) {
			$wp_post_id = $apost->ID;
		}

		if ( isset( self::$instance[ $wp_post_id ] ) ) {
			return self::$instance[ $wp_post_id ];
		}

		return self::$instance[ $wp_post_id ] = new self( $wp_post_id, $uid );

	}

	public function __construct( $wp_post_id, $uid = null ) {

		self::checkIfPostTypeIsSupported( $wp_post_id );
		$this->wp_post_id = (int) $wp_post_id;

		if ( $this->wp_post_id ) {
			$this->wp_post = get_post( $this->wp_post_id );
		}

		// get the storage values
		$storage = $this->storage();
		//$storageData          = $storage->get_storage();
		$storage_post = $storage->get( self::BRIZY_POST, false );

		$this->loadStorageData( $storage_post );

		if ( $uid ) {
			$this->uid = $uid;
			update_post_meta( $this->get_parent_id(), 'brizy_post_uid', $this->uid );
		} else {
			$this->create_uid();
		}
	}

	public function jsonSerialize() {
		$data = get_object_vars( $this );
		$data['editor_data'] = base64_decode( $data['editor_data'] );
		unset( $data['wp_post'] );
		return $data;
	}
}