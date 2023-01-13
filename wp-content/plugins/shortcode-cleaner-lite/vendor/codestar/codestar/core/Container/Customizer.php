<?php

namespace Codestar\Container;

use Codestar\Config;
use Codestar\Helper\Sanitize;
use Codestar\Module\Customize;
use Codestar\Module\Enqueue;

/**
 * Customizer class.
 *
 * Inherits the default WordPress Customizer with integration of own custom fields.
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
class Customizer
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
     * Available customizer options.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $options = array();
    /**
     * Customizer panel priority.
     *
     * @since 2.0.0
     * @access protected
     * @var boolean
     */
    protected $priority = 1;

    /**
     * Setup our customizer container.
     *
     * @since 2.0.0
     * @access public
     * @param string $unique_options_id unique options id (name) inside database
     * @param array  $options           available options for each settings section
     */
    public function __construct($unique_options_id = '', $options = array())
    {

        // set unique options name
        if (!empty($unique_options_id)) {
            $this->unique_options_id = '_' . $unique_options_id;
        } else {
            $this->unique_options_id = Config::CUSTOMIZE;
        }

        /**
         * Set options.
         * filter @hook Override or update customizer options.
         */
        $this->options = apply_filters(
            Config::PREFIX . Config::CUSTOMIZER . $this->unique_options_id . '_options', $options
        );

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
     * Register our customizer options to WP.
     *
     * @since 2.0.0
     * @access public
     */
    public function register()
    {

        // we have options? register customize controls
        if (!empty($this->options)) {
            add_action('customize_register', array($this, 'customize_register'));
        }

    }

    /**
     * Actually register customize.
     *
     * @since 2.0.0
     * @access public
     * @param object $wp_customize WP core instance of the WP_Customize_Manager class
     */
    public function customize_register($wp_customize)
    {

        // action @hook Add extra fields for wp customizer
        do_action(Config::PREFIX . Config::CUSTOMIZER . $this->unique_options_id . '_register', $wp_customize);

        $panel_priority = 1;

        // get customize options
        foreach ($this->options as $value) {

            // set priority
            $this->priority = $panel_priority;

            // we have sections? add panels
            if (isset($value['sections'])) {

                // add new panel
                $wp_customize->add_panel($value['name'], array(
                    'title'       => $value['title'],
                    'priority'    => (isset($value['priority'])) ? $value['priority'] : $panel_priority,
                    'description' => (isset($value['description'])) ? $value['description'] : '',
                ));

                $this->add_section($wp_customize, $value, $value['name']);

            } else {
                // just add section

                $this->add_section($wp_customize, $value);

            }

            $panel_priority++;

        } // end foreach $this->options

    }

    /**
     * Add new customize sections.
     *
     * @since 2.0.0
     * @access public
     * @param object  $wp_customize
     * @param mixed   $value
     * @param boolean $panel
     */
    public function add_section($wp_customize, $value, $panel = false)
    {

        // set sections and priority
        $section_priority = ($panel) ? 1 : $this->priority;
        $sections         = ($panel) ? $value['sections'] : array('sections' => $value);

        // get customize sections
        foreach ($sections as $section) {

            // add section
            $wp_customize->add_section($section['name'], array(
                'title'       => $section['title'],
                'priority'    => (isset($section['priority'])) ? $section['priority'] : $section_priority,
                'description' => (isset($section['description'])) ? $section['description'] : '',
                'panel'       => ($panel) ? $panel : '',
            ));

            // set section setting priority
            $setting_priority = 1;

            foreach ($section['settings'] as $setting) {

                $setting_name = $this->unique_options_id . '[' . $setting['name'] . ']';

                // add setting
                $wp_customize->add_setting($setting_name,
                    wp_parse_args($setting, array(
                        'type'              => 'option',
                        'capability'        => 'edit_theme_options',
                        'sanitize_callback' => Sanitize::clean(),
                    )
                    )
                );

                // add control
                $control_args = wp_parse_args($setting['control'], array(
                    'unique'   => $this->unique_options_id,
                    'section'  => $section['name'],
                    'settings' => $setting_name,
                    'priority' => $setting_priority,
                ));

                // we have our custom control type? use it
                if ($control_args['type'] == 'cs_field') {

                    // get our custom controls
                    $wp_customize->add_control(new Customize($wp_customize, $setting['name'], $control_args));

                } else {
                    // get default WP controls

                    $wp_controls = array('color', 'upload', 'image', 'media');
                    $call_class  = 'WP_Customize_' . ucfirst($control_args['type']) . '_Control';

                    if (in_array($control_args['type'], $wp_controls) && class_exists($call_class)) {

                        $wp_customize->add_control(new $call_class($wp_customize, $setting['name'], $control_args));

                    } else {

                        $wp_customize->add_control($setting['name'], $control_args);

                    }

                } // end check $control_args['type']

                $setting_priority++;

            } // end foreach $section['settings']

            $section_priority++;

        } // end foreach $sections

    }

}
