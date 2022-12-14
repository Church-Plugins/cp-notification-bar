<?php
/**
 * Plugin constants
 */

/**
 * Setup/config constants
 */
if( !defined( 'CPNB_PLUGIN_FILE' ) ) {
	 define ( 'CPNB_PLUGIN_FILE',
	 	dirname( dirname( __FILE__ ) ) . "/cp-notification-bars.php"
	);
}
if( !defined( 'CPNB_PLUGIN_DIR' ) ) {
	 define ( 'CPNB_PLUGIN_DIR',
	 	plugin_dir_path( CPNB_PLUGIN_FILE )
	);
}
if( !defined( 'CPNB_PLUGIN_URL' ) ) {
	 define ( 'CPNB_PLUGIN_URL',
	 	plugin_dir_url( CPNB_PLUGIN_FILE )
	);
}
if( !defined( 'CPNB_INCLUDES' ) ) {
	 define ( 'CPNB_INCLUDES',
	 	plugin_dir_path( dirname( __FILE__ ) ) . 'includes'
	);
}
if( !defined( 'CPNB_PREFIX' ) ) {
	define ( 'CPNB_PREFIX',
		'cpc'
   );
}
if( !defined( 'CPNB_TEXT_DOMAIN' ) ) {
	 define ( 'CPNB_TEXT_DOMAIN',
		'cp-notification-bars'
   );
}
if( !defined( 'CPNB_DIST' ) ) {
	 define ( 'CPNB_DIST',
		CPNB_PLUGIN_URL . "/dist/"
   );
}

/**
 * Licensing constants
 */
if( !defined( 'CPNB_STORE_URL' ) ) {
	 define ( 'CPNB_STORE_URL',
	 	'https://churchplugins.com'
	);
}
if( !defined( 'CPNB_ITEM_NAME' ) ) {
	 define ( 'CPNB_ITEM_NAME',
	 	'Church Plugins - Live'
	);
}

/**
 * App constants
 */
if( !defined( 'CPNB_APP_PATH' ) ) {
	 define ( 'CPNB_APP_PATH',
	 	plugin_dir_path( dirname( __FILE__ ) ) . 'app'
	);
}
if( !defined( 'CPNB_ASSET_MANIFEST' ) ) {
	 define ( 'CPNB_ASSET_MANIFEST',
	 	plugin_dir_path( dirname( __FILE__ ) ) . 'app/build/asset-manifest.json'
	);
}
