<?php

namespace Codestar\Options\Field;

use Codestar\Options\Field;

/**
 * Field: Sorter
 *
 * output sorter field content.
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
class Sorter extends Field
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

        $value          = $this->get_element_value();
        $value          = (!empty($value)) ? $value : $this->field['default'];
        $enabled        = (!empty($value['enabled'])) ? $value['enabled'] : array();
        $disabled       = (!empty($value['disabled'])) ? $value['disabled'] : array();
        $enabled_title  = (isset($this->field['enabled_title'])) ? $this->field['enabled_title'] : esc_html('Enabled Modules');
        $disabled_title = (isset($this->field['disabled_title'])) ? $this->field['disabled_title'] : esc_html('Disabled Modules');

        echo '<div class="cs-modules">';
        echo '<h3>' . $enabled_title . '</h3>';
        echo '<ul class="cs-enabled">';
        if (!empty($enabled)) {
            foreach ($enabled as $en_id => $en_name) {
                echo '<li><input type="hidden" name="' . $this->get_element_name('[enabled][' . $en_id . ']') . '" value="' . $en_name . '"/><label>' . $en_name . '</label></li>';
            }
        }
        echo '</ul>';
        echo '</div>';

        echo '<div class="cs-modules">';
        echo '<h3>' . $disabled_title . '</h3>';
        echo '<ul class="cs-disabled">';
        if (!empty($disabled)) {
            foreach ($disabled as $dis_id => $dis_name) {
                echo '<li><input type="hidden" name="' . $this->get_element_name('[disabled][' . $dis_id . ']') . '" value="' . $dis_name . '"/><label>' . $dis_name . '</label></li>';
            }
        }
        echo '</ul>';
        echo '</div>';
        echo '<div class="clear"></div>';

        echo $this->get_content_after_element();

    }

}
