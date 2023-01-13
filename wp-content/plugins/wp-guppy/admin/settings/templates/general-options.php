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
$user_rolse = array();
if( function_exists( 'wpguppy_user_roles' ) ) {
	$user_rolse = wpguppy_user_roles();
}

$gp_post_types = array();
if( function_exists( 'wpguppy_post_types' ) ) {
	$gp_post_types = wpguppy_post_types();
}
$new_user_roles     = $user_rolse;
$db_user_role       = !empty($wpguppy_settings['user_role']) ? $wpguppy_settings['user_role'] : array();
$auto_invite        = !empty($wpguppy_settings['auto_invite']) ? $wpguppy_settings['auto_invite'] : array();
$create_group        = !empty($wpguppy_settings['create_group']) ? $wpguppy_settings['create_group'] : array();
$db_post_type       = !empty($wpguppy_settings['post_type']) ? $wpguppy_settings['post_type'] : array();
?>

<h3><?php esc_html_e('General settings','wp-guppy');?></h3>
<div class="guppy-shortcode-bar">
    <label for="gp-shorcode"><span><?php esc_html_e('Shortcode','wp-guppy');?></span></label>
    <input type="text" id="gp-shortcode-coppy" aria-describedby="gp-shorcode-description" value="<?php echo esc_attr('[getGuppyConversation]');?>" class="regular-text code">
    <button type="button" class="gp-copy-code button">
        <span aria-hidden="true" data-code="<?php echo esc_attr('[getGuppyConversation]');?>"><?php esc_html_e('Copy code','wp-guppy');?></span>
    </button>
    <p class="description" id="gp-shorcode-description"><?php esc_html_e('Add shortcode to use anywhere in the PHP files. Like below code','wp-guppy');?></p><br>
    <code>&lt;?php echo do_shortcode('[getGuppyConversation]');?&gt;</code>
