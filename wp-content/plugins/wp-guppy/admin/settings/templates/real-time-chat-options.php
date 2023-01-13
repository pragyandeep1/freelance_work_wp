<?php
/**
 * Pusher settings
 *
 *
 * @link       https://wp-guppy.com/
 * @since      1.0.0
 *
 * @package    wp-guppy
 * @subpackage wp-guppy/admin/templates
 */
global  $wpguppy_settings;
$rt_chat_settings   = !empty($wpguppy_settings['rt_chat_settings']) ? $wpguppy_settings['rt_chat_settings'] : '';
$pusher             = !empty($wpguppy_settings['pusher']) ? $wpguppy_settings['pusher'] : 'disable';
$socket             = !empty($wpguppy_settings['socket']) ? $wpguppy_settings['socket'] : 'disable';
$option             = !empty($wpguppy_settings['option']) ? $wpguppy_settings['option'] : array();
$pusher_class       = !empty($pusher) && $pusher == 'enable' ? '' : 'hide-if-js';
$socket_class       = !empty($socket) && $socket == 'enable' ? '' : 'hide-if-js';

$app_id             = !empty($option['app_id'])             ? $option['app_id'] : '';
$app_key            = !empty($option['app_key'])            ? $option['app_key'] : '';
$app_secret         = !empty($option['app_secret'])         ? $option['app_secret'] : '';
$app_cluster        = !empty($option['app_cluster'])        ? $option['app_cluster'] : '';
$socket_host_url    = !empty($option['socket_host_url'])    ? $option['socket_host_url'] : '';
$socket_port_id     = !empty($option['socket_port_id'])     ? $option['socket_port_id'] : '81';

