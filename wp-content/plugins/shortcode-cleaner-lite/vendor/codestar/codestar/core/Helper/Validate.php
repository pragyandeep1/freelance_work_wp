<?php

namespace Codestar\Helper;

use Codestar\Config;

/**
 * Helper validate functions class.
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
class Validate
{

    /**
     * Add all validate filters.
     *
     * @since 2.0.0
     * @access public
     */
    public static function add_filters()
    {

        add_filter(Config::PREFIX . 'validate_email', array(__CLASS__, 'email'), 10, 2);
        add_filter(Config::PREFIX . 'validate_numeric', array(__CLASS__, 'numeric'), 10, 2);
        add_filter(Config::PREFIX . 'validate_required', array(__CLASS__, 'required'));

    }

    /**
     * Email validate.
     *
     * @todo Note: i found issue that validate didn't work when 'ajax_save' is true.
     *
     * @since 2.0.0
     * @access public
     * @param  string $value
     * @param  array $field
     * @return string
     */
    public static function email($value, $field)
    {

        // use WP core sanitize_email() function
        if (!sanitize_email($value)) {
            /**
             * @todo should make settings option for override this text
             */
            return esc_html('Please write a valid email address!');
        }

    }

    /**
     * Numeric validate.
     *
     * @todo Note: i found issue that validate didn't work when 'ajax_save' is true.
     *
     * @since 2.0.0
     * @access public
     * @param  int $value
     * @param  array $field
     * @return int
     */
    public static function numeric($value, $field)
    {

        if (!is_numeric($value)) {
            /**
             * @todo should make settings option for override this text
             */
            return esc_html('Please write a numeric data!');
        }

    }

    /**
     * Required validate.
     *
     * @todo Note: i found issue that validate didn't work when 'ajax_save' is true.
     *
     * @since 2.0.0
     * @access public
     * @param  string $value
     * @return string
     */
    public static function required($value)
    {

        if (empty($value)) {
            /**
             * @todo should make settings option for override this text
             */
            return esc_html('Fatal Error! This field is required!');
        }

    }

}
