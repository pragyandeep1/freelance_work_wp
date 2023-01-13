<?php

namespace Codestar\Container;

use Codestar\Config;
use Codestar\Helper\Helper;
use Codestar\Module\Enqueue;
use Codestar\Options\Options;

/**
 * Shortcoder class.
 *
 * Shortcode editor to manage your content.
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
class Shortcoder
{

    /**
     * Unique options id for shortcoder instance.
     *
     * will be used in html and js code.
     *
     * @since 2.0.0
     * @access protected
     * @var string
     */
    protected $unique_options_id;
    /**
     * Config settings for shortcoder options.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $settings;
    /**
     * Available shortcoder options.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $options = array();
    /**
     * Shortcodes options.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $shortcodes = array();
    /**
     * Exclude post types from show Shortcodes options.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $exclude_post_types = array();

    /**
     * Setup our shortcoder container.
     *
     * @since 2.0.0
     * @access public
     * @param string $unique_options_id unique options id for shortcoder instance
     * @param array  $settings          config settings for shortcoder
     * @param array  $options           available options for each shortcoder options
     */
    public function __construct(
        $unique_options_id = '', $settings = array(), $options = array()
    ) {

        // set unique options id name
        if (!empty($unique_options_id)) {
            $this->unique_options_id = $unique_options_id;
        } else {
            $this->unique_options_id = Config::SHORTCODE;
        }

        /**
         * Set options settings
         */

        // our default settings
        $default_settings = array(
            'exclude_post_types' => array(),
            'button_title'       => esc_html('Add Shortcode'),
            'dialog_title'       => esc_html('Add Shortcode'),
            'select_title'       => esc_html('Select a shortcode'),
            'insert_title'       => esc_html('Insert Shortcode'),
        );

        /**
         * Set new settings by user.
         * filter @hook override or update shortcoder settings.
         */
        $this->settings = apply_filters(
            Config::PREFIX . Config::SHORTCODER . '_' . $this->unique_options_id . '_settings',
            array_merge($default_settings, $settings)
        );

        /**
         * Set options.
         * filter @hook override or update shortcoder options.
         */
        $this->options = apply_filters(
            Config::PREFIX . Config::SHORTCODER . '_' . $this->unique_options_id . '_options', $options
        );

        /**
         * Set exclude post types.
         * filter @hook override or update shortcoder exclude post types.
         */
        $this->exclude_post_types = apply_filters(Config::PREFIX . Config::SHORTCODER . '_' . $this->unique_options_id . '_exclude_post_types', $this->settings['exclude_post_types']);

        // we have options? add shortcoder options
        if (!empty($this->options)) {
            $this->shortcodes = $this->get_shortcodes();
        }

    }

    /**
     * Get all shortcodes options.
     *
     * @since 2.0.0
     * @access public
     * @return array
     */
    public function get_shortcodes()
    {

        // save shortcodes
        $shortcodes = array();

        // get shortcodes
        foreach ($this->options as $group_value) {

            foreach ($group_value['shortcodes'] as $shortcode) {
                $shortcodes[$shortcode['name']] = $shortcode;
            }

        }

        return $shortcodes;

    }

    /**
     * Enqueue admin scripts.
     *
     * @since 2.0.0
     * @access public
     * @param  array $enqueue_settings
     */
    public function enqueue($enqueue_settings = array())
    {

        // add post type for enqueue settings
        $enqueue_settings['exclude_post_types'] = $this->exclude_post_types;

        //enqueue admin scripts
        $enqueue = new Enqueue($enqueue_settings);
        $enqueue->admin_scripts();

    }

    /**
     * Register our shortcoder options to WP.
     *
     * @since 2.0.0
     * @access public
     */
    public function register()
    {

        // we have options? add shortcoder options
        if (!empty($this->options)) {

            add_action('media_buttons', array($this, 'media_shortcode_button'), 99);
            add_action('admin_footer', array($this, 'shortcode_dialog'), 99);
            add_action('customize_controls_print_footer_scripts', array($this, 'shortcode_dialog'), 99);
            // here we make ajax by unique shortcoder options id
            add_action('wp_ajax_' . $this->unique_options_id, array($this, 'shortcode_generator'), 99);

        }

    }

    /**
     * Add shortcoder button.
     *
     * @since 2.0.0
     * @access public
     * @param  string $editor_id wp editor unique id
     * @return string
     */
    public function media_shortcode_button($editor_id)
    {

        global $post;

        // get post type
        $post_type = (isset($post->post_type)) ? $post->post_type : '';

        /**
         * Check post types before add button.
         *
         * Note: we set the third parameter to true for in_array
         * to force use of strict comparison and improve the performance slightly.
         */
        if (!in_array($post_type, $this->exclude_post_types, true)) {

            echo '<a href="#" class="button button-primary cs-shortcode" data-id="' . $this->unique_options_id . '" data-editor-id="' . $editor_id . '">' . $this->settings['button_title'] . '</a>';

        }

    }

    /**
     * Shortcoder dialog HTML content.
     *
     * @since 2.0.0
     * @access public
     * @return string
     */
    public function shortcode_dialog()
    {
        // get shortcoder dialog HTML ouput content.
        include Config::get_dir() . '/' . Config::TEMPLATES . '/shortcoder-dialog.php';
    }

    /**
     * Generate shortcodes for dialog.
     *
     * @since 2.0.0
     * @access public
     */
    public function shortcode_generator()
    {

        // get ajax request type
        $request = Helper::get_var('shortcode');

        // we don't have any request? go back
        if (empty($request)) {
            die();
        }

        // get shortcodes from ajax request
        $shortcode = $this->shortcodes[$request];

        // we have fields? get it
        if (isset($shortcode['fields'])) {

            foreach ($shortcode['fields'] as $key => $field) {

                if (isset($field['id'])) {

                    $field['attributes'] = (isset($field['attributes'])) ? wp_parse_args(array('data-atts' => $field['id']), $field['attributes']) : array('data-atts' => $field['id']);

                }

                $field_default = (isset($field['default'])) ? $field['default'] : '';

                if (
                    in_array($field['type'], array('image_select', 'checkbox'), true) &&
                    isset($field['options'])
                ) {

                    $field['attributes']['data-check'] = true;

                }

                // add our fields
                echo Options::add_field($field, $field_default, 'shortcode');

            }

        } // end check $shortcode['fields']

        // clone fields?
        if (isset($shortcode['clone_fields'])) {

            $clone_id = isset($shortcode['clone_id']) ? $shortcode['clone_id'] : $shortcode['name'];

            echo '<div class="cs-shortcode-clone" data-clone-id="' . $clone_id . '">';
            echo '<a href="#" class="cs-remove-clone"><i class="fas fa-trash"></i></a>';

            foreach ($shortcode['clone_fields'] as $key => $field) {

                $field['sub']        = true;
                $field['attributes'] = (isset($field['attributes'])) ? wp_parse_args(array('data-clone-atts' => $field['id']), $field['attributes']) : array('data-clone-atts' => $field['id']);
                $field_default       = (isset($field['default'])) ? $field['default'] : '';

                if (
                    in_array($field['type'], array('image_select', 'checkbox'), true) &&
                    isset($field['options'])
                ) {

                    $field['attributes']['data-check'] = true;

                }

                echo Options::add_field($field, $field_default, 'shortcode');

            } // end foreach $shortcode['clone_fields']

            echo '</div>';
            echo '<div class="cs-clone-button"><a id="shortcode-clone-button" class="button" href="#"><i class="fas fa-plus-circle"></i> ' . $shortcode['clone_title'] . '</a></div>';

        } // end check $shortcode['clone_fields']

        /**
         * we must use die() at the end. If you don’t,
         * admin-ajax.php will execute it’s own die(0) code,
         * echoing an additional zero in your response.
         */
        die();

    }

}
