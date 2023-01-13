<?php

namespace Codestar\Options;

use Codestar\Helper\Helper;
use Codestar\Module\Language;

/**
 * Base options class for available fields.
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
class Options
{

    /**
     * Add new field element option.
     *
     * @since 2.0.0
     * @access public
     * @param array  $field  field settings
     * @param string $value  field value
     * @param string $unique field Unique options id (name) inside database
     */
    public static function add_field($field = array(), $value = '', $unique = '')
    {

        // save field element output
        $output = '';

        // save field element dependcy
        $depend = '';

        // get sub field element
        $sub = (isset($field['sub'])) ? 'sub-' : '';

        // get field option unique id
        $unique = (isset($unique)) ? $unique : '';

        // get language defaults
        $languages = Language::get_defaults();

        /**
         * Get field type class name OOP.
         * example: text > Text, color_picker > ColorPicker ..etc
         */
        $class_field_name = Helper::get_field_type_class($field['type']);

        /**
         * Get main field ::class OOP
         * example: text > Text, color_picker > ColorPicker ..etc
         */
        $class = '\Codestar\Options\Field\\' . $class_field_name;

        // get wrapper css class for field element
        $wrap_class = (isset($field['wrap_class'])) ? ' ' . $field['wrap_class'] : '';

        // get field element css class
        $el_class = (isset($field['title'])) ? sanitize_title($field['title']) : 'no-title';

        // get hidden field element
        $hidden = (isset($field['show_only_language']) && ($field['show_only_language'] != $languages['current'])) ? ' hidden' : '';

        // get field dependency
        $is_pseudo = (isset($field['pseudo'])) ? ' cs-pseudo-field' : '';
        if (isset($field['dependency'])) {
            $hidden = ' hidden';
            $depend .= ' data-' . $sub . 'controller="' . $field['dependency'][0] . '"';
            $depend .= ' data-' . $sub . 'condition="' . $field['dependency'][1] . '"';
            $depend .= ' data-' . $sub . 'value="' . $field['dependency'][2] . '"';
        }

        // html wrapper for field element
        $output .= '<div class="cs-element cs-element-' . $el_class . ' cs-field-' . $field['type'] . $is_pseudo . $wrap_class . $hidden . '"' . $depend . '>';

        // we have title with description? show it
        if (isset($field['title'])) {

            // we have description for field title? show it
            $field_desc = (isset($field['desc'])) ? '<p class="cs-text-desc">' . $field['desc'] . '</p>' : '';
            $output .= '<div class="cs-title"><h4>' . $field['title'] . '</h4>' . $field_desc . '</div>';

        }

        // we have title? show it's html before wrapper
        $output .= (isset($field['title'])) ? '<div class="cs-fieldset">' : '';

        // get field element value
        $value = (!isset($value) && isset($field['default'])) ? $field['default'] : $value;
        $value = (isset($field['value'])) ? $field['value'] : $value;

        // we have field class? so we can create and instantiate new Field\Type
        if (class_exists($class)) {

            ob_start();

            // instantiate new Field\Type
            $element = new $class($field, $value, $unique);
            // then show field element output content
            $element->output();

            $output .= ob_get_clean();

        } else {
            // we don't have this field class? show notice

            /**
             * @todo should make settings option for override this text
             */
            $output .= '<p>' . esc_html('This field class is not available!') . '</p>';

        }

        // we have title? show it's html after wrapper
        $output .= (isset($field['title'])) ? '</div>' : '';

        // just clear html content
        $output .= '<div class="clear"></div>';
        $output .= '</div>';

        // finally return all this field element content
        return $output;

    }

    /**
     * Get one option name or all options from database.
     *
     * example:
     * get all options: self::get_option('unique_options_id');
     * get one option: self::get_option('unique_options_id', 'option_name');
     *
     * @since 2.0.0
     * @access public
     * @param  string $unique_options_id unique options id (name)
     * @param  string $option_name       option name inside unique options id
     * @param  string $default           default option value
     * @return mixed
     */
    public static function get_option(
        $unique_options_id = '', $option_name = '', $default = ''
    ) {

        // if something wrong? go back
        if (empty($unique_options_id)) {
            return;
        }

        // get options from database
        $options = get_option('_' . $unique_options_id);

        // we don't have any options? go back
        if (!$options) {
            return;
        }

        // we pass option name?
        if (!empty($option_name)) {

            // we have option name?
            if (isset($option_name) && !empty($options[$option_name])) {

                return $options[$option_name];

            } else {

                return (!empty($default)) ? $default : null;

            }

        }

        // get all available options
        return $options;

    }

    /**
     * Get multi language option.
     *
     * example: self::get_multilang_option('unique_options_id', 'option_name');
     *
     * @since 2.0.0
     * @access public
     * @param  string $unique_options_id unique options id (name)
     * @param  string $option_name       option name inside unique options id
     * @param  string $default           default option value
     * @return mixed
     */
    public static function get_multilang_option(
        $unique_options_id = '', $option_name = '', $default = ''
    ) {

        // get option value
        $value = self::get_option($unique_options_id, $option_name, $default);

        // get language defaults
        $languages = Language::get_defaults();
        $default   = $languages['default'];
        $current   = $languages['current'];

        // get our option value
        if (is_array($value) && is_array($languages) && isset($value[$current])) {

            return $value[$current];

        } else if ($default != $current) {

            return '';

        }

        // return final value
        return $value;

    }

    /**
     * Get multi language value.
     *
     * @since 2.0.0
     * @access public
     * @param  string $value
     * @param  string $default
     * @return mixed
     */
    public static function get_multilang_value($value = '', $default = '')
    {

        // get language defaults
        $languages = Language::get_defaults();
        $default   = $languages['default'];
        $current   = $languages['current'];

        // get our value
        if (is_array($value) && is_array($languages) && isset($value[$current])) {

            return $value[$current];

        } else if ($default != $current) {

            return '';

        }

        // return final value
        return $value;

    }

    /**
     * Set option id (name) value.
     *
     * example:
     * set option: self::set_option('unique_options_id', 'option_name', 'new_value');
     *
     * @since 2.0.0
     * @access public
     * @param  string $unique_options_id unique options id (name)
     * @param  string $option_name       option name inside unique options id
     * @param  string $new_value         new option value
     * @return mixed
     */
    public static function set_option(
        $unique_options_id = '', $option_name = '', $new_value = ''
    ) {

        // if something wrong? go back
        if (empty($unique_options_id)) {
            return;
        }

        // get options from database
        $options = get_option('_' . $unique_options_id);

        // we don't have any options? go back
        if (!$options) {
            return;
        }

        // we pass option name?
        if (!empty($option_name)) {

            // we have option name?
            if (isset($option_name)) {

                /**
                 * @todo should sanitize and validate new value before update it.
                 */
                $options[$option_name] = $new_value;
                update_option('_' . $unique_options_id, $options);

            } else {

                return false;

            }

        }

        // we don't set option value
        return false;

    }

}
