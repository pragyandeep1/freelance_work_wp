<?php

/**
 * Uninstall the plugin.
 *
 * This file runs when the plugin is uninstalled (deleted).
 * This will not run when the plugin is deactivated.
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

/**
 * If plugin is not being uninstalled, exit (do nothing)
 */
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Delete shortcode cleaner plugin options if it is being uninstalled.
 */

// save options names
$settings_option_name     = '_jo_scl_settings_options';
$history_data_option_name = 'sc_shortcode_cleaner_lite_history_data';

// delete settings options for single site
delete_option($settings_option_name);

// delete settings options for site in multisite
delete_site_option($settings_option_name);

// delete cleaner history data for single site
delete_option($history_data_option_name);

// delete cleaner history data for site in multisite
delete_site_option($history_data_option_name);
