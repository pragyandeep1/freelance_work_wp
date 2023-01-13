<?php

namespace Codestar\Options\Field;

use Codestar\Options\Field;
use Codestar\Options\Options;

/**
 * Field: Fieldset
 *
 * output fieldset field content.
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
class Fieldset extends Field
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

        echo '<div class="cs-inner">';

        foreach ($this->field['fields'] as $field) {

            $field_id      = (isset($field['id'])) ? $field['id'] : '';
            $field_default = (isset($field['default'])) ? $field['default'] : '';
            $field_value   = (isset($this->value[$field_id])) ? $this->value[$field_id] : $field_default;
            $unique_id     = $this->unique . '[' . $this->field['id'] . ']';

            if (!empty($this->field['un_array'])) {

                echo Options::add_field($field, cs_get_option($field_id), $this->unique);

            } else {

                echo Options::add_field($field, $field_value, $unique_id);

            }

        }

        echo '</div>';

        echo $this->get_content_after_element();

    }

}
