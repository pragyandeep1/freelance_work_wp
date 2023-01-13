<?php

namespace Codestar\Options\Field;

use Codestar\Options\Field;

/**
 * Field: Select
 *
 * output select field content.
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
class Select extends Field
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

        if (isset($this->field['options'])) {

            $options    = $this->field['options'];
            $class      = $this->get_element_css_class();
            $options    = (is_array($options)) ? $options : array_filter($this->get_element_data($options));
            $extra_name = (isset($this->field['attributes']['multiple'])) ? '[]' : '';
            $chosen_rtl = (is_rtl() && strpos($class, 'chosen')) ? 'chosen-rtl' : '';

            echo '<select name="' . $this->get_element_name($extra_name) . '"' . $this->get_element_css_class($chosen_rtl) . $this->get_element_attributes() . '>';

            echo (isset($this->field['default_option'])) ? '<option value="">' . $this->field['default_option'] . '</option>' : '';

            if (!empty($options)) {
                foreach ($options as $key => $value) {
                    echo '<option value="' . $key . '" ' . $this->get_element_checked($this->get_element_value(), $key, 'selected') . '>' . $value . '</option>';
                }
            }

            echo '</select>';

        }

        echo $this->get_content_after_element();

    }

}
