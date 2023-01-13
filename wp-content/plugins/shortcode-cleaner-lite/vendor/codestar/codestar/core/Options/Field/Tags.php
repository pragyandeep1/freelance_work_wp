<?php

namespace Codestar\Options\Field;

use Codestar\Options\Field;

/**
 * Field: Tags
 *
 * output tags field content.
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
class Tags extends Field
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

        $add_tags_button_title = (isset($this->field['add_tags_button_title'])) ? $this->field['add_tags_button_title'] : esc_html('Add');

        echo '<input type="text" class="cs-tags-input"' . $this->get_element_css_class() . $this->get_element_attributes() . '/>';

        echo '<input type="button" class="button cs-tags-button" value="' . $add_tags_button_title . '" />';

        echo '<input type="hidden" class="cs-tags-value" name="' . $this->get_element_name() . '" value="' . wp_filter_nohtml_kses($this->get_element_value()) . '"/>';

        echo $this->get_content_after_element();

        echo '<div class="cs-tags-list"><ul></ul></div>';

    }

}
