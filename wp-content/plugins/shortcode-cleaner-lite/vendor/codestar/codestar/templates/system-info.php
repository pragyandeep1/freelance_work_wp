<?php

use Codestar\Helper\Helper;
use Codestar\Helper\System;

/**
 * System info template.
 * included in > core/Options/Field/SystemInfo.php
 */

// get all system info
$system_info = System::get_info('all', true);

// get wp info
$system_info_wp = System::get_info('wp');

// get server info
$system_info_server = System::get_info('server');

// get plugins info
$system_info_plugins = System::get_info('plugins');

// get theme info
$system_info_theme = System::get_info('theme');

// check show wp info
$show_wp = (isset($this->field['show_wp'])) ? $this->field['show_wp'] : true;

// check show server info
$show_server = (isset($this->field['show_server'])) ? $this->field['show_server'] : true;

// check show browser info
$show_browser = (isset($this->field['show_browser'])) ? $this->field['show_browser'] : true;

// check show plugins info
$show_plugins = (isset($this->field['show_plugins'])) ? $this->field['show_plugins'] : true;

// check show theme info
$show_theme = (isset($this->field['show_theme'])) ? $this->field['show_theme'] : true;

// check expand all info
$expand_all = (isset($this->field['expand_all'])) ? $this->field['expand_all'] : false;
$expand_class = 'closed';
if ($expand_all) {
    $expand_class = 'opened';
}

// check close all info
$close_all = (isset($this->field['close_all'])) ? $this->field['close_all'] : false;
$close_class = 'opened';
if ($close_all) {
    $close_class = 'closed';
}

// get all text
$wp_info_title = (isset($this->field['wp_info_title'])) ? $this->field['wp_info_title'] : esc_html('WordPress Environment');
$server_info_title = (isset($this->field['server_info_title'])) ? $this->field['server_info_title'] : esc_html('Server Environment');
$browser_info_title = (isset($this->field['browser_info_title'])) ? $this->field['browser_info_title'] : esc_html('Browser & Operating System');
$plugins_info_title = (isset($this->field['plugins_info_title'])) ? $this->field['plugins_info_title'] : esc_html('Active Plugins');
$theme_info_title = (isset($this->field['theme_info_title'])) ? $this->field['theme_info_title'] : esc_html('Active Theme');

?>

<?php
if ($show_wp) {
?>

<div class="cs-system-info-container">

<table id="system-info-wp" class="cs-system-info widefat <?php echo $close_class; ?>">

    <thead>
        <tr>
            <th colspan="2">
                <i class="fab fa-wordpress"></i> <?php echo $wp_info_title; ?>
                <span class="cs-expand"><i class="fas fa-chevron-up"></i></span>
            </th>
        </tr>
    </thead>

    <tbody>
        <?php

            foreach ($system_info_wp as $name => $data) {

                echo '<tr>';
                echo '<td>' . esc_html(Helper::handle_string_name($name)) . '</td>';
                echo '<td>';
                if ($data === 'true' || $data === true) {
                    echo '<i class="fas fa-check-circle cs-on-status"></i>';
                } elseif ($data === 'false' || $data === false) {
                    echo '<i class="fas fa-times-circle cs-off-status"></i>';
                } elseif ($name === 'WP_memory_limit') {

                    $memory = $data['raw'];

                    if ($memory < 40000000) {
                        echo '<mark class="error">' . esc_html($data['size']) . ' - WordPress recommend setting memory to at least 40MB. See: <a href="http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">Increasing memory allocated to PHP</a></mark>';
                    } else {

                        echo '<mark class="yes">' . esc_html($data['size']) . '</mark>';

                    }

                } else {
                    echo esc_html($data);
                }
                echo '</td>';
                echo '</tr>';

            } // end foreach $system_info_wp

        ?>
    </tbody>

</table><!-- /end wp info -->

<br/>

<?php
} // end check show wp info
?>

