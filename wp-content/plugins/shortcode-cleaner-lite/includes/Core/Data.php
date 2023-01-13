<?php

namespace JO\ShortcodeCleaner\Core;

/**
 * Sharing the plugin data info.
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
class Data
{

    /**
     * The plugin name.
     *
     * @since 1.0.0
     * @access protected
     * @var string
     */
    const PLUGIN_NAME = 'Shortcode Cleaner Lite';
    /**
     * The plugin short name.
     *
     * @since 1.0.0
     * @access protected
     * @var string
     */
    const PLUGIN_SHORT_NAME = 'Cleaner';
    /**
     * The plugin slug.
     *
     * @since 1.0.0
     * @access protected
     * @var string
     */
    const PLUGIN_SLUG = 'shortcode-cleaner-lite';
    /**
     * The plugin small slug.
     *
     * @since 1.0.0
     * @access protected
     * @var string
     */
    const PLUGIN_SMALL_SLUG = 'scl';
    /**
     * The plugin version.
     *
     * @since 1.0.0
     * @access protected
     * @var string
     */
    const PLUGIN_VERSION = '1.0.9';
    /**
     * The plugin prefix.
     *
     * @since 1.0.0
     * @access protected
     * @var string
     */
    const PLUGIN_PREFIX = 'jo_scl_';
    /**
     * The plugin settings admin page menu slug.
     *
     * @since 1.0.0
     * @access protected
     * @var string
     */
    const PLUGIN_MENU_PAGE_SLUG = 'sc_shortcode_cleaner_lite';
    /**
     * Minimum WordPress version required (or later).
     *
     * @since 1.0.0
     * @access protected
     * @var string
     */
    const MIN_WP_VERSION = '4.6';
    /**
     * Minimum PHP version required (or later).
     *
     * @since 1.0.0
     * @access protected
     * @var string
     */
    const MIN_PHP_VERSION = '5.6';
    /**
     * The plugin assets folder name.
     *
     * @since 1.0.0
     * @access protected
     * @var string
     */
    const PLUGIN_ASSETS = 'assets';
    /**
     * The plugin templates folder name.
     *
     * @since 1.0.0
     * @access protected
     * @var string
     */
    const PLUGIN_TEMPLATES = 'templates';

    /**
     * Get unique settings options id (name).
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_unique_settings_options_id()
    {
        return self::PLUGIN_PREFIX . 'settings_options';
    }

    /**
     * Get shortcodes status cache key.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_shortcodes_status_cache_key()
    {
        return self::PLUGIN_MENU_PAGE_SLUG . '_shortcodes_status';
    }

    /**
     * Get nav menus cache key.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_nav_menus_cache_key()
    {
        return self::PLUGIN_MENU_PAGE_SLUG . '_nav_menus';
    }

    /**
     * Get nav menus items (links) cache key.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_nav_menus_items_cache_key()
    {
        return self::PLUGIN_MENU_PAGE_SLUG . '_nav_menus_items';
    }

    /**
     * Get cleaner history data cache key.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_cleaner_history_data_cache_key()
    {
        return self::PLUGIN_MENU_PAGE_SLUG . '_history_data';
    }

    /**
     * Get the filesystem dir path (with trailing slash) for the plugin.
     * for loading PHP files.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_plugin_path()
    {
        return plugin_dir_path(dirname(dirname(__FILE__)));
    }

    /**
     * Get the URL dir path (with trailing slash) for the plugin.
     * for loading images, css, js, fonts files.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_plugin_url()
    {
        return plugin_dir_url(dirname(dirname(__FILE__)));
    }

    /**
     * Get the plugin dir name enough.
     * will output: 'shortcodes-cleaner'
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_plugin_dir_name()
    {
        return dirname(dirname(dirname(plugin_basename(__FILE__))));
    }

    /**
     * Get the plugin filesystem dir with file name.
     * for getting the plugin data info.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_plugin_path_file()
    {
        return self::get_plugin_path() . self::get_plugin_dir_name() . '.php';
    }

    /**
     * Get the plugin dir with file name enough.
     * for add links to plugin links list.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_plugin_dir_file_name()
    {
        return self::get_plugin_dir_name() . '/' . self::get_plugin_dir_name() . '.php';
    }

    /**
     * Get full assets dir path of the plugin.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_plugin_assets_dir()
    {
        return self::get_plugin_path() . self::PLUGIN_ASSETS . '/';
    }

    /**
     * Get full templates dir path of the plugin.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_plugin_templates_dir()
    {
        return self::get_plugin_path() . self::PLUGIN_TEMPLATES . '/';
    }

    /**
     * Get the current WordPress version.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_wp_version()
    {
        global $wp_version;
        return $wp_version;
    }

    /**
     * Get the current PHP version.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_php_version()
    {
        return phpversion();
    }

}
