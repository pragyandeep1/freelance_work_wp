<?php

namespace Codestar\Helper;

use Codestar\Config;

/**
 * System info class.
 *
 * @source redux-framework ReduxCore/inc/class.redux_helpers.php#L383
 *
 * ----------------------------------------------------------------------------
 * This is Codestar framework version 2.0.0, which i rebuild and redesign
 * the framework code and added new functionality to improve it, @see README.md.
 *
 * - new version 2.0.0 @copyright:
 * @package   Codestar
 * @author    mohamdio [jozoor.com]
 * @link      https://github.com/mohamdio/codestar
 * @copyright 2017 mohamdio [jozoor.com]
 * @license   GPL-2.0+
 * @version   2.0.0
 *
 * - old version 1.0.2 @copyright:
 * @package   Codestar_Framework
 * @author    Codestar [codestarlive.com]
 * @link      https://github.com/Codestar/codestar-framework
 * @copyright 2017 Codestar [codestarlive.com]
 * @license   GPL-2.0+
 * @version   1.0.2
 * ----------------------------------------------------------------------------
 *
 * @since 2.0.0
 */
class System
{

    /**
     * Get system info.
     *
     * @since 2.0.0
     * @access public
     * @param  string $info
     * @param  bool   $json_output
     * @return array
     */
    public static function get_info($info = 'all', $json_output = false)
    {

        // save final info
        $final_info = array();

        // get all system info
        if (empty($info) || $info === 'all') {

            $final_info = array_merge(
                self::get_wp_info(),
                self::get_server_info(),
                self::get_plugins_info(),
                self::get_theme_info()
            );
        }

        // get wp info
        if ($info === 'wp') {
            $final_info = self::get_wp_info();
        }

        // get server info
        if ($info === 'server') {
            $final_info = self::get_server_info();
        }

        // get plugins info
        if ($info === 'plugins') {
            $final_info = self::get_plugins_info();
        }

        // get theme info
        if ($info === 'theme') {
            $final_info = self::get_theme_info();
        }

        // filter @hook get all system info.
        $final_info = apply_filters(
            Config::PREFIX . 'get_system_info', $final_info
        );

        // we need json output?
        if ($json_output) {
            return json_encode($final_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        // return final info
        return $final_info;

    }

    /**
     * Get WordPress environment info.
     *
     * @since 2.0.0
     * @access public
     * @param  string $type
     * @return array
     */
    public static function get_wp_info()
    {

        // save info
        $info = array();

        // get global core wp vars
        global $wpdb, $wp_version;

        // start collect info
        $info['home_URL']       = home_url();
        $info['site_URL']       = site_url();
        $info['WP_content_URL'] = WP_CONTENT_URL;
        $info['WP_version']     = $wp_version;
        $info['WP_multisite']   = is_multisite();

        $info['permalink_structure'] = get_option('permalink_structure') ? get_option('permalink_structure') : 'Default';

        $info['front_page_display'] = get_option('show_on_front');
        if ($info['front_page_display'] == 'page') {
            $front_page_id      = get_option('page_on_front');
            $blog_page_id       = get_option('page_for_posts');
            $info['front_page'] = $front_page_id != 0 ? get_the_title($front_page_id) . ' (#' . $front_page_id . ')' : 'Unset';
            $info['posts_page'] = $blog_page_id != 0 ? get_the_title($blog_page_id) . ' (#' . $blog_page_id . ')' : 'Unset';
        }

        $info['WP_memory_limit']['raw']  = self::string_to_number(WP_MEMORY_LIMIT);
        $info['WP_memory_limit']['size'] = size_format($info['WP_memory_limit']['raw']);

        $info['database_table_prefix'] = 'Length: ' . strlen($wpdb->prefix) . ' - Status: ' . (strlen($wpdb->prefix) > 16 ? 'ERROR: Too long' : 'Acceptable');

        $info['WP_debug_mode'] = 'false';
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $info['WP_debug_mode'] = 'true';
        }

        $info['WP_language'] = get_locale();

        // filter @hook get wp system info.
        return apply_filters(
            Config::PREFIX . 'get_system_info_wp', $info
        );

    }

    /**
     * Get server environment info.
     *
     * @since 2.0.0
     * @access public
     * @param  string $type
     * @return array
     */
    public static function get_server_info()
    {

        // save info
        $info = array();

        // get global core wp vars
        global $wpdb;

        // start collect info
        $info['server_info']           = esc_html($_SERVER['SERVER_SOFTWARE']);
        $info['localhost_environment'] = self::boolean_to_string(self::is_localhost());
        $info['php_version']           = function_exists('phpversion') ? esc_html(phpversion()) : 'phpversion() function does not exist.';
        $info['ABSPATH']               = ABSPATH;
        if (function_exists('ini_get')) {
            $info['php_memory_limit']   = size_format(self::string_to_number(ini_get('memory_limit')));
            $info['php_post_max_size']  = size_format(self::string_to_number(ini_get('post_max_size')));
            $info['php_time_limit']     = ini_get('max_execution_time');
            $info['php_max_input_vars'] = ini_get('max_input_vars');
            $info['php_display_errors'] = self::boolean_to_string(ini_get('display_errors'));
        }
        $info['suhosin_installed']       = extension_loaded('suhosin');
        $info['mysql_version']           = $wpdb->db_version();
        $info['max_upload_size']         = size_format(wp_max_upload_size());
        $info['default_timezone_is_utc'] = 'true';
        if (date_default_timezone_get() !== 'UTC') {
            $info['default_timezone_is_utc'] = 'false';
        }
        $info['fsockopen_curl'] = 'false';
        if (function_exists('fsockopen') || function_exists('curl_init')) {
            $info['fsockopen_curl'] = 'true';
        }

        // filter @hook get server system info.
        return apply_filters(
            Config::PREFIX . 'get_system_info_server', $info
        );

    }

    /**
     * Get WordPress active plugins info.
     *
     * @since 2.0.0
     * @access public
     * @param  string $type
     * @return array
     */
    public static function get_plugins_info()
    {

        // save info
        $info = array();

        // start collect info
        $active_plugins = (array) get_option('active_plugins', array());
        if (is_multisite()) {
            $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
        }
        $info['plugins'] = array();
        foreach ($active_plugins as $plugin) {
            $plugin_data                   = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
            $plugin_name                   = esc_html($plugin_data['Name']);
            $info['plugins'][$plugin_name] = $plugin_data;
        }

        // filter @hook get plugins system info.
        return apply_filters(
            Config::PREFIX . 'get_system_info_plugins', $info
        );

    }

    /**
     * Get WordPress active theme info.
     *
     * @since 2.0.0
     * @access public
     * @param  string $type
     * @return array
     */
    public static function get_theme_info()
    {

        // save info
        $info = array();

        // start collect info
        $active_theme           = wp_get_theme();
        $info['theme_name']     = $active_theme->Name;
        $info['theme_version']  = $active_theme->Version;
        $info['author_URL']     = $active_theme->{'Author URI'};
        $info['is_child_theme'] = self::boolean_to_string(is_child_theme());
        if (is_child_theme()) {
            $parent_theme              = wp_get_theme($active_theme->Template);
            $info['parent_name']       = $parent_theme->Name;
            $info['parent_version']    = $parent_theme->Version;
            $info['parent_author_URL'] = $parent_theme->{'Author URI'};
        }

        // filter @hook get theme system info.
        return apply_filters(
            Config::PREFIX . 'get_system_info_theme', $info
        );

    }

    /**
     * Convert string to number.
     *
     * @since 2.0.0
     * @access private
     * @param  string $size
     * @return int
     */
    private static function string_to_number($size)
    {

        $string = substr($size, -1);
        $ret    = substr($size, 0, -1);

        switch (strtoupper($string)) {
            case 'P':
                $ret *= 1024;
            case 'T':
                $ret *= 1024;
            case 'G':
                $ret *= 1024;
            case 'M':
                $ret *= 1024;
            case 'K':
                $ret *= 1024;
        }

        return $ret;

    }

    /**
     * Convert boolean value to string
     *
     * @since 2.0.0
     * @access public
     * @param  bolean $var
     * @return string
     */
    public static function boolean_to_string($var)
    {

        if ($var === false || $var === 'false' || $var === 0 || $var === '0' || $var === '' || empty($var)) {
            return 'false';
        } elseif ($var === true || $var === 'true' || $var === 1 || $var === '1') {
            return 'true';
        } else {
            return $var;
        }

    }

    /**
     * Check if is localhost.
     *
     * @since 2.0.0
     * @access public
     * @return string
     */
    public static function is_localhost()
    {
        return ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === 'localhost') ? 1 : 0;
    }

}
