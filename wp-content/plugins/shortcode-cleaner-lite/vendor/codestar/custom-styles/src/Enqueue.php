<?php

namespace JO\Module\CodestarCustomStyles;

/**
 * Enqueue admin custom styles for Codestar framework.
 *
 * @package   Codestar_Custom_Styles
 * @author    Jozoor, mohamdio [jozoor.com]
 * @link      https://plugins.jozoor.com/shortcode-cleaner
 * @copyright 2017 Jozoor, mohamdio [jozoor.com]
 * @license   GPL-2.0+
 * @version   1.0.0
 *
 * @since  1.0.0
 */
class Enqueue
{

    /**
     * Enqueue admin custom styles for Codestar.
     *
     * @since 1.0.0
     * @access public
     */
    public static function register()
    {

        // make sure that Codestar framework exists
        if (class_exists('\\Codestar\\Codestar')) {
            add_action('cs_enqueue_custom_styles', array(__CLASS__, 'enqueue_styles'));
        }

    }

    /**
     * Actually enqueue custom styles.
     *
     * @since 1.0.0
     * @access public
     */
    public static function enqueue_styles()
    {

        // vendor fonts
        wp_enqueue_style('cs-framework-font-poppins', 'https://fonts.googleapis.com/css?family=Poppins:400,500', array(), '3.010', 'all');

        // enqueue custom styles
        wp_enqueue_style('cs-framework-custom-styles', self::get_url() . '/assets/css/cs-custom-styles.min.css', array(), '1.0.0', 'all');

        // enqueue rtl styles
        if (is_rtl()) {

            // vendor fonts
            wp_enqueue_style('cs-framework-font-sky', 'https://www.fontstatic.com/f=sky-bold,sky', array(), '1.0.0', 'all');

            // enqueue custom styles
            wp_enqueue_style('cs-framework-custom-styles-rtl', self::get_url() . '/assets/css/cs-custom-styles-rtl.min.css', array(), '1.0.0', 'all');

        } // end check rtl

    }

    /**
     * Get  root directory.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_dir()
    {
        return dirname(__DIR__);
    }

    /**
     * Get root URL.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public static function get_url()
    {

        // make sure that Codestar framework exists
        if (class_exists('\\Codestar\\Codestar')) {
            return \Codestar\Helper\Helper::directory_to_url(self::get_dir());
        }

        return '';

    }

}
