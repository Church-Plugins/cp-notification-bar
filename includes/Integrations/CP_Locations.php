<?php

namespace CPNB\Integrations;

class CP_Locations {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * Only make one instance of CP_Locations
	 *
	 * @return CP_Locations
	 */
	public static function get_instance() {
		if ( ! self::$_instance instanceof CP_Locations ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add Hooks and Actions
	 */
	public function __construct() {

	}
		
}