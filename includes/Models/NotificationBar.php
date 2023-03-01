<?php

namespace CPNB\Models;

use ChurchPlugins\Exception;
use ChurchPlugins\Models\Table;
use CPNB\Controllers\NotificationBar as Bar;

/**
 * Item DB Class
 *
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Item Class
 *
 * @since 1.0.0
 */
class NotificationBar extends Table{

	/**
	 * The ID of the bar
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * The post_type for bar
	 *
	 * @var string
	 */
	protected $post_type = 'cp_notification_bar';

	/**
	 * Get things started
	 *
	 * @since   1.0
	 */
	public function __construct( $id = 0 ) {
		if ( ! $id ) {
			return;
		}

		$this->id = $id;
	}

	/**
	 * return instance. For compatibility
	 *
	 * @param $origin_id
	 *
	 * @return NotificationBar
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public static function get_instance_from_origin( $origin_id ) {
		return new self( $origin_id );
	}

	/**
	 * return instance. For compatibility
	 *
	 * @param $id
	 *
	 * @return NotificationBar
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public static function get_instance( $id = 0 ) {
		return new self( $id );
	}

	public static function get_prop( $var ) {
		$class    = get_called_class();
		$instance = new $class();

		if ( property_exists( $instance, $var ) ) {
			return $instance->$var;
		}

		return '';
	}

	/**
	 * Create new notification bar
	 *
	 * @param $data
	 *
	 * @return \CPNB\Controllers\NotificationBar
	 * @throws Exception
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public static function insert( $data ) {
		$bar = wp_insert_post( [
			'post_type'   => self::get_prop( 'post_type' ),
			'post_status' => 'publish',
			'post_title'  => $data['title'],
		] );

		unset( $data['title'] );

		if ( is_wp_error( $bar ) ) {
			throw new Exception( $bar->get_error_message() );
		}

		$bar = new Bar( $bar );

		foreach ( $data as $key => $detail ) {
			$bar->model->update_meta_value( $key, $detail );
		}

		return $bar;
	}

	/**
	 * Delete this bar
	 *
	 * @return bool|void
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function delete() {
		wp_delete_post( $this->id );
	}

	/**
	 * Get current notification bar
	 *
	 * @return \CPNB\Controllers\NotificationBar|false
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public static function get_active() {
		$args = apply_filters( 'cp_notification_bars_get_active_args', [
			'post_type'      => self::get_prop( 'post_type' ),
			'posts_per_page' => 1,
			'fields' => 'ids',
		] );


		$bars = get_posts( $args );

		if ( empty( $bars ) ) {
			return false;
		}

		try {
			$bar = new Bar( $bars[0] );
		} catch( Exception $e ) {
			error_log( $e );
			return false;
		}

		return apply_filters( 'cp_notification_bars_get_active', $bar );
	}

	/**
	 * Update meta for this bar
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return bool|int
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function update_meta_value( $key, $value ) {
		return update_post_meta( $this->id, $key, $value );
	}

	/**
	 * blank function for compatibility
	 *
	 * @param $data
	 *
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function update( $data = [] ) {}

}
