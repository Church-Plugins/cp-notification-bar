<?php
/**
 * Plugin Name: CP Notification Bars
 * Plugin URL: https://churchplugins.com
 * Description: Easy and customizable notification bars.
 * Version: 1.0.0
 * Author: Church Plugins
 * Author URI: https://churchplugins.com
 * Text Domain: cp-notification-bars
 * Domain Path: languages
 */

if( !defined( 'CPNB_PLUGIN_VERSION' ) ) {
	 define ( 'CPNB_PLUGIN_VERSION',
	 	'1.0.0'
	);
}

require_once( dirname( __FILE__ ) . "/includes/Constants.php" );

require_once( CPNB_PLUGIN_DIR . "/includes/ChurchPlugins/init.php" );
require_once( CPNB_PLUGIN_DIR . 'vendor/autoload.php' );


use CPNB\_Init as Init;

/**
 * @var CPNB\_Init
 */
global $cp_notification_bars;
$cp_notification_bars = cp_notification_bars();

/**
 * @return CPNB\_Init
 */
function cp_notification_bars() {
	return Init::get_instance();
}

/**
 * Load plugin text domain for translations.
 *
 * @return void
 */
function cp_notification_bars_load_textdomain() {

	// Traditional WordPress plugin locale filter
	$get_locale = get_user_locale();

	/**
	 * Defines the plugin language locale used in RCP.
	 *
	 * @var string $get_locale The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
	 *                  otherwise uses `get_locale()`.
	 */
	$locale        = apply_filters( 'plugin_locale',  $get_locale, 'cp-notification-bars' );
	$mofile        = sprintf( '%1$s-%2$s.mo', 'cp-notification-bars', $locale );

	// Setup paths to current locale file
	$mofile_global = WP_LANG_DIR . '/cp-notification-bars/' . $mofile;

	if ( file_exists( $mofile_global ) ) {
		// Look in global /wp-content/languages/cp-notification-bars folder
		load_textdomain( 'cp-notification-bars', $mofile_global );
	}

}
add_action( 'init', 'cp_notification_bars_load_textdomain' );
