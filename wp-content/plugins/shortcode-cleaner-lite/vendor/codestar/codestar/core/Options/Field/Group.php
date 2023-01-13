<?php

namespace Codestar\Options\Field;

use Codestar\Helper\Helper;
use Codestar\Module\Language;
use Codestar\Options\Field;
use Codestar\Options\Options;

/**
 * Field: Group
 *
 * output group field content.
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
class Group extends Field
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

        $fields              = array_values($this->field['fields']);
        $last_id             = (is_array($this->value)) ? max(array_keys($this->value)) : 0;
        $acc_title           = (isset($this->field['accordion_title'])) ? $this->field['accordion_title'] : esc_html('Adding');
        $remove_button_title = (isset($this->field['remove_button_title'])) ? $this->field['remove_button_title'] : esc_html('Remove');
        $field_title         = (isset($fields[0]['title'])) ? $fields[0]['title'] : $fields[1]['title'];
        $field_id            = (isset($fields[0]['id'])) ? $fields[0]['id'] : $fields[1]['id'];
        $el_class            = (isset($this->field['title'])) ? sanitize_title($field_title) : 'no-title';
        $search_id           = Helper::array_search($fields, 'id', $acc_title);
        $hide_field_title    = (isset($this->field['hide_field_title'])) ? $this->field['hide_field_title'] : false;

        if (!empty($search_id)) {

            $acc_title = (isset($search_id[0]['title'])) ? $search_id[0]['title'] : $acc_title;
            $field_id  = (isset($search_id[0]['id'])) ? $search_id[0]['id'] : $field_id;

        }

        echo '<div class="cs-group cs-group-' . $el_class . '-adding hidden">';

        echo '<h4 class="cs-group-title">' . $acc_title . '</h4>';
        echo '<div class="cs-group-content">';

        foreach ($fields as $field) {

            $field['sub']  = true;
            $unique        = $this->unique . '[_nonce][' . $this->field['id'] . '][' . $last_id . ']';
            $field_default = (isset($field['default'])) ? $field['default'] : '';
            echo Options::add_field($field, $field_default, $unique);

        }

        echo '<div class="cs-element cs-text-right cs-remove"><a href="#" class="button cs-warning-primary cs-remove-group">' . $remove_button_title . '</a></div>';
        echo '</div>';

        echo '</div>';

        echo '<div class="cs-groups cs-accordion">';

        if (!empty($this->value)) {

            foreach ($this->value as $key => $value) {

                $title = (isset($this->value[$key][$field_id])) ? $this->value[$key][$field_id] : '';

                if (is_array($title) && isset($this->multilang)) {

                    $lang  = Language::get_defaults();
                    $title = $title[$lang['current']];
                    $title = is_array($title) ? $title[0] : $title;

                }

                $field_title = (!empty($search_id)) ? $acc_title : $field_title;

                echo '<div class="cs-group cs-group-' . $el_class . '-' . ($key + 1) . '">';
                if ($hide_field_title === true) {
                    echo '<h4 class="cs-group-title">' . $title . '</h4>';
                } else {
                    echo '<h4 class="cs-group-title">' . $field_title . ': ' . $title . '</h4>';
                }
                echo '<div class="cs-group-content">';

                foreach ($fields as $field) {
                    $field['sub'] = true;
                    $unique       = $this->unique . '[' . $this->field['id'] . '][' . $key . ']';
                    $value        = (isset($field['id']) && isset($this->value[$key][$field['id']])) ? $this->value[$key][$field['id']] : '';
                    echo Options::add_field($field, $value, $unique);
                }

                echo '<div class="cs-element cs-text-right cs-remove"><a href="#" class="button cs-warning-primary cs-remove-group">' . $remove_button_title . '</a></div>';
                echo '</div>';
                echo '</div>';

            }

        }

        echo '</div>';

        echo '<a href="#" class="button button-primary cs-add-group">' . $this->field['button_title'] . '</a>';

        echo $this->get_content_after_element();

    }

}