<?php
if ($show_server) {
?>

<table id="system-info-server" class="cs-system-info widefat <?php echo $expand_class; ?>">

    <thead>
        <tr>
            <th colspan="2">
                <i class="fas fa-server"></i> <?php echo $server_info_title; ?>
                <span class="cs-expand"><i class="fas fa-chevron-down"></i></span>
            </th>
        </tr>
    </thead>

    <tbody>
        <?php

            foreach ($system_info_server as $name => $data) {

                echo '<tr>';
                echo '<td>' . esc_html(Helper::handle_string_name($name)) . '</td>';
                echo '<td>';
                if ($data === 'true' || $data === true) {
                    echo '<i class="fas fa-check-circle cs-on-status"></i>';
                } elseif ($data === 'false' || $data === false) {
                    echo '<i class="fas fa-times-circle cs-off-status"></i>';
                } else {
                    echo esc_html($data);
                }
                echo '</td>';
                echo '</tr>';

            } // end foreach $system_info_server

        ?>
    </tbody>

</table><!-- /end server info -->

<br/>

<?php
} // end check show server info
?>

<?php
if ($show_browser) {
?>

<table id="system-info-browser" class="cs-system-info widefat <?php echo $expand_class; ?>">

    <thead>
        <tr>
            <th colspan="2">
                <i class="fas fa-laptop"></i> <?php echo $browser_info_title; ?>
                <span class="cs-expand"><i class="fas fa-chevron-down"></i></span>
            </th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>Browser Type</td>
            <td class="browser-type"></td>
        </tr>
        <tr>
            <td>Device Type</td>
            <td class="device-type"></td>
        </tr>
        <tr>
            <td>Screen Size</td>
            <td class="screen-size"></td>
        </tr>
        <tr>
            <td>Viewport Size</td>
            <td class="viewport-size"></td>
        </tr>
        <tr>
            <td>Operating System</td>
            <td class="operating-system"></td>
        </tr>
    </tbody>

</table><!-- /end theme info -->

<br/>

<?php
} // end check show browser info
?>

<?php
if ($show_plugins) {
?>

<table id="system-info-plugins" class="cs-system-info widefat <?php echo $expand_class; ?>">

    <thead>
        <tr>
            <th colspan="2">
                <i class="fas fa-plug"></i> <?php echo $plugins_info_title; ?>
                <span class="cs-expand"><i class="fas fa-chevron-down"></i></span>
            </th>
        </tr>
    </thead>

    <tbody>
        <?php

            foreach ($system_info_plugins['plugins'] as $name => $plugin_data) {

                $version_string = '';
                $network_string = '';

                if (!empty($plugin_data['Name'])) {

                    // link the plugin name to the plugin url if available
                    $plugin_name = esc_html($plugin_data['Name']);

                    if (!empty($plugin_data['PluginURI'])) {
                        $plugin_name = '<a href="' . esc_url($plugin_data['PluginURI']) . '" title="' . esc_attr('Visit plugin homepage') . '">' . esc_html($plugin_name) . '</a>';
                    }

        ?>
            <tr>
                <td><?php echo $plugin_name; ?></td>
                <td>
                    <?php echo 'by ' . $plugin_data['Author'] . ' &ndash; ' . esc_html($plugin_data['Version']) . $version_string . $network_string; ?>
                </td>
            </tr>

        <?php
                } // end check $plugin_data['Name']

            } // end foreach $system_info_plugins['plugins']
        ?>
    </tbody>

</table><!-- /end plugins info -->

<br/>

<?php
} // end check show plugins info
?>

<?php
if ($show_theme) {
?>

<table id="system-info-theme" class="cs-system-info widefat <?php echo $expand_class; ?>">

    <thead>
        <tr>
            <th colspan="2">
                <i class="fas fa-paint-brush"></i> <?php echo $theme_info_title; ?>
                <span class="cs-expand"><i class="fas fa-chevron-down"></i></span>
            </th>
        </tr>
    </thead>

    <tbody>
        <?php

    foreach ($system_info_theme as $name => $data) {

        echo '<tr>';
        echo '<td>' . esc_html(Helper::handle_string_name($name)) . '</td>';
        echo '<td>';
        if ($data === 'true' || $data === true) {
            echo '<i class="fas fa-check-circle cs-on-status"></i>';
        } elseif ($data === 'false' || $data === false) {
            echo '<i class="fas fa-times-circle cs-off-status"></i>';
        } else {
            echo esc_html($data);
        }
        echo '</td>';
        echo '</tr>';

    } // end foreach $system_info_theme

    ?>
    </tbody>

</table><!-- /end theme info -->

<?php
} // end check show theme info
?>

<pre class="cs-copy-system-info"><?php echo $system_info; ?></pre>

</div><!-- /end .cs-system-info-container -->