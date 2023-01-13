<?php

namespace Codestar\Module;

/**
 * Save all framework data.
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
class Datastore
{

    /**
     * All containers data.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected static $containers = array(
        'settings'   => array(),
        'customizer' => array(),
        'metaboxes'  => array(),
        'shortcoder' => array(),
        'taxonomy'   => array(),
    );
    /**
     * All hooks data.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected static $hooks = array(
        'action' => array(),
        'filter' => array(),
    );

    /**
     * Save containers data.
     *
     * @since 2.0.0
     * @access public
     * @param  string $container         container type name
     * @param  string $unique_options_id unique options id (name)
     * @param  array  $data              container data
     */
    public static function save_container($container, $unique_options_id, $data)
    {

        // set container data
        self::$containers[$container][$unique_options_id] = $data;

    }

    /**
     * Get containers data.
     *
     * @since 2.0.0
     * @access public
     * @param  string $container         container type name
     * @param  string $unique_options_id unique options id (name)
     * @return array
     */
    public static function get_container($container = '', $unique_options_id = '')
    {

        // get data for exact unique options id (name) upon container
        if (
            isset(self::$containers[$container]) &&
            isset(self::$containers[$container][$unique_options_id])
        ) {

            return self::$containers[$container][$unique_options_id];

        }

        // get all data inside exact container
        if (isset(self::$containers[$container])) {
            return self::$containers[$container];
        }

        // get all containers data
        return self::$containers;

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

        // we have container data for options id? update it
        if (
            isset(self::$containers[$container]) &&
            isset(self::$containers[$container][$unique_options_id])
        ) {

            if (!empty($data)) {
                self::$containers[$container][$unique_options_id] = $data;
                return true;
            }

        }

        // something wrong?
        return false;

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

        // we have container data for options id? delete it
        if (
            isset(self::$containers[$container]) &&
            isset(self::$containers[$container][$unique_options_id])
        ) {

            // delete options id data
            unset(self::$containers[$container][$unique_options_id]);
            return true;

        }

        // something wrong?
        return false;

    }

    /**
     * Save hooks data.
     *
     * @since 2.0.0
     * @access public
     * @param  string $hook hook type (action) or (filter)
     * @param  string $name hook name
     * @param  array  $data hook name data
     */
    public static function save_hook($hook, $name, $data)
    {

        // set container data
        self::$hooks[$hook][$name] = $data;

    }

    /**
     * Get hooks data.
     *
     * @since 2.0.0
     * @access public
     * @param  string $hook hook type (action) or (filter)
     * @param  string $name hook name
     * @return array
     */
    public static function get_hook($hook = '', $name = '')
    {

        // get data for exact hook name
        if (isset(self::$hooks[$hook]) && isset(self::$hooks[$hook][$name])) {
            return self::$hooks[$hook][$name];
        }

        // get all data inside exact hook
        if (isset(self::$hooks[$hook])) {
            return self::$hooks[$hook];
        }

        // get all hooks data
        return self::$hooks;

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

        // we have hook data for name? update it
        if (
            isset(self::$hooks[$hook]) &&
            isset(self::$hooks[$hook][$name])
        ) {

            if (!empty($data)) {
                self::$hooks[$hook][$name] = $data;
                return true;
            }

        }

        // something wrong?
        return false;

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

        // we have hook data for name? delete it
        if (
            isset(self::$hooks[$hook]) &&
            isset(self::$hooks[$hook][$name])
        ) {

            // delete hook name data
            unset(self::$hooks[$hook][$name]);
            return true;

        }

        // something wrong?
        return false;

    }

}
