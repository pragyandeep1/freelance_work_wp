<?php

namespace Codestar\Helper;

/**
 * Helper functions class.
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
class Helper
{

    /**
     * Current admin color scheme name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    public static $current_admin_color_scheme = '';
    /**
     * Current all available admin colors from scheme name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    public static $current_admin_scheme_colors = '';
    /**
     * All available admin color schemes.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    public static $all_admin_color_schemes = '';

    /**
     * Encode string for backup options.
     *
     * @since 2.0.0
     * @access public
     * @param  string $string
     * @return string
     */
    public static function encode_string($string)
    {
        return serialize($string);
    }

    /**
     * Decode string for backup options.
     *
     * @since 2.0.0
     * @access public
     * @param  string $string
     * @return string
     */
    public static function decode_string($string)
    {
        return unserialize($string);
    }

    /**
     * Array search key & value.
     *
     * @since 2.0.0
     * @access public
     * @param  array  $array
     * @param  string $key
     * @param  mixed  $value
     * @return array
     */
    public static function array_search($array, $key, $value)
    {

        // save results
        $results = array();

        // we have array?
        if (is_array($array)) {

            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $sub_array) {
                $results = array_merge($results, self::array_search($sub_array, $key, $value));
            }

        }

        return $results;

    }

    /**
     * Get POST Var.
     *
     * @since 2.0.0
     * @access public
     * @param  string $var
     * @param  string $default
     * @return string
     */
    public static function get_var($var, $default = '')
    {

        if (isset($_POST[$var])) {
            return $_POST[$var];
        }

        if (isset($_GET[$var])) {
            return $_GET[$var];
        }

        return $default;

    }

    /**
     * Get POST Vars
     *
     * @since 2.0.0
     * @access public
     * @param  string $var
     * @param  string $depth
     * @param  string $default
     * @return string
     */
    public static function get_vars($var, $depth, $default = '')
    {

        if (isset($_POST[$var][$depth])) {
            return $_POST[$var][$depth];
        }

        if (isset($_GET[$var][$depth])) {
            return $_GET[$var][$depth];
        }

        return $default;

    }

    /**
     * Get the public url of a directory inside WordPress.
     *
     * @source Carbon_fields package > carbon-fields/blob/master/core/Carbon_Fields.php#L227
     *
     * @since 2.0.0
     * @access public
     * @param  string $directory
     * @return string
     */
    public static function directory_to_url($directory)
    {

        $url   = \trailingslashit($directory);
        $count = 0;

        # Sanitize directory separator on Windows
        $url = str_replace('\\', '/', $url);

        $possible_locations = array(
            WP_PLUGIN_DIR  => \plugins_url(), # If installed as a plugin
            WP_CONTENT_DIR => \content_url(), # If anywhere in wp-content
            ABSPATH        => \site_url('/'), # If anywhere else within the WordPress installation
        );

        foreach ($possible_locations as $test_dir => $test_url) {
            $test_dir_normalized = str_replace('\\', '/', $test_dir);
            $url                 = str_replace($test_dir_normalized, $test_url, $url, $count);
            if ($count > 0) {
                return \untrailingslashit($url);
            }
        }

        return ''; // return empty string to avoid exposing half-parsed paths

    }

    /**
     * Get page slug form current hook.
     *
     * @since 2.0.0
     * @access public
     * @param  string $hook current page hook
     * @return string       page slug
     */
    public static function get_page_slug_hook($hook)
    {

        // get hook array > 'dashboard_page_{my_new_page_slug}'
        $page_slug = explode('_', $hook);

        // remove 2 first items from hook
        unset($page_slug[0]); // dashboard
        unset($page_slug[1]); // page

        // back cleaned hook array to string with page slug
        $page_slug = implode('_', $page_slug);

        return $page_slug;

    }

    /**
     * Get field type class name OOP.
     *
     * Handle our class field name from field type.
     * example: text > Text, color_picker > ColorPicker ..etc
     *
     * @since 2.0.0
     * @access public
     * @param  string $field_type field type name
     * @return string             field class name
     */
    public static function get_field_type_class($field_type)
    {

        /**
         * Handle our class field name from field type.
         * example: text > Text, color_picker > ColorPicker ..etc
         */
        $field_class_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $field_type)));

        return $field_class_name;

    }

    /**
     * Handle each system info name.
     *
     * example: home_url > Home Url ..etc
     *
     * @since 2.0.0
     * @access public
     * @param  string $info_name
     * @return string
     */
    public static function handle_string_name($info_name)
    {
        return ucwords(str_replace('_', ' ', $info_name));
    }

    /**
     * Add admin color scheme filters.
     *
     * @since 2.0.0
     * @access public
     */
    public static function add_admin_color_filters()
    {

        // set the default admin color scheme for WordPress user
        add_filter(
            'get_user_option_admin_color', array(__CLASS__, 'set_default_admin_color')
        );

        // get the current and all admin color scheme from WordPress user
        add_filter(
            'get_user_option_admin_color', array(__CLASS__, 'get_current_admin_colors')
        );

    }

    /**
     * Set the default admin color scheme for WordPress user.
     *
     * @since 2.0.0
     * @access public
     */
    public static function set_default_admin_color($result)
    {

        /**
         * Set new default admin color scheme.
         * Note: i don't need to change default color, maybe use this method later.
         * @todo maybe add filter @hook here later
         */
        // $result = 'midnight';

        // return the new default color
        return $result;

    }

    /**
     * Get the current and all admin color scheme from WordPress user.
     *
     * @since 2.0.0
     * @access public
     */
    public static function get_current_admin_colors($result)
    {

        global $_wp_admin_css_colors;

        // get current admin color scheme name
        $current_color_scheme = $result;

        // get all available colors from scheme name
        $current_colors = $_wp_admin_css_colors[$current_color_scheme];

        // get all available color schemes
        $all_colors = $_wp_admin_css_colors;

        // save admin color data
        self::$current_admin_color_scheme  = $current_color_scheme;
        self::$current_admin_scheme_colors = $current_colors;
        self::$all_admin_color_schemes     = $all_colors;

        // important: we should return the default color scheme
        return $result;

    }

}
