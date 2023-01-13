<?php

/**
 * Fired during plugin activation
 *
 * @link       wp-guppy.com
 * @since      1.0.0
 *
 * @package    wp-guppy
 * @subpackage wp-guppy/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    wp-guppy
 * @subpackage wp-guppy/includes
 * @author     wp-guppy <wpguppy@gmail.com>
 */
class WPGuppy_Activator {

	/**
	 * @init            save default settings
	 * @package         wp-guppy
	 * @subpackage      wp-guppy/admin/partials
	 * @since           1.0
	 * @desc            create tables when plugin get activate
	 */
    public static function activate() {
		global $wpdb;
		$wpguppy_message 			= $wpdb->prefix . 'wpguppy_message';
		$wpguppy_group 				= $wpdb->prefix . 'wpguppy_group';
		$wpguppy_group_member 		= $wpdb->prefix . 'wpguppy_group_member';
		$wpguppy_friend_list 		= $wpdb->prefix . 'wpguppy_friend_list';
		$wpguppy_chat_action 		= $wpdb->prefix . 'wpguppy_chat_action';
		$wpguppy_postchat_action 	= $wpdb->prefix . 'wpguppy_postchat_action';
		$wpguppy_users 				= $wpdb->prefix . 'wpguppy_users';
		$wpguppy_guest_account 		= $wpdb->prefix . 'wpguppy_guest_account';

		if ($wpdb->get_var("SHOW TABLES LIKE '$wpguppy_message'") != $wpguppy_message) {
			$charsetCollate = $wpdb->get_charset_collate();            
			$privateChat = "CREATE TABLE $wpguppy_message (
				id 					int(11) NOT NULL AUTO_INCREMENT,
				sender_id 			int(20) UNSIGNED NOT NULL,
				receiver_id 		int(20) UNSIGNED NOT NULL,
				post_id 			int(20) UNSIGNED DEFAULT NULL,
				message 			text NULL,
				group_id 			int(20) UNSIGNED DEFAULT NULL,
				attachments 		text NULL,
				reply_message 		text  DEFAULT NULL,
				user_type 			tinyint(1) NOT NULL DEFAULT '0' COMMENT '(0->guest, 1->registered)',
				chat_type 			tinyint(1) NOT NULL DEFAULT '0' COMMENT '(0->post based, 1->one to one, 2->group chat)',
				message_type 		tinyint(1) NOT NULL DEFAULT '0' COMMENT '(0->text, 1->attachment, 2->location ,3->voice note, 4->notify_message)',
				group_msg_seen_id 	varchar(255) DEFAULT NULL  		COMMENT 'group member message seen ids',
				message_status 		tinyint(1) NOT NULL DEFAULT '0' COMMENT '(0->unseen, 1->seen, 2->delete)',
				timestamp 			varchar(20) DEFAULT NULL,
				message_sent_time 	datetime DEFAULT NULL,
				message_seen_time 	datetime DEFAULT NULL,
				PRIMARY KEY (id),
				INDEX index_column (sender_id,receiver_id,post_id,group_id,user_type,chat_type,message_type,message_status)                           
				) $charsetCollate;";   
									
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($privateChat);     
		}

