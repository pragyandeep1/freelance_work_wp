<?php

namespace Codestar;

use Codestar\Config;
use Codestar\Module\Datastore;
use Codestar\Module\Init;

/**
 * Codestar framework manager class.
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
final class Codestar
{

    /**
     * Check if Codestar has been booted
     *
     * @since 2.0.0
     * @access protected
     * @var boolean
     */
    protected static $booted = false;
    /**
     * Container options name.
     *
     * default container: 'settings_options' or 'theme_options' or 'plugin_options'
     * other containers:
     * 'customizer_options' - 'metaboxes_options' - 'shortcoder_options' - 'taxonomy_options'
     *
     * @since 2.0.0
     * @access protected
     * @var string
     */
    protected $container;
    /**
     * Unique options id (name).
     *
     * @since 2.0.0
     * @access protected
     * @var string
     */
    protected $unique_options_id;
    /**
     * Get main page arguments.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $page;
    /**
     * Get sub page arguments.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $sub_page;
    /**
     * Menu action hook.
     *
     * default: 'admin_menu', and for network use 'network_admin_menu'
     *
     * @since 2.0.0
     * @access protected
     * @var string
     */
    protected $menu_hook;
    /**
     * Save all menu settings.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $menu_settings;
    /**
     * Config settings for container options.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $settings;
    /**
     * Available options for container.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $options;
    /**
     * Save all enqueue settings.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $enqueue_settings;

    /**
     * Set container options name and settings.
     *
     * @since 2.0.0
     * @access public
     * @param  string $container container options name
     */
    public function __construct($container)
    {

        // boot Codestar common functionality just onetime
        self::boot();

        // set container options name
        $this->container = $container;

        // set unique options id (name).
        $this->unique_options_id = '';

        // set default menu action hook
        $this->menu_settings = array('menu_hook' => 'admin_menu');

        // set default settings
        $this->settings = array();

        // set default options
        $this->options = array();

        // set default enqueue settings
        $this->enqueue_settings = array('theme_skin' => '');

    }

    /**
     * Boot Codestar with common and default functionality.
     *
     * @since 2.0.0
     * @access public
     */
    public static function boot()
    {

        // action @hook Make anything before load framework.
        do_action(Config::PREFIX . 'before_loaded');

        // Codestar already booted? go back
        if (self::is_booted()) {

            // action @hook Make anything after load framework.
            do_action(Config::PREFIX . 'after_loaded');

            return;

        }

        // run and load initialization
        Init::run();

        // boot Codestar first time
        self::$booted = true;

        // action @hook Make sure framework is loaded.
        do_action(Config::PREFIX . 'is_loaded');

    }

    /**
     * Check if Codestar has been booted.
     *
     * @since 2.0.0
     * @access public
     * @return boolean
     */
    public static function is_booted()
    {
        return self::$booted;
    }

    /**
     * Make options for exact container.
     *
     * @since 2.0.0
     * @access public
     * @param  string $container container options name
     * @return self
     */
    public static function make($container = 'settings_options')
    {
        return new self($container);
    }

    /**
     * Set unique options id (name).
     *
     * this is most unique name that gets used as the option name.
     *
     * @since 2.0.0
     * @access public
     * @param string $unique_options_id unique options id (name)
     * @return self
     */
    public function set_options_id($unique_options_id)
    {

        // return the object if something wrong
        if (empty($unique_options_id) || !is_string($unique_options_id)) {
            return $this;
        }

        // set unique options id (name)
        $this->unique_options_id = $unique_options_id;

        // return object
        return $this;

    }

    /**
     * Set menu action hook.
     *
     * default: 'admin_menu', and for network use 'network_admin_menu'
     *
     * @since 2.0.0
     * @access public
     * @param string $hook menu action hook
     * @return self
     */
    public function for_menu($menu_hook = 'admin_menu')
    {

        // return the object if something wrong
        if (empty($menu_hook)) {
            return $this;
        }

        // set menu action hook
        $this->menu_hook = $menu_hook;

        // save menu action hook in menu settings
        $this->menu_settings['menu_hook'] = $this->menu_hook;

        // return object
        return $this;

    }

    /**
     * Add main menu page.
     *
     * @since 2.0.0
     * @access public
     * @param array $page
     * @return self
     */
    public function add_page($page = array())
    {

        // return the object if something wrong
        if (empty($page)) {
            return $this;
        }

        // set page arguments
        $this->page = $page;

        // save page arguments in menu settings
        $this->menu_settings['page'] = $this->page;

        // save page slug in enqueue settings
        if (isset($this->page['menu_slug'])) {
            $this->enqueue_settings['page_slug'] = $this->page['menu_slug'];
        }

        // return object
        return $this;

    }

    /**
     * Add sub menu page.
     *
     * @since 2.0.0
     * @access public
     * @param array $sub_page
     * @return self
     */
    public function add_sub_page($sub_page = array())
    {

        // return the object if something wrong
        if (empty($sub_page)) {
            return $this;
        }

        // set sub page arguments
        $this->sub_page = $sub_page;

        // save sub page arguments in menu settings
        $this->menu_settings['sub_page'] = $this->sub_page;

        // save sub page slug in enqueue settings
        if (isset($this->sub_page['menu_slug'])) {
            $this->enqueue_settings['page_slug'] = $this->sub_page['menu_slug'];
        }

        // return object
        return $this;

    }

    /**
     * Setup container config.
     *
     * @since 2.0.0
     * @access public
     * @param array $settings
     * @return self
     */
    public function set_config($settings = array())
    {

        // return the object if something wrong
        if (empty($settings)) {
            return $this;
        }

        // set container settings
        if (!empty($settings)) {
            $this->settings = $settings;
        }

        // save settings theme skin in enqueue settings
        if (isset($this->settings['theme_skin'])) {
            $this->enqueue_settings['theme_skin'] = $this->settings['theme_skin'];
        }

        // return object
        return $this;

    }

    /**
     * Add container options.
     *
     * @since 2.0.0
     * @access public
     * @param array $options
     */
    public function add_options($options = array())
    {

        // set container options
        if (!empty($options)) {
            $this->options = $options;
        }

        // get container class name from $this->container name
        switch ($this->container) {

            case 'taxonomy_options':

                // save container class OOP
                $class = '\Codestar\Container\Taxonomy';

                if (class_exists($class)) {

                    // save container data
                    $this->save_data('taxonomy');

                    // get default available options
                    if ($this->options === 'all') {
                        $this->options = self::get_default_options('taxonomy');
                    }

                    // create new container
                    $container = new $class(
                        $this->unique_options_id, $this->settings, $this->options
                    );

                    // enqueue admin scripts
                    $container->enqueue($this->enqueue_settings);

                    // actually register this container
                    $container->register();

                }

                break;

            case 'shortcoder_options':

                // save container class OOP
                $class = '\Codestar\Container\Shortcoder';

                if (class_exists($class)) {

                    // save container data
                    $this->save_data('shortcoder');

                    // get default available options
                    if ($this->options === 'all') {
                        $this->options = self::get_default_options('shortcoder');
                    }

                    // create new container
                    $container = new $class(
                        $this->unique_options_id, $this->settings, $this->options
                    );

                    // enqueue admin scripts
                    $container->enqueue($this->enqueue_settings);

                    // actually register this container
                    $container->register();

                }

                break;

            case 'metaboxes_options':

                // save container class OOP
                $class = '\Codestar\Container\Metaboxes';

                if (class_exists($class)) {

                    // save container data
                    $this->save_data('metaboxes');

                    // get default normal available options
                    if ($this->options === 'all_normal') {
                        $this->options = self::get_default_options('metaboxes');
                    }

                    // get default side available options
                    if ($this->options === 'all_side') {
                        $this->options = self::get_default_options('metaboxes', 'side');
                    }

                    // create new container
                    $container = new $class(
                        $this->unique_options_id, $this->settings, $this->options
                    );

                    // enqueue admin scripts
                    $container->enqueue($this->enqueue_settings);

                    // actually register this container
                    $container->register();

                }

                break;

            case 'customizer_options':

                // save container class OOP
                $class = '\Codestar\Container\Customizer';

                if (class_exists($class)) {

                    // save container data
                    $this->save_data('customizer');

                    // get default available options
                    if ($this->options === 'all') {
                        $this->options = self::get_default_options('customizer');
                    }

                    // create new container
                    $container = new $class($this->unique_options_id, $this->options);

                    // enqueue admin scripts
                    $container->enqueue($this->enqueue_settings);

                    // actually register this container
                    $container->register();

                }

                break;

            default:

                // save container class OOP
                $class = '\Codestar\Container\Settings';

                if (class_exists($class)) {

                    // save container data
                    $this->save_data('settings');

                    // get default available options
                    if ($this->options === 'all') {
                        $this->options = self::get_default_options('settings');
                    }

                    // create new container
                    $container = new $class(
                        $this->unique_options_id, $this->menu_settings, $this->settings, $this->options
                    );

                    // enqueue admin scripts
                    $container->enqueue($this->enqueue_settings);

                    // actually register this container
                    $container->register();

                }

                break;

        }

    }

    /**
     * Save container data.
     *
     * @since 2.0.0
     * @access protected
     * @param  string $container_type
     */
    protected function save_data($container_type)
    {

        // save our container data
        $data = array();

        // set container menu settings
        $data['menu_settings'] = $this->menu_settings;

        // set container settings
        $data['settings'] = $this->settings;

        // set container options
        $data['options'] = $this->options;

        // now save container data
        Datastore::save_container($container_type, $this->unique_options_id, $data);

    }

    /**
     * Get default container options.
     *
     * @since 2.0.0
     * @access protected
     * @param  string $container_type
     * @param  string $context
     * @return array
     */
    protected function get_default_options($container_type, $context = 'normal')
    {
        return Config::get_all_default_options($container_type, $context);
    }

}
