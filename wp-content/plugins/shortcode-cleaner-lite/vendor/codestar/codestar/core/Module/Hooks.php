<?php

namespace Codestar\Module;

use Codestar\Config;
use Codestar\Module\Datastore;

/**
 * All available Hooks in Codestar framework.
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
class Hooks
{

    /**
     * Save all hooks data.
     *
     * @since 2.0.0
     * @access public
     */
    public static function save_data()
    {

        // save all action hooks
        foreach (self::get_actions() as $action => $data) {
            Datastore::save_hook('action', $action, $data);
        }

        // save all filter hooks
        foreach (self::get_filters() as $filter => $data) {
            Datastore::save_hook('filter', $filter, $data);
        }

    }

    /**
     * Add all available action hooks.
     *
     * @since 2.0.0
     * @access public
     * @return array
     */
    public static function get_actions()
    {

        // add all action hooks
        $actions = array(

            Config::PREFIX . 'before_loaded' => array(
                'action_desc' => 'This action for make anything before load framework.',
            ),
            Config::PREFIX . 'after_loaded' => array(
                'action_desc' => 'This action for make anything after load framework.',
            ),
            Config::PREFIX . 'is_loaded' => array(
                'action_desc' => 'This action for Make sure framework is loaded.',
            ),
            Config::PREFIX . Config::CUSTOMIZER . '_{unique_options_id}_register' => array(
                'action_desc' => 'This action for Add extra fields for wp customizer.',
            ),
            Config::PREFIX . 'validate_save_after' => array(
                'action_desc' => 'This action for do action after validate settings options save changes.',
            ),
            Config::PREFIX . 'add_icons_before' => array(
                'action_desc' => 'This action for do action before add icons.',
            ),
            Config::PREFIX . 'add_icons' => array(
                'action_desc' => 'This action for add icons.',
            ),
            Config::PREFIX . 'add_icons_after' => array(
                'action_desc' => 'This action for do action after add icons.',
            ),
            Config::PREFIX . 'enqueue_custom_styles' => array(
                'action_desc' => 'This action for enqueue custom styles.',
            ),
            Config::PREFIX . 'enqueue_custom_scripts' => array(
                'action_desc' => 'This action for enqueue custom scripts.',
            ),
            Config::PREFIX . Config::SETTINGS . '_{unique_options_id}_before_main_content' => array(
                'action_desc' => 'This action for add new html content before settings page content.',
            ),
            Config::PREFIX . Config::SETTINGS . '_{unique_options_id}_after_main_content' => array(
                'action_desc' => 'This action for add new html content after settings page content.',
            ),
            Config::PREFIX . Config::SETTINGS . '_{unique_options_id}_before_header' => array(
                'action_desc' => 'This action for add new html content before settings page header.',
            ),
            Config::PREFIX . Config::SETTINGS . '_{unique_options_id}_after_header' => array(
                'action_desc' => 'This action for add new html content after settings page header.',
            ),
            Config::PREFIX . Config::SETTINGS . '_{unique_options_id}_before_footer' => array(
                'action_desc' => 'This action for add new html content before settings page footer.',
            ),
            Config::PREFIX . Config::SETTINGS . '_{unique_options_id}_after_footer' => array(
                'action_desc' => 'This action for add new html content after settings page footer.',
            ),
            Config::PREFIX . Config::SETTINGS . '_{unique_options_id}_before_header_buttons' => array(
                'action_desc' => 'This action for add new html content before buttons in header.',
            ),
            Config::PREFIX . Config::SETTINGS . '_{unique_options_id}_after_header_buttons' => array(
                'action_desc' => 'This action for add new html content after buttons in header.',
            ),
            Config::PREFIX . 'typography_family' => array(
                'action_desc' => 'This action for add new typography family for typography field type.',
            ),

        );

        return $actions;

    }

    /**
     * Add all available filter hooks.
     *
     * @since 2.0.0
     * @access public
     * @return array
     */
    public static function get_filters()
    {

        // add all filter hooks
        $filters = array(

            Config::PREFIX . 'allow_debug' => array(
                'filter_desc' => 'This filter for allow full debug for options fields',
                'filter_args' => array(
                    '$allow' => 'true or false, default = false',
                ),
            ),
            Config::PREFIX . 'allow_debug_light' => array(
                'filter_desc' => 'This filter for allow light debug for options fields',
                'filter_args' => array(
                    '$allow' => 'true or false, default = false',
                ),
            ),
            Config::PREFIX . 'allow_minify_styles' => array(
                'filter_desc' => 'This filter for allow minify core styles.',
                'filter_args' => array(
                    '$allow' => 'true or false, default = true',
                ),
            ),
            Config::PREFIX . 'allow_minify_scripts' => array(
                'filter_desc' => 'This filter for allow minify core scripts.',
                'filter_args' => array(
                    '$allow' => 'true or false, default = true',
                ),
            ),
            Config::PREFIX . Config::CUSTOMIZER . '_{unique_options_id}_options' => array(
                'filter_desc' => 'This filter for override or update customizer options.',
                'filter_args' => array(
                    '$options' => 'use it to update or change options.',
                ),
            ),
            Config::PREFIX . Config::METABOXES . '_{unique_options_id}_settings' => array(
                'filter_desc' => 'This filter for override or update metabox settings.',
                'filter_args' => array(
                    '$settings' => 'use it to update or change settings.',
                ),
            ),
            Config::PREFIX . Config::METABOXES . '_{unique_options_id}_options' => array(
                'filter_desc' => 'This filter for override or update metabox options.',
                'filter_args' => array(
                    '$options' => 'use it to update or change options.',
                ),
            ),
            Config::PREFIX . 'save_post' => array(
                'filter_desc' => 'This filter for update metabox options save changes.',
                'filter_args' => array(
                    '$request' => 'get request when save changes',
                    '$request_key' => 'unique options id for metaxbox',
                    '$post' => 'core wp post object.',
                ),
            ),
            Config::PREFIX . Config::SETTINGS . '_{unique_options_id}_menu_settings' => array(
                'filter_desc' => 'This filter for override or update settings menu settings.',
                'filter_args' => array(
                    '$settings' => 'update menu settings',
                ),
            ),
            Config::PREFIX . Config::SETTINGS . '_{unique_options_id}_settings' => array(
                'filter_desc' => 'This filter for override or update settings container settings.',
                'filter_args' => array(
                    '$settings' => 'settings container settings',
                ),
            ),
            Config::PREFIX . Config::SETTINGS . '_{unique_options_id}_options' => array(
                'filter_desc' => 'This filter for override or update settings container options.',
                'filter_args' => array(
                    '$options' => 'settings container options',
                ),
            ),
            Config::PREFIX . 'validate_save' => array(
                'filter_desc' => 'This filter for validate settings options save changes.',
                'filter_args' => array(
                    '$request' => 'get request when save changes',
                ),
            ),
            Config::PREFIX . Config::SHORTCODER . '_{unique_options_id}_settings' => array(
                'filter_desc' => 'This filter for override or update shortcoder settings.',
                'filter_args' => array(
                    '$settings' => 'shortcoder settings',
                ),
            ),
            Config::PREFIX . Config::SHORTCODER . '_{unique_options_id}_options' => array(
                'filter_desc' => 'This filter for override or update shortcoder options.',
                'filter_args' => array(
                    '$options' => 'use it to update or change options.',
                ),
            ),
            Config::PREFIX . Config::SHORTCODER . '_{unique_options_id}_exclude_post_types' => array(
                'filter_desc' => 'This filter for override or update shortcoder exclude post types.',
                'filter_args' => array(
                    '$exclude_post_types' => 'update exclude post types',
                ),
            ),
            Config::PREFIX . Config::TAXONOMIES . '_{unique_options_id}_settings' => array(
                'filter_desc' => 'This filter for override or update taxonomy settings.',
                'filter_args' => array(
                    '$settings' => 'use it to update or change settings.',
                ),
            ),
            Config::PREFIX . Config::TAXONOMIES . '_{unique_options_id}_options' => array(
                'filter_desc' => 'This filter for override or update taxonomy options.',
                'filter_args' => array(
                    '$options' => 'use it to update or change options.',
                ),
            ),
            Config::PREFIX . 'save_taxonomy' => array(
                'filter_desc' => 'This filter for update taxonomy options save changes.',
                'filter_args' => array(
                    '$request' => 'get request when save changes',
                    '$request_key' => 'unique options id for taxonomy',
                    '$term_id' => 'taxonomy term id',
                ),
            ),
            Config::PREFIX . 'add_icons_json' => array(
                'filter_desc' => 'This filter for add new or override icons json files.',
                'filter_args' => array(
                    '$icons_files' => 'add new or override icons json files.',
                ),
            ),
            Config::PREFIX . 'language_defaults' => array(
                'filter_desc' => 'This filter for add multilangual languages.',
                'filter_args' => array(
                    '$multilang' => 'add multilangual languages.',
                ),
            ),
            Config::PREFIX . Config::SETTINGS . '_{unique_options_id}_header_title' => array(
                'filter_desc' => 'This filter for override or update settings page header title content.',
                'filter_args' => array(
                    '$title' => 'add new title html content.',
                ),
            ),
            Config::PREFIX . Config::SETTINGS . '_{unique_options_id}_footer_left_block' => array(
                'filter_desc' => 'This filter for override or update footer left block content.',
                'filter_args' => array(
                    '$left_block_content' => 'add new content.',
                ),
            ),
            Config::PREFIX . Config::SETTINGS . '_{unique_options_id}_footer_right_block' => array(
                'filter_desc' => 'This filter for override or update footer right block content.',
                'filter_args' => array(
                    '$right_block_content' => 'add new content.',
                ),
            ),
            Config::PREFIX . 'websafe_fonts' => array(
                'filter_desc' => 'This filter for add new or override default websafe fonts.',
                'filter_args' => array(
                    '$websafe_fonts' => 'add default websafe fonts.',
                ),
            ),
            Config::PREFIX . 'websafe_fonts_variants' => array(
                'filter_desc' => 'This filter for add new or override default websafe fonts variants.',
                'filter_args' => array(
                    '$default_variants' => 'add default websafe fonts variants.',
                ),
            ),
            Config::PREFIX . 'get_system_info_wp' => array(
                'filter_desc' => 'This filter for override wp system info',
                'filter_args' => array(
                    '$wp_info' => 'return array of wp info',
                ),
            ),
            Config::PREFIX . 'get_system_info_server' => array(
                'filter_desc' => 'This filter for override server system info',
                'filter_args' => array(
                    '$server_info' => 'return array of server info',
                ),
            ),
            Config::PREFIX . 'get_system_info_plugins' => array(
                'filter_desc' => 'This filter for override plugins system info',
                'filter_args' => array(
                    '$plugins_info' => 'return array of plugins info',
                ),
            ),
            Config::PREFIX . 'get_system_info_theme' => array(
                'filter_desc' => 'This filter for override theme system info',
                'filter_args' => array(
                    '$theme_info' => 'return array of theme info',
                ),
            ),
            Config::PREFIX . 'get_system_info' => array(
                'filter_desc' => 'This filter for override all system info',
                'filter_args' => array(
                    '$system_info' => 'return array of system info',
                ),
            ),

        );

        return $filters;

    }

}
