<?php
$options = get_option( 'epv_verify_settings' );
$purchase_code	= !empty($options['purchase_code']) ? $options['purchase_code'] : '';
?>
<h3><?php esc_html_e('Revoke License','wp-guppy');?></h3>
<form action='' method='post'>   
    <?php do_action('epv_form_render_before'); ?>
    <table class="form-table" role="chat">
        <tbody>
            <tr>
                <th scope="row"><label for="group"><?php esc_html_e('Remove Guppy Purchase License','wp-guppy');?></label></th>
                <td>
                    <input type="text" id="epv_purchase_code" name="epv_purchase_code" value="<?php echo esc_attr( $purchase_code ) ?>" title="<?php esc_attr_e( 'Enter purchase code','wp-guppy' ) ?>"  />
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="remove" class="button button-primary" id="epv_remove_license_btn" value="<?php esc_attr_e( 'Remove license','wp-guppy' ); ?>" />
                </td>
            </tr>    
        </tbody>
    </table>
    <?php do_action('epv_form_render_after'); ?>
</form>