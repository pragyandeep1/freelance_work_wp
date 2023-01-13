<?php

namespace Codestar\Options;

use Codestar\Config;
use Codestar\Helper\Helper;
use Codestar\Module\Language;
use WP_Query;

/**
 * Base abstract field class.
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
abstract class Field
{

    /**
     * Field settings.
     *
     * @since 2.0.0
     * @access protected
     * @var array
     */
    protected $field;
    /**
     * Field value.
     *
     * @since 2.0.0
     * @access protected
     * @var mixed
     */
    protected $value;
    /**
     * Field original value before translated.
     *
     * @since 2.0.0
     * @access protected
     * @var mixed
     */
    protected $org_value;
    /**
     * Field Unique options id (name) inside database.
     *
     * @since 2.0.0
     * @access protected
     * @var string
     */
    protected $unique;
    /**
     * Field multilangual support.
     *
     * @since 2.0.0
     * @access protected
     * @var boolean
     */
    protected $multilang;

    /**
     * Setup field settings.
     *
     * @since 2.0.0
     * @access public
     * @param array  $field  field settings
     * @param string $value  field value
     * @param string $unique field Unique options id (name) inside database
     */
    public function __construct($field = array(), $value = '', $unique = '')
    {

        $this->field     = $field;
        $this->value     = $value;
        $this->org_value = $value;
        $this->unique    = $unique;
        $this->multilang = $this->get_element_multilang();

    }

    /**
     * Get field element value.
     *
     * @since 2.0.0
     * @access public
     * @param  string $value
     * @return string
     */
    public function get_element_value($value = '')
    {

        // save default value
        $value = $this->value;

        // get multilangual value
        if (is_array($this->multilang) && is_array($value)) {

            // current language
            $current = $this->multilang['current'];

            // we have value with current launguage? set it
            if (isset($value[$current])) {

                $value = $value[$current];

            } else if ($this->multilang['current'] == $this->multilang['default']) {
                // or use default language value

                $value = $this->value;

            } else {

                $value = '';

            }

        } else if (
            !is_array($this->multilang) &&
            isset($this->value['multilang']) && is_array($this->value)
        ) {

            $value = array_values($this->value);
            $value = $value[0];

        } else if (
            is_array($this->multilang) && !is_array($value) &&
            ($this->multilang['current'] != $this->multilang['default'])
        ) {

            $value = '';

        }

        // return final value
        return $value;

    }

    /**
     * Get field element name.
     *
     * @since 2.0.0
     * @access public
     * @param  string  $extra_name
     * @param  boolean $multilang
     * @return string
     */
    public function get_element_name($extra_name = '', $multilang = false)
    {

        // get field element id
        $element_id = (isset($this->field['id'])) ? $this->field['id'] : '';

        // get extra field element name
        $extra_multilang = (!$multilang && is_array($this->multilang)) ? '[' . $this->multilang['current'] . ']' : '';

        return (isset($this->field['name'])) ? $this->field['name'] . $extra_name : $this->unique . '[' . $element_id . ']' . $extra_multilang . $extra_name;

    }

    /**
     * Get field element type.
     *
     * @since 2.0.0
     * @access public
     * @return string
     */
    public function get_element_type()
    {

        // get field type
        $type = (isset($this->field['attributes']['type'])) ? $this->field['attributes']['type'] : $this->field['type'];

        return $type;

    }

    /**
     * Get field element css class
     *
     * @since 2.0.0
     * @access public
     * @param  string $element_class css class name
     * @return string
     */
    public function get_element_css_class($element_class = '')
    {

        // get field element class
        $field_class = (isset($this->field['class'])) ? ' ' . $this->field['class'] : '';

        return ($field_class || $element_class) ? ' class="' . $element_class . $field_class . '"' : '';

    }

    /**
     * Get field element attributes.
     *
     * @since 2.0.0
     * @access public
     * @param  array  $element_attributes array of field element attributes
     * @return array
     */
    public function get_element_attributes($element_attributes = array())
    {

        // get field attributes
        $attributes = (isset($this->field['attributes'])) ? $this->field['attributes'] : array();

        // get field element id
        $element_id = (isset($this->field['id'])) ? $this->field['id'] : '';

        // we have field attributes?
        if ($element_attributes !== false) {

            $sub_elemenet       = (isset($this->field['sub'])) ? 'sub-' : '';
            $element_attributes = (is_string($element_attributes) || is_numeric($element_attributes)) ? array('data-' . $sub_elemenet . 'depend-id' => $element_id . '_' . $element_attributes) : $element_attributes;
            $element_attributes = (empty($element_attributes) && isset($element_id)) ? array('data-' . $sub_elemenet . 'depend-id' => $element_id) : $element_attributes;

        }

        // merge default and new attributes
        $attributes = wp_parse_args($attributes, $element_attributes);

        // save final attributes
        $atts = '';

        // we have attributes?
        if (!empty($attributes)) {

            foreach ($attributes as $key => $value) {

                if ($value === 'only-key') {
                    $atts .= ' ' . $key;
                } else {
                    $atts .= ' ' . $key . '="' . $value . '"';
                }

            }

        }

        // return final attributes
        return $atts;

    }

    /**
     * Get extra content before field element.
     *
     * @since 2.0.0
     * @access public
     * @return string
     */
    public function get_content_before_element()
    {
        return (isset($this->field['before'])) ? $this->field['before'] : '';
    }

    /**
     * Get extra content after field element.
     *
     * @since 2.0.0
     * @access public
     * @return string
     */
    public function get_content_after_element()
    {

        $output = (isset($this->field['info'])) ? '<p class="cs-text-desc">' . $this->field['info'] . '</p>' : '';
        $output .= (isset($this->field['after'])) ? $this->field['after'] : '';
        $output .= $this->get_element_output_after_multilang();
        $output .= $this->get_element_error();
        $output .= $this->get_element_help();
        $output .= $this->get_element_debug();

        return $output;

    }

    /**
     * Show all field config information.
     *
     * @since 2.0.0
     * @access public
     * @return string
     */
    public function get_element_debug()
    {

        $output = '';

        // remove first underscore from unique options id
        $unique_id_array = explode('_', $this->unique);
        if ($unique_id_array[0] == '') {
            array_shift($unique_id_array);
        }
        $unique_id = implode('_', $unique_id_array);

        // field element need full debug?
        if (
            (isset($this->field['debug']) && $this->field['debug'] === true) ||
            Config::allow_debug()
        ) {

            $value = $this->get_element_value();
            $output .= "<pre>";

            $output .= "<strong>" . esc_html('CONFIG') . ":</strong>";

            $output .= "\n";
            ob_start();
            var_export($this->field);
            $output .= htmlspecialchars(ob_get_clean());
            $output .= "\n\n";

            $output .= "<strong>" . esc_html('USAGE') . ":</strong>";

            $output .= "\n";

            $output .= (isset($this->field['id'])) ? Config::PREFIX . "get_option('" . $unique_id . "' ,'" . $this->field['id'] . "' );" : '';

            if (!empty($value)) {

                $output .= "\n\n";

                $output .= "<strong>" . esc_html('VALUE') . ":</strong>";

                $output .= "\n";
                ob_start();
                var_export($value);
                $output .= htmlspecialchars(ob_get_clean());

            }

            $output .= "</pre>";

        }

        // field element need some debug?
        if (
            (isset($this->field['debug_light']) && $this->field['debug_light'] === true) ||
            Config::allow_debug_light()
        ) {

            $output .= "<pre>";

            $output .= "<strong>" . esc_html('USAGE') . ":</strong>";

            $output .= "\n";

            $output .= (isset($this->field['id'])) ? Config::PREFIX . "get_option('" . $unique_id . "' ,'" . $this->field['id'] . "' );" : '';

            $output .= "\n";

            $output .= "<strong>" . esc_html('ID') . ":</strong>";

            $output .= "\n";
            $output .= (isset($this->field['id'])) ? $this->field['id'] : '';
            $output .= "</pre>";

        }

        return $output;

    }

    /**
     * Get any field element errors
     *
     * @since 2.0.0
     * @access public
     * @return string
     */
    public function get_element_error()
    {

        // get current errors
        global $cs_errors;

        // save ouput errors
        $output = '';

        // we have any errors?
        if (!empty($cs_errors)) {

            foreach ($cs_errors as $key => $value) {

                if (isset($this->field['id']) && $value['code'] == $this->field['id']) {
                    $output .= '<p class="cs-text-warning">' . $value['message'] . '</p>';
                }

            }

        }

        return $output;

    }

    /**
     * Get field element help content.
     *
     * @since 2.0.0
     * @access public
     * @return string
     */
    public function get_element_help()
    {

        return (isset($this->field['help'])) ? '<span class="cs-help" data-title="' . $this->field['help'] . '"><span class="far fa-question-circle"></span></span>' : '';

    }

    /**
     * Get field element output after multilang value.
     *
     * @since 2.0.0
     * @access public
     * @return string
     */
    public function get_element_output_after_multilang()
    {

        // save field element output
        $output = '';

        // we have multilang value?
        if (is_array($this->multilang)) {

            $output .= '<fieldset class="hidden">';

            foreach ($this->multilang['languages'] as $key => $val) {

                // ignore current language for hidden element
                if ($key != $this->multilang['current']) {

                    // set default value
                    if (isset($this->org_value[$key])) {
                        $value = $this->org_value[$key];
                    } else if (!isset($this->org_value[$key]) && ($key == $this->multilang['default'])) {
                        $value = $this->org_value;
                    } else {
                        $value = '';
                    }

                    $cache_field = $this->field;
                    unset($cache_field['multilang']);
                    $cache_field['name'] = $this->get_element_name('[' . $key . ']', true);

                    /**
                     * Get field type class name OOP.
                     * example: text > Text, color_picker > ColorPicker ..etc
                     */
                    $class_field_name = Helper::get_field_type_class($field['type']);

                    /**
                     * Get main field ::class OOP
                     * example: text > Text, color_picker > ColorPicker ..etc
                     */
                    $class = '\Codestar\Options\Field\\' . $class_field_name;

                    // instantiate new Field\Type
                    $element = new $class($cache_field, $value, $this->unique);

                    ob_start();

                    // then show field element output content
                    $element->output();

                    // return output content
                    $output .= ob_get_clean();

                } // end check $this->multilang['current']

            } // end foreach $this->multilang['languages']

            $output .= '<input type="hidden" name="' . $this->get_element_name('[multilang]', true) . '" value="true" />';
            $output .= '</fieldset>';

            /**
             * @todo should make settings option for override this text
             */
            $output .= '<p class="cs-text-desc">You are editing language: ( <strong>' . $this->multilang['current'] . '</strong> )</p>';

        } // end check is_array($this->multilang)

        return $output;

    }

    /**
     * Database query using core WP_Query class.
     *
     * @since 2.0.0
     * @access public
     * @param  array $args query arguments
     * @return array
     */
    public function query($args = array())
    {

        // set query arguments
        $query_args = array_merge(

            /**
             * Set default args to improve database queries performance.
             * @see https://10up.github.io/Engineering-Best-Practices/php/#performance
             * Note: Do not use posts_per_page => -1
             * this is a performance hazard, what if we have 100,000 posts?
             * This could crash the site.
             */

            // default arguments
            array(
                // useful when pagination is not needed.
                'no_found_rows'          => true,
                // useful when post meta will not be utilized.
                'update_post_meta_cache' => false,
                // useful when taxonomy terms will not be utilized.
                'update_post_term_cache' => false,
                // determine upper limit for number of posts.
                'posts_per_page'         => 500,
                // just published posts
                'post_status'            => 'publish',
            ),

            // new arguments by user
            $args

        );

        // make database query
        $query = new WP_Query($query_args);

        // return query data
        return $query;

    }

    /**
     * Get WP data for field element options.
     *
     * @since 2.0.0
     * @access public
     * @param  string $type field element type
     * @return array        field element options
     */
    public function get_element_data($type = '')
    {

        // save options
        $options = array();

        /**
         * Get query arguments by user.
         * @see all available query $args https://gist.github.com/luetkemj/2023628
         */
        $query_args = (isset($this->field['query_args'])) ? $this->field['query_args'] : array();

        // start check each type to get data.
        switch ($type) {

            // get available pages
            case 'pages':
            case 'page':

                // set default post type argument
                $default_query_args = array('post_type' => 'page');
                $query_args         = array_merge($default_query_args, $query_args);

                // get pages
                $pages = $this->query($query_args)->posts;

                // we have pages? add it
                if (!is_wp_error($pages) && !empty($pages)) {
                    foreach ($pages as $page) {
                        $options[$page->ID] = $page->post_title;
                    }
                }

                // reset original post data
                wp_reset_postdata();

                break;

            // get available posts
            case 'posts':
            case 'post':

                // set default post type argument
                $default_query_args = array('post_type' => 'post');
                $query_args         = array_merge($default_query_args, $query_args);

                // get posts
                $posts = $this->query($query_args)->posts;

                // we have posts? add it
                if (!is_wp_error($posts) && !empty($posts)) {
                    foreach ($posts as $post) {
                        $options[$post->ID] = $post->post_title;
                    }
                }

                // reset original post data
                wp_reset_postdata();

                break;

            // get available categories
            case 'categories':
            case 'category':

                $categories = get_categories($query_args);

                if (!is_wp_error($categories) && !empty($categories) && !isset($categories['errors'])) {
                    foreach ($categories as $category) {
                        $options[$category->term_id] = $category->name;
                    }
                }

                break;

            // get available tags
            case 'tags':
            case 'tag':

                $taxonomies = (isset($query_args['taxonomies'])) ? $query_args['taxonomies'] : 'post_tag';
                $tags       = get_terms($taxonomies, $query_args);

                if (!is_wp_error($tags) && !empty($tags)) {
                    foreach ($tags as $tag) {
                        $options[$tag->term_id] = $tag->name;
                    }
                }

                break;

            // get available menus
            case 'menus':
            case 'menu':

                $menus = wp_get_nav_menus($query_args);

                if (!is_wp_error($menus) && !empty($menus)) {
                    foreach ($menus as $menu) {
                        $options[$menu->term_id] = $menu->name;
                    }
                }

                break;

            // get available custom post types
            case 'post_types':
            case 'post_type':

                $post_types = get_post_types(array(
                    'show_in_nav_menus' => true,
                ));

                if (!is_wp_error($post_types) && !empty($post_types)) {
                    foreach ($post_types as $post_type) {
                        $options[$post_type] = ucfirst($post_type);
                    }
                }

                break;

        }

        return $options;

    }

    /**
     * Get field element chekced status for checkbox type.
     *
     * @since 2.0.0
     * @access public
     * @param  string  $helper
     * @param  string  $current
     * @param  string  $type
     * @param  boolean $echo
     */
    public function get_element_checked(
        $helper = '', $current = '', $type = 'checked', $echo = false
    ) {

        if (is_array($helper) && in_array($current, $helper)) {
            $result = ' ' . $type . '="' . $type . '"';
        } else if ($helper == $current) {
            $result = ' ' . $type . '="' . $type . '"';
        } else {
            $result = '';
        }

        if ($echo) {
            echo $result;
        }

        return $result;

    }

    /**
     * Get element multilang.
     *
     * @since 2.0.0
     * @access public
     * @return mixed
     */
    public function get_element_multilang()
    {
        return (isset($this->field['multilang'])) ? Language::get_defaults() : false;
    }

}
