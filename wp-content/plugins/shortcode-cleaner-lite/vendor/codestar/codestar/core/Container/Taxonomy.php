<?php

namespace Codestar\Container;

use Codestar\Config;
use Codestar\Helper\Helper;
use Codestar\Module\Enqueue;
use Codestar\Helper\Fallback;
use Codestar\Options\Options;

/**
 * Taxonomy class.
 *
 * Allows you to bring custom taxonomy settings to all of your categories, tags or CPT.
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
class Taxonomy
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
     * Config settings for taxonomy options.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $settings;
    /**
     * Available taxonomy options.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $options = array();

    /**
     * Setup our taxonomy container.
     *
     * @since 2.0.0
     * @access public
     * @param string $unique_options_id unique options id (name) inside database
     * @param array  $settings          config settings for taxonomy options
     * @param array  $options           available options for each taxonomy
     */
    public function __construct(
        $unique_options_id = '', $settings = array(), $options = array()
    ) {

        // set unique options name
        if (!empty($unique_options_id)) {
            $this->unique_options_id = $unique_options_id;
        } else {
            $this->unique_options_id = Config::TAXONOMY;
        }

        /**
         * Set options settings
         */

        // our default settings
        $default_settings = array(
            'taxonomy'   => 'category', // category, post_tag or your custom taxonomy name
            'theme_skin' => 'light', // or ''
        );

        /**
         * Set new settings by user.
         * filter @hook Override or update taxonomy settings.
         */
        $this->settings = apply_filters(
            Config::PREFIX . Config::TAXONOMIES . '_' . $this->unique_options_id . '_settings',
            array_merge($default_settings, $settings)
        );

        /**
         * Set options.
         * filter @hook Override or update taxonomy options.
         */
        $this->options = apply_filters(
            Config::PREFIX . Config::TAXONOMIES . '_' . $this->unique_options_id . '_options', $options
        );

        // add our unique options id to settings
        $this->settings['id'] = $this->unique_options_id;

        // add our fields options to settings
        $this->settings['fields'] = $this->options;

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

        // add taxonomy for enqueue settings
        $enqueue_settings['taxonomy'] = $this->settings['taxonomy'];

        //enqueue admin scripts
        $enqueue = new Enqueue($enqueue_settings);
        $enqueue->admin_scripts();

    }

    /**
     * Register our taxonomy options to WP.
     *
     * @since 2.0.0
     * @access public
     */
    public function register()
    {

        // we have options? add taxonomy options
        if (!empty($this->options)) {

            add_action('admin_init', array($this, 'add_taxonomy_fields'));

        }

    }

    /**
     * Add and edit taxonomy fields.
     *
     * @since 2.0.0
     * @access public
     */
    public function add_taxonomy_fields()
    {

        // get current taxonomy
        $get_taxonomy = Helper::get_var('taxonomy');

        // we have exact taxonomy? add, edit and delete fields
        if ($get_taxonomy === $this->settings['taxonomy']) {

            add_action(
                $this->settings['taxonomy'] . '_add_form_fields', array($this, 'get_fields')
            );
            add_action(
                $this->settings['taxonomy'] . '_edit_form', array($this, 'get_fields')
            );
            add_action(
                'created_' . $this->settings['taxonomy'], array($this, 'save_taxonomy')
            );
            add_action(
                'edited_' . $this->settings['taxonomy'], array($this, 'save_taxonomy')
            );
            add_action(
                'delete_' . $this->settings['taxonomy'], array($this, 'delete_taxonomy')
            );

        }

    }

    /**
     * Render taxonomy form fields.
     *
     * @since 2.0.0
     * @access public
     * @param  string $term current taxonomy term
     * @return string
     */
    public function get_fields($term)
    {

        global $cs_errors;

        // handle taxonomy term form
        $form_edit = (is_object($term) && isset($term->taxonomy)) ? true : false;
        $taxonomy  = ($form_edit) ? $term->taxonomy : $term;
        $classname = ($form_edit) ? 'edit' : 'add';
        $cs_errors = get_transient(Config::SLUG . Config::TAXONOMIES . '-' . $this->unique_options_id . '-transient');

        wp_nonce_field(Config::SLUG . 'taxonomy', Config::SLUG . 'taxonomy-nonce');

        echo '<div class="cs-framework cs-taxonomy cs-taxonomy-' . $classname . '-fields">';

        // we have exact taxonomy?
        if ($taxonomy === $this->settings['taxonomy']) {

            $tax_value = ($form_edit) ? Fallback::get_term_meta($term->term_id, $this->settings['id'], true) : '';

            // add taxonomy fields
            foreach ($this->settings['fields'] as $field) {

                $default    = (isset($field['default'])) ? $field['default'] : '';
                $elem_id    = (isset($field['id'])) ? $field['id'] : '';
                $elem_value = (is_array($tax_value) && isset($tax_value[$elem_id])) ? $tax_value[$elem_id] : $default;

                echo Options::add_field($field, $elem_value, $this->settings['id']);

            }

        }

        echo '</div>';

    }

    /**
     * Save taxonomy form fields.
     *
     * @since 2.0.0
     * @access public
     * @param  int $term_id
     */
    public function save_taxonomy($term_id)
    {

        if (
            wp_verify_nonce(
                Helper::get_var(Config::SLUG . 'taxonomy-nonce'), Config::SLUG . 'taxonomy'
            )
        ) {

            $errors   = array();
            $taxonomy = Helper::get_var('taxonomy');

            if ($taxonomy === $this->settings['taxonomy']) {

                $request_key = $this->settings['id'];
                $request     = Helper::get_var($request_key, array());

                // ignore _nonce
                if (isset($request['_nonce'])) {
                    unset($request['_nonce']);
                }

                // get taxonomy fields
                if (isset($this->settings['fields'])) {

                    foreach ($this->settings['fields'] as $field) {

                        if (isset($field['type']) && isset($field['id'])) {

                            $field_value = Helper::get_vars($request_key, $field['id']);

                            // sanitize options
                            if (isset($field['sanitize']) && $field['sanitize'] !== false) {

                                $sanitize_type = $field['sanitize'];

                            } else if (!isset($field['sanitize'])) {

                                $sanitize_type = $field['type'];

                            }

                            if (has_filter(Config::PREFIX . 'sanitize_' . $sanitize_type)) {

                                $request[$field['id']] = apply_filters(Config::PREFIX . 'sanitize_' . $sanitize_type, $field_value, $field, $this->settings['fields']);

                            }

                            // validate options
                            if (
                                isset($field['validate']) &&
                                has_filter(Config::PREFIX . 'validate_' . $field['validate'])
                            ) {

                                $validate = apply_filters(Config::PREFIX . 'validate_' . $field['validate'], $field_value, $field, $this->settings['fields']);

                                if (!empty($validate)) {

                                    $meta_value = Fallback::get_term_meta($term_id, $request_key, true);

                                    $errors[$field['id']] = array('code' => $field['id'], 'message' => $validate, 'type' => 'error');

                                    $default_value = isset($field['default']) ? $field['default'] : '';

                                    $request[$field['id']] = (isset($meta_value[$field['id']])) ? $meta_value[$field['id']] : $default_value;

                                }

                            }

                        } // end check $field['type']

                    } // end foreach $this->settings['fields']

                } // end check $this->settings['fields']

                // filter @hook update taxonomy options save changes.
                $request = apply_filters(
                    Config::PREFIX . 'save_taxonomy', $request, $request_key, $term_id
                );

                if (empty($request)) {

                    Fallback::delete_term_meta($term_id, $request_key);

                } else {

                    if (Fallback::get_term_meta($term_id, $request_key, true)) {

                        Fallback::update_term_meta($term_id, $request_key, $request);

                    } else {

                        Fallback::add_term_meta($term_id, $request_key, $request);

                    }

                } // end check empty($request)

            } // end check $taxonomy

            set_transient(Config::SLUG . Config::TAXONOMIES . '-' . $this->unique_options_id . '-transient', $errors, 10);

        }

    }

    /**
     * Delete taxonomy term.
     *
     * @since 2.0.0
     * @access public
     * @param  int $term_id
     */
    public function delete_taxonomy($term_id)
    {

        $taxonomy = Helper::get_var('taxonomy');

        if (!empty($taxonomy)) {

            if ($taxonomy === $this->settings['taxonomy']) {

                $request_key = $this->settings['id'];
                Fallback::delete_term_meta($term_id, $request_key);

            }

        }

    }

}
