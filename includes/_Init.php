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
		$this->enqueue = new \WPackio\Enqueue( 'cpNotificationBars', 'dist', $this->get_version(), 'plugin', CPNB_PLUGIN_FILE );
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
		
//		Admin\_Init::get_instance();
		
		$this->setup = Setup\_Init::get_instance();
		$this->integrations = Integrations\_Init::get_instance();
	}
	
	protected function actions() {
		add_action( 'wp_body_open', [ $this, 'load_notification_bars' ] );
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

	public function load_notification_bars() {
		$args = apply_filters( 'cp_notification_bars_args', [
			'post_type' => $this->setup->post_types->notification_bars->post_type,
			'posts_per_page' => 1
		] );
		
		
		$bars = get_posts( $args );
		
		if ( empty( $bars ) ) {
			return;
		}
		
		$bar = $bars[0];

		$has_button = false;
		$text   = get_post_meta( $bar->ID, 'text', true );
		$url    = get_post_meta( $bar->ID, 'url', true );
		if ( $button = get_post_meta( $bar->ID, 'button_text', true ) ) {
			$has_button = true;
		}
		
		?>
		<div class="cp-notification-bar cp-color-primary <?php echo ! $has_button ? 'cp-clickable' : ''; ?>" <?php echo ! $has_button ? 'onclick="window.location.href = \'' . $url . '\'"' : ''; ?>>
			<div class="cp-notification-bar--content">
				<div class="cp-notification-bar--text"><?php echo wp_kses_post( $text ); ?></div>
				
				<?php if ( $has_button ) : ?>
					<div class="cp-notification-bar--button"><a class="cp-button" href="<?php echo esc_url( $url ); ?>"><span><?php echo wp_kses_post( $button ); ?></span></a></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
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
		return __( 'CP Notification Bars', 'cp-notification-bars' );
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
