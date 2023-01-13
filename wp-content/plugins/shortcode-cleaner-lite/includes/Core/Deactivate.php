<?php

namespace JO\ShortcodeCleaner\Core;

use JO\ShortcodeCleaner\Core\Data;

/**
 * The code that runs during plugin deactivation.
 *
 * @package   Shortcode_Cleaner_Lite
 * @author    Jozoor, mohamdio [jozoor.com]
 * @link      https://plugins.jozoor.com/shortcode-cleaner
 * @copyright 2017 Jozoor, mohamdio [jozoor.com]
 * @license   GPL2
 * @version   1.0.0
 *
 * @since  1.0.0
 */
class Deactivate
{

    /**
     * Run this actions on plugin deactivation
     *
     * @since 1.0.0
     * @access public
     */
    public static function run()
    {

        /**
         * delete transients if we set any.
         */

        // delete compatiblity check transients
        delete_transient(Data::PLUGIN_PREFIX . 'compatible_check_notice_wp_version');
        delete_transient(Data::PLUGIN_PREFIX . 'compatible_check_wp_version');
        delete_transient(Data::PLUGIN_PREFIX . 'compatible_check_notice_php_version');
        delete_transient(Data::PLUGIN_PREFIX . 'compatible_check_php_version');
        delete_transient(Data::PLUGIN_PREFIX . 'compatible_check_plugin_name');

        // delete shortcodes status cached data
        delete_transient(Data::get_shortcodes_status_cache_key());

        // delete nav menus cached data
        delete_transient(Data::get_nav_menus_cache_key());

        // delete nav menus items (links) cached data
        foreach (wp_get_nav_menus() as $menu_object) {
            delete_transient(Data::get_nav_menus_items_cache_key() . '_' . $menu_object->slug);
        }

    }

}