		if ($wpdb->get_var("SHOW TABLES LIKE '$wpguppy_group'") != $wpguppy_group) {
			$charsetCollate = $wpdb->get_charset_collate();            
			$privateChat = "CREATE TABLE $wpguppy_group (
				id 					int(11) NOT NULL AUTO_INCREMENT,
				group_title 		varchar (255) NOT NULL,
				group_description 	varchar (255)  NULL,
				group_image 		varchar (255)  NULL,
				disable_reply   	tinyint(1) NOT NULL DEFAULT '0' COMMENT '(1->disabled)',
				group_created_date 	datetime DEFAULT NULL,
				group_updated_date 	datetime DEFAULT NULL,
				PRIMARY KEY (id),
				INDEX index_column (disable_reply)                           
				) $charsetCollate;";   
									
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($privateChat);     
		}

		if ($wpdb->get_var("SHOW TABLES LIKE '$wpguppy_group_member'") != $wpguppy_group_member) {
			$charsetCollate = $wpdb->get_charset_collate();            
			$privateChat = "CREATE TABLE $wpguppy_group_member (
				id 				int(11)  NOT NULL AUTO_INCREMENT,
				group_id 		int(11)  UNSIGNED NOT NULL,
				member_id 		int(20)  UNSIGNED NOT NULL,
				group_role  	tinyint(1) NOT NULL DEFAULT '0' COMMENT '(1->creator, 2->admin)',
				member_status  	tinyint(1) NOT NULL DEFAULT '1' COMMENT '(0->left, 1->active, 2->blocked)',
				group_status  	tinyint(1) NOT NULL DEFAULT '1' COMMENT '(0->deleted)',
				member_added_date datetime DEFAULT NULL,
				PRIMARY KEY (id),
				INDEX index_column (group_id, member_id, group_role, member_status, group_status)                             
				) $charsetCollate;";   
									
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($privateChat);     
		}

		if ($wpdb->get_var("SHOW TABLES LIKE '$wpguppy_friend_list'") != $wpguppy_friend_list) {
			$charsetCollate = $wpdb->get_charset_collate();            
			$privateChat = "CREATE TABLE $wpguppy_friend_list (
				id 					int(11)  NOT NULL AUTO_INCREMENT,
				send_by 			int(20)  UNSIGNED NOT NULL,
				send_to 			int(20)  UNSIGNED NOT NULL,
				friend_created_date datetime DEFAULT NULL,
				friend_status  		tinyint(1) NOT NULL DEFAULT '0' COMMENT '(0->invite, 1->active, 2->decline, 3->blocked)',
				PRIMARY KEY (id),
				INDEX index_column (send_by,send_to,friend_status)                           
				) $charsetCollate;";   
									
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($privateChat);     
		}

		if ($wpdb->get_var("SHOW TABLES LIKE '$wpguppy_chat_action'") != $wpguppy_chat_action) {
			$charsetCollate = $wpdb->get_charset_collate();            
			$privateChat = "CREATE TABLE $wpguppy_chat_action (
				id 						int(11) NOT NULL AUTO_INCREMENT,
				action_by 				int(20)  UNSIGNED NOT NULL,
				corresponding_id 		int(20)  UNSIGNED DEFAULT NULL,
				chat_type  				tinyint(1) DEFAULT NULL COMMENT '(0->post based, 1->one to one, 2->group chat)',
				action_type  			tinyint(1) NOT NULL DEFAULT '0' COMMENT '(0->clear chat, 1-> mute all notification, 2-> mute specific notification, 3->group left, 4->removed from group, 5->group delete)',
				action_time 			datetime DEFAULT NULL,
				action_updated_time 	datetime DEFAULT NULL,
				PRIMARY KEY (id),
				INDEX index_column (action_by,corresponding_id,chat_type,action_type)                           
				) $charsetCollate;";   
									
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($privateChat);     
		}

		if ($wpdb->get_var("SHOW TABLES LIKE '$wpguppy_postchat_action'") != $wpguppy_postchat_action) {
			$charsetCollate = $wpdb->get_charset_collate();            
			$privateChat = "CREATE TABLE $wpguppy_postchat_action (
				id 						int(11) NOT NULL AUTO_INCREMENT,
				action_by 				int(20)  UNSIGNED NOT NULL,
				action_to 				int(20)  UNSIGNED NOT NULL,
				post_id 				int(20)  UNSIGNED DEFAULT NULL,
				action_type  			tinyint(1) NOT NULL DEFAULT '0' COMMENT '(0-> block all post chat, 1-> block single post chat, 2-> mute notification, 3-> clear chat)',
				action_time 			datetime DEFAULT NULL,
				PRIMARY KEY (id),
				INDEX index_column (action_by,action_to,post_id,action_type)                           
				) $charsetCollate;";   
									
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($privateChat);     
		}

		if ($wpdb->get_var("SHOW TABLES LIKE '$wpguppy_users'") != $wpguppy_users) {
			$charsetCollate = $wpdb->get_charset_collate();            
			$privateChat = "CREATE TABLE $wpguppy_users (
				id 					int(11) NOT NULL AUTO_INCREMENT,
				user_id 			int(20)  UNSIGNED NOT NULL,
				user_name 			varchar(255)   NOT NULL,
				user_email 			varchar(255)   DEFAULT NULL,
				user_image 			mediumtext  	DEFAULT NULL,
				user_phone 			varchar(255)   DEFAULT NULL,
				PRIMARY KEY (id),
				INDEX index_column (user_id)                           
				) $charsetCollate;";   
									
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($privateChat);     
		}

		if ($wpdb->get_var("SHOW TABLES LIKE '$wpguppy_guest_account'") != $wpguppy_guest_account) {
			$charsetCollate = $wpdb->get_charset_collate();            
			$privateChat = "CREATE TABLE $wpguppy_guest_account (
				id 					int(11) NOT NULL AUTO_INCREMENT,
				name 				varchar (255)  NOT NULL,
				email 				varchar (255)  NOT NULL,
				ip_address			varchar (255)  NOT NULL,
				user_agent			varchar (255)  NOT NULL,	
				PRIMARY KEY (id),
				INDEX index_column (email)                           
				) $charsetCollate;";   
									
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($privateChat);     
		}
		
		$wpguppy_settings = get_option('wpguppy_settings');
		
		if(empty($wpguppy_settings)){
			$enabled_tabs 				= array('contacts','messages','friends','blocked','posts');			
			$wpguppy_image_types 		= apply_filters('wpguppy_image_types','');			
			$wpguppy_audio_types 		= apply_filters('wpguppy_audio_types','');			
			$wpguppy_video_types 		= apply_filters('wpguppy_video_types','');			
			$wpguppy_file_types 		= apply_filters('wpguppy_file_types','');
			$wpguppy_reporting_reasons 	= apply_filters('wpguppy_reporting_reasons','');
			$translations 				= wp_list_pluck(apply_filters( 'wpguppy_default_text','' ),'default');
			$allow_img_ext = $allow_audio_ext = $allow_video_ext = $allow_file_ext= array();
			foreach($wpguppy_image_types as $key=> $name){
				$allow_img_ext[] = $key;
			}	
			
			foreach($wpguppy_audio_types as $key=> $name){
				$allow_audio_ext[] = $key;
			}	
			
			foreach($wpguppy_video_types as $key=> $name){
				$allow_video_ext[] = $key;
			}	
			
			foreach($wpguppy_file_types as $key=> $name){
				$allow_file_ext[] = $key;
			}
			
			$default_settings = array(
				'default_active_tab' 	=> 'contacts',
				'enabled_tabs' 			=> $enabled_tabs,
				'primary_color' 		=> '#FF7300',
				'secondary_color' 		=> '#0A0F26',
				'text_color' 			=> '#999999',
				'image_size' 			=> '5000',
				'file_size' 			=> '10000',
				'audio_size' 			=> '10000',
				'video_size' 			=> '10000',
				'upload_attachments' 	=> 'custom',
				'allow_img_ext' 		=> $allow_img_ext,
				'allow_audio_ext' 		=> $allow_audio_ext,
				'allow_video_ext' 		=> $allow_video_ext,
				'allow_file_ext' 		=> $allow_file_ext,
				'location_sharing' 		=> 'enable',
				'emoji_sharing' 		=> 'enable',
				'voicenote_sharing' 	=> 'enable',
				'pusher' 				=> 'disable',
				'group_chat' 			=> 'enable',
				'floating_window' 		=> 'enable',
				'delete_message'		=> 'enable',	
				'clear_chat'			=> 'enable',	
				'report_user'			=> 'enable',	
				'hide_acc_settings'		=> 'no',	
				'translations' 			=> $translations,
				'reporting_reasons' 	=> $wpguppy_reporting_reasons,
			);
			
			update_option('wpguppy_settings',$default_settings);
		}
    }

}
