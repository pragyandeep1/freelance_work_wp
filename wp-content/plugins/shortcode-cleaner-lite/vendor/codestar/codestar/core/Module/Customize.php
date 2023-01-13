<?php

namespace Codestar\Module;

use Codestar\Options\Options;

/**
 * WP Customize custom controls.
 *
 * extends default WP core class WP_Customize_Control.
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
class Customize extends \WP_Customize_Control
{

    /**
     * Custom control Type.
     *
     * @since 2.0.0
     * @access public
     * @var string
     */
    public $type = 'cs_field';
    /**
     * Unique options id (name) inside database for custom control.
     *
     * @since 2.0.0
     * @access protected
     * @var string
     */
    protected $unique;
    /**
     * Available options for each custom controls.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $options = array();

    /**
     * Render the control's content.
     *
     * default WP core method from WP_Customize_Control class.
     *
     * @since 2.0.0
     * @access public
     */
    public function render_content()
    {

        // set options
        $this->options['id']                                        = $this->id;
        $this->options['default']                                   = $this->setting->default;
        $this->options['attributes']['data-customize-setting-link'] = $this->settings['default']->id;

        // get custom control content
        echo Options::add_field($this->options, $this->value(), $this->unique);

    }

}
