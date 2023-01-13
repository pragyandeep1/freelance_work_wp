<?php

namespace JO\ShortcodeCleaner\Core;

use JO\ShortcodeCleaner\Core\Compatiblity;

/**
 * The code that runs during plugin activation.
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
class Activate
{

    /**
     * Run this actions on plugin activation
     *
     * @since 1.0.0
     * @access public
     */
    public static function run()
    {

        // check compatiblity
        Compatiblity::check();

        /**
         * Deactivate pro plugin if exists
         *
         * @since 1.0.2
         */
        if (is_plugin_active('shortcode-cleaner/shortcode-cleaner.php')) {
            add_action('update_option_active_plugins', array(__CLASS__, 'deactivate_pro_plugin'));
        }

    }

    /**
     * Deactivate pro plugin if exists
     *
     * @since 1.0.2
     * @access public
     */
    public static function deactivate_pro_plugin()
    {
        deactivate_plugins('shortcode-cleaner/shortcode-cleaner.php');
    }

}
