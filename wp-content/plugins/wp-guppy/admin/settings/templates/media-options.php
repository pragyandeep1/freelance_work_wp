<?php
/**
 * Media settings
 *
 *
 * @link       https://wp-guppy.com/
 * @since      1.0.0
 *
 * @package    wp-guppy
 * @subpackage wp-guppy/admin/templates
 */
global  $wpguppy_settings;
$default_bell_url       = esc_url(WPGuppy_GlobalSettings::get_plugin_url().'public/media/notification-bell.wav');
$notification_bell_url  = !empty($wpguppy_settings['notification_bell_url']) ? $wpguppy_settings['notification_bell_url'] : $default_bell_url;
$image_size             = !empty($wpguppy_settings['image_size']) ? $wpguppy_settings['image_size'] : '5000';
$audio_size             = !empty($wpguppy_settings['audio_size']) ? $wpguppy_settings['audio_size'] : '5000';
$video_size             = !empty($wpguppy_settings['video_size']) ? $wpguppy_settings['video_size'] : '5000';
$file_size              = !empty($wpguppy_settings['file_size']) ? $wpguppy_settings['file_size'] : '5000';
$bd_images_ex           = !empty($wpguppy_settings['allow_img_ext']) ? $wpguppy_settings['allow_img_ext'] : array();
$bd_audio_ex            = !empty($wpguppy_settings['allow_audio_ext']) ? $wpguppy_settings['allow_audio_ext'] : array();
$bd_video_ex            = !empty($wpguppy_settings['allow_video_ext']) ? $wpguppy_settings['allow_video_ext'] : array();
$bd_file_ex             = !empty($wpguppy_settings['allow_file_ext']) ? $wpguppy_settings['allow_file_ext'] : array();
$location_sharing       = !empty($wpguppy_settings['location_sharing']) ? $wpguppy_settings['location_sharing'] : 'disable';
$voicenote_sharing      = !empty($wpguppy_settings['voicenote_sharing']) ? $wpguppy_settings['voicenote_sharing'] : 'disable';
$upload_attachments     = !empty($wpguppy_settings['upload_attachments']) ? $wpguppy_settings['upload_attachments'] : 'custom';
$emoji_sharing          = !empty($wpguppy_settings['emoji_sharing']) ? $wpguppy_settings['emoji_sharing'] : 'disable';

$images_extenstions = apply_filters( 'wpguppy_image_types','' );
$audio_extenstions  = apply_filters( 'wpguppy_audio_types','' );
$video_extenstions  = apply_filters( 'wpguppy_video_types','' );
$file_extenstions   = apply_filters( 'wpguppy_file_types','' );
 ?>
