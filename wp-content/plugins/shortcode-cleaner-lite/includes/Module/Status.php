<?php

namespace JO\ShortcodeCleaner\Module;

use JO\ShortcodeCleaner\Core\Data;
use JO\ShortcodeCleaner\Module\Cleaner;
use WP_Query;

/**
 * Collect active and unused broken (inactive) shortcods data.
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
class Status
{

    /**
     * Active shortcodes.
     *
     * @since 1.0.0
     * @access protected
     * @var array
     */
    protected static $active_shortcodes = array();
    /**
     * Inactive shortcodes.
     *
     * @since 1.0.0
     * @access protected
     * @var array
     */
    protected static $inactive_shortcodes = array();
    /**
     * Run cleaner.
     *
     * @since 1.0.0
     * @access protected
     * @var boolean
     */
    protected static $run_cleaner_process = false;
    /**
     * Cleaner start date for history data.
     *
     * @since 1.0.0
     * @access protected
     * @var string
     */
    protected static $cleaner_start_date = '';

    /**
     * Database query using core WP_Query class.
     *
     * @since 1.0.0
     * @access public
     * @param  boolean $refresh query cache
     * @return array
     */
    public static function query($refresh = false)
    {

        // default query arguments
        $default_args = array(

            /**
             * Set default args to improve database queries performance.
             * @see https://10up.github.io/Engineering-Best-Practices/php/#performance
             * Note: Do not use posts_per_page => -1
             * this is a performance hazard, what if we have 100,000 posts?
             * This could crash the site.
             */

            // useful when pagination is not needed.
            'no_found_rows'          => true,
            // useful when post meta will not be utilized.
            'update_post_meta_cache' => false,
            // useful when taxonomy terms will not be utilized.
            'update_post_term_cache' => false,
            // determine upper limit for number of posts.
            'posts_per_page'         => 100,
            // just published posts
            'post_status'            => 'publish',

        );

        // filter @hook set shortcodes status query arguments
        $query_args = apply_filters(
            Data::PLUGIN_PREFIX . 'set_shortcodes_status_query_args',
            array_merge(
                // default arguments
                $default_args,
                // new arguments
                array(
                    's'         => '[',
                    'post_type' => 'any',
                )
            )
        );

        /**
         * Do database wp_query and cache it.
         */

        // save cache key
        $cache_key = Data::get_shortcodes_status_cache_key();

        // we don't have cached data or do refresh data? do new wp query
        if ($refresh || false === ($data = get_transient($cache_key))) {

            // do wp query
            $query = new WP_Query($query_args);

            // save query data
            $data = $query->posts;

            // run cleaner process
            self::$run_cleaner_process = true;

            // set new wp cache
            set_transient($cache_key, $data, 24 * HOUR_IN_SECONDS);

        }

        // return query data
        return $data;

    }

    /**
     * Output shortcodes status content.
     *
     * @since 1.0.0
     * @access public
     * @return string html content
     */
    public static function content()
    {

        /**
         * Make sure to do wp_query only on settings page.
         */

        // save current page slug
        $current_page_slug = '';

        // get current page slug
        global $pagenow;
        if ($pagenow === 'admin.php' && isset($_GET['page'])) {
            $current_page_slug = $_GET['page'];
        }

        // we are not inside our settings page? go back
        if ($current_page_slug !== Data::PLUGIN_MENU_PAGE_SLUG) {
            return;
        }

        // get posts data
        $posts = self::query();

        // save html content
        $content = '';

        // save inactive shortcodes data
        $inactive_shortcodes_data = array();

        /**
         * Save cleaner start date for history data.
         */

        // get date now
        $date_now = new \DateTime();

        // set cleaner start date
        if (empty(self::$cleaner_start_date)) {
            self::$cleaner_start_date = $date_now->format('d-m-Y');
        }

        // true if cleaner start date is more than a month ago? reset it
        if (strtotime(self::$cleaner_start_date) < strtotime('1 month ago')) {

            // reset cleaner start date
            self::$cleaner_start_date = $date_now->format('d-m-Y');

            // delete old month data
            update_option(Data::get_cleaner_history_data_cache_key(), array());

        }

        /**
         * Get shortcodes status from posts.
         */

        // we have posts? get shortcodes
        if (!is_wp_error($posts) && !empty($posts) && is_array($posts)) {

            // get active and inactive shortcodes
            foreach ($posts as $post) {

                // save post data content places
                $post_data_content_places = array(
                    array(
                        'content' => $post->post_title,
                        'place'   => __('Title', 'shortcode-cleaner'),
                    ),
                    array(
                        'content' => $post->post_content,
                        'place'   => __('Content', 'shortcode-cleaner'),
                    ),
                    array(
                        'content' => $post->post_excerpt,
                        'place'   => __('Excerpt', 'shortcode-cleaner'),
                    ),
                );

                /**
                 * Get shortcodes from post data content places.
                 * post_title, post_content, post_excerpt
                 */

                foreach ($post_data_content_places as $data) {

                    if (
                        !empty($data['content']) &&
                        false !== strpos($data['content'], '[')
                    ) {

                        // save active shortcodes from post content
                        self::$active_shortcodes = array_unique(array_merge(self::$active_shortcodes, Cleaner::clean($data['content'], 'active_shortcodes')));

                        // save inactive shortcodes from post content
                        self::$inactive_shortcodes = array_unique(array_merge(self::$inactive_shortcodes, Cleaner::clean($data['content'], 'inactive_shortcodes')));

                        // collect inactive shortcodes data from post content
                        foreach (Cleaner::clean($data['content'], 'inactive_shortcodes') as $shortcode) {
                            $inactive_shortcodes_data[$shortcode][] = array(
                                'post_id'   => $post->ID,
                                'post_type' => $post->post_type,
                                'place'     => $data['place'],
                            );
                        }

                    } // end check $data['content']

                } // end foreach $post_data_content_places

                /**
                 * Get shortcodes from post metaboxes (custom fields).
                 */

                if (is_numeric($post->ID)) {

                    // get all available custom fields
                    $custom_fields = get_post_meta($post->ID);

                    // get each metabox (custom field) value
                    foreach ($custom_fields as $key => $value) {

                        // get metabox value
                        $metabox_value = get_post_meta($post->ID, $key, true);

                        // we have any shortcode tags? save it
                        if (!empty($metabox_value) && is_string($metabox_value) && false !== strpos($metabox_value, '[')) {

                            /**
                             * Ignore any serialized meta value when array.
                             * Note: this will solve issue with elementor meta value
                             * '_elementor_data'
                             *
                             * @since 1.0.6
                             */
                            if (false === strpos($metabox_value, '[{')) {

                                // save active shortcodes from post metabox value
                                self::$active_shortcodes = array_unique(array_merge(self::$active_shortcodes, Cleaner::clean($metabox_value, 'active_shortcodes')));

                                // save inactive shortcodes from post metabox value
                                self::$inactive_shortcodes = array_unique(array_merge(self::$inactive_shortcodes, Cleaner::clean($metabox_value, 'inactive_shortcodes')));

                                // collect inactive shortcodes data from post metabox value
                                foreach (Cleaner::clean($metabox_value, 'inactive_shortcodes') as $shortcode) {
                                    $inactive_shortcodes_data[$shortcode][] = array(
                                        'post_id'   => $post->ID,
                                        'post_type' => $post->post_type,
                                        'place'     => sprintf(__('Metabox " %s "', 'shortcode-cleaner'), $key),
                                    );
                                }

                            } // end check $metabox_value > '[{'

                        } // end check $metabox_value

                    } // end foreach $custom_fields

                } // end check is_numeric($post->ID)

            } // end foreach $posts

        } // end check $posts

        // reset original post data
        wp_reset_postdata();

        /**
         * Get shortcodes status from general settings.
         * options: blogname, blogdescription
         */

        // save settings data options
        $settings_data_options = array(
            array(
                'content' => get_option('blogname'),
                'place'   => __('Site Title', 'shortcode-cleaner'),
            ),
            array(
                'content' => get_option('blogdescription'),
                'place'   => __('Site Description', 'shortcode-cleaner'),
            ),
        );

        // get shortcodes from settings options values
        foreach ($settings_data_options as $data) {

            if (
                !empty($data['content']) &&
                false !== strpos($data['content'], '[')
            ) {

                // save active shortcodes from settings option
                self::$active_shortcodes = array_unique(array_merge(self::$active_shortcodes, Cleaner::clean($data['content'], 'active_shortcodes')));

                // save inactive shortcodes from settings option
                self::$inactive_shortcodes = array_unique(array_merge(self::$inactive_shortcodes, Cleaner::clean($data['content'], 'inactive_shortcodes')));

                // collect inactive shortcodes data from settings option
                foreach (Cleaner::clean($data['content'], 'inactive_shortcodes') as $shortcode) {
                    $inactive_shortcodes_data[$shortcode][] = array(
                        'custom' => 'settings',
                        'place'  => $data['place'],
                    );
                }

            } // end check $data['content']

        } // end foreach $settings_data_options

        /**
         * Get shortcodes status from sidebars widgets.
         */

        // get available widgets from sidebars

        // get available sidebars widgets
        $sidebars_widgets = wp_get_sidebars_widgets();

        // remove inactive widgets
        unset($sidebars_widgets['wp_inactive_widgets']);

        // get available widgets data
        foreach ($sidebars_widgets as $sidebar => $widgets) {

            // save sidebar name from id
            $sidebar_name = '';

            // get sidebar name from id
            global $wp_registered_sidebars;
            foreach ($wp_registered_sidebars as $key => $value) {
                if ($value['id'] === $sidebar) {
                    $sidebar_name = $value['name'];
                }
            }

            // we have widgets in sidebar? get widgets data
            if (!empty($widgets)) {

                // save unique sidebar widgets names
                $active_widgets = array();

                // handle widget name and save it
                foreach ($widgets as $widget) {

                    $widget_unique_number = explode('-', $widget);
                    $widget_unique_number = intval(end($widget_unique_number));

                    /**
                     * Remove last unique number from name {-2}.
                     * And solve "Invalid argument supplied for foreach()" issue that come
                     * from "Text" widget which the name was come like > "text2", so i check
                     * the name again before save it.
                     *
                     * @since 1.0.7
                     */
                    $widget = preg_replace('~-(\d)~', '', $widget);
                    $widget = preg_replace('/\d+/u', '', $widget);

                    // save widget name with unique number
                    if (isset($active_widgets[$widget])) {
                        array_push($active_widgets[$widget], $widget_unique_number);
                    } else {
                        $active_widgets[$widget] = array($widget_unique_number);
                    }

                } // end foreach $widgets

                /**
                 * Get shortcodes status data from widgets in this sidebar.
                 */

                foreach ($active_widgets as $widget => $unique) {

                    // get widgets data
                    $widgets_data = get_option('widget_' . $widget);

                    // get each widget data
                    foreach ($widgets_data as $widget_id => $data) {

                        // we are in exact sidebar? get widget data
                        if (in_array($widget_id, $unique, true)) {

                            foreach ($data as $key => $value) {

                                /**
                                 * Get shortcodes status from widget data value
                                 * And solve "parameter 1 to be string, array given" issue that come
                                 * from any widget content which sometimes have arrays not strings.
                                 *
                                 * I added this new code below:
                                 * is_string($value) &&
                                 *
                                 * @since 1.0.7
                                 */
                                if (!empty($value) && is_string($value) && false !== strpos($value, '[')) {

                                    // save active shortcodes from widget data value
                                    self::$active_shortcodes = array_unique(array_merge(self::$active_shortcodes, Cleaner::clean($value, 'active_shortcodes')));

                                    // save inactive shortcodes from widget data value
                                    self::$inactive_shortcodes = array_unique(array_merge(self::$inactive_shortcodes, Cleaner::clean($value, 'inactive_shortcodes')));

                                    // collect inactive shortcodes data from widget data value
                                    foreach (Cleaner::clean($value, 'inactive_shortcodes') as $shortcode) {
                                        $inactive_shortcodes_data[$shortcode][] = array(
                                            'custom'  => 'widgets',
                                            'sidebar' => $sidebar_name,
                                            'place'   => ucwords(str_replace('-', ' ', $widget)),
                                        );
                                    }

                                } // end check $_value

                            } // end foreach widget $data

                        } // end check $widget_id

                    } // end foreach $widgets_data

                } // end foreach $active_widgets

            } // end check $widgets

        } // end foreach $sidebars_widgets

        /**
         * Get shortcodes status from nav menus.
         */

        // save nav menus cache key
        $nav_menus_cache_key = Data::get_nav_menus_cache_key();

        // we don't have cached data? do new query
        if (false === ($nav_menus_items = get_transient($nav_menus_cache_key))) {

            // get available items (links) in nav menus
            $nav_menus_items = wp_get_nav_menus();

            // run cleaner process
            self::$run_cleaner_process = true;

            // set new wp cache
            set_transient($nav_menus_cache_key, $nav_menus_items, 24 * HOUR_IN_SECONDS);

        }

        // get available menu items data
        foreach ($nav_menus_items as $menu_object) {

            // save nav menus cache key
            $menus_items_cache_key = Data::get_nav_menus_items_cache_key() . '_' . $menu_object->slug;

            // we don't have cached data? do new query
            if (false === ($nav_menu_items_data = get_transient($menus_items_cache_key))) {

                // get items (links) data (maybe use $menu_object->name)
                $nav_menu_items_data = wp_get_nav_menu_items($menu_object->slug);

                // run cleaner process
                self::$run_cleaner_process = true;

                // set new wp cache
                set_transient($menus_items_cache_key, $nav_menu_items_data, 24 * HOUR_IN_SECONDS);

            }

            // we have items in menu? get items data
            if (!empty($nav_menu_items_data)) {

                // get nav menu item data
                foreach ($nav_menu_items_data as $item_object) {

                    // save menu item data content places
                    $menu_item_data_places = array(
                        array(
                            'content' => $item_object->title,
                        ),
                        array(
                            'content' => $item_object->attr_title,
                        ),
                        array(
                            'content' => $item_object->description,
                        ),
                    );

                    // get shortcodes from menu item content
                    // title, attr_title, description
                    foreach ($menu_item_data_places as $data) {

                        if (
                            !empty($data['content']) &&
                            false !== strpos($data['content'], '[')
                        ) {

                            // save active shortcodes from menu item content
                            self::$active_shortcodes = array_unique(array_merge(self::$active_shortcodes, Cleaner::clean($data['content'], 'active_shortcodes')));

                            // save inactive shortcodes from menu item content
                            self::$inactive_shortcodes = array_unique(array_merge(self::$inactive_shortcodes, Cleaner::clean($data['content'], 'inactive_shortcodes')));

                            // collect inactive shortcodes data from menu item content
                            foreach (Cleaner::clean($data['content'], 'inactive_shortcodes') as $shortcode) {
                                $inactive_shortcodes_data[$shortcode][] = array(
                                    'custom'  => 'menus',
                                    'menu'    => $menu_object->name,
                                    'menu_id' => $menu_object->term_id,
                                    'place'   => $item_object->title,
                                );
                            }

                        } // end check $data['content']

                    } // end foreach $menu_item_data_places

                } // end foreach $nav_menu_items_data

            } // end check $nav_menu_items_data

        } // end foreach $nav_menus_items

        /**
         * Output html content.
         */

        // check if run cleaner process
        $run_cleaner_class = '';
        if (self::$run_cleaner_process) {
            $run_cleaner_class = ' sc-run';
        }

        // get inactive shortcodes number
        $inactive_shortcodes_number = count(self::$inactive_shortcodes);

        /**
         * Save cleaner history data.
         */

        // save today
        $today = $date_now->format('j');

        // save cache key
        $history_data_cache_key = Data::get_cleaner_history_data_cache_key();

        // we don't have cached cleaner data? save new data
        if (empty($cleaner_data = get_option($history_data_cache_key))) {

            // save start date
            $cleaner_data[$today] = $inactive_shortcodes_number;

            // set new data cache
            update_option($history_data_cache_key, $cleaner_data);

        }

        // we don't have data today? save new data
        if (!isset($cleaner_data[$today])) {
            $cleaner_data[$today] = $inactive_shortcodes_number;
            update_option($history_data_cache_key, $cleaner_data);
        }

        // save data per day and increase shortcodes number
        if ($cleaner_data[$today] < $inactive_shortcodes_number) {

            // calculate difference between shortcodes numbers
            $difference_number = $inactive_shortcodes_number - $cleaner_data[$today];

            // save final shortcodes number
            $cleaner_data[$today] = $cleaner_data[$today] + $difference_number;
            update_option($history_data_cache_key, $cleaner_data);

        }

        /**
         * Handle data for history chart
         */

        // save days data
        $chart_days_data = array();
        foreach ($cleaner_data as $day => $number) {
            $chart_days_data[] = $day;
        }
        $chart_days_data = implode(',', $chart_days_data);

        // save shortcodes numbers data
        $chart_numbers_data = array();
        foreach ($cleaner_data as $day => $number) {
            $chart_numbers_data[] = $number;
        }
        $chart_numbers_data = implode(',', $chart_numbers_data);

        // get current formatted month, year
        $chart_current_month = (new \DateTime(self::$cleaner_start_date))->format('M Y');

        // start html content
        $content .= '<div id="shortcode-cleaner-dashboard" class="sc-welcome-dashboard' . $run_cleaner_class . '">';

        // welcome top html content
        $content .= '<div class="sc-welcome-top">';
        $content .= '<h1 class="welcome-text">' . __('Welcome to Shortcode Cleaner Dashboard', 'shortcode-cleaner') . '</h1>';
        $content .= '<fieldset><a href="#" class="button button-primary sc-refresh-data"><i class="fas fa-sync-alt"></i> ' . __('Refresh', 'shortcode-cleaner') . '</a></fieldset><div class="clear"></div>';
        $content .= '</div><!-- /end .sc-welcome-top -->';
        $content .= '<div class="clear"></div>';

        /**
         * cleaner status and history html content
         */

        // start status html content
        $content .= '<div class="sc-welcome-center"><div class="pure-g">';

        // cleaner status content
        $content .= '<div class="pure-u-1 pure-u-xl-1-2"><div class="sc-data cleaner-status">';
        $content .= '<h3>' . __('Cleaner Status', 'shortcode-cleaner') . '</h3>';
        $content .= '<div id="cleaner-status-data" class="cleaner-status-data">';

        // status scan
        $content .= '<div class="status-data-scan">';
        $content .= '<div id="scan-process"></div>';
        $content .= '</div><!-- /end .status-data-scan -->';
        // status number
        $content .= '<div class="status-data-number">';

        // we don't have any broken shortcodes?
        if ($inactive_shortcodes_number === 0) {
            $content .= '<h2 class="broken-shortcodes none"><i class="far fa-smile"></i>' . __('Woohoo! You don\'t have any broken shortcodes.', 'shortcode-cleaner') . '</h2>';
        } else {
            $content .= '<h2 class="broken-shortcodes">' . $inactive_shortcodes_number . '</h2>';
            $content .= '<h4 class="broken-title">' . __('Broken Shortcodes Cleaned', 'shortcode-cleaner') . '</h4>';
        }

        $content .= '</div><!-- /end .status-data-number -->';

        $content .= '</div><!-- /end .cleaner-status-data -->';
        $content .= '</div></div><!-- /end .cleaner-status -->';

        // cleaner history content
        $content .= '<div class="pure-u-1 pure-u-xl-1-2"><div class="sc-data cleaner-history">';
        $content .= '<h3>' . __('Cleaner History', 'shortcode-cleaner') . '</h3>';
        $content .= '<div class="sc-data-loading"><i class="fas fa-spinner fa-pulse fa-2x fa-fw"></i></div>';
        $content .= '<div class="cleaner-history-data">';

        // chart data
        $content .= '<input type="hidden" id="sc_days_data" name="sc_days_data" value="[' . $chart_days_data . ']"><input type="hidden" id="sc_numbers_data" name="sc_numbers_data" value="[' . $chart_numbers_data . ']"><input type="hidden" id="yAxes_label" name="yAxes_label" value="' . __('Broken Shortcodes', 'shortcode-cleaner') . '"><input type="hidden" id="xAxes_label" name="xAxes_label" value="' . $chart_current_month . '">';
        $content .= '<canvas id="sc-cleaner-data" width="400" height="211"></canvas>';

        $content .= '</div><!-- /end .cleaner-history-data -->';
        $content .= '</div></div><!-- /end .cleaner-history -->';

        // end status html content
        $content .= '</div></div><!-- /end .sc-welcome-center -->';
        $content .= '<div class="clear"></div>';

        // we don't have any broken shortcodes?
        $hide_broken_shortcodes_list_class = '';
        if ($inactive_shortcodes_number === 0) {
            $hide_broken_shortcodes_list_class = ' hide';
        }

        // start broken shortcodes html content
        $content .= '<div class="sc-broken-shortcodes' . $hide_broken_shortcodes_list_class . '">';

        $content .= '<div class="sc-data shortcodes-list">';
        $content .= '<h3>' . __('Broken Shortcodes', 'shortcode-cleaner') . '<div id="search-broken-shortcode"><input type="text" placeholder="' . __('Search', 'shortcode-cleaner') . '.."><i class="fas fa-search"></i></div><div class="clear"></div></h3>';
        $content .= '<div id="broken-shortcodes-list" class="broken-shortcodes-list">';
        $content .= '<div class="pure-g">';

        // save shortcodes data html content
        $shortcodes_data_content = '';

        /**
         * Ouput inactive shortcodes data html content.
         */

        foreach ($inactive_shortcodes_data as $shortcode => $data) {

            // start shortcode tag name
            $shortcodes_data_content .= '<div class="pure-u-1-2 pure-u-md-1-2 pure-u-lg-1-2 pure-u-xl-1-3">';
            $shortcodes_data_content .= '<div class="broken-shortcode-tag">';

            // tag item
            $shortcodes_data_content .= '<div class="sc-tag-item">';
            $shortcodes_data_content .= '<span class="sc-tag-name">[' . $shortcode . ']</span>';

            // handle tag name for id
            $shortcode_id = trim($shortcode);
            $shortcode_id = str_replace(' ', '-', $shortcode_id);
            $shortcode_id = preg_replace('/[^A-Za-z0-9\-]/', '', $shortcode_id);

            $shortcodes_data_content .= '<span class="sc-show-details cs-help" data-title="' . __('Details', 'shortcode-cleaner') . '" data-id="' . $shortcode_id . '" data-name="' . $shortcode . '"><i class="fas fa-list-ul"></i></span>';
            $shortcodes_data_content .= '</div><!-- /end .sc-tag-item -->';

            // tag item details
            $shortcodes_data_content .= '<div id="' . $shortcode_id . '" class="sc-tag-details">';
            $shortcodes_data_content .= '<h4>' . __('This Broken Shortcode Found In', 'shortcode-cleaner') . ':</h4>';

            /**
             * separate broken shortcode tag places.
             */

            // posts
            $check_posts_data      = false;
            $shortcodes_posts_data = '<table class="pure-table pure-table-bordered"><thead><tr><th>' . __('Post Title', 'shortcode-cleaner') . '</th><th>' . __('Post Type', 'shortcode-cleaner') . '</th><th>' . __('Places', 'shortcode-cleaner') . '</th><th><i class="fas fa-ellipsis-h"></i></th></tr></thead><tbody>';

            // sidebars
            $check_sidebars_data      = false;
            $shortcodes_sidebars_data = '<table class="pure-table pure-table-bordered"><thead><tr><th>' . __('Sidebar Name', 'shortcode-cleaner') . '</th><th>' . __('Widgets', 'shortcode-cleaner') . '</th><th><i class="fas fa-ellipsis-h"></i></th></tr></thead><tbody>';

            // menus
            $check_menus_data      = false;
            $shortcodes_menus_data = '<table class="pure-table pure-table-bordered"><thead><tr><th>' . __('Menu Name', 'shortcode-cleaner') . '</th><th>' . __('Links', 'shortcode-cleaner') . '</th><th><i class="fas fa-ellipsis-h"></i></th></tr></thead><tbody>';

            // settings
            $check_settings_data      = false;
            $shortcodes_settings_data = '<table class="pure-table pure-table-bordered"><thead><tr><th>' . __('General Settings', 'shortcode-cleaner') . '</th><th>' . __('Options', 'shortcode-cleaner') . '</th><th><i class="fas fa-ellipsis-h"></i></th></tr></thead><tbody>';

            /**
             * Handle shortcodes data before show.
             * Note: we merge same unique data as one data and just collect places for it,
             * ex:
             * unique post > have more thans content places
             * unique sidebar > have more thans content widgets
             * unique settings > have more thans content options
             * unique menu > have more thans content items
             */

            // save shortcodes data
            $shortcodes_data = array();

            // get shortcodes data
            foreach ($data as $shortcode_data) {

                // get custom shortcode data > settings, widgets and menus
                if (isset($shortcode_data['custom'])) {

                    // get settings shortcode data
                    if ($shortcode_data['custom'] === 'settings') {

                        // the settings exists? push its place to current settings
                        if (isset($shortcodes_data[$shortcode_data['custom']])) {

                            array_push($shortcodes_data[$shortcode_data['custom']]['places'], $shortcode_data['place']);

                        } else {
                            // save this settings data as a new settings

                            $shortcodes_data[$shortcode_data['custom']] = array(
                                'places' => array($shortcode_data['place']),
                            );

                        }

                    } // end check settings

                    // get widgets shortcode data
                    if ($shortcode_data['custom'] === 'widgets') {

                        // the sidebar exists? push its widgets to current sidebar
                        if (isset($shortcodes_data[$shortcode_data['sidebar']])) {

                            array_push($shortcodes_data[$shortcode_data['sidebar']]['places'], $shortcode_data['place']);

                        } else {
                            // save this sidebar widgets data as a new sidebar

                            $shortcodes_data[$shortcode_data['sidebar']] = array(
                                'custom' => $shortcode_data['custom'],
                                'places' => array($shortcode_data['place']),
                            );

                        }

                    } // end check widgets

                    // get menus shortcode data
                    if ($shortcode_data['custom'] === 'menus') {

                        // the menu exists? push its items to current menu
                        if (isset($shortcodes_data[$shortcode_data['menu']])) {

                            array_push($shortcodes_data[$shortcode_data['menu']]['places'], $shortcode_data['place']);

                        } else {
                            // save this menu items data as a new menu

                            $shortcodes_data[$shortcode_data['menu']] = array(
                                'custom'  => $shortcode_data['custom'],
                                'menu_id' => $shortcode_data['menu_id'],
                                'places'  => array($shortcode_data['place']),
                            );

                        }

                    } // end check menus

                } else {
                    // get shortcode data from posts

                    // the post id exists? push its place to current post
                    if (isset($shortcodes_data[$shortcode_data['post_id']])) {

                        array_push($shortcodes_data[$shortcode_data['post_id']]['places'], $shortcode_data['place']);

                    } else {
                        // save this post data as a new post

                        $shortcodes_data[$shortcode_data['post_id']] = array(
                            'post_type' => $shortcode_data['post_type'],
                            'places'    => array($shortcode_data['place']),
                        );

                    }

                } // end check $shortcode_data['custom']

            } // end foreach $data

            // now we can show shortcode data html content
            foreach ($shortcodes_data as $key => $value) {

                // collect shortcode data places
                $shortcode_data_places = implode(', ', array_unique($value['places']));

                /**
                 * Output shortcode data html content
                 */

                // output custom shortcode settings data html content
                if ($key === 'settings') {

                    // this will show settings content
                    $check_settings_data = true;

                    $shortcodes_settings_data .= '<tr>';

                    $shortcodes_settings_data .= '<td><a href="' . admin_url('options-general.php') . '" target="_blank" title="' . __('Edit', 'shortcode-cleaner') . '">' . ucwords(Cleaner::clean($key)) . '</a></td><td>' . $shortcode_data_places . '</td><td><a href="' . admin_url('options-general.php') . '" target="_blank" title="' . __('Edit', 'shortcode-cleaner') . '"><i class="fas fa-edit"></i></a></td>';

                    $shortcodes_settings_data .= '</tr>';

                } elseif (isset($value['custom']) && $value['custom'] === 'widgets') {
                    // output custom shortcode sidebar widgets data html content

                    // this will show sidebars content
                    $check_sidebars_data = true;

                    $shortcodes_sidebars_data .= '<tr>';

                    $shortcodes_sidebars_data .= '<td><a href="' . admin_url('widgets.php') . '" target="_blank" target="_blank" title="' . __('Edit', 'shortcode-cleaner') . '">' . ucwords(Cleaner::clean($key)) . '</a></td><td>' . $shortcode_data_places . '</td><td><a href="' . admin_url('widgets.php') . '" target="_blank" target="_blank" title="' . __('Edit', 'shortcode-cleaner') . '"><i class="fas fa-edit"></i></a></td>';

                    $shortcodes_sidebars_data .= '</tr>';

                } elseif (isset($value['custom']) && $value['custom'] === 'menus') {
                    // output custom shortcode menus data html content

                    // this will show menus content
                    $check_menus_data = true;

                    $shortcodes_menus_data .= '<tr>';

                    $shortcodes_menus_data .= '<td><a href="' . admin_url('nav-menus.php?action=edit&menu=' . $value['menu_id']) . '" target="_blank" title="' . __('Edit', 'shortcode-cleaner') . '">' . ucwords(Cleaner::clean($key)) . '</a></td><td>' . $shortcode_data_places . '</td><td><a href="' . admin_url('nav-menus.php?action=edit&menu=' . $value['menu_id']) . '" target="_blank" title="' . __('Edit', 'shortcode-cleaner') . '"><i class="fas fa-edit"></i></a></td>';

                    $shortcodes_menus_data .= '</tr>';

                } else {
                    // output posts shortcode data html content

                    // this will show posts content
                    $check_posts_data = true;

                    $shortcodes_posts_data .= '<tr>';

                    $shortcodes_posts_data .= '<td><a href="' . get_the_permalink($key) . '" target="_blank" title="' . __('View', 'shortcode-cleaner') . '">' . Cleaner::clean(get_the_title($key)) . '</a></td><td>' . $value['post_type'] . '</td><td>' . $shortcode_data_places . '</td><td><a href="' . get_the_permalink($key) . '" target="_blank" title="' . __('View', 'shortcode-cleaner') . '"><i class="fas fa-eye"></i></a> <a href="' . get_edit_post_link($key) . '" target="_blank" title="' . __('Edit', 'shortcode-cleaner') . '"><i class="fas fa-edit"></i></a></td>';

                    $shortcodes_posts_data .= '</tr>';

                } // end check custom $key

            } // end foreach $shortcodes_data

            // close shortcode data tables
            $shortcodes_posts_data .= '</tbody></table>';
            $shortcodes_sidebars_data .= '</tbody></table>';
            $shortcodes_menus_data .= '</tbody></table>';
            $shortcodes_settings_data .= '</tbody></table>';

            // check if we have content?
            if ($check_posts_data) {
                $shortcodes_data_content .= $shortcodes_posts_data;
            }
            if ($check_sidebars_data) {
                $shortcodes_data_content .= $shortcodes_sidebars_data;
            }
            if ($check_menus_data) {
                $shortcodes_data_content .= $shortcodes_menus_data;
            }
            if ($check_settings_data) {
                $shortcodes_data_content .= $shortcodes_settings_data;
            }

            $shortcodes_data_content .= '</div><!-- /end .sc-tag-details -->';

            // end broken shortcode
            $shortcodes_data_content .= '</div></div><!-- /end .broken-shortcode-tag -->';

        } // end foreach $inactive_shortcodes_data

        // appened shortcodes data html content
        $content .= $shortcodes_data_content;

        // end broken shortcodes list content
        $content .= '</div></div><!-- /end .broken-shortcodes-list -->';
        $content .= '</div><!-- /end .shortcodes-list -->';

        // end broken shortcodes html content
        $content .= '</div><!-- /end .sc-broken-shortcodes -->';
        $content .= '<div class="clear"></div>';

        $content .= '</div><!-- /end .sc-welcome-dashboard -->';

        return $content;

    }

}
