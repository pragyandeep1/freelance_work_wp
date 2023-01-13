<?php

/**
 * Plugin Name:  Shortcode Cleaner Lite
 * Plugin URI:   https://plugins.jozoor.com/shortcode-cleaner
 * Description:  Clean your WordPress content from unused broken shortcodes.
 * Version:      1.0.9
 * Author:       Jozoor
 * Author URI:   https://jozoor.com/
 * License:      GPL2
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  shortcode-cleaner
 * Domain Path:  /languages
 *
 * @package      Shortcode_Cleaner_Lite
 * @author       Jozoor
 * @license      GPL2
 */

/**
 * If this file is called directly, abort.
 *
 * @since  1.0.0
 */
if (!defined('ABSPATH')) {
    die;
}

/**
 * Require once the Composer Autoload.
 *
 * @since  1.0.0
 */
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * The code that runs during plugin activation.
 *
 * @since  1.0.0
 */
function jo_activate_shortcode_cleaner_lite()
{
    JO\ShortcodeCleaner\Core\Activate::run();
}
register_activation_hook(__FILE__, 'jo_activate_shortcode_cleaner_lite');

/**
 * The code that runs during plugin deactivation.
 *
 * @since  1.0.0
 */
function jo_deactivate_shortcode_cleaner_lite()
{
    JO\ShortcodeCleaner\Core\Deactivate::run();
}
register_deactivation_hook(__FILE__, 'jo_deactivate_shortcode_cleaner_lite');

/**
 * Initialize all the core classes of the plugin.
 *
 * @since  1.0.0
 */
function jo_init_shortcode_cleaner_lite()
{
    JO\ShortcodeCleaner\Init::run();
}
add_action('plugins_loaded', 'jo_init_shortcode_cleaner_lite');

/**
 * Load normal functions if needed.
 * for compatiblity check admin notices or anything else.
 *
 * @since  1.0.0
 */
if (file_exists(__DIR__ . '/includes/Core/functions.php')) {
    require_once __DIR__ . '/includes/Core/functions.php';
}
