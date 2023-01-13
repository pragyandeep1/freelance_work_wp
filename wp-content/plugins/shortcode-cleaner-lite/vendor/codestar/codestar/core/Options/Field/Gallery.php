<?php

namespace Codestar\Options\Field;

use Codestar\Options\Field;

/**
 * Field: Gallery
 *
 * output gallery field content.
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
class Gallery extends Field
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
        $add    = (!empty($this->field['add_title'])) ? $this->field['add_title'] : esc_html('Add Gallery');
        $edit   = (!empty($this->field['edit_title'])) ? $this->field['edit_title'] : esc_html('Edit Gallery');
        $clear  = (!empty($this->field['clear_title'])) ? $this->field['clear_title'] : esc_html('Clear');
        $hidden = (empty($value)) ? ' hidden' : '';

        echo '<ul>';

        if (!empty($value)) {

            $values = explode(',', $value);
            foreach ($values as $id) {
                $attachment = wp_get_attachment_image_src($id, 'thumbnail');
                echo '<li><img src="' . $attachment[0] . '" alt="" /></li>';
            }

        }

        echo '</ul>';
        echo '<a href="#" class="button button-primary cs-add">' . $add . '</a>';
        echo '<a href="#" class="button cs-edit' . $hidden . '">' . $edit . '</a>';
        echo '<a href="#" class="button cs-warning-primary cs-remove' . $hidden . '">' . $clear . '</a>';
        echo '<input type="text" name="' . $this->get_element_name() . '" value="' . $value . '"' . $this->get_element_css_class() . $this->get_element_attributes() . '/>';

        echo $this->get_content_after_element();

    }

}
