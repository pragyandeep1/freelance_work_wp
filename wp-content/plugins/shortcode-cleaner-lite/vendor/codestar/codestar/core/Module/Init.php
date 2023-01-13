<?php

namespace Codestar\Module;

use Codestar\Config;
use Codestar\Helper\Helper;
use Codestar\Helper\Sanitize;
use Codestar\Helper\Validate;
use Codestar\Module\Data;
use Codestar\Module\Export;
use Codestar\Module\Hooks;

/**
 * Load and initialize common and default Codestar functionality.
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
class Init
{

    /**
     * Run and load any common and default functionality.
     *
     * @since 2.0.0
     * @access public
     */
    public static function run()
    {

        // if files is called directly, abort.
        if (!defined('ABSPATH')) {
            throw new \Exception(Config::NAME . ' cannot be used outside of a WordPress environment.');
        }

        /**
         * Include normal API functions
         * Note: any initialize files should included here.
         */
        require_once Config::get_dir() . '/core/functions.php';

        /**
         * Load textdomain
         * Note: we used 'after_setup_theme' here because it loaded before other hooks.
         */
        // add_action('after_setup_theme', array(__CLASS__, 'load_textdomain'), 9999);

        // save all available hooks data.
        Hooks::save_data();

        // add admin color filters
        Helper::add_admin_color_filters();

        // add wp_ajax action when exported options backup loaded
        Export::options_backup();

        // add wp_ajax action when get icons data loaded
        Data::get_icons();

        // add sanitize filters
        Sanitize::add_filters();

        // add validate filters
        Validate::add_filters();

    }

    /**
     * Load the framework textdomain.
     *
     * @since 2.0.0
     * @access public
     */
    public static function load_textdomain()
    {

        // get main dir
        $dir = Config::get_dir() . '/' . Config::LANGUAGES . '/';

        // load textdomain file
        load_textdomain('cs-framework', $dir . 'cs-framework-' . get_locale() . '.mo');

    }

}
