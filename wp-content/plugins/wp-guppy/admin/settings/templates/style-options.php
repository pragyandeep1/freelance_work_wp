<?php
/**
 * Style settings
 *
 *
 * @link       https://wp-guppy.com/
 * @since      1.0.0
 *
 * @package    wp-guppy
 * @subpackage wp-guppy/admin/templates
 */
global  $wpguppy_settings;
$primary_color      = !empty($wpguppy_settings['primary_color']) ? $wpguppy_settings['primary_color'] : '#FF7300';
$secondary_color    = !empty($wpguppy_settings['secondary_color']) ? $wpguppy_settings['secondary_color'] : '#0A0F26';
$text_color         = !empty($wpguppy_settings['text_color']) ? $wpguppy_settings['text_color'] : '#999999';
$default_text_color = '#999999'
 ?>
<h3><?php esc_html_e('Styling settings','wp-guppy');?></h3>
<table class="form-table" role="style">
    <tbody>
        <tr>
            <th scope="row"><label for="gp-primary-color"><span><?php esc_html_e('Primary color','wp-guppy');?></span></label></th>
            <td>
                <input name="wpguppy_settings[primary_color]" class="gp-color-field" type="text" value="<?php echo esc_attr($primary_color);?>" data-default-color="<?php echo esc_attr($primary_color);?>" />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="gp-secondary-color"><span><?php esc_html_e('Secondary color','wp-guppy');?></span></label></th>
            <td>
                <input name="wpguppy_settings[secondary_color]" class="gp-color-field" type="text" value="<?php echo esc_attr($secondary_color);?>" data-default-color="<?php echo esc_attr($secondary_color);?>" />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="gp-text-color"><span><?php esc_html_e('Text color','wp-guppy');?></span></label></th>
            <td>
                <input name="wpguppy_settings[text_color]" class="gp-color-field" type="text" value="<?php echo esc_attr($text_color);?>" data-default-color="<?php echo esc_attr($default_text_color);?>" />
            </td>
        </tr>
    </tbody>
</table>