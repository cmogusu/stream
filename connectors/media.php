<?php

class WP_Stream_Connector_Media extends WP_Stream_Connector {

	/**
	 * Context name
	 * @var string
	 */
	public static $name = 'media';

	/**
	 * Actions registered for this context
	 * @var array
	 */
	public static $actions = array(
		'add_attachment',
		'edit_attachment',
		'delete_attachment',
	);

	/**
	 * Return translated context label
	 *
	 * @return string Translated context label
	 */
	public static function get_label() {
		return __( 'Media', 'stream' );
	}

	/**
	 * Return translated action labels
	 *
	 * @return array Action label translations
	 */
	public static function get_action_labels() {
		return array(
			'attached'   => __( 'Attached', 'stream' ),
			'uploaded'   => __( 'Uploaded', 'stream' ),
			'updated'    => __( 'Updated', 'stream' ),
			'deleted'    => __( 'Deleted', 'stream' ),
			'assigned'   => __( 'Assigned', 'stream' ),
			'unassigned' => __( 'Unassigned', 'stream' ),
		);
	}

	/**
	 * Return translated context labels
	 *
	 * @return array Context label translations
	 */
	public static function get_context_labels() {
		return array(
			'media' => __( 'Media', 'stream' ),
		);
	}

	/**
	 * Add action links to Stream drop row in admin list screen
	 *
	 * @filter wp_stream_action_links_posts
	 * @param  array $links      Previous links registered
	 * @param  int   $stream_id  Stream drop id
	 * @param  int   $object_id  Object ( post ) id
	 * @return array             Action links
	 */
	public static function action_links( $links, $stream_id, $object_id ) {
		
		return $links;
	}

	/**
	 * Tracks creation of attachments
	 *
	 * @action add_attachment
	 */
	public static function callback_add_attachment( $post_id ) {
		$post = get_post( $post_id );
		if ( $post->post_parent ) {
			$message = __( 'Attached "%s" to "%s"', 'stream' );
		} else {
			$message = __( 'Added "%s" to Media library', 'stream' );
		}
		$name      = $post->post_title;
		$url       = $post->guid;
		$parent_id = $post->post_parent;
		if ( $parent_id && $parent = get_post( $post->post_parent ) ) $parent_title = $parent->post_title;

		self::log(
			$message,
			compact( 'name', 'parent_title', 'parent_id', 'url' ),
			$post_id,
			array( 'media' => $post->post_parent ? 'attached' : 'uploaded' )
			);
	}

	/**
	 * Tracks editing attachments
	 *
	 * @action edit_attachment
	 */
	public static function callback_edit_attachment( $post_id ) {
		$post    = get_post( $post_id );
		$message = __( 'Updated "%s"', 'stream' );
		$name    = $post->post_title;

		self::log(
			$message,
			compact( 'name' ),
			$post_id,
			array( 'media' => 'updated' )
			);
	}

	/**
	 * Tracks deletion of attachments
	 *
	 * @action delete_attachment
	 */
	public static function callback_delete_attachment( $post_id ) {
		$post   = get_post( $post_id );
		$parent = $post->post_parent ? get_post( $post->post_parent ) : null;
		if ( $parent ) $parent_id = $parent->ID;
		$message = __( 'Deleted "%s"', 'stream' );
		$name    = $post->post_title;
		$url     = $post->guid;

		self::log(
			$message,
			compact( 'name', 'parent_id', 'url' ),
			$post_id,
			array( 'media' => 'deleted' )
			);
	}


}