<?php
/**
 * Templating functionality for Church Plugins groups
 */

namespace CPNB;

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/**
 * Handle views and template files.
 */
class Templates {

	public static function get_template_part( $template, $args = [] ) {
		$template = self::get_template_hierarchy( $template );
		include( $template );
	}

	/**
	 * Loads theme files in appropriate hierarchy: 1) child theme,
	 * 2) parent template, 3) plugin resources. will look in the cp-notification-bars/
	 * directory in a theme and the templates/ directory in the plugin
	 *
	 * @param string $template template file to search for
	 * @param array  $args     additional arguments to affect the template path
	 *                         - namespace
	 *                         - plugin_path
	 *                         - disable_view_check - bypass the check to see if the view is enabled
	 *
	 * @return string template path
	 **/
	public static function get_template_hierarchy( $template, $args = [] ) {
		if ( ! is_array( $args ) ) {
			$passed        = func_get_args();
			$args          = [];
			$backwards_map = [ 'namespace', 'plugin_path' ];
			$count         = count( $passed );

			if ( $count > 1 ) {
				for ( $i = 1; $i < $count; $i ++ ) {
					$args[ $backwards_map[ $i - 1 ] ] = $passed[ $i ];
				}
			}
		}

		$args = wp_parse_args(
			$args, [
				'namespace'          => '/',
				'plugin_path'        => '',
				'disable_view_check' => false,
			]
		);

		/**
		 * @var string $namespace
		 * @var string $plugin_path
		 * @var bool   $disable_view_check
		 */
		extract( $args );

		// append .php to file name
		if ( substr( $template, - 4 ) != '.php' && false === strpos( $template, '.json' ) ) {
			$template .= '.php';
		}

		// Allow base path for templates to be filtered
		$template_base_paths = apply_filters( 'cp_template_paths', ( array ) cp_notification_bars()->get_plugin_path() );

		// backwards compatibility if $plugin_path arg is used
		if ( $plugin_path && ! in_array( $plugin_path, $template_base_paths ) ) {
			array_unshift( $template_base_paths, $plugin_path );
		}

		$file = false;

		/* potential scenarios:

		- the user has no template overrides
			-> we can just look in our plugin dirs, for the specific path requested, don't need to worry about the namespace
		- the user created template overrides without the namespace, which reference non-overrides without the namespace and, their own other overrides without the namespace
			-> we need to look in their theme for the specific path requested
			-> if not found, we need to look in our plugin views for the file by adding the namespace
		- the user has template overrides using the namespace
			-> we should look in the theme dir, then the plugin dir for the specific path requested, don't need to worry about the namespace

		*/

		// check if there are overrides at all
		if ( locate_template( [ 'cp-notification-bars/' ] ) ) {
			$overrides_exist = true;
		} else {
			$overrides_exist = false;
		}

		if ( $overrides_exist ) {
			// check the theme for specific file requested
			$file = locate_template( [ 'cp-notification-bars/' . $template ], false, false );
			if ( ! $file ) {
				// if not found, it could be our plugin requesting the file with the namespace,
				// so check the theme for the path without the namespace
				$files = [];
				foreach ( array_keys( $template_base_paths ) as $namespace ) {
					if ( ! empty( $namespace ) && ! is_numeric( $namespace ) ) {
						$files[] = 'cp-notification-bars' . str_replace( $namespace, '', $template );
					}
				}
				$file = locate_template( $files, false, false );
				if ( $file ) {
					_deprecated_function( sprintf( esc_html__( 'Template overrides should be moved to the correct subdirectory: %s', 'cp-notification-bars' ), str_replace( get_stylesheet_directory() . '/cp-notification-bars/', '', $file ) ), '3.2', $template );
				}
			} else {
				$file = apply_filters( 'cp_template', $file, $template );
			}
		}

		// if the theme file wasn't found, check our plugins views dirs
		if ( ! $file ) {

			foreach ( $template_base_paths as $template_base_path ) {

				// make sure directories are trailingslashed
				$template_base_path = ! empty( $template_base_path ) ? trailingslashit( $template_base_path ) : $template_base_path;

				$file = $template_base_path . 'templates/' . $template;

				$file = apply_filters( 'cp_template', $file, $template );

				// return the first one found
				if ( file_exists( $file ) ) {
					break;
				} else {
					$file = false;
				}
			}
		}

		return apply_filters( 'cp_template_' . $template, $file );
	}

	
}
