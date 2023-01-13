<?php
/**
 * Reset database
 *
 *
 * @link       https://wp-guppy.com/
 * @since      1.0.0
 *
 * @package    wp-guppy
 * @subpackage wp-guppy/admin/templates
 */
?>
<h3><?php esc_html_e('Reset database','wp-guppy');?></h3>
<table class="form-table" role="chat">
    <tbody>
        <tr>
            <th scope="row"><label for="group"><?php esc_html_e('Reset guppy tables','wp-guppy');?></label></th>
            <td>
                <button type="button" class="button button-secondary" id="gb-rest-db"><?php esc_html_e( 'Rest database', 'wp-guppy' );?></button>
            </td>
        </tr>
    </tbody>
</table>