<?php

namespace Codestar\Options\Field;

use Codestar\Config;
use Codestar\Module\Data;
use Codestar\Options\Field;

/**
 * Field: Typography
 *
 * output typography field content.
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
class Typography extends Field
{

    /**
     * Setup field settings.
     *
     * @since 2.0.0
     * @access public
     * @param array  $field  field settings
     * @param string $value  field value
     * @param string $unique field Unique options id (name) inside database
     */
    public function __construct($field, $value = '', $unique = '')
    {
        parent::__construct($field, $value, $unique);
    }

    /**
     * Output field element content.
     *
     * @since 2.0.0
     * @access public
     * @return string
     */
    public function output()
    {

        echo $this->get_content_before_element();

        $defaults_value = array(
            'family'  => 'Arial',
            'variant' => 'regular',
            'font'    => 'websafe',
        );

        // filter @hook add new or override default websafe fonts variants.
        $default_variants = apply_filters(Config::PREFIX . 'websafe_fonts_variants', array(
            'regular',
            'italic',
            '700',
            '700italic',
            'inherit',
        ));

        // filter @hook add new or override default websafe fonts.
        $websafe_fonts = apply_filters(Config::PREFIX . 'websafe_fonts', array(
            'Arial',
            'Arial Black',
            'Comic Sans MS',
            'Impact',
            'Lucida Sans Unicode',
            'Tahoma',
            'Trebuchet MS',
            'Verdana',
            'Courier New',
            'Lucida Console',
            'Georgia, serif',
            'Palatino Linotype',
            'Times New Roman',
        ));

        $value                = wp_parse_args($this->get_element_value(), $defaults_value);
        $family_value         = $value['family'];
        $variant_value        = $value['variant'];
        $is_variant           = (isset($this->field['variant']) && $this->field['variant'] === false) ? false : true;
        $is_chosen            = (isset($this->field['chosen']) && $this->field['chosen'] === false) ? '' : 'chosen ';
        $chosen_rtl           = (is_rtl() && !empty($is_chosen)) ? 'chosen-rtl ' : '';
        $default_fonts_title  = (isset($this->field['default_fonts_title'])) ? $this->field['default_fonts_title'] : esc_html('Web Safe Fonts');
        $google_fonts_title   = (isset($this->field['google_fonts_title'])) ? $this->field['google_fonts_title'] : esc_html('Google Fonts');
        $arabic_fonts_title   = (isset($this->field['arabic_fonts_title'])) ? $this->field['arabic_fonts_title'] : esc_html('Arabic Fonts');
        $load_file_error_desc = (isset($this->field['load_file_error_desc'])) ? $this->field['load_file_error_desc'] : esc_html('Error! Can not load json file.');

        echo '<label class="cs-typography-family">';
        echo '<select name="' . $this->get_element_name('[family]') . '" class="' . $is_chosen . $chosen_rtl . 'cs-typo-family" data-atts="family"' . $this->get_element_attributes() . '>';

        // action @hook add new typography family for typography field type.
        do_action(Config::PREFIX . 'typography_family', $family_value, $this);

        echo '<optgroup label="' . $default_fonts_title . '">';
        foreach ($websafe_fonts as $websafe_value) {
            echo '<option value="' . $websafe_value . '" data-variants="' . implode('|', $default_variants) . '" data-type="websafe"' . selected($websafe_value, $family_value, true) . '>' . $websafe_value . '</option>';
        }
        echo '</optgroup>';

        /**
         * Get fonts json data files.
         * filter @hook add new or override fonts json files.
         */
        $fonts_files = apply_filters(
            Config::PREFIX . 'add_fonts_json',
            array(
                /**
                 * For know how to use google fonts,
                 * @see https://fonts.google.com
                 */
                'google' => Config::get_dir() . '/data/fonts/google.json',
                /**
                 * For know how to use arabic fonts from fontface.me,
                 * @see https://www.fontface.me/font/list
                 */
                'arabic' => Config::get_dir() . '/data/fonts/arabic.json',
            )
        );

        if (!empty($fonts_files)) {

            foreach ($fonts_files as $type => $data_file) {

                // get data object content
                $object = Data::get_data($data_file);

                // we have data object content?
                if (is_object($object)) {

                    // get google fonts
                    if ($type === 'google') {

                        $googlefonts = array();

                        foreach ($object->items as $key => $font) {
                            $googlefonts[$font->family] = $font->variants;
                        }

                        $is_google = (array_key_exists($family_value, $googlefonts)) ? true : false;

                        echo '<optgroup label="' . $google_fonts_title . '">';
                        foreach ($googlefonts as $google_key => $google_value) {
                            echo '<option value="' . $google_key . '" data-variants="' . implode('|', $google_value) . '" data-type="google"' . selected($google_key, $family_value, true) . '>' . $google_key . '</option>';
                        }
                        echo '</optgroup>';

                    } // end check google fonts

                    // get arabic fonts
                    if ($type === 'arabic') {

                        $arabicfonts = array();

                        foreach ($object->items as $key => $font) {
                            $arabicfonts[$font->permalink] = $font->name;
                        }

                        echo '<optgroup label="' . $arabic_fonts_title . '">';
                        foreach ($arabicfonts as $arabic_key => $arabic_value) {
                            echo '<option value="' . $arabic_key . '" data-variants="' . implode('|', $default_variants) . '" data-type="arabic"' . selected($arabic_key, $family_value, true) . '>' . $arabic_value . '</option>';
                        }
                        echo '</optgroup>';

                    } // end check arabic fonts

                } else {

                    echo $load_file_error_desc;

                }

            } // end foreach $fonts_files

        } // end check $fonts_files

        echo '</select>';
        echo '</label>';

        if (!empty($is_variant)) {

            $variants = ($is_google) ? $googlefonts[$family_value] : $default_variants;
            $variants = ($value['font'] === 'google' || $value['font'] === 'websafe') ? $variants : array('regular');

            echo '<label class="cs-typography-variant">';
            echo '<select name="' . $this->get_element_name('[variant]') . '" class="' . $is_chosen . $chosen_rtl . 'cs-typo-variant" data-atts="variant">';
            foreach ($variants as $variant) {
                echo '<option value="' . $variant . '"' . $this->get_element_checked($variant_value, $variant, 'selected') . '>' . $variant . '</option>';
            }
            echo '</select>';
            echo '</label>';

        }

        echo '<input type="text" name="' . $this->get_element_name('[font]') . '" class="cs-typo-font hidden" data-atts="font" value="' . wp_filter_nohtml_kses($value['font']) . '" />';

        echo $this->get_content_after_element();

    }

}