<h3><?php esc_html_e('Media settings','wp-guppy');?></h3>
<table class="form-table" role="media">
    <tbody>
        <tr>
            <th scope="row"><label for="group"><?php esc_html_e('Notification bell url','wp-guppy');?></label></th>
            <td>
                <input name="wpguppy_settings[notification_bell_url]" placeholder="<?php esc_attr_e('Add bell URL','wp-guppy');?>" type="text"  value="<?php echo esc_url($notification_bell_url);?>"/>
                <p class="description"><?php esc_html_e('You can upload mp3 or wav file into WordPress media and copy that URL and add here for the ring tune.','wp-guppy');?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="gp-uploadattachments"><span><?php esc_html_e('Upload Attachments','wp-guppy');?></span></label></th>
            <td>
                <fieldset>
                    <label><input type="radio" name="wpguppy_settings[upload_attachments]" value="custom" <?php if( !empty($upload_attachments) && $upload_attachments == 'custom') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Custom folder','wp-guppy');?></code></label>
                    <label><input type="radio" name="wpguppy_settings[upload_attachments]" value="wpmedia" <?php if( !empty($upload_attachments) && $upload_attachments == 'wpmedia') {?>checked="checked"<?php } ?>> 
                    <code><?php esc_html_e('WordPress media','wp-guppy');?></code></label>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="gp-image_size"><span><?php esc_html_e('Image sharing','wp-guppy');?></span></label></th>
            <td>
                <input name="wpguppy_settings[image_size]" type="number"  min="1" value="<?php echo intval($image_size);?>"/>
                <label for="image_size-text"><?php esc_html_e('KB','wp-guppy');?></label>
            </td>
        </tr>
        <tr>
            <th scope="row"></th>
            <td><strong><?php esc_html_e('Allow extenstions','wp-guppy');?></strong></td>
        </tr>
        <tr>
            <th scope="row"></th>
            <td>
                <fieldset>
                    <?php 
                        foreach($images_extenstions as $post_key => $post_value ){
                            $post_checked   = '';
                            if(!empty($bd_images_ex) && is_array($bd_images_ex) && in_array($post_key,$bd_images_ex) ){
                                $post_checked   = 'checked';
                            }
                        ?>
                        <label>
                            <input <?php echo esc_attr($post_checked);?> type="checkbox" name="wpguppy_settings[allow_img_ext][]" value="<?php echo esc_attr($post_key);?>">
                            <span><?php echo esc_html($post_value);?></span>
                        </label>
                    <?php } ?>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="gp-audio_size"><span><?php esc_html_e('Audio sharing','wp-guppy');?></span></label></th>
            <td>
                <input name="wpguppy_settings[audio_size]" type="number"  min="1" value="<?php echo intval($audio_size);?>"/>
                <label for="audio_size-text"><?php esc_html_e('KB','wp-guppy');?></label>
            </td>
        </tr>
        <tr>
            <th scope="row"></th>
            <td><strong><?php esc_html_e('Allow extenstions','wp-guppy');?></strong></td>
        </tr>
        <tr>
            <th scope="row"></th>
            <td>
                <fieldset>
                    <?php 
                        foreach($audio_extenstions as $post_key => $post_value ){
                            $post_checked   = '';
                            if(!empty($bd_audio_ex) && is_array($bd_audio_ex) && in_array($post_key,$bd_audio_ex) ){
                                $post_checked   = 'checked';
                            }
                        ?>
                        <label>
                            <input <?php echo esc_attr($post_checked);?> type="checkbox" name="wpguppy_settings[allow_audio_ext][]" value="<?php echo esc_attr($post_key);?>">
                            <span><?php echo esc_html($post_value);?></span>
                        </label>
                    <?php } ?>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="gp-video_size"><span><?php esc_html_e('Video sharing','wp-guppy');?></span></label></th>
            <td>
                <input name="wpguppy_settings[video_size]" type="number"  min="1" value="<?php echo intval($video_size);?>"/>
                <label for="video_size-text"><?php esc_html_e('KB','wp-guppy');?></label>
            </td>
        </tr>
        <tr>
            <th scope="row"></th>
            <td><strong><?php esc_html_e('Allow extenstions','wp-guppy');?></strong></td>
        </tr>
        <tr>
            <th scope="row"></th>
            <td>
                <fieldset>
                    <?php 
                        foreach($video_extenstions as $post_key => $post_value ){
                            $post_checked   = '';
                            if(!empty($bd_video_ex) && is_array($bd_video_ex) && in_array($post_key,$bd_video_ex) ){
                                $post_checked   = 'checked';
                            }
                        ?>
                        <label>
                            <input <?php echo esc_attr($post_checked);?> type="checkbox" name="wpguppy_settings[allow_video_ext][]" value="<?php echo esc_attr($post_key);?>">
                            <span><?php echo esc_html($post_value);?></span>
                        </label>
                    <?php } ?>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="gp-images_size"><span><?php esc_html_e('File sharing','wp-guppy');?></span></label></th>
            <td>
                <input name="wpguppy_settings[file_size]" type="number"  min="1" value="<?php echo intval($file_size);?>"/>
                <label for="file_size-text"><?php esc_html_e('KB','wp-guppy');?></label>
            </td>
        </tr>
        <tr>
            <th scope="row"></th>
            <td><strong><?php esc_html_e('Allow extenstions','wp-guppy');?></strong></td>
        </tr>
        <tr>
            <th scope="row"></th>
            <td>
                <fieldset>
                    <?php 
                        foreach($file_extenstions as $file_key => $file_value ){
                            $file_checked   = '';
                            if(!empty($bd_file_ex) && is_array($bd_file_ex) && in_array($file_key,$bd_file_ex) ){
                                $file_checked   = 'checked';
                            }
                        ?>
                        <label>
                            <input <?php echo esc_attr($file_checked);?> type="checkbox" name="wpguppy_settings[allow_file_ext][]" value="<?php echo esc_attr($file_key);?>">
                            <span><?php echo esc_html($file_value);?></span>
                        </label>
                    <?php } ?>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="gp-location-sharing"><span><?php esc_html_e('Location sharing','wp-guppy');?></span></label></th>
            <td>
                <fieldset>
                    <label><input type="radio" name="wpguppy_settings[location_sharing]" value="enable" <?php if( !empty($location_sharing) && $location_sharing == 'enable') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Enable','wp-guppy');?></code></label>
                    <label><input type="radio" name="wpguppy_settings[location_sharing]" value="disable" <?php if( !empty($location_sharing) && $location_sharing == 'disable') {?>checked="checked"<?php } ?>> 
                    <code><?php esc_html_e('Disable','wp-guppy');?></code></label>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="gp-emoji-sharing"><span><?php esc_html_e('Emoji sharing','wp-guppy');?></span></label></th>
            <td>
                <fieldset>
                    <label><input type="radio" name="wpguppy_settings[emoji_sharing]" value="enable" <?php if( !empty($emoji_sharing) && $emoji_sharing == 'enable') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Enable','wp-guppy');?></code></label>
                    <label><input type="radio" name="wpguppy_settings[emoji_sharing]" value="disable" <?php if( !empty($emoji_sharing) && $emoji_sharing == 'disable') {?>checked="checked"<?php } ?>> 
                    <code><?php esc_html_e('Disable','wp-guppy');?></code></label>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="gp-voicenote-sharing"><span><?php esc_html_e('Voice Note sharing','wp-guppy');?></span></label></th>
            <td>
                <fieldset>
                    <label><input type="radio" name="wpguppy_settings[voicenote_sharing]" value="enable" <?php if( !empty($voicenote_sharing) && $voicenote_sharing == 'enable') {?>checked="checked"<?php } ?>>
                    <code><?php esc_html_e('Enable','wp-guppy');?></code></label>
                    <label><input type="radio" name="wpguppy_settings[voicenote_sharing]" value="disable" <?php if( !empty($voicenote_sharing) && $voicenote_sharing == 'disable') {?>checked="checked"<?php } ?>> 
                    <code><?php esc_html_e('Disable','wp-guppy');?></code></label>
                </fieldset>
            </td>
        </tr>
    </tbody>
</table>