?>
<h3><?php esc_html_e('Real-time chat settings','wp-guppy');?></h3>
<table class="form-table"  role="chat">
    <tbody>
        <tr>
            <th scope="row"><label for="group"><?php esc_html_e('Chat options','wp-guppy');?></label></th>
            <td>
                <fieldset>
                    <label><input class="rt-chat-settings" type="radio" name="wpguppy_settings[rt_chat_settings]" value="pusher" <?php if( !empty($rt_chat_settings) && $rt_chat_settings == 'pusher') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Pusher channel','wp-guppy');?></code></label>
                    <label><input class="rt-chat-settings" type="radio" name="wpguppy_settings[rt_chat_settings]" value="socket" <?php if( !empty($rt_chat_settings) && $rt_chat_settings == 'socket') {?>checked="checked"<?php } ?>> 
                    <code><?php esc_html_e('Socket-io','wp-guppy');?></code></label>
                </fieldset>
            </td>
        </tr>
        <tr class="rt-pusher <?php if( empty($rt_chat_settings) || $rt_chat_settings != 'pusher') { echo $pusher_class; } ?>">
            <th scope="row"><label for="group"><?php esc_html_e('Pusher settings','wp-guppy');?></label></th>
            <td>
                <fieldset>
                    <label><input class="gp-pusher-settings"  type="radio" name="wpguppy_settings[pusher]" value="enable" <?php if( !empty($pusher) && $pusher == 'enable') {?>checked="checked"<?php } ?>>
                    <code><?php  esc_html_e('Enable','wp-guppy');?></code></label>
                    <label><input class="gp-pusher-settings" id="pusher-setting" type="radio" name="wpguppy_settings[pusher]" value="disable" <?php if( !empty($pusher) && $pusher == 'disable') {?>checked="checked"<?php } ?>> 
                    <code><?php esc_html_e('Disable','wp-guppy');?></code></label>
                </fieldset>
            </td>
        </tr>
        <tr class="rt-socket <?php if( empty($rt_chat_settings) || $rt_chat_settings != 'socket') { echo $socket_class; } ?>">
            <th scope="row"><label for="group"><?php esc_html_e('Socket io settings','wp-guppy');?></label></th>
            <td>
                <fieldset>
                    <label><input class="gp-socket-settings" type="radio" name="wpguppy_settings[socket]" value="enable" <?php if( !empty($socket) && $socket == 'enable') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Enable','wp-guppy');?></code></label>
                    <label><input class="gp-socket-settings" type="radio" name="wpguppy_settings[socket]" value="disable" <?php if( !empty($socket) && $socket == 'disable') {?>checked="checked"<?php } ?>> 
                    <code><?php esc_html_e('Disable','wp-guppy');?></code></label>
                </fieldset>
            </td>
        </tr>
        <tr class="gp-pusher-options <?php echo esc_attr($pusher_class);?>">
            <td colspan="2"><p><?php echo do_shortcode('We have used <a href="https://pusher.com/channels">Pusher Channel API</a> for the real-time chat experience, you must create channel API keys and add into the below settings and need to enable client events in the pusher app settings.  You may <a href="https://www.youtube.com/watch?v=G5It46mSraI">check this guide</a>','wp-guppy');?></p></td>
        </tr>
        <tr class="gp-pusher-options <?php echo esc_attr($pusher_class);?>">
            <th scope="row"><label for="gp-app-id"><span><?php esc_html_e('App ID','wp-guppy');?></span></label></th>
            <td><input class="regular-text ltr" name="wpguppy_settings[option][app_id]" type="text" value="<?php echo esc_attr($app_id);?>" /></td>
        </tr>
        <tr class="gp-pusher-options <?php echo esc_attr($pusher_class);?>">
            <th scope="row"><label for="gp-app-key"><span><?php esc_html_e('App Key','wp-guppy');?></span></label></th>
            <td><input class="regular-text ltr" name="wpguppy_settings[option][app_key]" type="text" value="<?php echo esc_attr($app_key);?>" /></td>
        </tr>
        <tr class="gp-pusher-options <?php echo esc_attr($pusher_class);?>">
            <th scope="row"><label for="gp-app-secret"><span><?php esc_html_e('App Secret','wp-guppy');?></span></label></th>
            <td><input class="regular-text ltr" name="wpguppy_settings[option][app_secret]" type="text" value="<?php echo esc_attr($app_secret);?>" /></td>
        </tr>
        <tr class="gp-pusher-options <?php echo esc_attr($pusher_class);?>">
            <th scope="row"><label for="gp-app-cluster"><span><?php esc_html_e('App Cluster','wp-guppy');?></span></label></th>
            <td><input class="regular-text ltr" name="wpguppy_settings[option][app_cluster]" type="text" value="<?php echo esc_attr($app_cluster);?>" /></td>
        </tr>
        <tr class="gp-socket-options <?php echo esc_attr($socket_class);?>">
            <th scope="row"><label for="gp-host-url"><span><?php esc_html_e('Host url','wp-guppy');?></span></label></th>
            <td>
                <input class="regular-text ltr" name="wpguppy_settings[option][socket_host_url]" type="text" value="<?php echo esc_attr($socket_host_url);?>" />
                <p><?php esc_html_e('Please add the host url, it could be https://yourdomain.com','wp-guppy');?></p>
            </td>
        </tr>
        <tr class="gp-socket-options <?php echo esc_attr($socket_class);?>">
            <th scope="row"><label for="gp-port-id"><span><?php esc_html_e('Port ID','wp-guppy');?></span></label></th>
            <td>
            <input class="regular-text ltr" name="wpguppy_settings[option][socket_port_id]" type="number" value="<?php echo esc_attr($socket_port_id);?>" />
            <p><?php esc_html_e('Please add the available port for node server, default would be 81.','wp-guppy');?></p>
            <p><?php esc_html_e('1) Some server uses 80, 81, 8080 or 3000.','wp-guppy');?></p>
            <p><?php esc_html_e('2) Please consult with your hosting provider.','wp-guppy');?></p>
            <p><?php esc_html_e('3) You need to install pm2 globally on your hosting server through this command npm install pm2 -g for this, you can contact your hosting service provider or your server manager.','wp-guppy');?></p>
            <p><?php esc_html_e('4) You have to add your port id in .env file located in plugins > wp-guppy > node-server > .env at line PORT_ID.','wp-guppy');?></p>
            <p><?php esc_html_e('5) You have to add your host url as CORS origin in .env file located in plugins > wp-guppy > node-server > .env at line DOMIAN.','wp-guppy');?></p>
            <p><?php esc_html_e('6) No need to change the port if your server is using port 81. (if you will change this port then you have to change it in the .env file located in plugins > wp-guppy > node-server > .env at line PORT_ID).','wp-guppy');?></p>
            <p><?php esc_html_e('7) You need to run the node server file (located in plugins > wp-guppy > node-server) on the server through server access like (SSH access), for this, you can contact your hosting service provider or your server manager. if you have an HTTP server then you need to just run the server.js file through the command ( pm2 start server.js ), but if you have an HTTPS server then you have to download an SSL certificate (SSL cert, SSL private key ) from your server then replace these files located in plugins > wp-guppy > node-server > sslcert with the same name given in that folder, after that you need to run command (pm2 start server-ssl.js) at (plugins > wp-guppy > node-server) path.','wp-guppy');?></p>
            <p><?php esc_html_e('8) If the plugin update is available for node server packages than you need to run command(npm update) at (plugins > wp-guppy > node-server) path.','wp-guppy');?></p>
            <p><?php esc_html_e('9) If the plugin update is available for node server then you need to run command(pm2 restart server.js) for HTTP servers and command(pm2 restart server-ssl.js) for HTTPS servers at (plugins > wp-guppy > node-server) path.','wp-guppy');?></p>
            </td>
        </tr>
    </tbody>
</table>