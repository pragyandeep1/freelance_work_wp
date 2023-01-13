<?php

namespace JO\ShortcodeCleaner\Module;

use Codestar\Codestar;
use JO\Module\CodestarCustomStyles\Enqueue;
use JO\ShortcodeCleaner\Core\Data;
use JO\ShortcodeCleaner\Module\Status;

/**
 * Plugin settings page.
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
class Settings
{

    /**
     * Register settings page.
     *
     * @since 1.0.0
     * @access public
     */
    public static function register()
    {

        /**
         * Setup settings page.
         */

        Codestar::make('settings_options')
            ->set_options_id(Data::get_unique_settings_options_id())
            ->add_page(
                array(
                    'page_title' => Data::PLUGIN_NAME,
                    'menu_title' => Data::PLUGIN_SHORT_NAME,
                    'menu_slug'  => Data::PLUGIN_MENU_PAGE_SLUG,
                    'icon_url'   => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAAEEfUpiAAAABGdBTUEAALGPC/xhBQAAA9FJREFUWAm1l0uIjlEYx2cMwyAL18kCiWRhqxQpQzbsJJJyWbksKEnZISVMFjKZXJKFLCyQRJSUsHJJpCRm4W5BKXd+//M9z3He877fN8blqf/73P7nOZf3fOc9X1NTJs3u/0Bk95OBjMA+FJI1v5b1QE8weJBsCzXEsuCmfmZ0mn4WtNW5Z8HQtpXgZwXQX4wQVH89jXkO8yR2f/OltqpFIEhLFEXtcbvZjMvEB4BJ4AHoaEbQjYXGD1XulmheUjqR9liC4LMkEUwl4yzUp6KxhRlhpTwpnRDeBtvKnjXt04yVfK1jQxHTSk5oM4bWo7FQoB3sbsyqLYt6Czz0dzBODlqznWf2Cex6skkcLdMctIZ2GLuJ8c9DrwMXRWggYXp6qPFcoHm20fAGerAtxHHserIsJGgQRA5GXG4LX7P4fvNdrQ+NLRmCZucF/L35ftoB+WNsnBcwf4R1c9CJ3q37qdaKvyMwLA3Ws9MNUuBQZCCYWAj24sA/BBaVaAQ7wZhSIgmQ7waSbq38FHBMeXT4yaA/mD8Eu548DjXJrgLPg1MrMh9fhWaBT6BSnF/SsFWwV/GG6ukMjv/a1mD3gE9OqKf9jYQ5Z6RB+N/AlyxecL2An5IvyT4yhnbaYKDcV4uVFCNvocg3rbykctcR97cSSMljqVcMP0l3RMjsIfgFjvL0fMJ5paQnTE+DXDjjiN9OOb4GaaxgMyh94NSrL7jOjii9FjDmGwq1q0hsaUZhCilBNnI9abAtsaPpI1hAD5diFCPx9VmrK15gLAwhlY7UwX6f+dUuPc8EhalVM/sepW4LOANcHpQWNS8L8yax6Ra/yrrOTjnkl+Br9hdAi0ETcFta/kIQvkVoibZTrRZFTvuQ0ItD2h74R5JcV5YbSe5Jkv8d8zWkyWmdYBOcCkaVEhYgp6vUSnAH/I1sTPvwTaQfii5PlUJvR0msqEz2PRj7VFNdR182mI6+s60MbiXcVnBKjf5StCeiaHOMjl7ZWEBIn5XzaB2f+gpqBvEww+6rFFbAj2NfhHgse1USflEQ5zLQWa92Wr2j4E9kgtevOwCqrgZ7wYGsh7v4O8FQG4gO2a6MU+Wejp2mRsaMK0C8dM/KuHJnZLX2VXA8ND7luv1PTjx60M1qOHtkA9Dhtss7ML2R8NMsFtx/MgAq6XW8ZRB6PaPobIsNZDPxbdj7Qm8Vj+KOLBLO4fYA3VJWJan72FfMf5HEZU4DrxhEF52uBb3+16i7Cb0wxdJfAe6vC0xfOM4taVX8DxI3c6nDLKBXsBxsAW1Z7k9dXZq3/27jn4eMLUGBci9yAAAAAElFTkSuQmCC',
                    'position'   => 2,
                )
            )
            ->set_config(
                array(
                    'title'                               => '<img alt="' . Data::PLUGIN_NAME . '" src="' . Data::get_plugin_url() . Data::PLUGIN_ASSETS . '/admin/images/' . Data::PLUGIN_SLUG . '.png"/> ' . Data::PLUGIN_NAME . ' <a href="https://1.envato.market/a7d2Z" class="sc-upgrade-now" target="_blank"><i class="fas fa-star"></i> ' . __('Upgrade Now!', 'shortcode-cleaner') . '</a>',
                    'theme_skin'                          => '',
                    'ajax_save'                           => true,
                    'show_reset_all'                      => true,
                    'allow_sticky_header'                 => true,
                    'powered_by_text'                     => sprintf(__('Version %1s, by %2s', 'shortcode-cleaner'), '<a>' . Data::PLUGIN_VERSION . '</a>', '<a href="https://jozoor.com/" target="_blank">Jozoor</a>'),
                    'version_text'                        => '<div class="sc-social"><a href="https://twitter.com/jozoor" target="_blank"><i class="fab fa-twitter"></i></a><a href="https://www.facebook.com/Jozoor" target="_blank"><i class="fab fa-facebook-f"></i></a><a href="https://youtube.com/jozoor" target="_blank"><i class="fab fa-youtube"></i></a><a href="https://dribbble.com/jozoor" target="_blank"><i class="fab fa-dribbble"></i></a><a href="https://jozoor.com/contact" target="_blank"><i class="far fa-envelope"></i></a></div>',
                    'hide_show_all_options'               => true,
                    'button_save_text'                    => esc_html__('Save Changes', 'shortcode-cleaner'),
                    'button_reset_section_text'           => esc_html__('Reset Section', 'shortcode-cleaner'),
                    'button_reset_all_text'               => esc_html__('Reset All', 'shortcode-cleaner'),
                    'after_saved_text'                    => sprintf(__('%s Settings Saved', 'shortcode-cleaner'), '<i class="cs-icon fas fa-check"></i>'),
                    'on_saving_text'                      => esc_html__('Saving...', 'shortcode-cleaner'),
                    'after_saved_text'                    => esc_html__('Settings saved.', 'shortcode-cleaner'),
                    'after_imported_text'                 => esc_html__('Success. Imported backup options.', 'shortcode-cleaner'),
                    'after_all_options_restored_text'     => esc_html__('Default options restored.', 'shortcode-cleaner'),
                    'after_section_options_restored_text' => esc_html__('Default options restored for only this section.', 'shortcode-cleaner'),
                    'show_all_options_text'               => esc_html__('show all options', 'shortcode-cleaner'),

                )

            )->add_options(self::options());

        /**
         * Enqueue admin menu svg icon style.
         */
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue'));

        /**
         * Append broken shortcode tag details dialog.
         */
        add_action('admin_footer', array(__CLASS__, 'broken_shortcode_dialog'), 99);

        /**
         * Append new content before dashboard buttons in header.
         */
        add_action(
            'cs_settings_' . Data::get_unique_settings_options_id() . '_before_header_buttons',
            array(__CLASS__, 'append_header_content')
        );

        /**
         * Enqueue custom styles for Codestar framework.
         */

        // make sure that Codestar custom styles exists
        if (class_exists(Enqueue::class)) {
            Enqueue::register();
        }

        /**
         * Enqueue plugin styles and scripts.
         */

        // enqueue plugin styles
        add_action('cs_enqueue_custom_styles', array(__CLASS__, 'enqueue_styles'));

        // enqueue plugin scripts
        add_action('cs_enqueue_custom_scripts', array(__CLASS__, 'enqueue_scripts'));

        /**
         * Flush shortcodes status.
         */

        // flush shortcodes status after post saved or updated
        add_action('save_post', array(__CLASS__, 'flush_shortcodes_status_cache'));

        // flush shortcodes status after post meta added
        add_action(
            'added_post_meta', array(__CLASS__, 'flush_shortcodes_status_cache'), 10, 4
        );

        // flush shortcodes status after post meta updated
        add_action(
            'updated_post_meta', array(__CLASS__, 'flush_shortcodes_status_cache'), 10, 4
        );

        // flush shortcodes status after blogname option updated from settings
        add_action(
            'update_option_blogname', array(__CLASS__, 'flush_shortcodes_status_cache'), 10, 2
        );

        // flush shortcodes status after blogdescription option updated from settings
        add_action(
            'update_option_blogdescription', array(__CLASS__, 'flush_shortcodes_status_cache'), 10, 2
        );

        // flush shortcodes status after widgets saved or updated (filter)
        add_filter(
            'widget_update_callback', array(__CLASS__, 'flush_shortcodes_status_cache_widgets'), 10, 4
        );

        // flush shortcodes status after navigation menu created
        add_action(
            'wp_create_nav_menu', array(__CLASS__, 'flush_shortcodes_status_cache'), 10, 2
        );

        // flush shortcodes status after navigation menu updated
        add_action(
            'wp_update_nav_menu', array(__CLASS__, 'flush_shortcodes_status_cache'), 10, 2
        );

        // flush shortcodes status after any plugin is activated
        add_action(
            'activated_plugin', array(__CLASS__, 'flush_shortcodes_status_cache'), 10, 2
        );

        // flush shortcodes status after any plugin is deactivated
        add_action(
            'deactivated_plugin', array(__CLASS__, 'flush_shortcodes_status_cache'), 10, 2
        );

        // flush shortcodes status after a theme switch
        add_action(
            'switch_theme', array(__CLASS__, 'flush_shortcodes_status_cache')
        );

    }

    /**
     * Append new buttons before dashboard buttons in header.
     *
     * @since 1.0.0
     * @access public
     */
    public static function append_header_content()
    {
        echo '<a href="https://plugins.jozoor.com/shortcode-cleaner" class="sc-header-links" target="_blank"><i class="fas fa-home"></i> ' . __('Homepage', 'shortcode-cleaner') . '</a>';
        echo '<a href="https://help.jozoor.com/wordpress.org/support/" class="sc-header-links" target="_blank"><i class="fas fa-life-ring"></i> ' . __('Support', 'shortcode-cleaner') . '</a>';
        echo '<a href="https://help.jozoor.com/envato/" class="sc-header-links" target="_blank"><i class="fas fa-comment-alt"></i> ' . __('Feedback', 'shortcode-cleaner') . '</a>';
    }

    /**
     * Enqueue admin menu svg icon style.
     *
     * @since 1.0.0
     * @access public
     * @param  string $hook current admin page hook name
     */
    public static function enqueue($hook)
    {
        wp_enqueue_style(Data::PLUGIN_SLUG . '-icon', Data::get_plugin_url() . Data::PLUGIN_ASSETS . '/admin/css/' . Data::PLUGIN_SMALL_SLUG . '-icon.min.css', array(), Data::PLUGIN_VERSION, 'all');
    }

    /**
     * Append broken shortcode tag details dialog.
     *
     * @since 1.0.0
     * @access public
     */
    public static function broken_shortcode_dialog()
    {
        echo '<div id="sc-broken-shortcode-tag" class="cs-dialog sc-broken-shortcode-dialog" style="display: none;">';
        echo '<div class="load-shortcode-details"></div>';
        echo '</div>';
    }

    /**
     * Actually enqueue plugin styles.
     *
     * @since 1.0.0
     * @access public
     */
    public static function enqueue_styles()
    {

        // enqueue vendor styles
        wp_enqueue_style(Data::PLUGIN_SLUG . '-purecss', Data::get_plugin_url() . Data::PLUGIN_ASSETS . '/admin/css/vendor/purecss.css', array(), '1.0.0', 'all');

        // enqueue plugin styles
        wp_enqueue_style(Data::PLUGIN_SLUG, Data::get_plugin_url() . Data::PLUGIN_ASSETS . '/admin/css/' . Data::PLUGIN_SLUG . '.min.css', array(), Data::PLUGIN_VERSION, 'all');

        // enqueue plugin rtl styles
        if (is_rtl()) {

            wp_enqueue_style(Data::PLUGIN_SLUG . '-rtl', Data::get_plugin_url() . Data::PLUGIN_ASSETS . '/admin/css/' . Data::PLUGIN_SLUG . '-rtl.min.css', array(), Data::PLUGIN_VERSION, 'all');

        } // end check rtl

    }

    /**
     * Actually enqueue plugin scripts.
     *
     * @since 1.0.0
     * @access public
     */
    public static function enqueue_scripts()
    {

        // enqueue vendor scripts
        wp_enqueue_script(Data::PLUGIN_SLUG . '-progressbar', 'https://cdnjs.cloudflare.com/ajax/libs/progressbar.js/1.0.1/progressbar.min.js', array(), '1.0.1', true);
        wp_enqueue_script(Data::PLUGIN_SLUG . '-chartjs', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js', array(), '2.7.1', true);

        // enqueue plugin scripts
        wp_enqueue_script(Data::PLUGIN_SLUG, Data::get_plugin_url() . Data::PLUGIN_ASSETS . '/admin/js/' . Data::PLUGIN_SLUG . '.min.js', array('jquery'), Data::PLUGIN_VERSION, true);
    }

    /**
     * Flush shortcodes status on save or update data.
     *
     * @since 1.0.0
     * @access public
     */
    public static function flush_shortcodes_status_cache()
    {

        // delete shortcodes status cached data
        delete_transient(Data::get_shortcodes_status_cache_key());

        // delete nav menus cached data
        delete_transient(Data::get_nav_menus_cache_key());

        // delete nav menus items (links) cached data
        foreach (wp_get_nav_menus() as $menu_object) {
            delete_transient(Data::get_nav_menus_items_cache_key() . '_' . $menu_object->slug);
        }

    }

    /**
     * Flush shortcodes status on save or update widgets.
     *
     * @since 1.0.0
     * @access public
     * @param  array  $instance current widget instance's settings
     * @param  array  $new      an array of new widget settings
     * @param  array  $old      an array of old widget settings
     * @param  Object $obj      current widget instance
     */
    public static function flush_shortcodes_status_cache_widgets($instance, $new, $old, $obj)
    {

        // delete shortcodes status cached data
        delete_transient(Data::get_shortcodes_status_cache_key());

        // delete nav menus cached data
        delete_transient(Data::get_nav_menus_cache_key());

        // delete nav menus items (links) cached data
        foreach (wp_get_nav_menus() as $menu_object) {
            delete_transient(Data::get_nav_menus_items_cache_key() . '_' . $menu_object->slug);
        }

        // we should finally return the widget instance
        return $instance;

    }

    /**
     * Add settings options.
     *
     * @since 1.0.0
     * @access public
     * @return array
     */
    public static function options()
    {

        // save all available default options
        $options = array();

        /**
         * ----------------------------------------
         * Dashboard section
         * ----------------------------------------
         */
        $options[] = array(
            'name'   => 'dashboard',
            'title'  => __('Dashboard', 'shortcode-cleaner'),
            'icon'   => 'fas fa-tachometer-alt',
            'fields' => array(
                array(
                    'type'    => 'content',
                    'content' => Status::content(),
                ),
            ),
        );

        /**
         * ----------------------------------------
         * Settings section
         * ----------------------------------------
         */
        $options[] = array(
            'name'     => 'settings',
            'title'    => __('Settings', 'shortcode-cleaner'),
            'icon'     => 'fas fa-cog',
            'sections' => array(

                // general settings
                array(
                    'name'   => 'general',
                    'title'  => __('General', 'shortcode-cleaner'),
                    'icon'   => 'fas fa-sliders-h',
                    'fields' => array(

                        array(
                            'type'    => 'heading',
                            'content' => sprintf(__('%sGeneral Settings', 'shortcode-cleaner'), '<i class="fas fa-sliders-h"></i> '),
                        ),

                        // enable clean frontend content
                        array(
                            'id'        => 'enable_clean_frontend_content',
                            'type'      => 'switcher',
                            'title'     => __('Enable Cleaner on the Frontend', 'shortcode-cleaner'),
                            'label'     => __('clean the frontend site content.', 'shortcode-cleaner'),
                            'default'   => true,
                            'help'      => __('Cleaner will just hide this broken shotcodes from any frontend site content without remove it.', 'shortcode-cleaner'),
                            'on_title'  => __('ON', 'shortcode-cleaner'),
                            'off_title' => __('OFF', 'shortcode-cleaner'),
                        ),

                        // ignore broken shortcodes from cleaning
                        array(
                            'id'                    => 'ignore_inactive_shortcode_tags',
                            'type'                  => 'tags',
                            'title'                 => __('Ignore this broken Shortcodes from Cleaning', 'shortcode-cleaner'),
                            'after'                 => sprintf(__('%1sseparate shortcode tags with commas%2s', 'shortcode-cleaner'), '<p><i class="cs-text-muted">', ', ex: tag_1, tag_2</i></p>'),
                            'help'                  => __('Cleaner will not clean this broken shotcodes and let it appear as normal broken.', 'shortcode-cleaner'),
                            'attributes'            => array(
                                'placeholder' => '',
                                'style'       => 'width: 60%;',
                            ),
                            'default'               => '',
                            'add_tags_button_title' => __('Add', 'shortcode-cleaner'),
                        ),

                        // ignore any shortcodes within HTML code tags
                        array(
                            'id'                    => 'set_html_code_tags',
                            'type'                  => 'tags',
                            'title'                 => __('Show broken Shortcodes within this HTML code tags', 'shortcode-cleaner'),
                            'after'                 => sprintf(__('%1sseparate HTML code tags with commas%2s', 'shortcode-cleaner'), '<p><i class="cs-text-muted">', ', ex: code, pre</i></p>'),
                            'help'                  => __('Cleaner will not clean any broken shotcodes within this HTML code tags.', 'shortcode-cleaner'),
                            'attributes'            => array(
                                'placeholder' => '',
                                'style'       => 'width: 60%;',
                            ),
                            'default'               => 'code, pre, script, style',
                            'add_tags_button_title' => __('Add', 'shortcode-cleaner'),
                        ),

                        // clean all broken shortcodes content
                        array(
                            'id'        => 'clean_all_inactive_shortcode_tags_content',
                            'type'      => 'switcher',
                            'title'     => __('Clean all broken Shortcodes Content', 'shortcode-cleaner'),
                            'label'     => __('clean any content within broken shortcodes.', 'shortcode-cleaner'),
                            'help'      => __('In normal we keep any content between broken shortcods if exists after cleaning, but here you can clean broken shortcodes content also.', 'shortcode-cleaner'),
                            'default'   => false,
                            'on_title'  => __('ON', 'shortcode-cleaner'),
                            'off_title' => __('OFF', 'shortcode-cleaner'),
                        ),

                        // clean this broken shortcodes content
                        array(
                            'id'                    => 'clean_inactive_shortcode_tags_content',
                            'type'                  => 'tags',
                            'title'                 => __('Clean this broken Shortcodes Content', 'shortcode-cleaner'),
                            'after'                 => sprintf(__('%1sseparate shortcode tags with commas%2s', 'shortcode-cleaner'), '<p><i class="cs-text-muted">', ', ex: tag_1, tag_2</i></p>'),
                            'help'                  => __('In normal we keep any content between broken shortcods if exists after cleaning, but here you can clean broken shortcodes content also.', 'shortcode-cleaner'),
                            'attributes'            => array(
                                'placeholder' => '',
                                'style'       => 'width: 60%;',
                            ),
                            'default'               => '',
                            'dependency'            => array('clean_all_inactive_shortcode_tags_content', '!=', 'true'),
                            'add_tags_button_title' => __('Add', 'shortcode-cleaner'),
                        ),

                        // upgrade to pro version notice for this features
                        array(
                            'type'    => 'notice',
                            'class'   => 'success',
                            'content' => sprintf(__('%1sUpgrade to %2sShortcode Cleaner Pro%3s to Unlock this Features.%4s', 'shortcode-cleaner'), '<div class="sc-upgrade-notice"><i class="fas fa-unlock-alt"></i> ', '<a href="https://1.envato.market/a7d2Z" target="_blank"><b>', '</b></a>', '</div>'),
                        ),

                        // enable clean backend admin content
                        array(
                            'id'         => 'enable_clean_backend_content_pro',
                            'type'       => 'switcher',
                            'title'      => '<div class="sc-pro-features">' . __('Enable Cleaner on the Backend', 'shortcode-cleaner') . '</div>',
                            'label'      => __('clean the backend admin content.', 'shortcode-cleaner'),
                            'default'    => false,
                            'help'       => __('Cleaner will just hide this broken shotcodes from any backend admin content, until you update or save the cleaned content, after that all this broken shortcodes will be removed completely.', 'shortcode-cleaner'),
                            'on_title'   => __('ON', 'shortcode-cleaner'),
                            'off_title'  => __('OFF', 'shortcode-cleaner'),
                            'attributes' => array(
                                'readyonly' => 'only-key',
                                'disabled'  => 'only-key',
                            ),
                            'before'     => '<div class="sc-pro-features">',
                            'after'      => '</div>',
                        ),

                        // show admin bar link
                        array(
                            'id'         => 'show_admin_bar_link_pro',
                            'type'       => 'switcher',
                            'title'      => '<div class="sc-pro-features">' . __('Show broken Shortcodes Status in Admin Bar', 'shortcode-cleaner') . '</div>',
                            'label'      => __('show unused broken shortcodes status.', 'shortcode-cleaner'),
                            'default'    => false,
                            'on_title'   => __('ON', 'shortcode-cleaner'),
                            'off_title'  => __('OFF', 'shortcode-cleaner'),
                            'attributes' => array(
                                'readyonly' => 'only-key',
                                'disabled'  => 'only-key',
                            ),
                            'before'     => '<div class="sc-pro-features">',
                            'after'      => '</div>',
                        ),

                        // force this active shortcodes to be broken
                        array(
                            'id'                    => 'set_inactive_shortcode_tags_pro',
                            'type'                  => 'tags',
                            'title'                 => '<div class="sc-pro-features">' . __('Force this active Shortcodes to be broken (inactive)', 'shortcode-cleaner') . '</div>',
                            'after'                 => sprintf(__('%1sseparate shortcode tags with commas%2s', 'shortcode-cleaner'), '<p><i class="cs-text-muted">', ', ex: tag_1, tag_2</i></p></div>'),
                            'help'                  => __('Cleaner will clean this active broken shotcodes.', 'shortcode-cleaner'),
                            'attributes'            => array(
                                'placeholder' => '',
                                'style'       => 'width: 60%;',
                                'readyonly'   => 'only-key',
                                'disabled'    => 'only-key',
                            ),
                            'default'               => '',
                            'add_tags_button_title' => __('Add', 'shortcode-cleaner'),
                            'before'                => '<div class="sc-pro-features">',
                        ),

                    ),
                ),

                // advanced settings
                array(
                    'name'   => 'advanced',
                    'title'  => __('Advanced', 'shortcode-cleaner'),
                    'icon'   => 'fas fa-cogs',
                    'fields' => array(

                        array(
                            'type'    => 'heading',
                            'content' => sprintf(__('%sAdvanced Settings', 'shortcode-cleaner'), '<i class="fas fa-cogs"></i> '),
                        ),

                        // set frontend site WP Content filters
                        array(
                            'id'      => 'set_frontend_content_filters',
                            'type'    => 'checkbox',
                            'title'   => __('Available default Frontend Site WP Content Filters', 'shortcode-cleaner'),
                            'desc'    => __('Cleaner use this default WordPress filters when cleaning.', 'shortcode-cleaner'),
                            'class'   => 'cs-disable-max-height',
                            'help'    => __('Cleaner use this frontend WP filters to clean its Content from broken Shortcodes, so here you can enable or disable any filter from cleaning its content.', 'shortcode-cleaner'),
                            'options' => array(
                                'the_content'       => 'the_content',
                                'the_content_feed'  => 'the_content_feed',
                                'the_excerpt'       => 'the_excerpt',
                                'wp_trim_excerpt'   => 'wp_trim_excerpt',
                                'the_excerpt_rss'   => 'the_excerpt_rss',
                                'get_the_excerpt'   => 'get_the_excerpt',
                                'the_title'         => 'the_title',
                                'the_title_rss'     => 'the_title_rss',
                                'single_post_title' => 'single_post_title',
                                'wp_title'          => 'wp_title',
                                'bloginfo'          => 'bloginfo',
                                'widget_title'      => 'widget_title',
                                'widget_text'       => 'widget_text',
                                'get_post_metadata' => 'get_post_metadata',
                                'wp_nav_menu_items' => 'wp_nav_menu_items',
                            ),
                            'default' => array(
                                'the_content', 'the_content_feed', 'the_excerpt',
                                'wp_trim_excerpt', 'the_excerpt_rss', 'get_the_excerpt',
                                'the_title', 'the_title_rss', 'single_post_title',
                                'wp_title', 'bloginfo', 'widget_title', 'widget_text',
                                'get_post_metadata', 'wp_nav_menu_items',
                            ),
                        ),

                        // upgrade to pro version notice for this features
                        array(
                            'type'    => 'notice',
                            'class'   => 'success',
                            'content' => sprintf(__('%1sUpgrade to %2sShortcode Cleaner Pro%3s to Unlock this Features.%4s', 'shortcode-cleaner'), '<div class="sc-upgrade-notice"><i class="fas fa-unlock-alt"></i> ', '<a href="https://1.envato.market/a7d2Z" target="_blank"><b>', '</b></a>', '</div>'),
                        ),

                        // enable cleaner into the database
                        array(
                            'id'         => 'enable_clean_database_content_pro',
                            'type'       => 'switcher',
                            'title'      => '<div class="sc-pro-features">' . __('Enable Cleaner into the Database', 'shortcode-cleaner') . '</div>',
                            'label'      => __('clean the content into database', 'shortcode-cleaner'),
                            'help'       => __('Cleaner will clean the content into database and any broken shortcodes will be removed completely.', 'shortcode-cleaner'),
                            'default'    => false,
                            'on_title'   => __('ON', 'shortcode-cleaner'),
                            'off_title'  => __('OFF', 'shortcode-cleaner'),
                            'attributes' => array(
                                'readyonly' => 'only-key',
                                'disabled'  => 'only-key',
                            ),
                            'before'     => '<div class="sc-pro-features">',
                            'after'      => '</div>',
                        ),

                        // determine upper limit for number of posts
                        array(
                            'id'         => 'clean_database_posts_number_pro',
                            'type'       => 'number',
                            'default'    => '500',
                            'title'      => '<div class="sc-pro-features">' . __('Determine Upper Limit for Number of Posts', 'shortcode-cleaner') . '</div>',
                            'after'      => sprintf(__('%1s define number of posts to be cleaned, default is: %2s %3s', 'shortcode-cleaner'), '<i class="cs-text-muted">', '500', '</i></div>'),
                            'help'       => __('Cleaner will clean the content into database for this max number of posts if contain any broken shortcodes, you can increase this number to clean more posts if you have, also posts here mean (posts, pages, custom post types).', 'shortcode-cleaner'),
                            'desc'       => '<div class="sc-pro-features">' . __('posts here mean (posts, pages, custom post types)', 'shortcode-cleaner') . '</div>',
                            'before'     => '<div class="sc-pro-features">',
                            'attributes' => array(
                                'readyonly' => 'only-key',
                                'disabled'  => 'only-key',
                            ),
                        ),

                        // enable custom frontend site WP Content filters
                        array(
                            'id'         => 'enable_custom_frontend_content_filters_pro',
                            'type'       => 'switcher',
                            'title'      => '<div class="sc-pro-features">' . __('Enable custom Frontend Site Content Filters', 'shortcode-cleaner') . '</div>',
                            'label'      => __('add new custom frontend site content filters', 'shortcode-cleaner'),
                            'help'       => __('Here you can add new custom content filters from WP or any custom Plugin or Theme to let cleaner clean this filter content.', 'shortcode-cleaner'),
                            'default'    => false,
                            'on_title'   => __('ON', 'shortcode-cleaner'),
                            'off_title'  => __('OFF', 'shortcode-cleaner'),
                            'attributes' => array(
                                'readyonly' => 'only-key',
                                'disabled'  => 'only-key',
                            ),
                            'before'     => '<div class="sc-pro-features">',
                            'after'      => '</div>',
                        ),

                        // set backend admin WP Content filters
                        array(
                            'id'         => 'set_backend_content_filters_pro',
                            'type'       => 'switcher',
                            'title'      => '<div class="sc-pro-features">' . __('Available default Backend Admin WP Content Filters', 'shortcode-cleaner') . '</div>',
                            'label'      => __('Cleaner use this default WordPress filters when cleaning.', 'shortcode-cleaner'),
                            'help'       => __('Cleaner use this backend WP filters to clean its Content from broken Shortcodes, so here you can enable or disable any filter from cleaning its content.', 'shortcode-cleaner'),
                            'default'    => false,
                            'on_title'   => __('ON', 'shortcode-cleaner'),
                            'off_title'  => __('OFF', 'shortcode-cleaner'),
                            'attributes' => array(
                                'readyonly' => 'only-key',
                                'disabled'  => 'only-key',
                            ),
                            'before'     => '<div class="sc-pro-features">',
                            'after'      => '</div>',
                        ),

                        // enable custom backend site WP Content filters
                        array(
                            'id'         => 'enable_custom_backend_content_filters_pro',
                            'type'       => 'switcher',
                            'title'      => '<div class="sc-pro-features">' . __('Enable custom Backend Admin Content Filters', 'shortcode-cleaner') . '</div>',
                            'label'      => __('add new custom backend admin content filters', 'shortcode-cleaner'),
                            'help'       => __('Here you can add new custom content filters from WP or any custom Plugin or Theme to let cleaner clean this filter content.', 'shortcode-cleaner'),
                            'default'    => false,
                            'on_title'   => __('ON', 'shortcode-cleaner'),
                            'off_title'  => __('OFF', 'shortcode-cleaner'),
                            'attributes' => array(
                                'readyonly' => 'only-key',
                                'disabled'  => 'only-key',
                            ),
                            'before'     => '<div class="sc-pro-features">',
                            'after'      => '</div>',
                        ),

                    ),
                ),

            ),
        );

        /**
         * ----------------------------------------
         * Backup section
         * ----------------------------------------
         */
        $options[] = array(
            'name'   => 'backup',
            'title'  => __('Backup', 'shortcode-cleaner'),
            'icon'   => 'fas fa-shield-alt',
            'fields' => array(

                array(
                    'type'    => 'heading',
                    'content' => sprintf(__('%sBackup', 'shortcode-cleaner'), '<i class="fas fa-shield-alt"></i> '),
                ),

                array(
                    'type'     => 'backup',
                    'settings' => array(
                        'import_title'                 => __('Import a Backup', 'shortcode-cleaner'),
                        'import_desc'                  => __('Copy & paste your backup settings data.', 'shortcode-cleaner'),
                        'import_file_button_title'     => __('Import from File', 'shortcode-cleaner'),
                        'import_button_title'          => __('Import a Backup', 'shortcode-cleaner'),
                        'warning_desc'                 => __('WARNING! This will overwrite all existing settings, please proceed with caution!', 'shortcode-cleaner'),
                        'export_title'                 => __('Export a Backup', 'shortcode-cleaner'),
                        'export_desc'                  => __('Save a backup from current settings data.', 'shortcode-cleaner'),
                        'export_copy_button_title'     => __('Copy Data', 'shortcode-cleaner'),
                        'export_or_text'               => __('OR', 'shortcode-cleaner'),
                        'export_download_button_title' => __('Download Data File', 'shortcode-cleaner'),
                    ),
                ),

            ),
        );

        /**
         * ----------------------------------------
         * System info section
         * ----------------------------------------
         */
        $options[] = array(
            'name'   => 'system_info',
            'title'  => __('System Info', 'shortcode-cleaner'),
            'icon'   => 'fas fa-exclamation-circle',
            'fields' => array(

                array(
                    'type'    => 'heading',
                    'content' => sprintf(__('%1sSystem Info%2s', 'shortcode-cleaner'), '<i class="fas fa-exclamation-circle"></i> ', '<a href="#" class="button button-primary cs-copy-info"><i class="fas fa-clipboard"></i> ' . __('Copy Info', 'shortcode-cleaner') . '</a><span id="cs-save-ajax" class="copy-info">' . __('Copied', 'shortcode-cleaner') . '</span>'),
                ),
                array(
                    'type'               => 'system_info',
                    'show_plugins'       => false,
                    'show_theme'         => false,
                    'wp_info_title'      => __('WordPress Environment', 'shortcode-cleaner'),
                    'server_info_title'  => __('Server Environment', 'shortcode-cleaner'),
                    'browser_info_title' => __('Browser & Operating System', 'shortcode-cleaner'),
                    'plugins_info_title' => __('Active Plugins', 'shortcode-cleaner'),
                    'theme_info_title'   => __('Active Theme', 'shortcode-cleaner'),
                ),

            ),
        );

        /**
         * ----------------------------------------
         * Support section
         * ----------------------------------------
         */
        $options[] = array(
            'name'   => 'support',
            'title'  => __('Support', 'shortcode-cleaner'),
            'icon'   => 'fas fa-life-ring',
            'fields' => array(

                array(
                    'type'    => 'heading',
                    'content' => sprintf(__('%sSupport', 'shortcode-cleaner'), '<i class="fas fa-life-ring"></i> '),
                ),
                array(
                    'type'    => 'content',
                    'content' => '<div class="sc-support-content"><h3>' . __('Have any questions? Something is not clear enough? <br> Do not hesitate to contact us via support platform. We reply on all questions as soon as possible!', 'shortcode-cleaner') . '</h3><a href="https://plugins.jozoor.com/shortcode-cleaner" class="sc-header-links" target="_blank"><i class="fas fa-home"></i> ' . __('Homepage', 'shortcode-cleaner') . '</a><a href="https://help.jozoor.com/envato/" class="sc-header-links" target="_blank"><i class="fas fa-file-alt"></i> ' . __('Documentation', 'shortcode-cleaner') . '</a><a href="https://help.jozoor.com/wordpress.org/support/" class="sc-header-links" target="_blank"><i class="fas fa-life-ring"></i> ' . __('Support', 'shortcode-cleaner') . '</a></div>',
                ),

            ),
        );

        /**
         * ----------------------------------------
         * Feedback section
         * ----------------------------------------
         */
        $options[] = array(
            'name'   => 'feedback',
            'title'  => __('Feedback', 'shortcode-cleaner'),
            'icon'   => 'fas fa-comment-alt',
            'fields' => array(

                array(
                    'type'    => 'heading',
                    'content' => sprintf(__('%sFeedback', 'shortcode-cleaner'), '<i class="fas fa-comment-alt"></i> '),
                ),
                array(
                    'type'    => 'content',
                    'content' => '<div class="sc-support-content"><h3>' . __('We would love to hear your feedback. Tell us what features you want. <br>Many thanks for your valuable feedback!', 'shortcode-cleaner') . '</h3><a href="https://plugins.jozoor.com/shortcode-cleaner" class="sc-header-links" target="_blank"><i class="fas fa-home"></i> ' . __('Homepage', 'shortcode-cleaner') . '</a><a href="https://help.jozoor.com/envato/" class="sc-header-links" target="_blank"><i class="fas fa-comment-alt"></i> ' . __('Send Feedback', 'shortcode-cleaner') . '</a></div>',
                ),

            ),
        );

        // return all available default options
        return $options;

    }

}
