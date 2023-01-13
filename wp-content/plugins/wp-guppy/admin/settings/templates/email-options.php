<?php
/**
 * Email
 *
 *
 * @link       https://wp-guppy.com/
 * @since      1.0.0
 *
 * @package    wp-guppy
 * @subpackage wp-guppy/admin/templates
 */
global  $wpguppy_settings;
$report_admin_email     = !empty($wpguppy_settings['report_admin_email']) ? $wpguppy_settings['report_admin_email'] : get_option('admin_email');
$report_subject         = !empty($wpguppy_settings['report_subject']) ? $wpguppy_settings['report_subject'] : esc_html__( "User reported against {{reason}}", 'wp-guppy' );
$report_header_logo     = !empty($wpguppy_settings['report_header_logo']) ? $wpguppy_settings['report_header_logo'] : "";
$report_content         = !empty($wpguppy_settings['report_content']) ? $wpguppy_settings['report_content'] : wp_kses( __( "Hey,\n\n“{{who_report}}” reported a complaint of “{{report_against}}” against “{{reason}}”. Please read the description below. \n\n{{user_content}} \n\nThanks & regards \n“{{who_report}}”", 'wp-guppy' ), array(
    'a' => array(
        'href' => array(),
        'title' => array()
    ),
    'br' => array(),
    'em' => array(),
    'strong' => array(),
) );
?>
<table class="form-table" role="report-email" id="gb-report-email">
    <h3><?php esc_html_e('General settings','wp-guppy');?></h3>
    <tr>
        <th scope="row"><label for="group"><?php esc_html_e('Email header logo','wp-guppy');?></label></th>
        <td>
            <input name="wpguppy_settings[report_header_logo]" type="text"  value="<?php echo esc_url(stripslashes($report_header_logo));?>"/>
            <p class="description"><?php esc_html_e('Please add email logo here, leave this empty to hide the logo from the email.','wp-guppy');?></p>
        </td>
    </tr>
</table>
<h3><?php esc_html_e('Report a user email','wp-guppy');?></h3>
<table class="form-table" role="report-email" id="gb-report-email">
    <tbody>
        <tr>
            <th scope="row"><label for="group"><?php esc_html_e('Admin email','wp-guppy');?></label></th>
            <td>
                <input name="wpguppy_settings[report_admin_email]" type="email"  value="<?php echo esc_attr(stripslashes($report_admin_email));?>"/>
                <p class="description"><?php esc_html_e('Add admin email address, leave this empty to use default email address from the WordPress settings','wp-guppy');?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="group"><?php esc_html_e('Subject','wp-guppy');?></label></th>
            <td>
                <input name="wpguppy_settings[report_subject]" type="text"  value="<?php echo esc_attr(stripslashes($report_subject));?>"/>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="group"><?php esc_html_e('Params','wp-guppy');?></label></th>
            <td>
                <code>{{who_report}}        <?php esc_html_e('User name who is reporting','wp-guppy');?></code><br><br>
                <code>{{report_against}}    <?php esc_html_e('User name against report','wp-guppy');?></code><br><br>
                <code>{{reason}}            <?php esc_html_e('Reason(s) of reporting','wp-guppy');?></code> <br><br>
                <code>{{user_content}}      <?php esc_html_e('User issue reporting description','wp-guppy');?></code>
            </td>
        </tr>
        
        <tr>
            <th scope="row"><label for="group"><?php esc_html_e('Email content','wp-guppy');?></label></th>
            <td>
                <textarea name="wpguppy_settings[report_content]" rows="9" cols="70"><?php echo do_shortcode(stripslashes($report_content));?></textarea>
                <p class="description"><?php esc_html_e('Please don\'t use any HTM tags into this template','wp-guppy');?></p>
            </td>
        </tr>
    </tbody>
</table>
<?php do_action('wpguppy_add_new_email_templates');?>