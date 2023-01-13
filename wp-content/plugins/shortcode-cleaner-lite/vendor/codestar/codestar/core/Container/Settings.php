<?php

namespace Codestar\Container;

use Codestar\Config;
use Codestar\Helper\Helper;
use Codestar\Module\Enqueue;
use Codestar\Module\Language;
use Codestar\Module\Menu;
use Codestar\Options\Options;

/**
 * Settings class.
 *
 * High number of custom fields and tons of options for themes and plugins.
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
class Settings
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
     * Config settings for menu page.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $menu_settings;
    /**
     * Config settings for options page.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $settings;
    /**
     * Available options for each settings section.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $options;
    /**
     * Available settings sections.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $sections;
    /**
     * Save available options.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $get_option;

    /**
     * Setup our settings container.
     *
     * @since 2.0.0
     * @access public
     * @param string $unique_options_id unique options id (name) inside database
     * @param array  $menu_settings     config settings for menu page
     * @param array  $settings          config settings for options page
     * @param array  $options           available options for each settings section
     */
    public function __construct(
        $unique_options_id = '', $menu_settings = array(), $settings = array(), $options = array()
    ) {

        // set unique options name
        if (!empty($unique_options_id)) {
            $this->unique_options_id = '_' . $unique_options_id;
        } else {
            $this->unique_options_id = Config::OPTION;
        }

        /**
         * Set menu settings.
         * filter @hook override or update settings menu settings.
         */
        $this->menu_settings = apply_filters(
            Config::PREFIX . Config::SETTINGS . $this->unique_options_id . '_menu_settings', $menu_settings
        );

        /**
         * Set options settings
         */

        // our default settings
        $default_settings = array(
            'title'                               => Config::NAME . ' <small>by ' . Config::AUTHOR . '</small>',
            'theme_skin'                          => '', // or light
            'ajax_save'                           => false,
            'show_reset_all'                      => false,
            'button_save_text'                    => esc_html('Save'),
            'button_reset_section_text'           => esc_html('Reset'),
            'button_reset_all_text'               => esc_html('Reset All Options'),
            'on_saving_text'                      => esc_html('Saving...'),
            'after_saved_text'                    => esc_html('Settings saved.'),
            'after_imported_text'                 => esc_html('Success. Imported backup options.'),
            'after_all_options_restored_text'     => esc_html('Default options restored.'),
            'after_section_options_restored_text' => esc_html('Default options restored for only this section.'),
            'show_all_options_text'               => esc_html('show all options'),
            'powered_by_text'                     => esc_html('Powered by ' . Config::NAME),
            'version_text'                        => esc_html('Version ' . Config::VERSION),
            'hide_show_all_options'               => false,
            'allow_sticky_header'                 => false,
            'hide_footer'                         => false,
        );

        /**
         * Set new settings by user.
         * filter @hook override or update settings settings.
         */
        $this->settings = apply_filters(
            Config::PREFIX . Config::SETTINGS . $this->unique_options_id . '_settings',
            array_merge($default_settings, $settings)
        );

        /**
         * Set options.
         * filter @hook override or update settings options.
         */
        $this->options = apply_filters(
            Config::PREFIX . Config::SETTINGS . $this->unique_options_id . '_options', $options
        );

        // we have options? get sections
        if (!empty($this->options)) {
            $this->sections = $this->get_sections();
        }

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
        $enqueue = new Enqueue($enqueue_settings);
        $enqueue->admin_scripts();
    }

    /**
     * Register our settings page and menu to WP.
     *
     * @since 2.0.0
     * @access public
     */
    public function register()
    {

        // we have options? add settings and menu page
        if (!empty($this->options)) {

            // get our options by unique id (name)
            $this->get_option = get_option($this->unique_options_id);

            // register our settings page
            add_action('admin_init', array($this, 'register_settings'));

            // get page content method
            $page_content = array(
                'function' => array($this, 'get_settings_page'),
            );

            /**
             * Get page settings
             *
             * @todo should add filter here to override this menu page settings
             */

            // main page settings
            if (
                isset($this->menu_settings['page']) &&
                !empty($this->menu_settings['page'])
            ) {
                $page_settings = $this->menu_settings['page'];
            }

            // we have sub page? get it's settings
            if (
                isset($this->menu_settings['sub_page']) &&
                !empty($this->menu_settings['sub_page'])
            ) {
                $page_settings = $this->menu_settings['sub_page'];
            }

            // merge our page content with menu settings
            $page_data = array_merge($page_content, $page_settings);

            /**
             * Add our new settings menu or sub menu page
             */

            // init our Menu class with hook
            $new_menu = new Menu($this->menu_settings['menu_hook']);

            // actually add menu or sub menu page

            // we have sub page? add it
            if (
                isset($this->menu_settings['sub_page']) &&
                !empty($this->menu_settings['sub_page'])
            ) {
                $new_menu->add_sub_page($page_data)->register();
            }

            // add main page
            if (
                isset($this->menu_settings['page']) &&
                !empty($this->menu_settings['page'])
            ) {
                $new_menu->add_page($page_data)->register();
            }

        }

    }

    /**
     * Get available settings sections.
     *
     * @since 2.0.0
     * @access protected
     * @return array
     */
    protected function get_sections()
    {

        // save tab sections
        $sections = array();

        // get all tabs sections
        foreach ($this->options as $key => $value) {

            // we have tab sections? get it
            if (isset($value['sections'])) {

                foreach ($value['sections'] as $section) {
                    if (isset($section['fields'])) {
                        $sections[] = $section;
                    }
                }

            } else {
                // here we have just tab

                if (isset($value['fields'])) {
                    $sections[] = $value;
                }

            }

        } // end foreach $this->options

        return $sections;

    }

    /**
     * Register settings options using WP settings api.
     *
     * @since 2.0.0
     * @access public
     */
    public function register_settings()
    {

        // save default settings
        $defaults = array();

        // register settings and validate before save
        register_setting($this->unique_options_id, $this->unique_options_id, array($this, 'validate_save'));

        // get each section options
        foreach ($this->sections as $section) {

            // we havae fields?
            if (isset($section['fields'])) {

                foreach ($section['fields'] as $field_key => $field) {

                    // set default option if exists
                    if (isset($field['default'])) {

                        $defaults[$field['id']] = $field['default'];
                        if (
                            !empty($this->get_option) &&
                            !isset($this->get_option[$field['id']])
                        ) {
                            $this->get_option[$field['id']] = $field['default'];
                        }

                    }

                } // end foreach $section['fields']

            } // end check $section['fields']

        } // end foreach $this->sections

        // set default variable if empty options and not empty defaults
        if (empty($this->get_option) && !empty($defaults)) {

            update_option($this->unique_options_id, $defaults);
            $this->get_option = $defaults;

        }

    }

    /**
     * Validate section fields on save.
     *
     * @since 2.0.0
     * @access public
     * @param  string $request
     * @return string
     */
    public function validate_save($request)
    {

        // save errors
        $add_errors = array();

        // get section id
        $section_id = Helper::get_var(Config::PREFIX . 'section_id');

        // ignore nonce requests
        if (isset($request['_nonce'])) {
            unset($request['_nonce']);
        }

        /**
         * Save options when ajax_save is false.
         */
        if (isset($request['save']) && !empty($request['save'])) {

            // save admin notice message
            $add_errors[] = $this->add_settings_error($this->settings['after_saved_text'], 'updated');

            /**
             * Set transient for normal requests.
             * Note: i moved set transient here from down, because if request is 'resetall'
             * this request will use the down set transient, not inside if request, so
             * i added set transient for each request.
             */
            $transient_time = (Language::get_defaults() !== false) ? 25 : 5;
            set_transient(Config::SLUG . Config::SETTINGS . $this->unique_options_id . '-transient', array('errors' => $add_errors, 'section_id' => $section_id), $transient_time);

        }

        /**
         * Import options.
         */
        if (isset($request['import']) && !empty($request['import'])) {

            $decode_string = Helper::decode_string($request['import']);

            if (is_array($decode_string)) {

                // save admin notice message
                $add_errors[] = $this->add_settings_error($this->settings['after_imported_text'], 'updated');

                /**
                 * Set transient.
                 * Note: here we set transient directly here, because this main method
                 * do return, so we will not arrive to the default transient code below.
                 */
                $transient_time = (Language::get_defaults() !== false) ? 25 : 5;
                set_transient(Config::SLUG . Config::SETTINGS . $this->unique_options_id . '-transient', array('errors' => $add_errors, 'section_id' => $section_id), $transient_time);

                return $decode_string;

            }

        }

        /**
         * Reset all options.
         */
        if (isset($request['resetall'])) {

            // save admin notice message
            $add_errors[] = $this->add_settings_error($this->settings['after_all_options_restored_text'], 'updated');

            /**
             * Set transient.
             * Note: here we set transient directly here, because this main method
             * do return, so we will not arrive to the default transient code below.
             */
            $transient_time = (Language::get_defaults() !== false) ? 25 : 5;
            set_transient(Config::SLUG . Config::SETTINGS . $this->unique_options_id . '-transient', array('errors' => $add_errors, 'section_id' => $section_id), $transient_time);

            return;

        }

        /**
         * Reset only activate section.
         */
        if (isset($request['reset']) && !empty($section_id)) {

            foreach ($this->sections as $value) {

                if ($value['name'] == $section_id) {

                    foreach ($value['fields'] as $field) {

                        if (isset($field['id'])) {
                            if (isset($field['default'])) {
                                $request[$field['id']] = $field['default'];
                            } else {
                                unset($request[$field['id']]);
                            }
                        }

                    } // end foearch $value['fields']

                } // end check $value['name']

            } // end foreach $this->sections

            // save admin notice message
            $add_errors[] = $this->add_settings_error($this->settings['after_section_options_restored_text'], 'updated');

            /**
             * Set transient for normal requests.
             * Note: i moved set transient here from down, because if request is 'resetall'
             * this request will use the down set transient, not inside if request, so
             * i added set transient for each request.
             */
            $transient_time = (Language::get_defaults() !== false) ? 25 : 5;
            set_transient(Config::SLUG . Config::SETTINGS . $this->unique_options_id . '-transient', array('errors' => $add_errors, 'section_id' => $section_id), $transient_time);

        } // end check $request['reset']

        /**
         * Sanitize and validate option.
         */
        foreach ($this->sections as $section) {

            if (isset($section['fields'])) {

                foreach ($section['fields'] as $field) {

                    // ignore santize and validate if element multilangual
                    if (isset($field['type']) && !isset($field['multilang']) && isset($field['id'])) {

                        /**
                         * Sanitize options.
                         */
                        $request_value = isset($request[$field['id']]) ? $request[$field['id']] : '';
                        $sanitize_type = $field['type'];

                        if (isset($field['sanitize'])) {
                            $sanitize_type = ($field['sanitize'] !== false) ? $field['sanitize'] : false;
                        }

                        if ($sanitize_type !== false && has_filter(Config::PREFIX . 'sanitize_' . $sanitize_type)) {
                            $request[$field['id']] = apply_filters(Config::PREFIX . 'sanitize_' . $sanitize_type, $request_value, $field, $section['fields']);
                        }

                        /**
                         * Validate options.
                         */
                        if (isset($field['validate']) && has_filter(Config::PREFIX . 'validate_' . $field['validate'])) {

                            $validate = apply_filters(Config::PREFIX . 'validate_' . $field['validate'], $request_value, $field, $section['fields']);

                            if (!empty($validate)) {

                                $add_errors[]          = $this->add_settings_error($validate, 'error', $field['id']);
                                $request[$field['id']] = (isset($this->get_option[$field['id']])) ? $this->get_option[$field['id']] : '';

                            }

                        }

                    } // end check $field['type']

                    if (!isset($field['id']) || empty($request[$field['id']])) {
                        continue;
                    }

                } // end foreach $section['fields']

            } // end check $section['fields']

        } // end foreach $this->sections

        // filter @hook validate settings options save changes.
        $request = apply_filters(Config::PREFIX . 'validate_save', $request);

        // action @hook do action after validate settings options save changes.
        do_action(Config::PREFIX . 'validate_save_after', $request);

        return $request;

    }

    /**
     * Add field option.
     *
     * @since 2.0.0
     * @access protected
     * @param array $field field element
     */
    protected function add_field($field)
    {

        // get field value
        $value = (isset($field['id']) && isset($this->get_option[$field['id']])) ? $this->get_option[$field['id']] : '';

        // display field element output content
        echo Options::add_field($field, $value, $this->unique_options_id);

    }

    /**
     * Show any settings errors.
     *
     * @since 2.0.0
     * @access public
     * @param string $message
     * @param string $type
     * @param string $id
     */
    public function add_settings_error($message, $type = 'error', $id = 'global')
    {
        return array('setting' => Config::SLUG . 'errors', 'code' => $id, 'message' => $message, 'type' => $type);
    }

    /**
     * Get settings options page HTML ouput content.
     *
     * @since 2.0.0
     * @access public
     */
    public function get_settings_page()
    {

        // action @hook add new html content before settings page content.
        do_action(Config::PREFIX . Config::SETTINGS . $this->unique_options_id . '_before_main_content');

        include Config::get_dir() . '/' . Config::TEMPLATES . '/settings-options.php';

        // action @hook add new html content after settings page content.
        do_action(Config::PREFIX . Config::SETTINGS . $this->unique_options_id . '_after_main_content');

    }

}
