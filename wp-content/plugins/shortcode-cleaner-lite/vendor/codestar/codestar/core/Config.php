<?php

namespace Codestar;

use Codestar\Helper\Helper;

/**
 * Codestar framework config data info.
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
class Config
{

    /**
     * Framework name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const NAME = 'Codestar Framework';
    /**
     * Framework author copyright.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const AUTHOR = 'Codestar';
    /**
     * Framework version.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const VERSION = '2.0.0';
    /**
     * Framework prefix.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const PREFIX = 'cs_';
    /**
     * Framework slug.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const SLUG = 'cs-';
    /**
     * Framework default option database/data name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const OPTION = '_cs_options';
    /**
     * Framework default customizer option name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const CUSTOMIZE = '_cs_customize_options';
    /**
     * Framework default metaboxes option name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const METABOX = '_cs_metabox_options';
    /**
     * Framework default shortcoder option name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const SHORTCODE = 'cs-shortcode-options';
    /**
     * Framework default taxonomy option name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const TAXONOMY = '_cs_taxonomy_options';
    /**
     * Framework settings container name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const SETTINGS = 'settings';
    /**
     * Framework customizer container name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const CUSTOMIZER = 'customizer';
    /**
     * Framework metaboxes container name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const METABOXES = 'metaboxes';
    /**
     * Framework shortcoder container name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const SHORTCODER = 'shortcoder';
    /**
     * Framework taxonomies container name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const TAXONOMIES = 'taxonomies';
    /**
     * Framework assets folder name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const ASSETS = 'assets';
    /**
     * Framework templates folder name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const TEMPLATES = 'templates';
    /**
     * Framework languages folder name.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    const LANGUAGES = 'languages';

    /**
     * Get framework root directory.
     *
     * @since 2.0.0
     * @access public
     * @return string
     */
    public static function get_dir()
    {
        return dirname(__DIR__);
    }

    /**
     * Get framework root URL.
     *
     * @since 2.0.0
     * @access public
     * @return string
     */
    public static function get_url()
    {
        return Helper::directory_to_url(self::get_dir());
    }

    /**
     * Allow full fields debug.
     *
     * @since 2.0.0
     * @access public
     * @return boolen
     */
    public static function allow_debug()
    {

        // filter @hook Allow full debug for options fields.
        $debug = apply_filters(
            self::PREFIX . 'allow_debug', false
        );

        return $debug;

    }

    /**
     * Allow light fields debug.
     *
     * @since 2.0.0
     * @access public
     * @return boolen
     */
    public static function allow_debug_light()
    {

        // filter @hook Allow light debug for options fields.
        $debug = apply_filters(
            self::PREFIX . 'allow_debug_light', false
        );

        return $debug;

    }

    /**
     * Allow minify core styles.
     *
     * @since 2.0.0
     * @access public
     * @return boolen
     */
    public static function allow_minify_styles()
    {

        // filter @hook Allow minify core styles.
        $minify_styles = apply_filters(
            self::PREFIX . 'allow_minify_styles', true
        );

        return $minify_styles;

    }

    /**
     * Allow minify core scripts.
     *
     * @since 2.0.0
     * @access public
     * @return boolen
     */
    public static function allow_minify_scripts()
    {

        // filter @hook Allow minify core scripts.
        $minify_scripts = apply_filters(
            self::PREFIX . 'allow_minify_scripts', true
        );

        return $minify_scripts;

    }

    /**
     * Get all available default container options.
     *
     * @since 2.0.0
     * @access public
     * @param  string $container_type
     * @param  string $context
     * @return array
     */
    public static function get_all_default_options($container_type, $context = 'normal')
    {

        // get all available default container options.
        switch ($container_type) {

            case 'taxonomy':

                $options_file = Config::get_dir() . '/data/options/taxonomy.php';
                if (file_exists($options_file)) {
                    require_once $options_file;
                    return cs_get_default_taxonomy_options();
                }

                break;

            case 'shortcoder':

                $options_file = Config::get_dir() . '/data/options/shortcoder.php';
                if (file_exists($options_file)) {
                    require_once $options_file;
                    return cs_get_default_shortcoder_options();
                }

                break;

            case 'metaboxes':

                $options_file = Config::get_dir() . '/data/options/metaboxes.php';
                if (file_exists($options_file)) {
                    require_once $options_file;
                    return cs_get_default_metaboxes_options($context);
                }

                break;

            case 'customizer':

                $options_file = Config::get_dir() . '/data/options/customizer.php';
                if (file_exists($options_file)) {
                    require_once $options_file;
                    return cs_get_default_customizer_options();
                }

                break;

            case 'settings':

                $options_file = Config::get_dir() . '/data/options/settings.php';
                if (file_exists($options_file)) {
                    require_once $options_file;
                    return cs_get_default_settings_options();
                }

                break;

        }

    }

}
