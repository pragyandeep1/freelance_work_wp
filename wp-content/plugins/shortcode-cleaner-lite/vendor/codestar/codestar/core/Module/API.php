<?php

namespace Codestar\Module;

use Codestar\Module\Datastore;
use Codestar\Options\Options;

/**
 * Codestar framework API.
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
class API
{

    /**
     * Get one option name or all options from database.
     *
     * example:
     * get all options: self::get_option('unique_options_id');
     * get one option: self::get_option('unique_options_id', 'option_name');
     *
     * @since 2.0.0
     * @access public
     * @param  string $unique_options_id unique options id (name)
     * @param  string $option_name       option name inside unique options id
     * @param  string $default           default option value
     * @return mixed
     */
    public static function get_option(
        $unique_options_id = '', $option_name = '', $default = ''
    ) {
        return Options::get_option($unique_options_id, $option_name, $default);
    }

    /**
     * Get multi language option.
     *
     * example: self::get_multilang_option('unique_options_id', 'option_name');
     *
     * @since 2.0.0
     * @access public
     * @param  string $unique_options_id unique options id (name)
     * @param  string $option_name       option name inside unique options id
     * @param  string $default           default option value
     * @return mixed
     */
    public static function get_multilang_option(
        $unique_options_id = '', $option_name = '', $default = ''
    ) {
        return Options::get_multilang_option($unique_options_id, $option_name, $default);
    }

    /**
     * Get multi language value.
     *
     * example: self::get_multilang_value('value', 'default');
     *
     * @since 2.0.0
     * @access public
     * @param  string $value
     * @param  string $default
     * @return mixed
     */
    public static function get_multilang_value($value = '', $default = '')
    {
        return Options::get_multilang_value($value, $default);
    }

    /**
     * Set option id (name) value.
     *
     * example: self::set_option('unique_options_id', 'option_name', 'new_value');
     *
     * @since 2.0.0
     * @access public
     * @param  string $unique_options_id unique options id (name)
     * @param  string $option_name       option name inside unique options id
     * @param  string $new_value         new option value
     * @return mixed
     */
    public static function set_option(
        $unique_options_id = '', $option_name = '', $new_value = ''
    ) {
        Options::set_option($unique_options_id, $option_name, $new_value);
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
    public static function get_container($container = '', $unique_options_id = '')
    {
        return Datastore::get_container($container, $unique_options_id);
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
    public static function update_container(
        $container = '', $unique_options_id = '', $data = array()
    ) {
        Datastore::update_container($container, $unique_options_id, $data);
    }

    /**
     * Delete containers data.
     *
     * @since 2.0.0
     * @access public
     * @param  string $container         container type name
     * @param  string $unique_options_id unique options id (name)
     */
    public static function delete_container($container = '', $unique_options_id = '')
    {
        Datastore::delete_container($container, $unique_options_id);
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
    public static function get_hook($hook = '', $name = '')
    {
        return Datastore::get_hook($hook, $name);
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
    public static function update_hook($hook = '', $name = '', $data = array())
    {
        Datastore::update_hook($hook, $name, $data);
    }

    /**
     * Delete hooks data.
     *
     * @since 2.0.0
     * @access public
     * @param  string $hook hook type (action) or (filter)
     * @param  string $name hook name
     */
    public static function delete_hook($hook = '', $name = '')
    {
        Datastore::delete_hook($hook, $name);
    }

}
