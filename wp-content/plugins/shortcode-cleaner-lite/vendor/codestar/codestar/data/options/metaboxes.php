<?php

namespace Codestar;

/**
 * Get all available default metaboxes options.
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
 * @param  string $context
 * @return array
 */
function cs_get_default_metaboxes_options($context = 'normal')
{

    // save some available normal default options
    $normal_options = array(

        // begin: a section
        array(
            'name'   => 'section_13',
            'title'  => 'Section 1',
            'icon'   => 'fas fa-cog',

            // begin: fields
            'fields' => array(
                // begin: a field
                array(
                    'id'    => 'section_1_text3',
                    'type'  => 'text',
                    'title' => 'Text Field',
                ),
                // end: a field
                array(
                    'id'    => 'section_1_textarea3',
                    'type'  => 'textarea',
                    'title' => 'Textarea Field',
                ),
                array(
                    'id'    => 'section_1_upload3',
                    'type'  => 'upload',
                    'title' => 'Upload Field',
                ),
                array(
                    'id'    => 'section_1_switcher3',
                    'type'  => 'switcher',
                    'title' => 'Switcher Field',
                    'label' => 'Yes, Please do it.',
                ),
            ), // end: fields

        ), // end: a section

        // begin: a section
        array(
            'name'   => 'section_2',
            'title'  => 'Section 2',
            'icon'   => 'fas fa-tint',

            'fields' => array(
                array(
                    'id'      => 'section_2_color_picker_13',
                    'type'    => 'color_picker',
                    'title'   => 'Color Picker 1',
                    'default' => '#2ecc71',
                ),
                array(
                    'id'      => 'section_2_color_picker_23',
                    'type'    => 'color_picker',
                    'title'   => 'Color Picker 2',
                    'default' => '#3498db',
                ),
                array(
                    'id'      => 'section_2_color_picker_33',
                    'type'    => 'color_picker',
                    'title'   => 'Color Picker 3',
                    'default' => '#9b59b6',
                ),
                array(
                    'id'      => 'section_2_color_picker_43',
                    'type'    => 'color_picker',
                    'title'   => 'Color Picker 4',
                    'default' => '#34495e',
                ),
                array(
                    'id'      => 'section_2_color_picker_53',
                    'type'    => 'color_picker',
                    'title'   => 'Color Picker 5',
                    'default' => '#e67e22',
                ),
            ),

        ),
        // end: a section

    );

    // save some available side default options
    $side_options = array(

        array(
            'name'   => 'section_3',

            'fields' => array(
                array(
                    'id'      => 'section_3_image_select',
                    'type'    => 'image_select',
                    'options' => array(
                        'value-1' => 'https://raw.githubusercontent.com/mohamdio/data-images-placeholder/master/65x65-2ecc71.png',
                        'value-2' => 'https://raw.githubusercontent.com/mohamdio/data-images-placeholder/master/65x65-e74c3c.png',
                        'value-3' => 'https://raw.githubusercontent.com/mohamdio/data-images-placeholder/master/65x65-3498db.png',
                    ),
                    'default' => 'value-2',
                ),
                array(
                    'id'         => 'section_3_text',
                    'type'       => 'text',
                    'attributes' => array(
                        'placeholder' => 'do stuff',
                    ),
                ),
                array(
                    'id'      => 'section_3_switcher',
                    'type'    => 'switcher',
                    'label'   => 'Are you sure ?',
                    'default' => true,
                ),
            ),

        ),

    );

    // we need 'side' options? return it
    if ($context === 'side') {
        return $side_options;
    }

    // return all available default options
    return $normal_options;

}
