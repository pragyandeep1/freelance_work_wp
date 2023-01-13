<?php
/**
 *
 * Hooks
 *
 * @package   Workreap
 * @author    amentotech
 * @link      https://themeforest.net/user/amentotech/portfolio
 * @since 1.0
 */
if(!function_exists('workreap_admin_notices_list')){
	function workreap_admin_notices_list() {
		if(!is_plugin_active('wp-guppy/wp-guppy.php')){?>
			<div class="notice notice-success is-dismissible">
				<p><strong><?php esc_html_e( 'WP Guppy - A live chat plugin is compatible with workreap freelance marketplace', 'workreap' ); ?></strong></p>
				<p><a class="button button-primary" target="_blank" href="https://codecanyon.net/item/wpguppy-a-live-chat-plugin-for-wordpress/34619534?s_rank=1"><?php esc_html_e( 'Install WP Guppy', 'workreap' ); ?></a></p>
			</div>
			<?php
		}
	}
	add_action( 'admin_notices', 'workreap_admin_notices_list' );
}