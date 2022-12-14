<?php

namespace CPNB\Setup;

/**
 * Setup plugin initialization
 */
class _Init {

	/**
	 * @var _Init
	 */
	protected static $_instance;

	/**
	 * @var PostTypes\_Init;
	 */
	public $post_types;
	
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
	 */
	protected function __construct() {
		$this->includes();
		$this->actions();
	}

	/**
	 * Admin init includes
	 *
	 * @return void
	 */
	protected function includes() {
		$this->post_types = PostTypes\_Init::get_instance();
	}

	protected function actions() {}

	/** Actions ***************************************************/

}