</div>
<h3 class="title"><?php esc_html_e('Role management','wp-guppy');?></h3>
<div class="at-chatroles">
    <?php 
    if( !empty($user_rolse) ){
        foreach($user_rolse as $key => $user_rols ) {
            $current_user_role  = !empty($db_user_role[$key]) ? $db_user_role[$key] : array();
            $current_post_type  = !empty($db_post_type[$key]) ? $db_post_type[$key] : array();
            $invite_checked  = '';
            $group_checked = false;
            if(!empty($auto_invite) &&  in_array($key,$auto_invite)){
                $invite_checked = 'checked';
            }
             
            if(!empty($create_group[$key]) && $create_group[$key] == 'yes'){
                $group_checked = true;
            }
            ?>
            <div class="at-chatroles_item">
                <div class="at-chatrole">
                    <div class="at-chatrole_title">
                        <h6><?php echo esc_html($user_rols);?></h6>
                    </div>
                    <div class="at-chatrole_head">
                        <div class="at-roleoption">
                            <div class="at-roleoption_info">
                                <i class="dashicons dashicons-admin-users"></i>
                                <span><?php esc_html_e('Add privileges to these users to create groups','wp-guppy');?></span>
                            </div>
                            <div class="at-roleoption_radio at-switchbtn">
                                <label>
                                    <span><?php esc_html_e('YES','wp-guppy');?></span>
                                    <input <?php if( $group_checked) {?>checked="checked"<?php } ?> type="checkbox" name="wpguppy_settings[create_group][<?php echo esc_attr($key);?>]" value="yes">
                                    <i></i>
                                </label>
                            </div>
                        </div>
                        <div class="at-roleoption">
                            <div class="at-roleoption_info">
                                <i class="dashicons dashicons-buddicons-pm"></i>
                                <span><?php esc_html_e('You can enable auto invite for this role. This role will be able to start chat with any user from the contact list','wp-guppy');?></span>
                            </div>
                            <div class="at-roleoption_radio at-switchbtn">
                                <label>
                                    <span><?php esc_html_e('ON','wp-guppy');?></span>
                                    <input type="checkbox" <?php echo esc_attr($invite_checked);?> type="checkbox" name="wpguppy_settings[auto_invite][]" value="<?php echo esc_attr($key);?>" >
                                    <i></i>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="at-chatroletabs">
                        <div class="at-chatroletabs_list">
                            <a  href="javascript;"   data-tab_id="user-roles-privileges"    class="gp-inner-tabs-settings nav-tab nav-tab-active"><?php esc_html_e('Assign roles','wp-guppy');?></a>
                            <a  href="javascript;" data-tab_id="user-post-roles-privileges" class="gp-inner-tabs-settings nav-tab"><?php esc_html_e('Allow post types','wp-guppy');?></a>
                        </div>
                        <div class="tab-content gp-role-content"  id="user-roles-privileges">
                            <div class="at-roletabs">
                                <div class="at-roletabs_info">
                                    <p><?php echo sprintf(esc_html__('This will allow the "%s" role, to start the chat with the below roles. Leave this empty to show users from all the roles.', 'wp-guppy'), $user_rols);?></p>
                                </div>
                                <div class="at-roletabs_search">
                                    <i class="dashicons dashicons-search"></i>
                                    <input type="search" name="search" class="guppy-search-filter" placeholder="<?php esc_attr_e('Search with keyword');?>">
                                </div>
                                <ul class="at-roletabs_list">
                                    <?php 
                                        $counter = 1;
                                        foreach($new_user_roles as $role_key => $role_value ){
                                            $user_checked   = '';
                                            if(!empty($current_user_role) && is_array($current_user_role) && in_array($role_key,$current_user_role) ){
                                                $user_checked   = 'checked';
                                            }
                                            ?>
                                            <li>
                                                <div class="at-checkbox">
                                                    <input id="<?php echo esc_attr_e($user_rols.'-'.$role_value.'-'  . $counter); ?>" <?php echo esc_attr($user_checked);?> type="checkbox" name="wpguppy_settings[user_role][<?php echo esc_attr($key);?>][]" value="<?php echo esc_attr($role_key);?>">
                                                    <label for="<?php echo esc_attr_e($user_rols.'-'.$role_value.'-' . $counter++); ?>"><em class="dashicons dashicons-yes"></em><span><?php echo esc_html($role_value);?></span></label>
                                                </div>
                                            </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content gp-role-content hide-if-js"  id="user-post-roles-privileges">
                            <div class="at-roletabs">
                                <div class="at-roletabs_info">
                                    <p><?php esc_html_e('This will allow the logged-in users to start chatting on the post detail page. You can enable which post type should show the chat window.','wp-guppy');?></p>
                                </div>
                                <div class="at-roletabs_search">
                                    <i class="dashicons dashicons-search"></i>
                                    <input type="search" name="search" class="guppy-search-filter" placeholder="<?php esc_attr_e('Search with keyword');?>">
                                </div>
                                <ul class="at-roletabs_list">
                                    <?php 
                                        $counter = 1;
                                        foreach($gp_post_types as $post_key => $post_value ){
                                            $post_checked   = '';
                                            if(!empty($current_post_type) && is_array($current_post_type) && in_array($post_key,$current_post_type) ){
                                                $post_checked   = 'checked';
                                            }
                                            ?>
                                            <li>
                                                <div class="at-checkbox">
                                                    <input id="<?php echo esc_attr_e($user_rols.'-'.$post_key.'-'  . $counter); ?>" <?php echo esc_attr($post_checked);?> type="checkbox" name="wpguppy_settings[post_type][<?php echo esc_attr($key);?>][]" value="<?php echo esc_attr($post_key);?>">
                                                    <label for="<?php echo esc_attr_e($user_rols.'-'.$post_key.'-' . $counter++); ?>"><em class="dashicons dashicons-yes"></em><span><?php echo esc_html($post_value);?></span></label>
                                                </div>
                                            </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</div>
