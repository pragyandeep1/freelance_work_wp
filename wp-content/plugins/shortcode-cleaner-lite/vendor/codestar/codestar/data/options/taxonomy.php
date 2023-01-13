<?php

namespace Codestar;

/**
 * Get all available default taxonomy options.
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
 * @return array
 */
function cs_get_default_taxonomy_options()
{

    // save some available default options
    $options = array(

        array(
            'id'    => 'section_1_text',
            'type'  => 'text',
            'title' => 'Text Field',
        ),
        array(
            'id'    => 'section_1_textarea',
            'type'  => 'textarea',
            'title' => 'Textarea Field',
        ),

    );

    // return all available default options
    return $options;

}
