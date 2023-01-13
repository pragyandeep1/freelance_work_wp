<?php
/**
 *
 * The template part for displaying the dashboard menu
 *
 * @package   workreap
 * @author    Amentotech
 * @link      http://amentotech.com/
 * @since 1.0
 */

global $current_user, $wp_roles, $userdata, $post;
$reference 		 = (isset($_GET['ref']) && $_GET['ref'] <> '') ? $_GET['ref'] : '';
$mode 			 = (isset($_GET['mode']) && $_GET['mode'] <> '') ? $_GET['mode'] : '';
$user_identity = $current_user->ID;

$menuTipsoClass = '';
if( !empty($args['menu_type']) && $args['menu_type'] === 'dashboard-menu-left'  ){
	$menuTipsoClass = 'hover-tipso-services';
}

if( apply_filters('workreap_system_access','service_base') === true ){ ?>
	<li class="menu-item-has-children toolip-wrapo <?php echo esc_attr( $reference === 'services' ? 'wt-open' : ''); ?>">
		<span class="wt-dropdowarrow"><i class="lnr lnr-chevron-right"></i></span>
		<a href="#" class="<?php echo esc_attr($menuTipsoClass);?>">
			<i class="ti-pencil-alt"></i>
			<span><?php esc_html_e('Manage services','workreap');?></span>
			<?php do_action('workreap_get_tooltip','element','manage-service');?>
		</a>
		<ul class="sub-menu" <?php echo esc_attr( $reference ) === 'services' ? 'style="display: block;"' : ''; ?>>
			<li class="<?php echo esc_attr( $reference === 'services' && $mode === 'ongoing' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('services', $user_identity,'','ongoing'); ?>"><?php esc_html_e('Ongoing services','workreap');?></a></li>
			<li class="<?php echo esc_attr( $reference === 'services' && $mode === 'completed' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('services', $user_identity,'','completed'); ?>"><?php esc_html_e('Completed services','workreap');?></a></li>
			<li class="<?php echo esc_attr( $reference === 'services' && $mode === 'cancelled' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('services', $user_identity,'','cancelled'); ?>"><?php esc_html_e('Cancelled services','workreap');?></a></li>
		</ul>

		<?php if( !empty($args['menu_type']) && $args['menu_type'] === 'dashboard-menu-left'  ){?>
			<script type="text/template" id="sub-menu-services">
				<ul class="tipso-menu-items" <?php echo esc_attr( $reference ) === 'services' ? 'style="display: block;"' : ''; ?>>
					<li class="<?php echo esc_attr( $reference === 'services' && $mode === 'ongoing' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('services', $user_identity,'','ongoing'); ?>"><?php esc_html_e('Ongoing services','workreap');?></a></li>
					<li class="<?php echo esc_attr( $reference === 'services' && $mode === 'completed' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('services', $user_identity,'','completed'); ?>"><?php esc_html_e('Completed services','workreap');?></a></li>
					<li class="<?php echo esc_attr( $reference === 'services' && $mode === 'cancelled' ? 'wt-active' : ''); ?>"><hr><a href="<?php Workreap_Profile_Menu::workreap_profile_menu_link('services', $user_identity,'','cancelled'); ?>"><?php esc_html_e('Cancelled services','workreap');?></a></li>
				</ul>
			</script>
		<?php }?>
	</li>
<?php } ?>
