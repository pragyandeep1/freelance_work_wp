<?php

namespace Codestar\Options\Field;

use Codestar\Module\Datastore;
use Codestar\Options\Field;

/**
 * Field: Textarea
 *
 * output textarea field content.
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
class Textarea extends Field
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

        // get shortcodes generator
        echo $this->shortcode_generator();

        echo '<textarea name="' . $this->get_element_name() . '"' . $this->get_element_css_class() . $this->get_element_attributes() . '>' . $this->get_element_value() . '</textarea>';

        echo $this->get_content_after_element();

    }

    /**
     * Output shortcodes generator button content.
     *
     * @since 2.0.0
     * @access public
     * @return string
     */
    public function shortcode_generator()
    {

        if (isset($this->field['shortcode'])) {

            // get all available shortcoder buttons
            $shortcoder_buttons = Datastore::get_container('shortcoder');

            // add each shortcoder button
            foreach ($shortcoder_buttons as $id => $data) {

                echo '<a href="#" class="button button-primary cs-shortcode cs-shortcode-textarea" data-id="' . $id . '">' . $data['settings']['button_title'] . '</a>';

            }

        }

    }

}
