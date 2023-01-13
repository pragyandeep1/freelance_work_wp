<?php

namespace Codestar\Options\Field;

use Codestar\Options\Field;

/**
 * Field: Icon
 *
 * output icon field content.
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
class Icon extends Field
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

        $value  = wp_filter_nohtml_kses($this->get_element_value());
        $hidden = (empty($value)) ? ' hidden' : '';

        $add_icon_button_title    = (isset($this->field['add_icon_button_title'])) ? $this->field['add_icon_button_title'] : esc_html('Add Icon');
        $remove_icon_button_title = (isset($this->field['remove_icon_button_title'])) ? $this->field['remove_icon_button_title'] : esc_html('Remove Icon');

        echo '<div class="cs-icon-select">';
        echo '<span class="cs-icon-preview' . $hidden . '"><i class="' . $value . '"></i></span>';
        echo '<a href="#" class="button button-primary cs-icon-add">' . $add_icon_button_title . '</a>';
        echo '<a href="#" class="button cs-warning-primary cs-icon-remove' . $hidden . '">' . $remove_icon_button_title . '</a>';
        echo '<input type="text" name="' . $this->get_element_name() . '" value="' . $value . '"' . $this->get_element_css_class('cs-icon-value') . $this->get_element_attributes() . ' />';
        echo '</div>';

        echo $this->get_content_after_element();

    }

}
