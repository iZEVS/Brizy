<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 9/13/18
 * Time: 4:47 PM
 */

class Brizy_Admin_Migrations_GlobalBlocksToCustomPostMigration implements Brizy_Admin_Migrations_MigrationInterface {

	/**
	 * Return the version
	 *
	 * @return mixed
	 */
	public function getVersion() {
		return '1.0.80';
	}

	/**
	 * @return int|mixed|WP_Error
	 * @throws Brizy_Editor_Exceptions_NotFound
	 */
	public function execute() {

		$project = Brizy_Editor_Project::get();
		$globals = $project->getGlobals();

		if ( $globals->project->globalBlocks ) {
			foreach ( get_object_vars( $globals->project->globalBlocks ) as $uid => $data ) {

				$post = wp_insert_post( array(
					'post_status' => 'publish',
					'post_type'   => Brizy_Admin_Blocks_Main::CP_GLOBAL
				) );

				if ( $post ) {
					$brizyPost = Brizy_Editor_Block::get( $post, $uid );
					$brizyPost->set_editor_data( json_encode( $data ) );
					$brizyPost->set_uses_editor( true );
					$brizyPost->set_needs_compile( true );
					$brizyPost->save();
				}

			}
		}

		if ( $globals->project->savedBlocks ) {
			foreach ( get_object_vars( $globals->project->savedBlocks ) as $uid => $data ) {

				$post = wp_insert_post( array(
					'post_status' => 'publish',
					'post_type'   => Brizy_Admin_Blocks_Main::CP_SAVED
				) );

				if ( $post ) {
					$brizyPost = Brizy_Editor_Block::get( $post, $uid );
					$brizyPost->set_editor_data( json_encode( $data ) );
					$brizyPost->set_uses_editor( true );
					$brizyPost->set_needs_compile( true );
					$brizyPost->save();
				}
			}
		}

		$globals->project->globalBlocks = (object) array();
		$globals->project->savedBlocks  = (object) array();

		$project->setGlobals( $globals );

		update_post_meta( $project->getWpPost()->ID, 'brizy-bk-' . get_class( $this ) . '-' . $this->getVersion(), $globals );
	}

	private function getPostIdByUid( $uid ) {
		global $wpdb;
		$CP_GLOBAL = 'brizy-global-block';
		$CP_SAVED  = 'brizy-saved-block';

		$prepare = $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} p 
								JOIN {$wpdb->postmeta} pm  ON 
								pm.post_id=p.ID and 
								meta_key='brizy_post_uid' and 
								meta_value='%s'   
								WHERE p.post_type IN ('{$CP_GLOBAL}','{$CP_SAVED}')
								LIMIT 1", array( $uid ) );

		return $wpdb->get_var( $prepare );
	}
}