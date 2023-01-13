<?php

namespace Codestar\Module;

use Codestar\Config;
use Codestar\Helper\Helper;
use Codestar\Module\Data;

/**
 * Enqueue admin styles and scripts.
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
class Enqueue
{

    /**
     * Config settings for enqueue.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $enqueue_settings;

    /**
     * Setup enqueue settings.
     *
     * @since 2.0.0
     * @access public
     * @param array $enqueue_settings
     */
    public function __construct($enqueue_settings = array())
    {

        // set our enqueue settings
        $this->enqueue_settings = $enqueue_settings;

    }

    /**
     * Enqueue admin styles and scripts.
     *
     * @since 2.0.0
     * @access public
     */
    public function admin_scripts()
    {

        /**
         * Actually enqueue admin scripts.
         * Note: here we don't load styles and scripts when customizer preview, to fix
         * conflict with customizer scripts.
         * @todo maybe later i will remove customizer container.
         */
        if (!is_customize_preview()) {
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        }

    }

    /**
     * Enqueue admin scripts.
     *
     * Note: here we enqueue scripts for our exact pages enough upon $hook
     *
     * @since 2.0.0
     * @access public
     * @param  string $hook current admin page hook name
     */
    public function enqueue_admin_scripts($hook)
    {

        global $post;

        // get post type
        $post_type = (isset($post->post_type)) ? $post->post_type : '';

        // get page slug form current hook
        $page_slug = Helper::get_page_slug_hook($hook);

        // check minify styles
        $minify_styles = '';
        if (Config::allow_minify_styles()) {
            $minify_styles = '.min';
        }

        // check minify scripts
        $minify_scripts = '';
        if (Config::allow_minify_scripts()) {
            $minify_scripts = '.min';
        }

        /**
         * Actually enqueue admin scripts.
         */

        /**
         * we are inside exact options page? enqueue admin scripts.
         * here we check all pages slug to enqueue only if our options exists,
         * for > settings, customizer, metaboxes, shortcoder, taxonomy.
         */
        if (
            (isset($this->enqueue_settings['page_slug']) && $page_slug === $this->enqueue_settings['page_slug']) ||
            (isset($this->enqueue_settings['post_type']) && $this->enqueue_settings['post_type'] === get_current_screen()->id) ||
            (isset($this->enqueue_settings['exclude_post_types']) && !in_array($post_type, $this->enqueue_settings['exclude_post_types'], true) && 'post.php' === $hook) ||
            (isset($this->enqueue_settings['exclude_post_types']) && !in_array($post_type, $this->enqueue_settings['exclude_post_types'], true) && 'post-new.php' === $hook) ||
            (isset($this->enqueue_settings['taxonomy']) && $this->enqueue_settings['taxonomy'] === get_current_screen()->taxonomy && 'edit-tags.php' === $hook) ||
            (isset($this->enqueue_settings['taxonomy']) && $this->enqueue_settings['taxonomy'] === get_current_screen()->taxonomy && 'term.php' === $hook)
        ) {

            // admin utilities
            wp_enqueue_media();

            // wp core styles
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_style('wp-jquery-ui-dialog');

            // vendor styles
            wp_enqueue_style('cs-framework-font-awesome', 'https://use.fontawesome.com/releases/v5.0.1/css/all.css', array(), '5.0.1', 'all');

            // framework core styles
            wp_enqueue_style('cs-framework', Config::get_url() . '/' . Config::ASSETS . '/css/cs-framework' . $minify_styles . '.css', array(), Config::VERSION, 'all');

            // want to change style? get it
            if (!empty($this->enqueue_settings['theme_skin'])) {

                $css_file = Config::get_dir() . '/' . Config::ASSETS . '/css/cs-framework-' . $this->enqueue_settings['theme_skin'] . '.css';

                // we have css file? enqueue it
                if (file_exists($css_file)) {

                    wp_enqueue_style(
                        'cs-framework-theme',
                        Config::get_url() . '/' . Config::ASSETS . '/css/cs-framework-' . $this->enqueue_settings['theme_skin'] . '.css',
                        array(),
                        Config::VERSION,
                        'all'
                    );

                }

            }

            // our site is RTL? get rtl style
            if (is_rtl()) {
                wp_enqueue_style('cs-framework-rtl', Config::get_url() . '/' . Config::ASSETS . '/css/cs-framework-rtl' . $minify_styles . '.css', array(), Config::VERSION, 'all');
            }

            /**
             * action @hook use this action to add custom styles.
             * example:
             * add_action('cs_enqueue_custom_styles', 'your_function_name');
             * function your_function_name()
             * { // your new enqueue styles files using > wp_enqueue_style }
             */
            do_action(Config::PREFIX . 'enqueue_custom_styles');

            // wp core scripts
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_script('jquery-ui-dialog');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-accordion');

            // vendor scripts
            wp_enqueue_script('cs-framework-bowser', 'https://cdnjs.cloudflare.com/ajax/libs/bowser/1.9.1/bowser.min.js', array(), '1.9.1', true);

            // framework core scripts
            wp_enqueue_script('cs-framework-plugins', Config::get_url() . '/' . Config::ASSETS . '/js/cs-plugins' . $minify_scripts . '.js', array(), Config::VERSION, true);
            wp_enqueue_script('cs-framework', Config::get_url() . '/' . Config::ASSETS . '/js/cs-framework' . $minify_scripts . '.js', array('cs-framework-plugins'), Config::VERSION, true);

            /**
             * action @hook use this action to add custom scripts
             * example:
             * add_action('cs_enqueue_custom_scripts', 'your_function_name');
             * function your_function_name()
             * { // your new enqueue scripts files using > wp_enqueue_script }
             */
            do_action(Config::PREFIX . 'enqueue_custom_scripts');

            // set icons data for wp dialog when needed.
            Data::set_icons();

        } // end check $page_slug

    }

}
