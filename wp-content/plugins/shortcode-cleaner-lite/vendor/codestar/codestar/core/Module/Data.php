<?php

namespace Codestar\Module;

use Codestar\Config;

/**
 * Load static json data files for Codestar framework.
 *
 * like: fonts, icons, ..etc
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
class Data
{

    /**
     * Get data files content.
     *
     * @since 2.0.0
     * @access public
     * @param  string $data_file full path of data file
     * @return object
     */
    public static function get_data($data_file = '')
    {

        ob_start();

        // get data file
        if (file_exists($data_file)) {
            include $data_file;
        }

        // decode data file json content
        $json = ob_get_clean();
        $data = json_decode($json);

        // return decoded data content
        return $data;

    }

    /**
     * Get icons from admin ajax.
     *
     * @since 2.0.0
     * @access public
     */
    public static function get_icons()
    {
        add_action('wp_ajax_cs-get-icons', array(__CLASS__, 'get_icons_data'));
    }

    /**
     * Get icons data content from json files.
     *
     * @since 2.0.0
     * @access public
     */
    public static function get_icons_data()
    {

        // action @hook do action before add icons.
        do_action(Config::PREFIX . 'add_icons_before');

        /**
         * Get icons json data files.
         * filter @hook add new or override icons json files.
         */
        $icons_files = apply_filters(
            Config::PREFIX . 'add_icons_json',
            array(
                Config::get_dir() . '/data/icons/font-awesome.json',
            )
        );

        if (!empty($icons_files)) {

            foreach ($icons_files as $data_file) {

                // get data object content
                $object = self::get_data($data_file);

                // we have data object content?
                if (is_object($object)) {

                    echo (count($icons_files) >= 2) ? '<h4 class="cs-icon-title">' . $object->name . '</h4>' : '';

                    foreach ($object->icons as $icon) {
                        echo '<a class="cs-icon-tooltip" data-cs-icon="' . $icon . '" data-title="' . $icon . '"><span class="cs-icon cs-selector"><i class="' . $icon . '"></i></span></a>';
                    }

                } else {

                    /**
                     * @todo should make settings option for override this text
                     */
                    echo '<h4 class="cs-icon-title">' . esc_html('Error! Can not load json file.') . '</h4>';

                }

            } // end foreach $icons_files

        } // end check $icons_files

        // action @hook add icons.
        do_action(Config::PREFIX . 'add_icons');

        // action @hook do action after add icons.
        do_action(Config::PREFIX . 'add_icons_after');

        /**
         * we must use die() at the end. If you don’t,
         * admin-ajax.php will execute it’s own die(0) code,
         * echoing an additional zero in your response.
         */
        die();

    }

    /**
     * Set icons for wp dialog.
     *
     * @since 2.0.0
     * @access public
     */
    public static function set_icons()
    {
        add_action('admin_footer', array(__CLASS__, 'icons_data'));
        add_action('customize_controls_print_footer_scripts', array(__CLASS__, 'icons_data'));
    }

    /**
     * Set icons data content.
     *
     * @since 2.0.0
     * @access public
     */
    public static function icons_data()
    {
        /**
         * @todo should make settings option for override this texts
         */
        echo '<div id="cs-icon-dialog" class="cs-dialog" title="' . esc_html('Add Icon') . '">';
        echo '<div class="cs-dialog-header cs-text-center"><input type="text" placeholder="' . esc_html('Search for icon...') . '" class="cs-icon-search" /></div>';
        echo '<div class="cs-dialog-load"><div class="cs-icon-loading">' . esc_html('Loading...') . '</div></div>';
        echo '</div>';

    }

}
