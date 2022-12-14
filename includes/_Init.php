<?php
namespace CPNB;

use CPNB\Admin\Settings;

/**
 * Provides the global $cp_notification_bars object
 *
 * @author costmo
 */
class _Init {

	/**
	 * @var
	 */
	protected static $_instance;

	/**
	 * @var Setup\_Init
	 */
	public $setup;

	/**
	 * @var Services\_Init
	 */
	public $services;

	/**
	 * @var Integrations\_Init
	 */
	public $integrations;

	public $enqueue;

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
	 * Class constructor: Add Hooks and Actions
	 *
	 */
	protected function __construct() {
		$this->enqueue = new \WPackio\Enqueue( 'cpLive', 'dist', $this->get_version(), 'plugin', CPNB_PLUGIN_FILE );
		add_action( 'plugins_loaded', [ $this, 'maybe_setup' ], - 9999 );
		add_action( 'init', [ $this, 'maybe_init' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Plugin setup entry hub
	 *
	 * @return void
	 */
	public function maybe_setup() {
		if ( ! $this->check_required_plugins() ) {
			return;
		}

		$this->includes();
		$this->actions();
	}

	/**
	 * Actions that must run through the `init` hook
	 *
	 * @return void
	 * @author costmo
	 */
	public function maybe_init() {

		if ( ! $this->check_required_plugins() ) {
			return;
		}

	}

	/**
	 * `wp_enqueue_scripts` actions for the app's compiled sources
	 *
	 * @return void
	 * @author costmo
	 */
	public function enqueue_scripts() {
		$this->enqueue->enqueue( 'styles', 'main', [] );
		$this->enqueue->enqueue( 'scripts', 'main', [] );
	}

	/**
	 * Includes
	 *
	 * @return void
	 */
	protected function includes() {
		require_once( 'Templates.php' );
		
		Admin\_Init::get_instance();
		
		$this->setup = Setup\_Init::get_instance();
		$this->integrations = Integrations\_Init::get_instance();
	}
	
	protected function actions() {
		add_action( 'plugins_loaded', [ $this, 'load_services' ] );
	}
	
	/**
	 * Required Plugins notice
	 *
	 * @return void
	 */
	public function required_plugins() {
		printf( '<div class="error"><p>%s</p></div>', __( 'Your system does not meet the requirements for Church Plugins - Live', 'cp-notification-bars' ) );
	}

	/** Helper Methods **************************************/

	/**
	 * Determine if any active services are live. Return the id of the first live service found
	 * 
	 * @return mixed|void
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function is_live() {
		$is_live = false;
		
		foreach( $this->services->active as $id => $service ) {
			/** @var $service Services\Service */
			if ( $service->is_live() ) {
				$is_live = $id;
				break;	
			}
		}
		
		return apply_filters( 'cp_notification_bars_is_live', $is_live );
	}

	/**
	 * Return the live embed for the live service
	 * 
	 * @return mixed|void
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function get_live_embed() {
		$embed = '';
		
		foreach( array_reverse( $this->services->active ) as $service ) {
			/** @var $service Services\Service */
			$embed = $service->get_embed();

			if ( $service->is_live() ) {
				break;	
			}
		}
		
		return apply_filters( 'cp_notification_bars_get_live_embed', $embed );		
	}

	/**
	 * Check if we are in the window of a schedule to check for a live stream
	 * 
	 * @param $schedules
	 *
	 * @return bool
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function schedule_is_now( $schedules = false ) {
		if ( false === $schedules ) {
			$schedules = Settings::get( 'schedule_group' );
		}
		
		$day       = strtolower( date( 'l', current_time( 'timestamp' ) ) );
		$timestamp = current_time( 'timestamp' );
		$buffer    = Settings::get_advanced( 'buffer_before', 8 ) * MINUTE_IN_SECONDS; // start watching 15 minutes before the start time
		$duration  = Settings::get_advanced( 'buffer_after', 12 ) * MINUTE_IN_SECONDS; // how long we'll keep checking after the service should have started. Allow for the initial 15 min. 

		if ( empty( $schedules ) ) {
			return false;
		}

		foreach ( $schedules as $schedule ) {
			if ( $day !== $schedule['day'] ) {
				continue;
			}
			
			if ( empty( $schedule['time'] ) ) {
				continue;
			}

			foreach ( $schedule['time'] as $time ) {
				$start = strtotime( 'today ' . $time, current_time( 'timestamp' ) ) - $buffer;
				$end   = $start + $duration + $buffer;

				// if we fall in the window, continue with the check
				if ( $timestamp > $start && $timestamp < $end ) {
					return true;
				}
			}
		}

		return false;		
	}

	/**
	 * Return the next scheduled event
	 * 
	 * @param $schedules
	 *
	 * @return false|mixed|null
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function get_next_schedule( $schedules = false ) {
		if ( false === $schedules ) {
			$schedules = Settings::get( 'schedule_group' );
		}
		
		$times = [];
		
		if ( empty( $schedules ) ) {
			return false;
		}

		foreach ( $schedules as $schedule ) {
			if ( empty( $schedule['time'] ) ) {
				continue;
			}

			foreach ( $schedule['time'] as $time ) {
				$times[] = strtotime( $schedule['day'] . ' ' . $time );
			}
		}
		
		asort( $times );
		
		if ( empty( $times ) ) {
			return false;
		}
		
		return array_shift( $times ); // - (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
	}
	
	public function load_services() {
		
	}
	
	public function get_default_thumb() {
		return CPNB_PLUGIN_URL . '/app/public/logo512.png';
	}

	/**
	 * Make sure required plugins are active
	 *
	 * @return bool
	 */
	protected function check_required_plugins() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// @todo check for requirements before loading
		if ( 1 ) {
			return true;
		}

		add_action( 'admin_notices', array( $this, 'required_plugins' ) );

		return false;
	}

	/**
	 * Gets the plugin support URL
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_support_url() {
		return 'https://churchplugins.com/support';
	}

	/**
	 * Returns the plugin name, localized
	 *
	 * @since 1.0.0
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'Church Plugins - Live', 'cp-notification-bars' );
	}

	/**
	 * Returns the plugin name, localized
	 *
	 * @since 1.0.0
	 * @return string the plugin name
	 */
	public function get_plugin_path() {
		return CPNB_PLUGIN_DIR;
	}

	/**
	 * Provide a unique ID tag for the plugin
	 *
	 * @return string
	 */
	public function get_id() {
		return 'cp-notification-bars';
	}

	/**
	 * Provide a unique ID tag for the plugin
	 *
	 * @return string
	 */
	public function get_version() {
		return CPNB_PLUGIN_VERSION;
	}

	/**
	 * Get the API namespace to use
	 *
	 * @return string
	 * @since  1.0.0
	 *
	 * @author Tanner Moushey
	 */
	public function get_api_namespace() {
		return $this->get_id() . '/v1';
	}

	public function enabled() {
		return true;
	}

}
