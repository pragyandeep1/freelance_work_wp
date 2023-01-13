<?php

namespace JO\ShortcodeCleaner\Core;

use JO\ShortcodeCleaner\Core\Data;

/**
 * Compatiblity check on activation and after activation.
 *
 * Make sure we have minimum requirments (WP and PHP) versions,
 * to make our plugin working correctly, if not deactivate plugin.
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
class Compatiblity
{

    /**
     * Check compatiblity on and after activation.
     *
     * Here we just set transient for admin notice error message.
     *
     * @since 1.0.0
     * @access public
     */
    public static function check()
    {

        /**
         * Check compatible WordPress version.
         */
        if (!self::compatible_version(Data::get_wp_version(), Data::MIN_WP_VERSION)) {

            // save error admin notice
            set_transient(
                Data::PLUGIN_PREFIX . 'compatible_check_notice_wp_version', true, 5
            );
            // save compatible wp version
            set_transient(
                Data::PLUGIN_PREFIX . 'compatible_check_wp_version', Data::MIN_WP_VERSION, 5
            );
            // save the plugin name
            set_transient(
                Data::PLUGIN_PREFIX . 'compatible_check_plugin_name', Data::PLUGIN_NAME, 5
            );

        }

        /**
         * Check compatible PHP version.
         */
        if (!self::compatible_version(Data::get_php_version(), Data::MIN_PHP_VERSION)) {

            // save error admin notice
            set_transient(
                Data::PLUGIN_PREFIX . 'compatible_check_notice_php_version', true, 5
            );
            // save compatible wp version
            set_transient(
                Data::PLUGIN_PREFIX . 'compatible_check_php_version', Data::MIN_PHP_VERSION, 5
            );
            // save the plugin name
            set_transient(
                Data::PLUGIN_PREFIX . 'compatible_check_plugin_name', Data::PLUGIN_NAME, 5
            );

        }

    }

    /**
     * Check compatiblity on and after activation.
     *
     * Here we actually stop plugin from activation and deactivate it by return true.
     *
     * @since 1.0.3
     * @access public
     * @return bool
     */
    public static function incompatible()
    {

        /**
         * Check compatible WordPress version.
         */
        if (!self::compatible_version(Data::get_wp_version(), Data::MIN_WP_VERSION)) {
            return true;
        }

        /**
         * Check compatible PHP version.
         */
        if (!self::compatible_version(Data::get_php_version(), Data::MIN_PHP_VERSION)) {
            return true;
        }

        /**
         * Here the plugin is compatible.
         */
        return false;

    }

    /**
     * Check compatible version for (WP and PHP).
     *
     * @since 1.0.0
     * @access public
     * @param  string $current_version
     * @param  string $compatible_version
     * @return boolean
     */
    public static function compatible_version($current_version, $compatible_version)
    {

        if (version_compare($current_version, $compatible_version, '<')) {
            return false;
        }

        return true;

    }

}
