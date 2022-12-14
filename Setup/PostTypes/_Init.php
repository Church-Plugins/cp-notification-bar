<?php

namespace CPNB\Setup\PostTypes;

/**
 * Setup plugin initialization for CPTs
 */
class _Init {

	/**
	 * @var _Init
	 */
	protected static $_instance;
	
	/**
	 * Setup Staff CPT
	 *
	 * @var NotificationBars
	 */
	public $notification_bars;

	/**
	 * Only make one instance of _Init
	 *
	 * @return _Init
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof _Init ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class constructor
	 *
	 * Run includes and actions on instantiation
	 *
	 */
	protected function __construct() {
		$this->includes();
		$this->actions();
	}

	/**
	 * Plugin init includes
	 *
	 * @return void
	 */
	protected function includes() {}

	public function in_post_types( $type ) {
		return in_array( $type, $this->get_post_types() );
	}

	public function get_post_types() {
		return [ $this->notification_bars->post_type ];
	}
	
	/**
	 * Plugin init actions
	 *
	 * @return void
	 * @author costmo
	 */
	protected function actions() {
		add_filter( 'use_block_editor_for_post_type', [ $this, 'disable_gutenberg' ], 10, 2 );
		add_action( 'init', [ $this, 'register_post_types' ], 4 );
	}

	public function register_post_types() {

		$this->notification_bars = NotificationBars::get_instance();
		do_action( 'cp_register_post_types' );
	}
	
	public function disable_gutenberg( $status, $post_type ) {
		if ( $this->in_post_types( $post_type ) ) {
			return false;
		}

		return $status;
	}
	

}
