<?php

use Codestar\Module\API;

/**
 * Set Codestar framework API normal functions.
 *
 * Note: you can change framework api functions prefix 'cs_' by change functions name below.
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

/**
 * Get one option name or all options from database.
 *
 * example:
 * get all options: cs_get_option('unique_options_id');
 * get one option: cs_get_option('unique_options_id', 'option_name');
 *
 * @since 2.0.0
 * @param  string $unique_options_id unique options id (name)
 * @param  string $option_name       option name inside unique options id
 * @param  string $default           default option value
 * @return mixed
 */
if (!function_exists('cs_get_option')) {

    function cs_get_option($unique_options_id = '', $option_name = '', $default = '')
    {
        return API::get_option($unique_options_id, $option_name, $default);
    }

}

/**
 * Get multi language option.
 *
 * example: cs_get_multilang_option('unique_options_id', 'option_name');
 *
 * @since 2.0.0
 * @access public
 * @param  string $unique_options_id unique options id (name)
 * @param  string $option_name       option name inside unique options id
 * @param  string $default           default option value
 * @return mixed
 */
if (!function_exists('cs_get_multilang_option')) {

    function cs_get_multilang_option($unique_options_id = '', $option_name = '', $default = '')
    {
        return API::get_multilang_option($unique_options_id, $option_name, $default);
    }

}

/**
 * Get multi language value.
 *
 * example: cs_get_multilang_value('value', 'default');
 *
 * @since 2.0.0
 * @access public
 * @param  string $value
 * @param  string $default
 * @return mixed
 */
if (!function_exists('cs_get_multilang_value')) {

    function cs_get_multilang_value($value = '', $default = '')
    {
        return API::get_multilang_value($value, $default);
    }

}

/**
 * Set option id (name) value.
 *
 * example: cs_set_option('unique_options_id', 'option_name', 'new_value');
 *
 * @since 2.0.0
 * @access public
 * @param  string $unique_options_id unique options id (name)
 * @param  string $option_name       option name inside unique options id
 * @param  string $new_value         new option value
 * @return mixed
 */
if (!function_exists('cs_set_option')) {

    function cs_set_option($unique_options_id = '', $option_name = '', $new_value = '')
    {
        API::set_option($unique_options_id, $option_name, $new_value);
    }

}

/**
 * Get containers data.
 *
 * example:
 * get all containers: self::get_container('container_type');
 * get one container: self::get_container('container_type', 'unique_options_id');
 *
 * @since 2.0.0
 * @access public
 * @param  string $container         container type name
 * @param  string $unique_options_id unique options id (name)
 * @return array
 */
if (!function_exists('cs_get_container')) {

    function cs_get_container($container = '', $unique_options_id = '')
    {
        return API::get_container($container, $unique_options_id);
    }

}

/**
 * Update containers data.
 *
 * @since 2.0.0
 * @access public
 * @param  string $container         container type name
 * @param  string $unique_options_id unique options id (name)
 * @param  array  $data              container data
 */
if (!function_exists('cs_update_container')) {

    function cs_update_container($container = '', $unique_options_id = '', $data = array())
    {
        API::update_container($container, $unique_options_id, $data);
    }

}

/**
 * Delete containers data.
 *
 * @since 2.0.0
 * @access public
 * @param  string $container         container type name
 * @param  string $unique_options_id unique options id (name)
 */
if (!function_exists('cs_delete_container')) {

    function cs_delete_container($container = '', $unique_options_id = '')
    {
        API::delete_container($container, $unique_options_id);
    }

}

/**
 * Get hooks data.
 *
 * example:
 * get all hooks: self::get_container('hook_type');
 * get one hook: self::get_container('hook_type', 'hook_name');
 *
 * @since 2.0.0
 * @access public
 * @param  string $hook hook type (action) or (filter)
 * @param  string $name hook name
 * @return array
 */
if (!function_exists('cs_get_hook')) {

    function cs_get_hook($hook = '', $name = '')
    {
        return API::get_hook($hook, $name);
    }

}

/**
 * Update containers data.
 *
 * @since 2.0.0
 * @access public
 * @param  string $hook hook type (action) or (filter)
 * @param  string $name hook name
 * @param  array  $data hook name data
 */
if (!function_exists('cs_update_hook')) {

    function cs_update_hook($hook = '', $name = '', $data = array())
    {
        API::update_hook($hook, $name, $data);
    }

}

/**
 * Delete hooks data.
 *
 * @since 2.0.0
 * @access public
 * @param  string $hook hook type (action) or (filter)
 * @param  string $name hook name
 */
if (!function_exists('cs_delete_hook')) {

    function cs_delete_hook($hook = '', $name = '')
    {
        API::delete_hook($hook, $name);
    }

}
