<?php
/**
 * Report a user
 *
 *
 * @link       https://wp-guppy.com/
 * @since      1.0.0
 *
 * @package    wp-guppy
 * @subpackage wp-guppy/admin/templates
 */
global  $wpguppy_settings;
$reasons     = !empty($wpguppy_settings['reporting_reasons']) ? $wpguppy_settings['reporting_reasons'] : apply_filters('wpguppy_reporting_reasons','');
?>
<h3><?php esc_html_e('Report a user','wp-guppy');?></h3>
<table class="form-table" role="report" id="gb-report-user">
    <tbody>
        <tr>
            <th scope="row"><label for="group"><?php esc_html_e('Report a user repeater field','wp-guppy');?></label></th>
            <td>
                <button type="button" class="button button-secondary" id="gb-add-reson"><?php esc_html_e( 'Add reasons', 'wp-guppy' );?></button>
            </td>
        </tr>
        <?php foreach($reasons as $key => $val ){?>
            <tr>
                <td></td>
                <td><input name="wpguppy_settings[reporting_reasons][]" type="text"  value="<?php echo esc_attr(stripslashes($val));?>"/><a href="javascript:;" class="gb-remove-reason"><span class="dashicons dashicons-trash"></span></a></td>
            </tr>
        <?php } ?>
    </tbody>
</table>