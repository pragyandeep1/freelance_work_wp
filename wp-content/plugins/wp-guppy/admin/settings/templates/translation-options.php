<?php
/**
 * General settings
 *
 *
 * @link       https://wp-guppy.com/
 * @since      1.0.0
 *
 * @package    wp-guppy
 * @subpackage wp-guppy/admin/templates
 */

global  $wpguppy_settings;
$translations = apply_filters( 'wpguppy_default_text','' );
?>

<h3><?php esc_html_e('Translation settings','wp-guppy');?></h3>
<code><?php esc_html_e("Please don't remove or make any change in ",'wp-guppy');?><b><?php esc_html_e('((xxx)) for example ((username))','wp-guppy');?></b></code>
<table class="form-table" role="general">
    <tbody>
        <?php 
		if(!empty($translations)){
			foreach($translations as $key => $translation){
		
			$translation_val	= !empty($wpguppy_settings['translations'][$key]) ? $wpguppy_settings['translations'][$key] : $translation['default'];
			$title				= !empty($translation['title']) ? $translation['title'] : '';
			?>
			<tr>
				<th scope="row"><label for="gp-shorcode"><span><?php echo esc_html($title);?></span></label></th>
				<td>
					<input type="text" name="wpguppy_settings[translations][<?php echo esc_attr($key);?>]" id="gp-shortcode-coppy" aria-describedby="gp-shorcode-description" value="<?php echo esc_attr(stripslashes($translation_val));?>" placeholder="<?php echo esc_attr(stripslashes($translation_val));?>" class="regular-text code">
				</td>
			</tr>
        <?php }}?>
    </tbody>
</table>