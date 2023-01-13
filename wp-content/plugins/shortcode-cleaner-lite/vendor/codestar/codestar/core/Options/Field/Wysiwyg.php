<?php

namespace Codestar\Options\Field;

use Codestar\Options\Field;

/**
 * Field: Wysiwyg
 *
 * output wysiwyg field content.
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
class Wysiwyg extends Field
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

        $defaults = array(
            'textarea_rows' => 10,
            'textarea_name' => $this->get_element_name(),
        );

        $settings = (!empty($this->field['settings'])) ? $this->field['settings'] : array();
        $settings = wp_parse_args($settings, $defaults);

        $field_id    = (!empty($this->field['id'])) ? $this->field['id'] : '';
        $field_value = $this->get_element_value();

        wp_editor($field_value, $field_id, $settings);

        echo $this->get_content_after_element();

    }

}
