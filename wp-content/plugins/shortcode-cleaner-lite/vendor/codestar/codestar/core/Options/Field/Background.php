<?php

namespace Codestar\Options\Field;

use Codestar\Options\Field;
use Codestar\Options\Options;

/**
 * Field: Background
 *
 * output background field content.
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
class Background extends Field
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

        // set default value
        $value_defaults = array(
            'image'      => '',
            'repeat'     => '',
            'position'   => '',
            'attachment' => '',
            'size'       => '',
            'color'      => '',
        );

        // get custom value
        $this->value = wp_parse_args($this->get_element_value(), $value_defaults);

        // we have field settings? use it
        if (isset($this->field['settings'])) {
            extract($this->field['settings']);
        }

        // get settings options
        $upload_type  = (isset($upload_type)) ? $upload_type : 'image';
        $button_title = (isset($button_title)) ? $button_title : esc_html('Upload');
        $frame_title  = (isset($frame_title)) ? $frame_title : esc_html('Upload');
        $insert_title = (isset($insert_title)) ? $insert_title : esc_html('Use Image');

        // start HTML content
        echo '<div class="cs-field-upload">';
        echo '<input type="text" name="' . $this->get_element_name('[image]') . '" value="' . wp_filter_nohtml_kses($this->value['image']) . '"' . $this->get_element_css_class() . $this->get_element_attributes() . '/>';
        echo '<a href="#" class="button cs-add" data-frame-title="' . $frame_title . '" data-upload-type="' . $upload_type . '" data-insert-title="' . $insert_title . '">' . $button_title . '</a>';
        echo '</div>';

        // add background attributes
        echo '<fieldset>';

        echo Options::add_field(array(
            'pseudo'     => true,
            'type'       => 'select',
            'name'       => $this->get_element_name('[repeat]'),
            'options'    => array(
                ''          => 'repeat',
                'repeat-x'  => 'repeat-x',
                'repeat-y'  => 'repeat-y',
                'no-repeat' => 'no-repeat',
                'inherit'   => 'inherit',
            ),
            'attributes' => array(
                'data-atts' => 'repeat',
            ),
            'value'      => $this->value['repeat'],
        ));

        echo Options::add_field(array(
            'pseudo'     => true,
            'type'       => 'select',
            'name'       => $this->get_element_name('[position]'),
            'options'    => array(
                ''              => 'left top',
                'left center'   => 'left center',
                'left bottom'   => 'left bottom',
                'right top'     => 'right top',
                'right center'  => 'right center',
                'right bottom'  => 'right bottom',
                'center top'    => 'center top',
                'center center' => 'center center',
                'center bottom' => 'center bottom',
            ),
            'attributes' => array(
                'data-atts' => 'position',
            ),
            'value'      => $this->value['position'],
        ));

        echo Options::add_field(array(
            'pseudo'     => true,
            'type'       => 'select',
            'name'       => $this->get_element_name('[attachment]'),
            'options'    => array(
                ''      => 'scroll',
                'fixed' => 'fixed',
            ),
            'attributes' => array(
                'data-atts' => 'attachment',
            ),
            'value'      => $this->value['attachment'],
        ));

        echo Options::add_field(array(
            'pseudo'     => true,
            'type'       => 'select',
            'name'       => $this->get_element_name('[size]'),
            'options'    => array(
                ''        => 'size',
                'cover'   => 'cover',
                'contain' => 'contain',
                'inherit' => 'inherit',
                'initial' => 'initial',
            ),
            'attributes' => array(
                'data-atts' => 'size',
            ),
            'value'      => $this->value['size'],
        ));

        echo Options::add_field(array(
            'pseudo'     => true,
            'id'         => $this->field['id'] . '_color',
            'type'       => 'color_picker',
            'name'       => $this->get_element_name('[color]'),
            'attributes' => array(
                'data-atts' => 'bgcolor',
            ),
            'value'      => $this->value['color'],
            'default'    => (isset($this->field['default']['color'])) ? $this->field['default']['color'] : '',
            'rgba'       => (isset($this->field['rgba']) && $this->field['rgba'] === false) ? false : '',
        ));

        echo '</fieldset>';

        echo $this->get_content_after_element();

    }

}
