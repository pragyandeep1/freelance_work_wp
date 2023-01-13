<?php

namespace Codestar\Module;

/**
 * Create admin menu and submenu pages.
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
class Menu
{

    /**
     * Get menu action hook.
     *
     * @since 2.0.0
     * @access protected
     * @var string
     */
    protected $hook;
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
     * Get dashboard menus.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $menus;

    /**
     * Set menu action hook.
     * default: 'admin_menu' for network use 'network_admin_menu'
     *
     * @since 2.0.0
     * @access public
     * @param string $hook menu action hook
     */
    public function __construct($hook = 'admin_menu')
    {
        $this->hook = $hook;
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
        if (empty($page) || !is_array($page)) {
            return $this;
        }

        // set page arguments
        $this->page = array_merge(
            array(
                'capability' => 'manage_options',
                'function'   => '',
                'icon_url'   => '',
                'position'   => 101,
            ),
            $page
        );

        // rerurn the object
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
        if (empty($sub_page) || !is_array($sub_page)) {
            return $this;
        }

        // set sub page arguments
        $this->sub_page = array_merge(
            array(
                'capability' => 'manage_options',
            ),
            $sub_page
        );

        // rerurn the object
        return $this;

    }

    /**
     * Register all new menu pages and sub pages.
     *
     * @since 2.0.0
     * @access public
     */
    public function register()
    {
        $this->add_action('register_menu_pages');
    }

    /**
     * Add menu pages and sub pages.
     *
     * @since 2.0.0
     * @access public
     */
    public function register_menu_pages()
    {

        /**
         * Add menu page.
         */

        // now add the page
        if (!empty($this->page)) {

            add_menu_page(
                $this->page['page_title'],
                $this->page['menu_title'],
                $this->page['capability'],
                $this->page['menu_slug'],
                $this->page['function'],
                $this->page['icon_url'],
                $this->set_position($this->page['position'])
            );

        }

        /**
         * Add menu sub page.
         */

        // empty sub page? go back
        if (empty($this->sub_page)) {
            return;
        }

        // now add sub page
        add_submenu_page(
            $this->get_menu_slug($this->sub_page['parent_slug']),
            $this->sub_page['page_title'],
            $this->sub_page['menu_title'],
            $this->sub_page['capability'],
            $this->sub_page['menu_slug'],
            $this->sub_page['function']
        );

    }

    /**
     * Set menu position.
     *
     * @since 2.0.0
     * @access protected
     * @param string/int $position menu position
     * @return int menu position
     */
    protected function set_position($position)
    {

        // menu position is integer number? use it
        if (is_int($position) || is_float($position)) {
            return $position;
        }

        /**
         * menu position is string with exact menu name? set correct position
         */
        global $menu;

        // get exact menu from main $menu array
        foreach ($menu as $menu_position => $data) {

            // get match menu from main $menu array by menu title or menu slug
            // if match? move our menu under this match menu
            if ($this->handle_title($data[0]) == $position || $data[2] == $position) {

                // here WP will make sure we don't have conflict between menus
                return $menu_position;

            }

        }

    }

    /**
     * Get menu slug from menu title
     *
     * @since 2.0.0
     * @access protected
     * @param  string $title menu title
     * @return string menu slug
     */
    protected function get_menu_slug($title)
    {

        // get current dashboard menus
        global $menu;

        // get exact menu from main $menu array
        foreach ($menu as $menu_position => $data) {

            // get match menu from main $menu array by menu title
            // if match? use this menu slug
            if ($this->handle_title($data[0]) == $title) {

                // here we return this menu slug
                return $data[2];

            }

        }

        // here we return normal direct menu slug
        return $title;

    }

    /**
     * Handle menu and sub menu title.
     * here we sanitize html codes and remove notifications numbers
     * behind menu title, like Dashboard, Plugins, Updates ..etc,
     * so make sure we get title name enough
     *
     * @param  string $title menu or sub menu title
     * @return string correct title
     */
    protected function handle_title($title)
    {

        // remove any html codes
        $title = sanitize_text_field($title);

        // remove any number behind title and space after removed number
        $title = sanitize_text_field(preg_replace('/\d/', '', $title));

        // now return clean title
        return $title;

    }

    /**
     * Add action per method.
     *
     * @since 2.0.0
     * @access protected
     * @param string $action action function name
     */
    protected function add_action($action)
    {
        add_action($this->hook, array($this, $action));
    }

}
