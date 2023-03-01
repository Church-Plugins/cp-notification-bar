<?php
namespace CPNB\Setup\PostTypes;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

use ChurchPlugins\Exception;
use ChurchPlugins\Setup\PostTypes\PostType;

/**
 * Setup for custom post type: Notification Bar
 *
 * @author costmo
 * @since 1.0
 */
class NotificationBar extends PostType {

	/**
	 * @var string
	 */
	public $expiration_action = 'cpnb_expire_bar';

	/**
	 * Child class constructor. Punts to the parent.
	 *
	 * @author costmo
	 */
	protected function __construct() {
		$this->post_type = "cp_notification_bar";

		$this->single_label = __( 'Notification Bar', 'cp-notification-bars' );
		$this->plural_label = __( 'Notification Bars', 'cp-notification-bars' );

		parent::__construct();
	}

	public function add_actions() {
		add_filter( 'enter_title_here', [ $this, 'add_title' ], 10, 2 );
		add_filter( 'cp_location_taxonomy_types', [ $this, 'location_tax' ] );
		add_action( 'save_post', [ $this, 'schedule_expiration' ], 500 );
		add_action( $this->expiration_action, [ $this, 'expire_bar' ] );

		parent::add_actions();
	}

	/**
	 * Update title placeholder in edit page
	 *
	 * @param $title
	 * @param $post
	 *
	 * @return string|void
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function add_title( $title, $post ) {
		if ( get_post_type( $post ) != $this->post_type ) {
			return $title;
		}

		return __( 'Add label', 'cp-notification-bars' );
	}

	/**
	 * Add Staff to locations taxonomy if it exists
	 *
	 * @param $types
	 *
	 * @return mixed
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function location_tax( $types ) {
		$types[] = $this->post_type;
		return $types;
	}

	/**
	 * Get the slug for this taxonomy
	 *
	 * @return false|mixed
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function get_slug() {
		if ( ! $type = get_post_type_object( $this->post_type ) ) {
			return false;
		}

		return $type->rewrite['slug'];
	}

	/**
	 * Setup arguments for this CPT
	 *
	 * @return array
	 * @author costmo
	 */
	public function get_args() {
		$args                = parent::get_args();
		$args['menu_icon']   = apply_filters( "{$this->post_type}_icon", 'dashicons-minus' );
		$args['has_archive'] = false;
		$args['supports']    = [ 'title' ];

		return $args;
	}

	public function register_metaboxes() {
		$this->meta_details();
	}

	protected function meta_details() {
		$cmb = new_cmb2_box( [
			'id' => 'notification_bar_meta',
			'title' => $this->single_label . ' ' . __( 'Details', 'cp-notification-bars' ),
			'object_types' => [ $this->post_type ],
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true,
		] );

		$cmb->add_field( [
			'name' => __( 'Text', 'cp-notification-bars' ),
			'desc' => __( 'The text to show on the notification bar.', 'cp-notification-bars' ),
			'id'   => 'text',
			'type' => 'wysiwyg',
			'options' => [
				'textarea_rows' => 3
			],
		] );

		$cmb->add_field( [
			'name' => __( 'Link', 'cp-notification-bars' ),
			'desc' => __( 'The link for the button. If no button text is entered, the whole bar will be clickable.', 'cp-notification-bars' ),
			'id'   => 'url',
			'type' => 'text_url',
		] );

		$cmb->add_field( [
			'name' => __( 'Button Text', 'cp-notification-bars' ),
			'desc' => __( 'The text to show for the notification call to button. (Leave blank for no button)', 'cp-notification-bars' ),
			'id'   => 'button_text',
			'type' => 'text',
		] );

//		$cmb->add_field( [
//			'name' => __( 'Button Icon', 'cp-notification-bars' ),
//			'desc' => __( 'The icon to show on the button. (Leave blank for no icon)', 'cp-notification-bars' ),
//			'id'   => 'button_icon',
//			'type' => 'file',
//		] );

		$cmb->add_field( [
			'name' => __( 'Expiration Date', 'cp-notification-bars' ),
			'desc' => __( 'Select a date for the bar to be automatically deleted. (Leave blank for bar to persist)', 'cp-notification-bars' ),
			'id'   => 'expiration_date',
			'type' => 'text_datetime_timestamp',
		] );

	}

	protected function visibility_options() {
		$cmb = new_cmb2_box( [
			'id'           => 'notification_bar_visibility',
			'title'        => $this->single_label . ' ' . __( 'Display Options', 'cp-notification-bars' ),
			'object_types' => [ $this->post_type ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		] );
	}

	/**
	 * Schedule the expiration of this bar if applicable
	 *
	 * @param $post_id
	 *
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function schedule_expiration( $post_id ) {
		try {
			$bar = new \CPNB\Controllers\NotificationBar( $post_id );
			$bar->schedule_expiration();
		} catch ( Exception $e ) {
			error_log( $e );
		}
	}

	/**
	 * Delete the provided notification bar
	 *
	 * @param $post_id
	 *
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function expire_bar( $post_id ) {
		try {
			$bar = new \CPNB\Controllers\NotificationBar( $post_id );
			$bar->model->delete();
		} catch ( Exception $e ) {
			error_log( $e );
		}
	}

}
