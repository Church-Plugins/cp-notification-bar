<?php
namespace CPNB\Setup\PostTypes;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

use ChurchPlugins\Setup\PostTypes\PostType;

/**
 * Setup for custom post type: Speaker
 *
 * @author costmo
 * @since 1.0
 */
class NotificationBars extends PostType {
	
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
		
		return __( 'Add notfication bar label', 'cp-notification-bars' );
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
		$args               = parent::get_args();
		$args['menu_icon']  = apply_filters( "{$this->post_type}_icon", 'dashicons-id' );
		$args['has_archive'] = false;
		$args['supports'][] = 'page-attributes';
		return $args;
	}
	
	public function register_metaboxes() {
		$this->meta_details();
	}

	protected function meta_details() {
		$cmb = new_cmb2_box( [
			'id' => 'staff_meta',
			'title' => $this->single_label . ' ' . __( 'Details', 'cp-notification-bars' ),
			'object_types' => [ $this->post_type ],
			'context' => 'normal',
			'priority' => 'high',
			'show_names' => true,
		] );

		$cmb->add_field( [
			'name' => __( 'Title', 'cp-notification-bars' ),
			'desc' => __( 'The title for this staff member.', 'cp-notification-bars' ),
			'id'   => 'title',
			'type' => 'text',
		] );

		$cmb->add_field( [
			'name' => __( 'Email', 'cp-notification-bars' ),
			'desc' => __( 'The email address for this staff member.', 'cp-notification-bars' ),
			'id'   => 'email',
			'type' => 'text_email',
		] );
	}
	
}
