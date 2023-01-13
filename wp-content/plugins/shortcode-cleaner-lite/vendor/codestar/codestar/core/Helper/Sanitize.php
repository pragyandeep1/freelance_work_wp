<?php

namespace Codestar\Helper;

use Codestar\Config;

/**
 * Helper sanitize functions class.
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
class Sanitize
{

    /**
     * Add all sanitize filters.
     *
     * @since 2.0.0
     * @access public
     */
    public static function add_filters()
    {

        add_filter(Config::PREFIX . 'sanitize_text', array(__CLASS__, 'text'), 10, 2);
        add_filter(Config::PREFIX . 'sanitize_textarea', array(__CLASS__, 'textarea'));
        add_filter(Config::PREFIX . 'sanitize_checkbox', array(__CLASS__, 'checkbox'));
        add_filter(Config::PREFIX . 'sanitize_switcher', array(__CLASS__, 'checkbox'));
        add_filter(Config::PREFIX . 'sanitize_image_select', array(__CLASS__, 'image_select'));
        add_filter(Config::PREFIX . 'sanitize_group', array(__CLASS__, 'group'));
        add_filter(Config::PREFIX . 'sanitize_title', array(__CLASS__, 'title'));
        add_filter(Config::PREFIX . 'sanitize_clean', array(__CLASS__, 'clean'), 10, 2);
        add_filter(Config::PREFIX . 'sanitize_tags', array(__CLASS__, 'text'), 10, 2);

    }

    /**
     * Text sanitize.
     *
     * @since 2.0.0
     * @access public
     * @param  string $value
     * @param  array $field
     * @return string
     */
    public static function text($value, $field)
    {
        return wp_filter_nohtml_kses($value);
    }

    /**
     * Textarea sanitize.
     *
     * @since 2.0.0
     * @access public
     * @param  string $value
     * @return mixed
     */
    public static function textarea($value)
    {

        global $allowedposttags;
        return wp_kses($value, $allowedposttags);

    }

    /**
     * Checkbox sanitize.
     *
     * @since 2.0.0
     * @access public
     * @param  boolean $value
     * @return boolean
     */
    public static function checkbox($value)
    {

        if (!empty($value) && $value == 1) {
            $value = true;
        }

        if (empty($value)) {
            $value = false;
        }

        return $value;

    }

    /**
     * Image select sanitize.
     *
     * @since 2.0.0
     * @access public
     * @param  string $value
     * @return string
     */
    public static function image_select($value)
    {

        if (isset($value) && is_array($value)) {

            if (count($value)) {
                $value = $value;
            } else {
                $value = $value[0];
            }

        } else if (empty($value)) {

            $value = '';

        }

        return $value;

    }

    /**
     * Group sanitize.
     *
     * @since 2.0.0
     * @access public
     * @param  string $value
     * @return string
     */
    public static function group($value)
    {
        return (empty($value)) ? '' : $value;
    }

    /**
     * Title sanitize.
     *
     * @since 2.0.0
     * @access public
     * @param  string $value
     * @return string
     */
    public static function title($value)
    {
        // use WP core sanitize_title() function
        return sanitize_title($value);
    }

    /**
     * Text clean.
     *
     * @since 2.0.0
     * @access public
     * @param  string $value
     * @return string
     */
    public static function clean($value)
    {
        return $value;
    }

}
