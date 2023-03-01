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
		add_filter( 'cp_notification_bars_get_active_args', [ $this, 'location_args' ] );
	}


	public function location_args( $args ) {
		// we are already on a location, let the default query filter handle
		if ( get_query_var( 'cp_location_id' ) ) {
			return $args;
		}

		$args[ cp_locations()->setup->taxonomies->location->taxonomy ] = 'global';

		return $args;
	}
}