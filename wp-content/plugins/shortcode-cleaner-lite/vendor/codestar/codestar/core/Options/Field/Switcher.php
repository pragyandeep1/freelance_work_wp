<?php

namespace Codestar\Options\Field;

use Codestar\Options\Field;

/**
 * Field: Switcher
 *
 * output switcher field content.
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
class Switcher extends Field
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

        $label     = (isset($this->field['label'])) ? '<div class="cs-text-desc">' . $this->field['label'] . '</div>' : '';
        $on_title  = (isset($this->field['on_title'])) ? $this->field['on_title'] : esc_html('ON');
        $off_title = (isset($this->field['off_title'])) ? $this->field['off_title'] : esc_html('OFF');

        echo '<label><input type="checkbox" name="' . $this->get_element_name() . '" value="1"' . $this->get_element_css_class() . $this->get_element_attributes() . checked($this->get_element_value(), 1, false) . '/><em data-on="' . $on_title . '" data-off="' . $off_title . '"></em><span></span></label>' . $label;

        echo $this->get_content_after_element();

    }

}
