<?php

/**
 * Check compatiblity admin notices.
 *
 * @package   Shortcode_Cleaner_Lite
 * @author    Jozoor, mohamdio [jozoor.com]
 * @link      https://plugins.jozoor.com/shortcode-cleaner
 * @copyright 2017 Jozoor, mohamdio [jozoor.com]
 * @license   GPL2
 * @version   1.0.0
 *
 */
add_action('admin_notices', 'jo_scl_check_compatiblity');
function jo_scl_check_compatiblity()
{

    // set plugin prefix
    $plugin_prefix = 'jo_scl_';

    // get correct plugin dir and file name
    $plugin_dir_file_name = dirname(dirname(dirname(plugin_basename(__FILE__)))) . '/' . dirname(dirname(dirname(plugin_basename(__FILE__)))) . '.php';

    /**
     * Get compatible WordPress version notice.
     */
    if (get_transient($plugin_prefix . 'compatible_check_notice_wp_version')) {

        // show error notice now
        echo '<div class="notice notice-error is-dismissible">';
        printf(__('<p><strong>%1$s</strong> plugin can not be activated because it requires a <strong>WordPress</strong> version at least <strong>%2$s</strong> (or later). Please go to Dashboard &#9656; Updates to get the latest version of WordPress.</p>', 'shortcode-cleaner'),
            get_transient($plugin_prefix . 'compatible_check_plugin_name'),
            get_transient($plugin_prefix . 'compatible_check_wp_version')
        );
        echo '</div>';

        // now deactivate the plugin
        deactivate_plugins($plugin_dir_file_name);

        /**
         * remove 'activate' action from url header, which will
         * disable activatation success admin notice, we don't need it.
         */
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        // now we can delete transients
        delete_transient($plugin_prefix . 'compatible_check_notice_wp_version');
        delete_transient($plugin_prefix . 'compatible_check_plugin_name');
        delete_transient($plugin_prefix . 'compatible_check_wp_version');

    }

    /**
     * Get compatible PHP version notice.
     */
    if (get_transient($plugin_prefix . 'compatible_check_notice_php_version')) {

        // show error notice now
        echo '<div class="notice notice-error is-dismissible">';
        printf(__('<p><strong>%1$s</strong> plugin can not be activated because it requires a <strong>PHP</strong> version at least <strong>%2$s</strong> (or later). Please contact with your hosting provider company to upgrade it to this version at least.</p>', 'shortcode-cleaner'),
            get_transient($plugin_prefix . 'compatible_check_plugin_name'),
            get_transient($plugin_prefix . 'compatible_check_php_version')
        );
        echo '</div>';

        // now deactivate the plugin
        deactivate_plugins($plugin_dir_file_name);

        /**
         * remove 'activate' action from url header, which will
         * disable activatation success admin notice, we don't need it.
         */
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        // now we can delete transients
        delete_transient($plugin_prefix . 'compatible_check_notice_php_version');
        delete_transient($plugin_prefix . 'compatible_check_plugin_name');
        delete_transient($plugin_prefix . 'compatible_check_php_version');

    }

}
