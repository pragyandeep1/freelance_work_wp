<?php

namespace Codestar\Options\Field;

use Codestar\Options\Field;

/**
 * Field: ColorPicker
 *
 * output color_picker field content.
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
class ColorPicker extends Field
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

        echo '<input type="text" name="' . $this->get_element_name() . '" value="' . wp_filter_nohtml_kses($this->get_element_value()) . '"' . $this->get_element_css_class('cs-field-color-picker') . $this->get_element_attributes($this->extra_attributes()) . '/>';

        echo $this->get_content_after_element();

    }

    /**
     * Output extra element attributes content.
     *
     * @since 2.0.0
     * @access public
     * @return string
     */
    public function extra_attributes()
    {

        $atts = array();

        if (isset($this->field['id'])) {
            $atts['data-depend-id'] = $this->field['id'];
        }

        if (isset($this->field['rgba']) && $this->field['rgba'] === false) {
            $atts['data-rgba'] = 'false';
        }

        if (isset($this->field['default'])) {
            $atts['data-default-color'] = $this->field['default'];
        }

        return $atts;

    }

}
