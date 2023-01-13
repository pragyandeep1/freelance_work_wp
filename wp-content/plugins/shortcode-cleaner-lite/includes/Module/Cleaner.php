<?php

namespace JO\ShortcodeCleaner\Module;

use JO\ShortcodeCleaner\Core\Data;

/**
 * Clean unused broken shortcode tags from any content.
 *
 * @package   Shortcode_Cleaner_Lite
 * @author    Jozoor, mohamdio [jozoor.com]
 * @link      https://plugins.jozoor.com/shortcode-cleaner
 * @copyright 2017 Jozoor, mohamdio [jozoor.com]
 * @license   GPL2
 * @version   1.0.0
 *
 * @since  1.0.0
 */
class Cleaner
{

    /**
     * Remove unused broken shortcode tags from content.
     *
     * @since 1.0.0
     * @access public
     * @param  string $content any wp content
     * @return string          cleaned wp content
     */
    public static function clean($content, $callback = '')
    {

        // make sure we have content
        if (empty($content)) {
            return $content;
        }

        /**
         * Make sure to keep ignored shortcode tags from settings.
         */

        // get ignored borken (inactive) shortcode tags from settings
        $ignored_shortcode_tags_from_settings = explode(',', cs_get_option(Data::get_unique_settings_options_id(), 'ignore_inactive_shortcode_tags'));

        // filter @hook ignore broken (inactive) shortcode tags.
        $ignored_shortcode_tags = array_unique(
            apply_filters(
                Data::PLUGIN_PREFIX . 'ignore_inactive_shortcode_tags',
                array_filter($ignored_shortcode_tags_from_settings)
            )
        );

        /**
         * Load any HTML tags from content.
         */

        // create new DOMDocument object php v5 core
        $dom = new \DOMDocument();

        // disable the invalid HTML warning
        libxml_use_internal_errors(true);

        // load any HTML tags from content
        $dom->loadHTML($content);

        // create new DOMXpath object php v5 core
        $xpath = new \DOMXpath($dom);

        /**
         * Make sure to keep ignored shortcode tags within HTML tags attributes
         * ex: '<div id="[shortcode]"></div>'.
         */

        // get any HTML element tag attributes
        $html_elements_attributes = $xpath->query('//@*');

        // we have html tags elements with attributes?
        if (!is_null($html_elements_attributes)) {

            foreach ($html_elements_attributes as $element) {

                // we have any shortcode tags? merge it with ignored tags from settings
                if (false !== strpos($element->nodeValue, '[')) {

                    // get shortcode tags from any html tag attributes
                    if (preg_match_all("/\\[(.*?)\\]/", $element->nodeValue, $tags)) {

                        // merge each tag with ignored tags from settings
                        foreach ($tags[1] as $tag) {

                            // handle tag name
                            $tag = str_replace('/', '', $tag);
                            $tag = str_replace('[', '', $tag);
                            $tag = str_replace(']', '', $tag);

                            // just get tag name without other options if exists
                            $tag_name_array = explode(' ', trim($tag));
                            $tag            = $tag_name_array[0];

                            // add tag to ignored tags
                            $ignored_shortcode_tags[] = $tag;

                        }

                    } // end check preg_match_all

                } // end check $element->nodeValue

            } // end foreach $html_elements_attributes

        } // end check $html_elements_attributes

        /**
         * Make sure to keep ignored shortcode tags within HTML code tags
         * ex: '<code>[shortcode]</code>' or <pre>[shortcode]</pre>' ..etc
         */

        // get available html code tags from settings
        $html_code_tags_from_settings = explode(',', cs_get_option(Data::get_unique_settings_options_id(), 'set_html_code_tags'));

        // filter @hook set html code tags.
        $html_code_tags = array_unique(
            apply_filters(
                Data::PLUGIN_PREFIX . 'set_html_code_tags',
                array_filter($html_code_tags_from_settings)
            )
        );

        // check each html code tag content
        foreach ($html_code_tags as $code_tag) {

            // get any HTML element code or pre .. tags
            $html_elements_code = $xpath->query('//' . $code_tag);

            // we have html code or pre .. tags elements?
            if (!is_null($html_elements_code)) {

                foreach ($html_elements_code as $element) {

                    // we have any shortcode tags? merge it with ignored tags from settings
                    if (false !== strpos($element->nodeValue, '[')) {

                        // get shortcode tags from any html tag attributes
                        if (preg_match_all("/\\[(.*?)\\]/", $element->nodeValue, $tags)) {

                            // merge each tag with ignored tags from settings
                            foreach ($tags[1] as $tag) {

                                // handle tag name
                                $tag = str_replace('/', '', $tag);
                                $tag = str_replace('[', '', $tag);
                                $tag = str_replace(']', '', $tag);

                                // just get tag name without other options if exists
                                $tag_name_array = explode(' ', trim($tag));
                                $tag            = $tag_name_array[0];

                                // add tag to ignored tags
                                $ignored_shortcode_tags[] = $tag;

                            }

                        } // end check preg_match_all

                    } // end check $element->nodeValue

                } // end foreach $html_elements_code

            } // end check $html_elements_code

        } // end foreach $html_code_tags

        /**
         * Merge ignored shortcode tags with active shortcode tags.
         */
        $active_shortcode_tags = array_merge(
            array_unique($ignored_shortcode_tags), self::get_active_shortcode_tags(true)
        );

        /**
         * Get inactive shortcode tags.
         */

        global $inactive_shortcode_tags;

        // save inactive shortcode tags
        $inactive_shortcode_tags = array();

        // search for inactive shortcode tags
        if (preg_match_all("/\\[(.*?)\\]/", $content, $tags)) {

            foreach ($tags[1] as $tag) {

                // handle tag name
                $tag = str_replace('/', '', $tag);
                $tag = str_replace('[', '', $tag);
                $tag = str_replace(']', '', $tag);

                /**
                 * Remove this new code '#038;' which be added when WP content parsed
                 * tag name like that '0-9()#&+*-=' to '0-9()#&#038;+*-=' so here we
                 * remove it to make tags name equal, so we can ignore it to be active.
                 *
                 * @since 1.0.6
                 */
                $tag = str_replace('#038;', '', $tag);

                // just get tag name without other options if exists
                $tag_name_array = explode(' ', trim($tag));
                $tag            = $tag_name_array[0];

                /**
                 * Make sure to skipping active shortcode tags.
                 * Note: here we ignore any tage name start with:
                 * ['{', '.', '$', '"', '_', '-', '@', '#', '%', '&', '*', '+', '=']
                 *
                 * @since 1.0.6
                 */
                if (
                    !in_array($tag, $active_shortcode_tags, true) &&
                    false === strpos($tag, 'embed') && false === strpos($tag, 'caption') &&
                    false === strpos($tag, 'wp_caption') && false === strpos($tag, 'audio') &&
                    false === strpos($tag, 'gallery') && false === strpos($tag, 'embed') &&
                    false === strpos($tag, 'playlist') && false === strpos($tag, 'video') &&
                    false === (substr($tag, 0, 1) === '{') &&
                    false === (substr($tag, 0, 1) === '.') &&
                    false === (substr($tag, 0, 1) === '$') &&
                    false === (substr($tag, 0, 1) === '"') &&
                    false === (substr($tag, 0, 1) === '_') &&
                    false === (substr($tag, 0, 1) === '-') &&
                    false === (substr($tag, 0, 1) === '@') &&
                    false === (substr($tag, 0, 1) === '#') &&
                    false === (substr($tag, 0, 1) === '%') &&
                    false === (substr($tag, 0, 1) === '&') &&
                    false === (substr($tag, 0, 1) === '*') &&
                    false === (substr($tag, 0, 1) === '+') &&
                    false === (substr($tag, 0, 1) === '=') &&
                    false === empty($tag)
                ) {

                    // add tag to inactive tags
                    $inactive_shortcode_tags[] = $tag;

                }

            } // end foreach $tags[1]

        } // end preg_match_all

        /**
         * Remove any duplicate inactive shortcode tags.
         * filter @hook active shortcodes to be broken.
         */
        $inactive_shortcode_tags = array_unique(
            apply_filters(
                Data::PLUGIN_PREFIX . 'set_inactive_shortcode_tags',
                array_unique($inactive_shortcode_tags)
            )
        );

        /**
         * Clean inactive shortcode tags content.
         *
         * in normal we keep any content between unused shortcode tags if exists,
         * but here we will let user to define any unused shortcode tags from settings
         * to clean its content also, so we will clean tag and its content.
         */

        // get this broken (inactive) shortcodes from settings to clean its content
        $clean_inactive_shortcode_tags_content_from_settings = explode(',', cs_get_option(Data::get_unique_settings_options_id(), 'clean_inactive_shortcode_tags_content'));

        // filter @hook set broken (inactive) shortcodes to clean its content.
        $clean_inactive_shortcode_tags_content = array_unique(
            apply_filters(
                Data::PLUGIN_PREFIX . 'clean_inactive_shortcode_tags_content',
                array_filter($clean_inactive_shortcode_tags_content_from_settings)
            )
        );

        // check if clean all broken shortcodes content from settings
        $clean_all_inactive_shortcode_tags_content_from_settings = false;
        if (cs_get_option(Data::get_unique_settings_options_id(), 'clean_all_inactive_shortcode_tags_content')) {
            $clean_all_inactive_shortcode_tags_content_from_settings = true;
        }

        // filter @hook check if clean all inactive shortcode tags content
        $clean_all_inactive_shortcode_tags_content = apply_filters(
            Data::PLUGIN_PREFIX . 'clean_all_inactive_shortcode_tags_content',
            $clean_all_inactive_shortcode_tags_content_from_settings
        );

        // if user set clean all inactive tags content 'true'? clean all tags content
        if ($clean_all_inactive_shortcode_tags_content) {
            $clean_inactive_shortcode_tags_content = $inactive_shortcode_tags;
        }

        // clean all inactive tags content
        foreach ($clean_inactive_shortcode_tags_content as $tag) {

            // save shortcode tag
            $start_tag = '[' . $tag . ']';
            $end_tag   = '[/' . $tag . ']';

            // get tag content
            $tag_content     = strpos($content, $start_tag);
            $tag_content_end = strpos($content, $end_tag);

            // we have any tags like [tag]..[/tag] and not [tag] enough? clean its content
            if (false !== $tag_content && false !== $tag_content_end) {

                // get tag content string length
                $tag_content += strlen($start_tag);

                // make sure we have content in between
                $tag_content_length = strpos($content, $end_tag, $tag_content) - $tag_content;

                // output final content in between
                $content_between_tag = substr($content, $tag_content, $tag_content_length);

                // now we can clean this content in between
                $content = str_replace($content_between_tag, '', $content);

                /**
                 * Remove broken shortcode content for each shortcode not just first one.
                 * This will solve the issue when use these options:
                 * "Clean this broken Shortcodes Content" or "Clean all broken Shortcodes Content"
                 *
                 * @since 1.0.7
                 */
                $content = str_replace($start_tag, '<!--scl-shortcode-cleaner-clean-content-start ', $content);
                $content = str_replace($end_tag, ' scl-shortcode-cleaner-clean-content-end-->', $content);
                $content = preg_replace('/<!--scl-shortcode-cleaner-clean-content-start (.*) scl-shortcode-cleaner-clean-content-end-->/', '', $content);

            }

        } // end foreach $clean_inactive_shortcode_tags_content

        // collect active shortcodes for Status
        if (!empty($callback) && $callback === 'active_shortcodes') {
            return $active_shortcode_tags;
        }

        // collect inactive shortcodes for Status
        if (!empty($callback) && $callback === 'inactive_shortcodes') {
            return $inactive_shortcode_tags;
        }

        /**
         * Handle content before replace shortcode tags.
         * avoid '/' chars in content breaks preg_replace.
         * @source https://plugins.trac.wordpress.org/remove-orphan-shortcodes.php#L29
         */
        
        /**
         * Fixed PHP 7.2+ error issue (A non well formed numeric value encountered)
         * Old code: $update_content_1 = md5(microtime());
         *
         * @since 1.0.7
         */
        $update_content_1 = md5((int)microtime());
        $content          = str_replace('[/', $update_content_1, $content);
        /**
         * Fixed PHP 7.2+ error issue (A non well formed numeric value encountered)
         * Old code: $update_content_2 = md5(microtime() + 1);
         *
         * @since 1.0.7
         */
        $update_content_2 = md5( (int)microtime() + 1);
        $content          = str_replace('/', $update_content_2, $content);
        $content          = str_replace($update_content_1, '[/', $content);

        /**
         * Make sure to keep active and ignored shortcode tags.
         */
        if (!empty($active_shortcode_tags)) {

            /**
             * Make sure to skipping any regex modifiers before preg_replace in
             * keep active tags executing (do_shortcode).
             *
             * @since 1.0.6
             */
            foreach ($active_shortcode_tags as $tag) {

                if (($key = array_search($tag, $active_shortcode_tags)) !== false) {
                    $tag                         = str_replace('#', '\#', $tag);
                    $tag                         = str_replace('*', '\*', $tag);
                    $active_shortcode_tags[$key] = $tag;
                }

            } // end foreach $active_shortcode_tags

            // collect all active and ignored tags
            $keep_active_tags = implode('|', $active_shortcode_tags);

            // check shortcode tags
            if (preg_match_all("/\[(.+)\]/", $content, $active_tags)) {

                foreach ($active_tags[1] as $tag) {

                    // hide escaped [[tag]] before executing (do_shortcode)
                    $content = str_replace('[[', '<!--scl-shortcode-cleaner-start', $content);
                    $content = str_replace(']]', 'scl-shortcode-cleaner-end-->', $content);

                    /**
                     * Hide all ignored tags with special characters that we skipped before
                     * ['{', '.', '$', '"', '_', '-', '@', '#', '%', '&', '*', '+', '=']
                     *
                     * @since 1.0.6
                     */
                    $content = str_replace('[{', '<!--scl-cleaner-curly-braces', $content);
                    $content = str_replace('[.', '<!--scl-cleaner-dots', $content);
                    $content = str_replace('[$', '<!--scl-cleaner-dollar', $content);
                    $content = str_replace('["', '<!--scl-cleaner-quote', $content);
                    $content = str_replace('[_', '<!--scl-cleaner-underscore', $content);
                    $content = str_replace('[-', '<!--scl-cleaner-dash', $content);
                    $content = str_replace('[@', '<!--scl-cleaner-at-email', $content);
                    $content = str_replace('[#', '<!--scl-cleaner-hash', $content);
                    $content = str_replace('[%', '<!--scl-cleaner-percentage', $content);
                    $content = str_replace('[&', '<!--scl-cleaner-and', $content);
                    $content = str_replace('[*', '<!--scl-cleaner-strict', $content);
                    $content = str_replace('[+', '<!--scl-cleaner-plus', $content);
                    $content = str_replace('[=', '<!--scl-cleaner-equal', $content);

                    // keep active tags executing (do_shortcode)
                    $content = preg_replace("~(?:\[/?)(?!(?:$keep_active_tags))[^/\]]+/?\]~s", '', $content);

                    // back escaped [[tag]] after executing (do_shortcode)
                    $content = str_replace('<!--scl-shortcode-cleaner-start', '[[', $content);
                    $content = str_replace('scl-shortcode-cleaner-end-->', ']]', $content);

                    /**
                     * back all ignored tags with special characters after preg_replace.
                     *
                     * @since 1.0.6
                     */
                    $content = str_replace('<!--scl-cleaner-curly-braces', '[{', $content);
                    $content = str_replace('<!--scl-cleaner-dots', '[.', $content);
                    $content = str_replace('<!--scl-cleaner-dollar', '[$', $content);
                    $content = str_replace('<!--scl-cleaner-quote', '["', $content);
                    $content = str_replace('<!--scl-cleaner-underscore', '[_', $content);
                    $content = str_replace('<!--scl-cleaner-dash', '[-', $content);
                    $content = str_replace('<!--scl-cleaner-at-email', '[@', $content);
                    $content = str_replace('<!--scl-cleaner-hash', '[#', $content);
                    $content = str_replace('<!--scl-cleaner-percentage', '[%', $content);
                    $content = str_replace('<!--scl-cleaner-and', '[&', $content);
                    $content = str_replace('<!--scl-cleaner-strict', '[*', $content);
                    $content = str_replace('<!--scl-cleaner-plus', '[+', $content);
                    $content = str_replace('<!--scl-cleaner-equal', '[=', $content);

                } // end foreach $active_tags[1]

            } // end check active tags

        } else {
            // strip all unused shortcode tags

            $content = preg_replace('~(?:\[/?)[^/\]]+/?\]~s', '', $content);

        }

        // set '/' back to its place in the content
        $content = str_replace($update_content_2, '/', $content);

        // we should finally return the content
        return $content;

    }

    /**
     * Get available active shortcode tags.
     *
     * @since 1.0.0
     * @access public
     * @param  boolean $keys get shortcode tags array keys
     * @return array
     */
    public static function get_active_shortcode_tags($keys = false)
    {

        // global wp core var
        global $shortcode_tags;

        // if something is wrong? return empty array
        if (
            !is_array($shortcode_tags) || empty($shortcode_tags) ||
            (is_array($shortcode_tags) && empty($shortcode_tags))
        ) {
            return array();
        }

        // we need just tags array keys? ok handle it.
        if ($keys) {
            return array_keys($shortcode_tags);
        }

        // return normal tags array
        return $shortcode_tags;

    }

}
