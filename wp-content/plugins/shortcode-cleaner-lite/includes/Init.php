<?php

namespace JO\ShortcodeCleaner;

use JO\Module\PluginActionLinks\Links;
use JO\ShortcodeCleaner\Core\Compatiblity;
use JO\ShortcodeCleaner\Core\Data;
use JO\ShortcodeCleaner\Module\Content;
use JO\ShortcodeCleaner\Module\Settings;

/**
 * The file that initialize all the core classes of the plugin.
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
class Init
{

    /**
     * The unique instance of the plugin.
     *
     * @since 1.0.0
     * @access protected
     * @var Init
     */
    protected static $instance;

    /**
     * Maybe for set any data for the plugin
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct()
    {
        /**
         * @todo maybe set default @settings..
         */
    }

    /**
     * Gets an instance of the plugin.
     *
     * @since 1.0.0
     * @access public
     * @return Init
     */
    public static function get_instance()
    {

        // get new instance of the class
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;

    }

    /**
     * Run and register our plugin with WordPress.
     *
     * @since 1.0.0
     * @access public
     */
    public static function run()
    {

        // if files is called directly, abort.
        if (!defined('ABSPATH')) {
            throw new \Exception(Data::PLUGIN_NAME . ' cannot be used outside of a WordPress environment.');
        }

        // load the plugin textdomain
        load_plugin_textdomain(
            'shortcode-cleaner',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );

        /**
         * Check compatiblity and stop the plugin if it incompatible.
         * @since 1.0.3
         */
        Compatiblity::check();
        if (Compatiblity::incompatible()) {
            return;
        }

        // run shortcode cleaner
        add_action('wp_loaded', array(Init::get_instance(), 'run_cleaner'));

    }

    /**
     * Init shortcode cleaner.
     *
     * @since 1.0.0
     * @access public
     */
    public static function run_cleaner()
    {

        /**
         * Register plugin settings page.
         */
        Settings::register();

        /**
         * Add a plugin links beside the activate and deactivate links
         */
        $add_plugin_links = new Links(Data::get_plugin_dir_file_name());
        $add_plugin_links->add(array(
            array(
                'dashboard' => '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=' . Data::PLUGIN_MENU_PAGE_SLUG)) . '">' . __('Dashboard', 'shortcode-cleaner') . '</a>',
            ),
            array(
                'go-pro' => '<a href="https://1.envato.market/a7d2Z" target="_blank" style="color:#39b54a;font-weight:700;">' . __('Go Pro', 'shortcode-cleaner') . '</a>',
            ),
        ));

        /**
         * Clean frontend content.
         */

        // get available frontend WP content filters from settings
        $frontend_filters_from_settings = cs_get_option(Data::get_unique_settings_options_id(), 'set_frontend_content_filters');

        // save frontend filters array
        $available_frontend_filters_from_settings = array();

        // we have filters? handle filters array
        if (!empty($frontend_filters_from_settings)) {

            foreach ($frontend_filters_from_settings as $filter) {

                $frontend_default_args = 1;

                if ($filter === 'the_title' || $filter === 'wp_nav_menu_items') {
                    $frontend_default_args = 2;
                }

                if ($filter === 'widget_title') {
                    $frontend_default_args = 3;
                }

                $available_frontend_filters_from_settings[] = array(
                    'filter' => $filter, 'priority' => 10, 'args' => $frontend_default_args,
                );

            } // end foreach $frontend_filters_from_settings

        } // end check $frontend_filters_from_settings

        // filter @hook set available frontend WP content filters
        $frontend_filters = apply_filters(
            Data::PLUGIN_PREFIX . 'set_frontend_content_filters',
            $available_frontend_filters_from_settings
        );

        // check enable clean frontend content option from settings
        $check_enable_clean_frontend_content = false;
        if (cs_get_option(Data::get_unique_settings_options_id(), 'enable_clean_frontend_content')) {
            $check_enable_clean_frontend_content = true;
        }

        // filter @hook check if enable clean frontend content
        $enable_clean_frontend_content = apply_filters(
            Data::PLUGIN_PREFIX . 'enable_clean_frontend_content',
            $check_enable_clean_frontend_content
        );

        // run content filters
        if (
            $enable_clean_frontend_content &&
            is_array($frontend_filters) &&
            !empty($frontend_filters)
        ) {

            Content::filter_frontend($frontend_filters);

        }

    }

}
