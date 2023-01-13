<?php
/**
 * Tabs settings
 *
 *
 * @link       https://wp-guppy.com/
 * @since      1.0.0
 *
 * @package    wp-guppy
 * @subpackage wp-guppy/admin/templates
 */
global  $wpguppy_settings;
$default_active_tab         = !empty($wpguppy_settings['default_active_tab']) ? $wpguppy_settings['default_active_tab'] : '';
$enabled_tabs               = !empty($wpguppy_settings['enabled_tabs']) ? $wpguppy_settings['enabled_tabs'] : array();
$group_chat                 = !empty($wpguppy_settings['group_chat']) ? $wpguppy_settings['group_chat'] : 'enable';
$floating_window            = !empty($wpguppy_settings['floating_window']) ? $wpguppy_settings['floating_window'] : 'enable';
$messanger_page_id          = !empty($wpguppy_settings['messanger_page_id']) ? $wpguppy_settings['messanger_page_id'] : '0';
$dock_layout_image          = !empty($wpguppy_settings['dock_layout_image']) ? $wpguppy_settings['dock_layout_image'] : '';
$delete_message             = !empty($wpguppy_settings['delete_message']) ? $wpguppy_settings['delete_message'] : 'enable';
$clear_chat                 = !empty($wpguppy_settings['clear_chat']) ? $wpguppy_settings['clear_chat'] : 'enable';
$report_user                = !empty($wpguppy_settings['report_user']) ? $wpguppy_settings['report_user'] : 'enable';
$hide_acc_settings          = !empty($wpguppy_settings['hide_acc_settings']) ? $wpguppy_settings['hide_acc_settings'] : 'no';
 ?>
<h3><?php esc_html_e('Tabs settings','wp-guppy');?></h3>
<table class="form-table" role="media">
    <tbody>
        <tr>
            <th scope="row"><label><span><?php esc_html_e('Default active tab','wp-guppy');?></span></label></th>
            <td>
                <fieldset>
                    <label><input type="radio" name="wpguppy_settings[default_active_tab]" value="contacts" <?php if( !empty($default_active_tab) && $default_active_tab == 'contacts') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Contacts list','wp-guppy');?></code></label>
                    <label><input type="radio" name="wpguppy_settings[default_active_tab]" value="messages" <?php if( !empty($default_active_tab) && $default_active_tab == 'messages') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Message list','wp-guppy');?></code></label>
                    <label><input type="radio" name="wpguppy_settings[default_active_tab]" value="friends" <?php if( !empty($default_active_tab) && $default_active_tab == 'friends') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Friend list','wp-guppy');?></code></label>
                    <label><input type="radio" name="wpguppy_settings[default_active_tab]" value="blocked" <?php if( !empty($default_active_tab) && $default_active_tab == 'blocked') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Blocked list','wp-guppy');?></code></label>
                    <label><input type="radio" name="wpguppy_settings[default_active_tab]" value="posts" <?php if( !empty($default_active_tab) && $default_active_tab == 'posts') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Post messages list','wp-guppy');?></code></label>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><span><?php esc_html_e('Enable tabs','wp-guppy');?></span></label></th>
            <td>
                <fieldset>

                    <label>
                    <input <?php echo in_array('contacts', $enabled_tabs) ? esc_attr('checked') : '' ;?> type="checkbox" name="wpguppy_settings[enabled_tabs][]" value="contacts">
                        <code><?php echo esc_html('Contacts list','wp-guppy');?></code> 
                    </label>

                    <label>
                    <input <?php echo in_array('messages', $enabled_tabs) ? esc_attr('checked') : '' ;?> type="checkbox" name="wpguppy_settings[enabled_tabs][]" value="messages">
                        <code><?php echo esc_html('Messages list','wp-guppy');?></code>
                    </label>

                    <label>
                    <input <?php echo in_array('friends', $enabled_tabs) ? esc_attr('checked') : '' ;?> type="checkbox" name="wpguppy_settings[enabled_tabs][]" value="friends">
                        <code><?php echo esc_html('Friends list','wp-guppy');?></code> 
                    </label>

                    <label>
                    <input <?php echo in_array('blocked', $enabled_tabs) ? esc_attr('checked') : '' ;?> type="checkbox" name="wpguppy_settings[enabled_tabs][]" value="blocked">
                        <code><?php echo esc_html('Blocked list','wp-guppy');?></code> 
                    </label>
                    <label>
                    <input <?php echo in_array('posts', $enabled_tabs) ? esc_attr('checked') : '' ;?> type="checkbox" name="wpguppy_settings[enabled_tabs][]" value="posts">
                        <code><?php echo esc_html('Post messages list','wp-guppy');?></code> 
                    </label>
                </fieldset>
            </td>
        </tr>
    </tbody>
