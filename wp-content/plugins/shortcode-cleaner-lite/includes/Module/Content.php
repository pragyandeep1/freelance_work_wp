<?php

namespace JO\ShortcodeCleaner\Module;

use JO\ShortcodeCleaner\Module\Cleaner;

/**
 * Filter frontend wp content.
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
class Content
{

    /**
     * Filter frontend content.
     *
     * @since 1.0.0
     * @access public
     * @param  array  $filters allowed content filters
     */
    public static function filter_frontend($filters = array())
    {

        // if something is wrong? go back
        if (empty($filters)) {
            return;
        }

        // register content filters
        foreach ($filters as $filter) {

            add_filter(
                $filter['filter'], array(__CLASS__, 'clean_frontend'),
                $filter['priority'], $filter['args']
            );

        }

    }

    /**
     * Clean unused shortcode tags from frontend content.
     *
     * @since 1.0.0
     * @access public
     * @param  string $content any wp frontend content
     * @return string          cleaned wp content
     */
    public static function clean_frontend($content)
    {

        // current content not string? go back
        if (!is_string($content)) {
            return $content;
        }

        // we don't have any shortcode tags? go back
        if (false === strpos($content, '[')) {
            return $content;
        }

        // clean the content
        $content = Cleaner::clean($content);

        // we should finally return the content
        return $content;

    }

}
