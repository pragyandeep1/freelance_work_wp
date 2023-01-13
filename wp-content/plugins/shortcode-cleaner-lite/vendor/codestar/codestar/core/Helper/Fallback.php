<?php

namespace Codestar\Helper;

/**
 * Helper fallback functions class.
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
class Fallback
{

    /**
     * Check WP version.
     *
     * @since 2.0.0
     * @access public
     * @return boolean
     */
    public static function older_wp_version()
    {

        // get current WP version
        global $wp_version;

        // fallback term meta for WP version 4.3 and older
        if (version_compare($wp_version, '4.4', '<')) {
            return true;
        }

        return false;

    }

    /**
     * A fallback for get term meta.
     *
     * Note: get_term_meta added since WP 4.4
     *
     * @since 2.0.0
     * @access public
     * @param  int     $term_id
     * @param  string  $key
     * @param  boolean $single
     * @return array
     */
    public static function get_term_meta($term_id, $key = '', $single = false)
    {

        // wp 4.3 and older
        if (self::older_wp_version()) {

            $terms = get_option('cs_term_' . $key);
            return (!empty($terms[$term_id])) ? $terms[$term_id] : false;

        }

        // wp 4.4 and later
        return get_term_meta($term_id, $key, $single);

    }

    /**
     * A fallback for add term meta.
     *
     * Note: add_term_meta added since WP 4.4
     *
     * @since 2.0.0
     * @access public
     * @param int     $term_id
     * @param string  $meta_key
     * @param string  $meta_value
     * @param boolean $unique
     */
    public static function add_term_meta($term_id, $meta_key = '', $meta_value, $unique = false)
    {

        // wp 4.3 and older
        if (self::older_wp_version()) {
            return self::update_term_meta($term_id, $meta_key, $meta_value, $unique);
        }

        // wp 4.4 and later
        add_term_meta($term_id, $meta_key, $meta_value, $unique);

    }

    /**
     * A fallback for update term meta.
     *
     * Note: update_term_meta added since WP 4.4
     *
     * @since 2.0.0
     * @access public
     * @param  int    $term_id
     * @param  string $meta_key
     * @param  string $meta_value
     * @param  string $prev_value
     */
    public static function update_term_meta($term_id, $meta_key, $meta_value, $prev_value = '')
    {

        // wp 4.3 and older
        if (self::older_wp_version()) {

            if (!empty($term_id) || !empty($meta_key) || !empty($meta_value)) {

                $terms           = get_option('cs_term_' . $meta_key);
                $terms[$term_id] = $meta_value;
                update_option('cs_term_' . $meta_key, $terms);

            }

        }

        // wp 4.4 and later
        update_term_meta($term_id, $meta_key, $meta_value, $prev_value);

    }

    /**
     * A fallback for delete term meta.
     *
     * Note: delete_term_meta added since WP 4.4
     *
     * @since 2.0.0
     * @access public
     * @param  int    $term_id
     * @param  string $meta_key
     * @param  string $meta_value
     * @param  boolean $delete_all
     */
    public static function delete_term_meta($term_id, $meta_key, $meta_value = '', $delete_all = false)
    {

        // wp 4.3 and older
        if (self::older_wp_version()) {

            if (!empty($term_id) || !empty($meta_key)) {

                $terms = get_option('cs_term_' . $meta_key);
                unset($terms[$term_id]);
                update_option('cs_term_' . $meta_key, $terms);

            }

        }

        // wp 4.4 and later
        delete_term_meta($term_id, $meta_key, $meta_value, $delete_all);

    }

}