</table>
<h3><?php esc_html_e('Chat settings','wp-guppy');?></h3>
<table class="form-table" role="media">
    <tbody>
        <tr>
            <th scope="row"><label><span><?php esc_html_e('Select Messanger Page','wp-guppy');?></span></label></th>
            <td>
            <fieldset>  
                <?php
                    $args = array(
                        'depth'                 => 0,
                        'child_of'              => 0,
                        'selected'              => $messanger_page_id ,
                        'echo'                  => 1,
                        'name'                  => 'wpguppy_settings[messanger_page_id]',
                        'id'                    => null, // string
                        'class'                 => null, // string
                        'show_option_none'      => null, // string
                        'show_option_no_change' => null, // string
                        'option_none_value'     => null, // string
                    );
                    wp_dropdown_pages( $args ); 
                ?>
            </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="group"><?php esc_html_e('Dock layout Image','wp-guppy');?></label></th>
            <td>
                <input name="wpguppy_settings[dock_layout_image]" placeholder="<?php esc_attr_e('Dock layout image URL','wp-guppy');?>" type="text"  value="<?php echo esc_url($dock_layout_image);?>"/>
                <p class="description"><?php esc_html_e('You can upload dock layout image into WordPress media and copy that URL and add here for dock image.','wp-guppy');?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="group"><?php esc_html_e('Groups Chat','wp-guppy');?></label></th>
            <td>
                <fieldset>
                    <label><input type="radio" name="wpguppy_settings[group_chat]" value="enable" <?php if( !empty($group_chat) && $group_chat == 'enable') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Enable','wp-guppy');?></code></label>
                    <label><input type="radio" name="wpguppy_settings[group_chat]" value="disable" <?php if( !empty($group_chat) && $group_chat == 'disable') {?>checked="checked"<?php } ?>> 
                    <code><?php esc_html_e('Disable','wp-guppy');?></code></label>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="group"><?php esc_html_e('Enable floating window','wp-guppy');?></label></th>
            <td>
                <fieldset>
                    <label><input type="radio" name="wpguppy_settings[floating_window]" value="enable" <?php if( !empty($floating_window) && $floating_window == 'enable') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Enable','wp-guppy');?></code></label>
                    <label><input type="radio" name="wpguppy_settings[floating_window]" value="disable" <?php if( !empty($floating_window) && $floating_window == 'disable') {?>checked="checked"<?php } ?>> 
                    <code><?php esc_html_e('Disable','wp-guppy');?></code></label>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="group"><?php esc_html_e('Delete message option','wp-guppy');?></label></th>
            <td>
                <fieldset>
                    <label><input type="radio" name="wpguppy_settings[delete_message]" value="enable" <?php if( !empty($delete_message) && $delete_message == 'enable') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Enable','wp-guppy');?></code></label>
                    <label><input type="radio" name="wpguppy_settings[delete_message]" value="disable" <?php if( !empty($delete_message) && $delete_message == 'disable') {?>checked="checked"<?php } ?>> 
                    <code><?php esc_html_e('Disable','wp-guppy');?></code></label>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="group"><?php esc_html_e('Clear chat option','wp-guppy');?></label></th>
            <td>
                <fieldset>
                    <label><input type="radio" name="wpguppy_settings[clear_chat]" value="enable" <?php if( !empty($clear_chat) && $clear_chat == 'enable') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Enable','wp-guppy');?></code></label>
                    <label><input type="radio" name="wpguppy_settings[clear_chat]" value="disable" <?php if( !empty($clear_chat) && $clear_chat == 'disable') {?>checked="checked"<?php } ?>> 
                    <code><?php esc_html_e('Disable','wp-guppy');?></code></label>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="group"><?php esc_html_e('Report user option','wp-guppy');?></label></th>
            <td>
                <fieldset>
                    <label><input type="radio" name="wpguppy_settings[report_user]" value="enable" <?php if( !empty($report_user) && $report_user == 'enable') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Enable','wp-guppy');?></code></label>
                    <label><input type="radio" name="wpguppy_settings[report_user]" value="disable" <?php if( !empty($report_user) && $report_user == 'disable') {?>checked="checked"<?php } ?>> 
                    <code><?php esc_html_e('Disable','wp-guppy');?></code></label>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="group"><?php esc_html_e('Hide account settings','wp-guppy');?></label></th>
            <td>
                <fieldset>
                    <label><input type="radio" name="wpguppy_settings[hide_acc_settings]" value="yes" <?php if( !empty($hide_acc_settings) && $hide_acc_settings == 'yes') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Yes','wp-guppy');?></code></label>
                    <label><input type="radio" name="wpguppy_settings[hide_acc_settings]" value="no" <?php if( !empty($hide_acc_settings) && $hide_acc_settings == 'no') {?>checked="checked"<?php } ?>> 
                    <code><?php esc_html_e('no','wp-guppy');?></code></label>
                </fieldset>
            </td>
        </tr>
       
    </tbody>
</table>

<?php  ?> 