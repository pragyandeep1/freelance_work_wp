<?php
/**
 * The plugin init file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wp-guppy.com
 * @since             1.0.0
 * @package           wp-guppy
 *
 * @wordpress-plugin
 * Plugin Name:       WP Guppy
 * Plugin URI:        https://wp-guppy.com/
 * Description:       WP Guppy is a well thought and clinically designed and developed WordPress chat plugin which has been engineered to fulfil the market needs. It is loaded with features without compromising on quality.
 * Version:           2.5
 * Author:            Amentotech Pvt ltd
 * Author URI:        https://themeforest.net/user/amentotech/portfolio
 * Text Domain:       wp-guppy
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$optionslc = get_option( 'epv_verify_settings' );
$optionslc['verified'] = true;
$optionslc['purchase_code'] = '***********';
update_option( 'epv_verify_settings', $optionslc );
if( !function_exists( 'wpguppy_load_last' ) ) {
	function wpguppy_load_last() {
		$wpguppy_file_path 		= preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__);
		$wpguppy_plugin 		= plugin_basename(trim($wpguppy_file_path));
		$wpguppy_active_plugins = get_option('active_plugins');
		$wpguppy_plugin_key 	= array_search($wpguppy_plugin, $wpguppy_active_plugins);
		array_splice($wpguppy_active_plugins, $wpguppy_plugin_key, 1);
		array_push($wpguppy_active_plugins, $wpguppy_plugin);
		update_option('active_plugins', $wpguppy_active_plugins);
	}
	
	add_action("activated_plugin", "wpguppy_load_last");
}

/**
 * Currently plugin version.
 */
define( 'WPGUPPY_VERSION', '2.3' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-guppy-activator.php
 */
function wpguppy_activate_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-guppy-activator.php';
	WPGuppy_Activator::activate();
}

register_activation_hook( __FILE__, 'wpguppy_activate_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/functions.php';
require plugin_dir_path( __FILE__ ) . 'config.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-guppy.php';
require plugin_dir_path( __FILE__ ) . 'admin/settings/settings.php';
require plugin_dir_path( __FILE__ ) . 'wpbakery/vc-guppy.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function wpguppy_run_chat() {
	$plugin = new WPGuppy();
	$plugin->run();
}
wpguppy_run_chat();

/**
 * verify purchase
 *
 *
 * @since    1.0.0
 */
function epv_settings_link ( $links ) {
	$mylinks = array(
		'<a href="' . admin_url( 'options-general.php?page=epv_verify_purchase' ) . '">' . esc_html__('Settings', 'wp-guppy') . '</a>',
	);
	return array_merge( $mylinks, $links );
}
add_action('plugin_action_links_' . plugin_basename(__FILE__), 'epv_settings_link');