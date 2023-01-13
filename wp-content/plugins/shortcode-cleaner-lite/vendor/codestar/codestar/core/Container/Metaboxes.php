<?php

namespace Codestar\Container;

use Codestar\Config;
use Codestar\Helper\Helper;
use Codestar\Module\Enqueue;
use Codestar\Options\Options;

/**
 * Metaboxes class.
 *
 * Allows you to bring custom metabox settings to all of your pages and posts.
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
class Metaboxes
{

    /**
     * Unique options id (name) inside database.
     *
     * @since 2.0.0
     * @access protected
     * @var string
     */
    protected $unique_options_id;
    /**
     * Config settings for mataboxes options.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $settings;
    /**
     * Available mataboxes options.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $options = array();

    /**
     * Setup our metaboxes container.
     *
     * @since 2.0.0
     * @access public
     * @param string $unique_options_id unique options id (name) inside database
     * @param array  $settings          config settings for metaboxes options
     * @param array  $options           available options for each metaboxes
     */
    public function __construct(
        $unique_options_id = '', $settings = array(), $options = array()
    ) {

        // set unique options name
        if (!empty($unique_options_id)) {
            $this->unique_options_id = $unique_options_id;
        } else {
            $this->unique_options_id = Config::METABOX;
        }

        /**
         * Set options settings
         */

        // our default settings
        $default_settings = array(
            'title'      => esc_html('Custom Options'),
            'post_type'  => 'page', // post, CPT
            'context'    => 'normal',
            'priority'   => 'default',
            'theme_skin' => 'light', // or ''
        );

        /**
         * Set new settings by user.
         * filter @hook Override or update metabox settings.
         */
        $this->settings = apply_filters(
            Config::PREFIX . Config::METABOXES . $this->unique_options_id . '_settings',
            array_merge($default_settings, $settings)
        );

        /**
         * Set options.
         * filter @hook Override or update metabox options.
         */
        $this->options = apply_filters(
            Config::PREFIX . Config::METABOXES . $this->unique_options_id . '_options', $options
        );

        // add our unique options id to settings
        $this->settings['id'] = $this->unique_options_id;

        // add our sections options to settings
        $this->settings['sections'] = $this->options;

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
        $enqueue_settings['post_type'] = $this->settings['post_type'];

        //enqueue admin scripts
        $enqueue = new Enqueue($enqueue_settings);
        $enqueue->admin_scripts();

    }

    /**
     * Register our metaboxes options to WP.
     *
     * @since 2.0.0
     * @access public
     */
    public function register()
    {

        // we have options? add metaboxes options
        if (!empty($this->options)) {

            add_action('add_meta_boxes', array($this, 'add_meta_box'));
            add_action('save_post', array($this, 'save_post'), 10, 2);
            /**
             * Add metaboxes using ajax.
             * @todo maybe later i will see how can i add metaboxes by ajax
             * when change page template, because at this time i can't do that now,
             * so i will let this code for future enhancment, but i will hide.
             */
            // add_action('wp_ajax_add-metaboxes-ajax', array($this, 'add_meta_box_ajax'));

        }

    }

    /**
     * Add metaboxes using ajax.
     *
     * @since 2.0.0
     * @access public
     */
    public function add_meta_box_ajax()
    {

        // this if i want to send back data to js file by ajax
        // wp_send_json(Datastore::get_container('metaboxes'));

        // get ajax request type
        $request = Helper::get_var('template_name');

        // we don't have any request? go back
        if (empty($request)) {
            die();
        }

        // here i don't know how add metaboxes on the fly

        /**
         * we must use die() at the end. If you don’t,
         * admin-ajax.php will execute it’s own die(0) code,
         * echoing an additional zero in your response.
         */
        die();

    }

    /**
     * Add metabox options.
     *
     * @since 2.0.0
     * @access public
     * @param string $post_type
     */
    public function add_meta_box($post_type)
    {

        /**
         * @todo later i will support custom_post_template also.
         */

        // we have page_template for 'page' post type?
        if (
            'page' === $this->settings['post_type'] &&
            isset($this->settings['page_template'])
        ) {

            if (isset($_REQUEST['post']) || isset($_REQUEST['post_ID'])) {

                // very important
                $post_id = empty($_REQUEST['post_ID']) ? $_REQUEST['post'] : $_REQUEST['post_ID'];

                // get current page template
                $current_page_template = get_post_meta($post_id, '_wp_page_template', true);

                // we have array of page templates?
                if (is_array($this->settings['page_template'])) {

                    // get exact page template
                    if (array_search($current_page_template, $this->settings['page_template']) !== false) {

                        add_meta_box(
                            $this->unique_options_id,
                            $this->settings['title'],
                            array($this, 'render_meta_box_content'),
                            $this->settings['post_type'],
                            $this->settings['context'],
                            $this->settings['priority'],
                            $this->settings
                        );

                    }

                } else if ($current_page_template === $this->settings['page_template']) {
                    // just single page template

                    add_meta_box(
                        $this->unique_options_id,
                        $this->settings['title'],
                        array($this, 'render_meta_box_content'),
                        $this->settings['post_type'],
                        $this->settings['context'],
                        $this->settings['priority'],
                        $this->settings
                    );

                }

            } // end check $_REQUEST

        } else {
            // end check page_template

            add_meta_box(
                $this->unique_options_id,
                $this->settings['title'],
                array($this, 'render_meta_box_content'),
                $this->settings['post_type'],
                $this->settings['context'],
                $this->settings['priority'],
                $this->settings
            );

        }

    }

    /**
     * Render metabox options content.
     *
     * @since 2.0.0
     * @access public
     * @param  object $post
     * @param  array $callback
     * @return string
     */
    public function render_meta_box_content($post, $callback)
    {

        global $post, $cs_errors, $typenow;

        wp_nonce_field(
            Config::SLUG . 'framework-metabox', Config::SLUG . 'framework-metabox-nonce'
        );

        // set options arguments
        $unique     = $callback['args']['id'];
        $sections   = $callback['args']['sections'];
        $meta_value = get_post_meta($post->ID, $unique, true);
        $transient  = get_transient(Config::SLUG . Config::METABOXES . '-' . $this->unique_options_id . '-transient');
        $cs_errors  = $transient['errors'];
        $has_nav    = (count($sections) >= 2 && $callback['args']['context'] != 'side') ? true : false;
        $show_all   = (!$has_nav) ? ' cs-show-all' : '';
        $section_id = (!empty($transient['ids'][$unique])) ? $transient['ids'][$unique] : '';
        $section_id = Helper::get_var('cs-section', $section_id);

        // get metaboxes options page HTML ouput content.
        include Config::get_dir() . '/' . Config::TEMPLATES . '/metaboxes-options.php';

    }

    /**
     * Save metabox options.
     *
     * @since 2.0.0
     * @access public
     * @param  int    $post_id
     * @param  object $post
     */
    public function save_post($post_id, $post)
    {

        if (
            wp_verify_nonce(
                Helper::get_var(Config::SLUG . 'framework-metabox-nonce'),
                Config::SLUG . 'framework-metabox'
            )
        ) {

            $errors    = array();
            $post_type = Helper::get_var('post_type');

            /**
             * Note: we set the third parameter to true for in_array
             * to force use of strict comparison and improve the performance slightly.
             */
            if (in_array($post_type, (array) $this->settings['post_type'], true)) {

                $request_key = $this->settings['id'];
                $request     = Helper::get_var($request_key, array());

                // ignore _nonce
                if (isset($request['_nonce'])) {
                    unset($request['_nonce']);
                }

                // get options sections
                foreach ($this->settings['sections'] as $key => $section) {

                    // we have fields?
                    if (isset($section['fields'])) {

                        // get our fields
                        foreach ($section['fields'] as $field) {

                            if (isset($field['type']) && isset($field['id'])) {

                                $field_value = Helper::get_vars($request_key, $field['id']);

                                // sanitize options
                                if (isset($field['sanitize']) && $field['sanitize'] !== false) {

                                    $sanitize_type = $field['sanitize'];

                                } else if (!isset($field['sanitize'])) {

                                    $sanitize_type = $field['type'];

                                }

                                if (
                                    has_filter(
                                        Config::PREFIX . 'sanitize_' . $sanitize_type
                                    )
                                ) {

                                    $request[$field['id']] = apply_filters(
                                        Config::PREFIX . 'sanitize_' . $sanitize_type, $field_value, $field, $section['fields']
                                    );

                                }

                                // validate options
                                if (
                                    isset($field['validate']) &&
                                    has_filter(
                                        Config::PREFIX . 'validate_' . $field['validate']
                                    )
                                ) {

                                    $validate = apply_filters(
                                        Config::PREFIX . 'validate_' . $field['validate'], $field_value, $field, $section['fields']
                                    );

                                    if (!empty($validate)) {

                                        $meta_value = get_post_meta($post_id, $request_key, true);

                                        $errors[$field['id']]  = array('code' => $field['id'], 'message' => $validate, 'type' => 'error');
                                        $default_value         = isset($field['default']) ? $field['default'] : '';
                                        $request[$field['id']] = (isset($meta_value[$field['id']])) ? $meta_value[$field['id']] : $default_value;

                                    }

                                }

                            } // end check isset($field['type'])

                        } // end foreach $section['fields']

                    } // end isset($section['fields'])

                } // end foreach $this->settings['sections']

                // filter @hook update metabox options save changes.
                $request = apply_filters(
                    Config::PREFIX . 'save_post', $request, $request_key, $post
                );

                if (empty($request)) {

                    delete_post_meta($post_id, $request_key);

                } else {

                    update_post_meta($post_id, $request_key, $request);

                }

                $transient['ids'][$request_key] = Helper::get_vars('cs_section_id', $request_key);
                $transient['errors']            = $errors;

            } // end check in_array > $post_type

            set_transient(Config::SLUG . Config::METABOXES . '-' . $this->unique_options_id . '-transient', $transient, 10);

        } // end wp_verify_nonce

    }

}
