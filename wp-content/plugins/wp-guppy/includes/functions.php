<?php

/**
 * Define Global Settings
 *
 * @throws error
 * @author WP Guppy<wpguppy@gmail.com>
 * @return 
 */

global $guppySetting;
$guppySetting	= get_option( "wpguppy_settings");

/**
 * Return user roles
 *
 * @throws error
 * @author WP Guppy<wpguppy@gmail.com>
 * @return 
 */
if( !function_exists( 'wpguppy_user_roles' ) ) {
	function wpguppy_user_roles(){
        global $wp_roles;
        if ( ! isset( $wp_roles ) ){
            $wp_roles = new WP_Roles();
        }
		
        $roles_array    = array();
        $roles          = !empty($wp_roles->roles) ? $wp_roles->roles : array();
		
        if( !empty($roles) ){
            foreach($roles as $key => $values ){
                $roles_array[$key]  = !empty($values['name']) ? $values['name'] : '';
            }
        }
		
        return $roles_array;
    }
}

/**
 * Return post types
 *
 * @throws error
 * @author WP Guppy<wpguppy@gmail.com>
 * @return 
 */
if( !function_exists( 'wpguppy_post_types' ) ) {
    function wpguppy_post_types() {
        $arg        = array('public'   => true);
        $post_types = get_post_types($arg, 'objects');
        $posts 		= array();
		
        foreach ($post_types as $post_type) {
            $posts[$post_type->name] = $post_type->labels->singular_name;
        }
		
        return $posts;
    }
}

/**
 * Return image type
 *
 * @throws error
 * @author WP Guppy<wpguppy@gmail.com>
 * @return 
 */
if( !function_exists( 'wpguppy_image_types' ) ) {
    function wpguppy_image_types($key='') {
        $list = array(
			'.jpg'       => esc_html__('jpg', 'wp-guppy'),
            '.png'       => esc_html__('png', 'wp-guppy'),
            '.jpeg'      => esc_html__('jpeg', 'wp-guppy'),
            '.gif'       => esc_html__('gif', 'wp-guppy'),
			
        );
		
		$list = apply_filters('wpguppy_image_types_filter', $list);
        if( !empty($key) ){
            $list   = !empty($list[$key]) ? $list[$key] : '';
        }
		
		return $list;
    }
    add_filter( 'wpguppy_image_types', 'wpguppy_image_types', 10, 1 );
}

/**
 * Return All static text
 *
 * @throws error
 * @author WP Guppy<wpguppy@gmail.com>
 * @return 
 */
if( !function_exists( 'wpguppy_default_text' ) ) {
    function wpguppy_default_text() {
        $list = array(
			'profile_settings'       => array( 'default' 	=> esc_html__('Profile settings', 'wp-guppy'),
											   'title'		=> esc_html__('Profile settings', 'wp-guppy')),
			'mute'       			 => array( 'default' 	=> esc_html__('Mute notifications', 'wp-guppy'),
											   'title'		=> esc_html__('Mute notifications', 'wp-guppy')),
			'logout'       			 => array( 'default' 	=> esc_html__('Logout', 'wp-guppy'),
											   'title'		=> esc_html__('Logout', 'wp-guppy')),
			'full_name'       		 => array( 'default' 	=> esc_html__('Full name', 'wp-guppy'),
											   'title'		=> esc_html__('Full name', 'wp-guppy')),
			'email'       			 => array( 'default' 	=> esc_html__('Email address', 'wp-guppy'),
											   'title'		=> esc_html__('Email address', 'wp-guppy')),
			'password'       			 => array( 'default' 	=> esc_html__('Password', 'wp-guppy'),
											   'title'		=> esc_html__('Password', 'wp-guppy')),
			'phone'       			 => array( 'default' 	=> esc_html__('Phone', 'wp-guppy'),
											   'title'		=> esc_html__('Phone', 'wp-guppy')),
			'upload_photo'       	 => array( 'default' 	=> esc_html__('to upload profile photo', 'wp-guppy'),
											   'title'		=> esc_html__('Upload photo', 'wp-guppy')),
			'remove'       			 => array( 'default' 	=> esc_html__('Remove', 'wp-guppy'),
											   'title'		=> esc_html__('Remove', 'wp-guppy')),
			'start_chat_text'       			 => array( 'default' 	=> esc_html__('Start now', 'wp-guppy'),
											   'title'		=> esc_html__('Start now text', 'wp-guppy')),
			'save_changes'       	=> array( 'default' 	=> esc_html__('Save changes', 'wp-guppy'),
											   'title'		=> esc_html__('Save changes', 'wp-guppy')),
			'contacts'       		=> array( 'default' 	=> esc_html__('Contacts', 'wp-guppy'),
											   'title'		=> esc_html__('Contacts', 'wp-guppy')),
			'requests_heading'       		=> array( 'default' 	=> esc_html__('Requests', 'wp-guppy'),
											   'title'		=> esc_html__('Requests heading text', 'wp-guppy')),
			'search'       			=> array( 'default' 	=> esc_html__('Search here', 'wp-guppy'),
											   'title'		=> esc_html__('Search here', 'wp-guppy')),
			'no_results'       		=> array( 'default' 	=> esc_html__('No results to show', 'wp-guppy'),
											   'title'		=> esc_html__('No results to show', 'wp-guppy')),
			'friends'       		=> array( 'default' 	=> esc_html__('Friends', 'wp-guppy'),
											   'title'		=> esc_html__('Friends', 'wp-guppy')),
			'blocked_users'       	=> array( 'default' 	=> esc_html__('Blocked users', 'wp-guppy'),
											   'title'		=> esc_html__('Blocked users', 'wp-guppy')),
			'sent'       			=> array( 'default' 	=> esc_html__('Sent', 'wp-guppy'),
											   'title'		=> esc_html__('Sent', 'wp-guppy')),
			'decline_user'       			=> array( 'default' 	=> esc_html__('Your request has been declined by this user', 'wp-guppy'),
											   'title'		=> esc_html__('Decline User Text', 'wp-guppy')),
			'invite'       			=> array( 'default' 	=> esc_html__('Invite', 'wp-guppy'),
											   'title'		=> esc_html__('Invite', 'wp-guppy')),
			'invitation_top_desc'    => array( 'default' 	=> esc_html__('Hey there! It looks like this contact is not in your friend list. Would you like to chat with this user?', 'wp-guppy'),
											   'title'		=> esc_html__('Contact is not in friend list', 'wp-guppy')),
			'invitaion_bottom_desc' => array( 'default' 	=> esc_html__('Hey there, I would like to add you in my friend list, Please hit “Accept” to start chatting', 'wp-guppy'),
											   'title'		=> esc_html__('Accept Friend Request', 'wp-guppy')),
			'accept_invite'       	=> array( 'default' 	=> esc_html__('Accept', 'wp-guppy'),
											   'title'		=> esc_html__('Accept', 'wp-guppy')),
			'decline_invite'       	=> array( 'default' 	=> esc_html__('Decline', 'wp-guppy'),
											   'title'		=> esc_html__('Decline', 'wp-guppy')),
			'start_chat_txt'       	=> array( 'default' 	=> esc_html__('Start chat', 'wp-guppy'),
											   'title'		=> esc_html__('Start chat text', 'wp-guppy')),
			'start_conversation'    => array( 'default' 	=> esc_html__('Select the user to start your conversation', 'wp-guppy'),
											   'title'		=> esc_html__('Start conversation', 'wp-guppy')),
			'no_attachments'    	=> array( 'default' 	=> esc_html__('No attachments to show', 'wp-guppy'),
											   'title'		=> esc_html__('No attachments to show', 'wp-guppy')),
			'voice_note'    		=> array( 'default' 	=> esc_html__('Voice note', 'wp-guppy'),
											   'title'		=> esc_html__('Voice note', 'wp-guppy')),
			'chat'       			=> array( 'default' 	=> esc_html__('Search chat', 'wp-guppy'),
											   'title'		=> esc_html__('Search Chat', 'wp-guppy')),
			'post_chat'       		=> array( 'default' 	=> esc_html__('Post chat history', 'wp-guppy'),
											   'title'		=> esc_html__('Posts chat', 'wp-guppy')),
			'sent_attachment'       => array( 'default' 	=> esc_html__('You sent an attachment', 'wp-guppy'),
											   'title'		=> esc_html__('You sent an attachment', 'wp-guppy')),
			'profile'       		=> array( 'default' 	=> esc_html__('Profile', 'wp-guppy'),
											   'title'		=> esc_html__('Profile', 'wp-guppy')),
			'video'       			=> array( 'default' 	=> esc_html__('Video', 'wp-guppy'),
											   'title'		=> esc_html__('Video', 'wp-guppy')),
			'audio'      			=> array( 'default' 	=> esc_html__('Audio', 'wp-guppy'),
											   'title'		=> esc_html__('Audio', 'wp-guppy')),
			'photo'       			=> array( 'default' 	=> esc_html__('Photo', 'wp-guppy'),
											   'title'		=> esc_html__('Photo', 'wp-guppy')),
			'document'       		=> array( 'default' 	=> esc_html__('Document', 'wp-guppy'),
											   'title'		=> esc_html__('Document', 'wp-guppy')),
			'location'       		=> array( 'default' 	=> esc_html__('Location', 'wp-guppy'),
											   'title'		=> esc_html__('Location', 'wp-guppy')),
			'type_message'       	=> array( 'default' 	=> esc_html__('Type your message here', 'wp-guppy'),
											   'title'		=> esc_html__('Type message', 'wp-guppy')),
			'search_results'        => array( 'default' 	=> esc_html__('Searching results', 'wp-guppy'),
											   'title'		=> esc_html__('Searching results', 'wp-guppy')),
			'unblock'       		=> array( 'default' 	=> esc_html__('Unblock', 'wp-guppy'),
											   'title'		=> esc_html__('Unblock', 'wp-guppy')),
			'block_user'       		=> array( 'default' 	=> esc_html__('Block user', 'wp-guppy'),
											   'title'		=> esc_html__('Block user', 'wp-guppy')),
			'unblock_user'       	=> array( 'default' 	=> esc_html__('Unblock user', 'wp-guppy'),
											   'title'		=> esc_html__('Unblock user', 'wp-guppy')),
			'offline'       		=> array( 'default' 	=> esc_html__('Offline', 'wp-guppy'),
											   'title'		=> esc_html__('Offline', 'wp-guppy')),
			'online'       			=> array( 'default' 	=> esc_html__('Online', 'wp-guppy'),
											   'title'		=> esc_html__('Online', 'wp-guppy')),
			'settings'       		=> array( 'default' 	=> esc_html__('Settings', 'wp-guppy'),
											   'title'		=> esc_html__('Settings', 'wp-guppy')),
			'actions'      			=> array( 'default' 	=> esc_html__('Actions', 'wp-guppy'),
											   'title'		=> esc_html__('Actions', 'wp-guppy')),
			'mute_conversation'     => array( 'default' 	=> esc_html__('Mute conversation', 'wp-guppy'),
											   'title'		=> esc_html__('Mute conversation', 'wp-guppy')),
			'unmute_conversation'   => array( 'default' 	=> esc_html__('Unmute conversation', 'wp-guppy'),
											   'title'		=> esc_html__('Unmute conversation', 'wp-guppy')),
			'privacy_settings'      => array( 'default' 	=> esc_html__('Privacy settings', 'wp-guppy'),
											   'title'		=> esc_html__('Privacy settings', 'wp-guppy')),
			'block_user'       		=> array( 'default' 	=> esc_html__('Block user', 'wp-guppy'),
											   'title'		=> esc_html__('Block user', 'wp-guppy')),
			'clear_chat'       		=> array( 'default' 	=> esc_html__('Clear chat', 'wp-guppy'),
											   'title'		=> esc_html__('Clear chat', 'wp-guppy')),
			'clear_chat_description'    => array( 'default' 	=> esc_html__('Are you sure you want to clear your chat history?', 'wp-guppy'),
											   'title'		=> esc_html__('Clear chat description', 'wp-guppy')),								   								   
			'clear_chat_button'    => array( 'default' 	=> esc_html__('Yes! clear all', 'wp-guppy'),
											   'title'		=> esc_html__('Yes! clear all', 'wp-guppy')),								   								   
			'report_user'    	   => array( 'default' 	=> esc_html__('Report', 'wp-guppy'),
											   'title'		=> esc_html__('Report', 'wp-guppy')),								   								   
			'report_group'    	   => array( 'default' 	=> esc_html__('Report Group', 'wp-guppy'),
											   'title'		=> esc_html__('Report Group', 'wp-guppy')),								   								   
			'report_heading'    	   => array( 'default' 	=> esc_html__('Report “((username))”', 'wp-guppy'),
											   'title'		=> esc_html__('Report user heading', 'wp-guppy')),								   								   
			'report_description'    	   => array( 'default' 	=> esc_html__('Please fill the report form below so we can review.', 'wp-guppy'),
											   'title'		=> esc_html__('Report user description', 'wp-guppy')),								   								   
			'report_title'    	   => array( 'default' 	=> esc_html__('Title the issue', 'wp-guppy'),
											   'title'		=> esc_html__('Report Title', 'wp-guppy')),								   								   
			'report_reason'    	   => array( 'default' 	=> esc_html__('Select report reason', 'wp-guppy'),
											   'title'		=> esc_html__('Select report reason', 'wp-guppy')),								   								   
			'report_issue_detail'    	   => array( 'default' 	=> esc_html__('Explain issue in detail', 'wp-guppy'),
											   'title'		=> esc_html__('Explain issue in detail', 'wp-guppy')),								   								   
			'report_add_description'    	   => array( 'default' 	=> esc_html__('Add description', 'wp-guppy'),
											   'title'		=> esc_html__('Add description', 'wp-guppy')),								   								   
			'report_submit'    	   => array( 'default' 	=> esc_html__('Submit report', 'wp-guppy'),
											   'title'		=> esc_html__('Submit report', 'wp-guppy')),								   								   
			'report_cancel'    	   => array( 'default' 	=> esc_html__('Cancel for now', 'wp-guppy'),
											   'title'		=> esc_html__('Cancel for now', 'wp-guppy')),								   								   
			'media'       			=> array( 'default' 	=> esc_html__('Media & attachments', 'wp-guppy'),
											   'title'		=> esc_html__('Media & attachments', 'wp-guppy')),
			'download_all'       			=> array( 'default' 	=> esc_html__('Download All', 'wp-guppy'),
											   'title'		=> esc_html__('Download All', 'wp-guppy')),
			'load_more'       			=> array( 'default' 	=> esc_html__('Load more', 'wp-guppy'),
											   'title'		=> esc_html__('Load more', 'wp-guppy')),
			'block_user_description'  => array( 'default' 	=> esc_html__('Are you sure you want to block this user?', 'wp-guppy'),
											   'title'		=> esc_html__('Block user description?', 'wp-guppy')),
			'block_user_title'        => array( 'default' 	=> esc_html__('Block user “((username))”', 'wp-guppy'),
											   'title'		=> esc_html__('Block user title', 'wp-guppy')),
			'block_user_button'       => array( 'default' 	=> esc_html__('Yes! block right now', 'wp-guppy'),
											   'title'		=> esc_html__('Block user button', 'wp-guppy')),
			'not_right_now'       	  => array( 'default' 	=> esc_html__('Not right now', 'wp-guppy'),
											   'title'		=> esc_html__('Not right now', 'wp-guppy')),
			'blocked_user_message'    => array( 'default' 	=> esc_html__('You have blocked this user. %Unblock% now to start chatting again', 'wp-guppy'),
											   'title'		=> esc_html__('Blocked user message', 'wp-guppy')),
			'unblock_user_heading'    => array( 'default' 	=> esc_html__('Unblock user “((username))”', 'wp-guppy'),
											   'title'		=> esc_html__('Unblock user heading', 'wp-guppy')),

			'you_are_blocked'    	  => array( 'default' 	=> esc_html__('You have been blocked by this user', 'wp-guppy'),
												'title'		=> esc_html__('You have been blocked by this user', 'wp-guppy')),

			'blocked'    	  			=> array( 'default' 	=> esc_html__('Blocked', 'wp-guppy'),
											   'title'		=> esc_html__('Blocked', 'wp-guppy')),

			'your_name'    	  			=> array( 'default' 	=> esc_html__('Your name', 'wp-guppy'),
											   'title'		=> esc_html__('Your name', 'wp-guppy')),

			'your_email'    	  			=> array( 'default' 	=> esc_html__('Your email', 'wp-guppy'),
											   'title'		=> esc_html__('Your email', 'wp-guppy')),

			'your_phone'    	  			=> array( 'default' 	=> esc_html__('Your phone number', 'wp-guppy'),
											   'title'		=> esc_html__('Your phone number', 'wp-guppy')),

			'respond_invite'    	  		=> array( 'default' 	=> esc_html__('Respond to invite', 'wp-guppy'),
											   'title'		=> esc_html__('Respond to invite', 'wp-guppy')),

			'resend_invite'    	  			=> array( 'default' 	=> esc_html__('Resend anyway', 'wp-guppy'),
											   'title'		=> esc_html__('Resend anyway', 'wp-guppy')),

			'is_typing'    	  				=> array( 'default' 	=> esc_html__('is typing', 'wp-guppy'),
												'title'		=> esc_html__('Typing for one user', 'wp-guppy')),
			'are_typing'    	  			=> array( 'default' 	=> esc_html__('are typing', 'wp-guppy'),
												'title'		=> esc_html__('Less than 4 users are typing', 'wp-guppy')),
			'more_user_typing'    	  		=> array( 'default' 	=> esc_html__('and ((user_count)) more are typing', 'wp-guppy'),
												'title'		=> esc_html__('More than 4 users are typing', 'wp-guppy')),
			'you_sent_attachment'    	  	=> array( 'default' 	=> esc_html__('You sent an attachment', 'wp-guppy'),
											   'title'		=> esc_html__('You sent an attachment', 'wp-guppy')),
			'grp_sent_attachment'    	  	=> array( 'default' 	=> esc_html__('((username)): Sent an attachment', 'wp-guppy'),
											   'title'		=> esc_html__('User sent an attachment text', 'wp-guppy')),
			'attachments_uploading'    	  	=> array( 'default' 	=> esc_html__('Your file is being uploaded', 'wp-guppy'),
											   'title'		=> esc_html__('Uploading media message text', 'wp-guppy')),

			'sent_you_attachment'    	  	=> array( 'default' 	=> esc_html__('Sent you an attachment', 'wp-guppy'),
											   'title'		=> esc_html__('Sent you an attachment', 'wp-guppy')),

			'you_sent_location'    	  		=> array( 'default' 	=> esc_html__('You sent a location', 'wp-guppy'),
											   'title'		=> esc_html__('You sent a location', 'wp-guppy')),
			'grp_sent_location'    	  		=> array( 'default' 	=> esc_html__('((username)): Sent a location', 'wp-guppy'),
											   'title'		=> esc_html__('User sent a location', 'wp-guppy')),

			'sent_you_location'    	  		=> array( 'default' 	=> esc_html__('Sent you a location', 'wp-guppy'),
											   'title'		=> esc_html__('Sent you a location', 'wp-guppy')),

			'you_sent_voice_note'    	  	=> array( 'default' 	=> esc_html__('You sent a voice note', 'wp-guppy'),
											   'title'		=> esc_html__('You sent a voice note', 'wp-guppy')),
			'grp_sent_voice_note'    	  	=> array( 'default' 	=> esc_html__('((username)): Sent a voice note', 'wp-guppy'),
											   'title'		=> esc_html__('User sent a voice note', 'wp-guppy')),

			'sent_you_voice_note'    	 	=> array( 'default' 	=> esc_html__('Sent you a voice note', 'wp-guppy'),
											   'title'		=> esc_html__('Sent you a voice note', 'wp-guppy')),

			'unblock_user_description'      => array( 'default' 	=> esc_html__('Are you sure you want to unblock this user?', 'wp-guppy'),
											   'title'		=> esc_html__('Unblock user description', 'wp-guppy')),
			'unblock_button'       			=> array( 'default' 	=> esc_html__('Yes! unblock right now ', 'wp-guppy'),
											   'title'		=> esc_html__('Unblock button', 'wp-guppy')),
			'reply_message'       			=> array( 'default' 	=> esc_html__('Reply message', 'wp-guppy'),
											   'title'		=> esc_html__('Reply message', 'wp-guppy')),
			'click_here'       				=> array( 'default' 	=> esc_html__('Click here', 'wp-guppy'),
											   'title'		=> esc_html__('click here', 'wp-guppy')),
			'delete'       					=> array( 'default' 	=> esc_html__('Delete', 'wp-guppy'),
											   'title'		=> esc_html__('Delete', 'wp-guppy')),
			'unblock_now'       			=> array( 'default' 	=> esc_html__('Unblock now', 'wp-guppy'),
											   'title'		=> esc_html__('Unblock now ', 'wp-guppy')),
			'download'       				=> array( 'default' 	=> esc_html__('Download', 'wp-guppy'),
											   'title'		=> esc_html__('Download', 'wp-guppy')),	
			'deleted_message'       		=> array( 'default' 	=> esc_html__('This message was deleted', 'wp-guppy'),
											   'title'		=> esc_html__('Deleted message', 'wp-guppy')),
			'recording_app_txt'       		=> array( 'default' 	=> esc_html__('Recording...', 'wp-guppy'),
											   'title'		=> esc_html__('Recording text for rect app', 'wp-guppy')),
			'stop_app_txt'       			=> array( 'default' 	=> esc_html__('Stop', 'wp-guppy'),
											   'title'		=> esc_html__('Stop text for rect app', 'wp-guppy')),
			'map_app_txt'       			=> array( 'default' 	=> esc_html__('Map', 'wp-guppy'),
											   'title'		=> esc_html__('Map text for rect app', 'wp-guppy')),
			'current_loc_app_txt'       	=> array( 'default' 	=> esc_html__('Send current location', 'wp-guppy'),
											   'title'		=> esc_html__('Send location text for rect app', 'wp-guppy')),
			'video_app_txt'       			=> array( 'default' 	=> esc_html__('Video', 'wp-guppy'),
											   'title'		=> esc_html__('Video text for rect app', 'wp-guppy')),
			'more_text'       			=> array( 'default' 	=> esc_html__('more', 'wp-guppy'),
											   'title'		=> esc_html__('User more text', 'wp-guppy')),
			'search_conversation'       => array( 'default' 	=> esc_html__('Search in conversation', 'wp-guppy'),
											   'title'		=> esc_html__('Search in conversation text', 'wp-guppy')),
			'edit_group'       => array( 'default' 	=> esc_html__('Edit group preferences', 'wp-guppy'),
											   'title'		=> esc_html__('Edit group preferences text', 'wp-guppy')),
			'leave_group_txt'       => array( 'default' 	=> esc_html__('Leave group', 'wp-guppy'),
											   'title'		=> esc_html__('Leave group text', 'wp-guppy')),
			'delete_group_txt'       => array( 'default' 	=> esc_html__('Delete group conversation', 'wp-guppy'),
											   'title'		=> esc_html__('Delete group conversation text', 'wp-guppy')),
			'report_group_txt'       => array( 'default' 	=> esc_html__('Report group', 'wp-guppy'),
											   'title'		=> esc_html__('Report group text', 'wp-guppy')),
			'group_users_txt'       => array( 'default' 	=> esc_html__('Group users', 'wp-guppy'),
											   'title'		=> esc_html__('Group users text', 'wp-guppy')),
			'admin_txt'       => array( 'default' 	=> esc_html__('ADMIN', 'wp-guppy'),
											   'title'		=> esc_html__('Admin text', 'wp-guppy')),
			'owner_txt'       => array( 'default' 	=> esc_html__('OWNER', 'wp-guppy'),
											   'title'		=> esc_html__('OWNER text', 'wp-guppy')),
			'create_group_txt'       => array( 'default' 	=> esc_html__('Create Group', 'wp-guppy'),
											   'title'		=> esc_html__('Create Group text', 'wp-guppy')),
			'edit_group_txt'       => array( 'default' 	=> esc_html__('Edit Group', 'wp-guppy'),
											   'title'		=> esc_html__('Edit Group text', 'wp-guppy')),
			'grp_photo_txt'       => array( 'default' 	=> esc_html__('Upload group photo', 'wp-guppy'),
											   'title'		=> esc_html__('Upload group photo text', 'wp-guppy')),
			'grp_photo_dsc_txt'       => array( 'default' 	=> esc_html__('to upload group photo', 'wp-guppy'),
											   'title'		=> esc_html__('Upload group photo text', 'wp-guppy')),
			'grp_title_txt'       => array( 'default' 	=> esc_html__('Add group title', 'wp-guppy'),
											   'title'		=> esc_html__('Add group title text', 'wp-guppy')),
			'grp_edit_title_txt'       => array( 'default' 	=> esc_html__('Edit group title', 'wp-guppy'),
											   'title'		=> esc_html__('Edit group title text', 'wp-guppy')),
			'grp_title_placeholder_txt' => array( 'default' 	=> esc_html__('Enter group title here', 'wp-guppy'),
											   'title'		=> esc_html__('group title placeholder text', 'wp-guppy')),
			'grp_users_txt'       => array( 'default' 	=> esc_html__('Select group users', 'wp-guppy'),
											   'title'		=> esc_html__('Select group users text', 'wp-guppy')),
			'grp_edit_users_txt'       => array( 'default' 	=> esc_html__('Update group users', 'wp-guppy'),
											   'title'		=> esc_html__('Update group users text', 'wp-guppy')),
			'grp_mk_admin_txt'       => array( 'default' 	=> esc_html__('MAKE ADMIN', 'wp-guppy'),
											   'title'		=> esc_html__('Make admin text', 'wp-guppy')),
			'grp_admin_txt'       	=> array( 'default' 	=> esc_html__('ADMIN', 'wp-guppy'),
											   'title'		=> esc_html__('Admin text', 'wp-guppy')),
			'grp_create_txt'       	=> array( 'default' 	=> esc_html__('Create now', 'wp-guppy'),
											   'title'		=> esc_html__('Create now text', 'wp-guppy')),
			'grp_updt_txt'       	=> array( 'default' 	=> esc_html__('Update now', 'wp-guppy'),
											   'title'		=> esc_html__('Update now text', 'wp-guppy')),
			'disable_grp_txt'       => array( 'default' 	=> esc_html__('Make disable replies of this group', 'wp-guppy'),
											   'title'		=> esc_html__('Disable group text', 'wp-guppy')),
			'you'  					=> array( 'default' 	=> esc_html__('You', 'wp-guppy'),
											   'title'		=> esc_html__('You', 'wp-guppy')),
			'error_title'  			=> array( 'default' 	=> esc_html__('Oops...', 'wp-guppy'),
											   'title'		=> esc_html__('Oops...', 'wp-guppy')),
			'select_admin_text'  => array( 'default' 	=> esc_html__('Please select atleast one admin before you leave', 'wp-guppy'),
											   'title'		=> esc_html__('Select admin before you leave text.', 'wp-guppy')),
			'group_created_notify'  => array( 'default' 	=> esc_html__('((username)) created this group', 'wp-guppy'),
											   'title'		=> esc_html__('Group created notify', 'wp-guppy')),
			'group_updated_notify'  => array( 'default' 	=> esc_html__('((username)) update this group', 'wp-guppy'),
											   'title'		=> esc_html__('Group updated notify', 'wp-guppy')),
			'group_removed_notify'  => array( 'default' 	=> esc_html__('You are removed by group admin', 'wp-guppy'),
											   'title'		=> esc_html__('Group removed Notify', 'wp-guppy')),
			'you_left'  			=> array( 'default' 	=> esc_html__('You left', 'wp-guppy'),
											   'title'		=> esc_html__('You left', 'wp-guppy')),
			'search_txt'       		=> array( 'default' 	=> esc_html__('Search user here', 'wp-guppy'),
											   'title'		=> esc_html__('Search user text', 'wp-guppy')),
			'search_user_heading_txt'	=> array( 'default' 	=> esc_html__('Search user', 'wp-guppy'),
											   'title'		=> esc_html__('Search user Heading text', 'wp-guppy')),
			'you_txt'       		=> array( 'default' 	=> esc_html__('You', 'wp-guppy'),
											   'title'		=> esc_html__('You text', 'wp-guppy')),
			'leave_group_heading'   => array( 'default' 	=> esc_html__('Leaving group?', 'wp-guppy'),
											   'title'		=> esc_html__('Leaving group text', 'wp-guppy')),
			'leave_group_dsc'       => array( 'default' 	=> esc_html__('Are you sure you want to leave “((groupname))”?', 'wp-guppy'),
											   'title'		=> esc_html__('Leaving group desc', 'wp-guppy')),
			'leave_group_opt_txt'       => array( 'default' 	=> esc_html__('Yes! leave now', 'wp-guppy'),
											   'title'		=> esc_html__('Leave group text', 'wp-guppy')),
			'delete_grp_txt'       => array( 'default' 	=> esc_html__('Delete “((groupname))”', 'wp-guppy'),
											   'title'		=> esc_html__('Delete group text', 'wp-guppy')),
			'delete_group_desc'       => array( 'default' 	=> esc_html__('Are you sure you want to delete this group?', 'wp-guppy'),
											   'title'		=> esc_html__('Delete group desc', 'wp-guppy')),
			'delete_group_opt_txt'       => array( 'default' 	=> esc_html__('Yes! remove right now', 'wp-guppy'),
											   'title'		=> esc_html__('Delete group option text', 'wp-guppy')),
			'disable_reply_txt'       => array( 'default' 	=> esc_html__('“Admin” disabled replies of this group', 'wp-guppy'),
											   'title'		=> esc_html__('Disable reply text', 'wp-guppy')),
			'add_grp_member_txt'       => array( 'default' 	=> esc_html__('Admin added “((username))” to this group', 'wp-guppy'),
											   'title'		=> esc_html__('Add group member text', 'wp-guppy')),
			'remove_grp_member_txt'       => array( 'default' 	=> esc_html__('Admin removed “((username))” from this group', 'wp-guppy'),
											   'title'		=> esc_html__('Remove group member text', 'wp-guppy')),
			'leave_grp_member_txt'       => array( 'default' 	=> esc_html__('“((username))” left this group', 'wp-guppy'),
											   'title'		=> esc_html__('Leave group member text', 'wp-guppy')),
			'grp_other_membert_txt'       => array( 'default' 	=> esc_html__(' and ((counter)) other members', 'wp-guppy'),
											   'title'		=> esc_html__('Counter text from other members', 'wp-guppy')),
			'delet_grp_member_txt'       => array( 'default' 	=> esc_html__('Delete “((username))”', 'wp-guppy'),
											   'title'		=> esc_html__('Delet group member text', 'wp-guppy')),
			'delet_grp_member_dsc'       => array( 'default' 	=> esc_html__('Are you sure you want to delete this member?', 'wp-guppy'),
											   'title'		=> esc_html__('Delet group member desc', 'wp-guppy')),
			'delet_grp_member_btn'       => array( 'default' 	=> esc_html__('Yes! detele right now', 'wp-guppy'),
											   'title'		=> esc_html__('Delet group member button text', 'wp-guppy')),
			'updt_grp_role'       => array( 'default' 	=> esc_html__('Admin update “((username))” group role', 'wp-guppy'),
											   'title'		=> esc_html__('Admin update group role', 'wp-guppy')),
			'before_leave_heading'       => array( 'default' 	=> esc_html__('Before you leave', 'wp-guppy'),
											   'title'		=> esc_html__('Befoe you leave text', 'wp-guppy')),
			'assign_group_admin_txt'       => array( 'default' 	=> esc_html__('Assign a new group admin', 'wp-guppy'),
											   'title'		=> esc_html__('Assign group admin text', 'wp-guppy')),
			'assign_grp_admin_btn_txt'       => array( 'default' 	=> esc_html__('Set admin and leave the group', 'wp-guppy'),
											   'title'		=> esc_html__('Set admin button text', 'wp-guppy')),
			'auto_inv_receiver_msg'       => array( 'default' 	=> esc_html__("“((username))” added you to the friend list, let\'s chat now", 'wp-guppy'),
											   'title'		=> esc_html__('Auto Invite receiver message text', 'wp-guppy')),
			'auto_inv_sender_msg'       => array( 'default' 	=> esc_html__("You have added “((username))” to your friend list, let\'s chat now", 'wp-guppy'),
											   'title'		=> esc_html__('Auto Invite sender message text', 'wp-guppy')),
			'group_txt_heading'       => array( 'default' 	=> esc_html__("Groups", 'wp-guppy'),
											   'title'		=> esc_html__('Groups text Heading', 'wp-guppy')),
			'private_txt_heading'       => array( 'default' 	=> esc_html__("Private", 'wp-guppy'),
											   'title'		=> esc_html__('Private text Heading', 'wp-guppy')),
			'for_this_post'       => array( 'default' 	=> esc_html__("For this post", 'wp-guppy'),
											   'title'		=> esc_html__('For this post text', 'wp-guppy')),
			'for_all_post'       => array( 'default' 	=> esc_html__("For all posts", 'wp-guppy'),
											   'title'		=> esc_html__('For all post text', 'wp-guppy')),
			'max_file_uploads_msg'       => array( 'default' 	=> esc_html__("Maximum number of allowable file uploads has been exceeded", 'wp-guppy'),
											   'title'		=> esc_html__('Maximum files allowable error message', 'wp-guppy')),
			'close_all_conversation'       => array( 'default' 	=> esc_html__("Close all conversation", 'wp-guppy'),
											   'title'		=> esc_html__('Close all conversation', 'wp-guppy')),
			'open_in_messenger'       => array( 'default' 	=> esc_html__("Open in messenger", 'wp-guppy'),
											   'title'		=> esc_html__('Open in messenger text', 'wp-guppy')),
			'empty_field_required'       => array( 'default' 	=> esc_html__("Please fill all the required fields.", 'wp-guppy'),
											   'title'		=> esc_html__('Empty fields required text.', 'wp-guppy')),
			'microphone_connection_desc'       => array( 'default' 	=> esc_html__("Please connect microphone and allow the permission.", 'wp-guppy'),
											   'title'		=> esc_html__('Microphone connection error.', 'wp-guppy')),
			'input_params_err'       => array( 'default' 	=> esc_html__("Something went wrong.", 'wp-guppy'),
											   'title'		=> esc_html__('Input params validation error.', 'wp-guppy')),
			'invalid_input_file'       => array( 'default' 	=> esc_html__("Invalid file type or file size.", 'wp-guppy'),
											   'title'		=> esc_html__('Invalid file type or file size error text.', 'wp-guppy')),
			'empty_input_err_txt'       => array( 'default' 	=> esc_html__("Please, enter all the required details.", 'wp-guppy'),
											   'title'		=> esc_html__('Empty input field error text', 'wp-guppy')),
			'crop_img_txt'       => array( 'default' 	=> esc_html__("Crop image", 'wp-guppy'),
											   'title'		=> esc_html__('Crop image text', 'wp-guppy')),
			'signin_box_hdr_txt'       => array( 'default' 	=> esc_html__("Let’s chat together", 'wp-guppy'),
											   'title'		=> esc_html__('Sign in widget box header text', 'wp-guppy')),
			'geo_location_error_txt'       => array( 'default' 	=> esc_html__("Sorry, your browser does not support HTML5 geolocation.", 'wp-guppy'),
											   'title'		=> esc_html__('Geo location error text', 'wp-guppy')),
			'cancel_txt'       => array( 'default' 	=> esc_html__("Cancel", 'wp-guppy'),
											   'title'		=> esc_html__('Cancel button text', 'wp-guppy')),
			'contact_tooltip_txt'       => array( 'default' 	=> esc_html__("Contacts", 'wp-guppy'),
											   'title'		=> esc_html__('Contact list tooltip text', 'wp-guppy')),
			'conv_tooltip_txt'       => array( 'default' 	=> esc_html__("Chats", 'wp-guppy'),
											   'title'		=> esc_html__('Conversation list tooltip text', 'wp-guppy')),
			'friend_tooltip_txt'       => array( 'default' 	=> esc_html__("Friends", 'wp-guppy'),
											   'title'		=> esc_html__('Friend list tooltip text', 'wp-guppy')),
			'block_tooltip_txt'       => array( 'default' 	=> esc_html__("Blocked users", 'wp-guppy'),
											   'title'		=> esc_html__('Block list tooltip text', 'wp-guppy')),
			'post_tooltip_txt'       => array( 'default' 	=> esc_html__("Post chat", 'wp-guppy'),
											   'title'		=> esc_html__('Post list tooltip text', 'wp-guppy')),
        );
		
		$list = apply_filters('wpguppy_default_text_filter', $list);

		return $list;
    }
    add_filter( 'wpguppy_default_text', 'wpguppy_default_text', 10, 1 );
}

/**
 * Return Reporting Reasons
 *
 * @throws error
 * @author WP Guppy<wpguppy@gmail.com>
 * @return 
 */

if( !function_exists( 'wpguppy_reporting_reasons' ) ) {

    function wpguppy_reporting_reasons($key='') {
        $list = array( 
			esc_html__('Inappropriate Content', 'wp-guppy'),
		 	esc_html__('Spam', 'wp-guppy'),
		 	esc_html__('Privacy violates', 'wp-guppy'),
			esc_html__('Others', 'wp-guppy'),
			
        );
		
		$list = apply_filters('wpguppy_reporting_reasons_filter', $list);
        if( !empty($key) ){
            $list   = !empty($list[$key]) ? $list[$key] : '';
        }
		
		return $list;
    }
    add_filter( 'wpguppy_reporting_reasons', 'wpguppy_reporting_reasons', 10, 1 );
}
/**
 * Return audio type
 *
 * @throws error
 * @author WP Guppy<wpguppy@gmail.com>
 * @return 
 */
if( !function_exists( 'wpguppy_audio_types' ) ) {

    function wpguppy_audio_types($key='') {
        $list = array(
			'.mp3'       => esc_html__('mp3', 'wp-guppy'),
            '.flac'      => esc_html__('flac', 'wp-guppy'),
            '.wav'       => esc_html__('wav', 'wp-guppy'),
            '.aac'       => esc_html__('aac', 'wp-guppy'),
            '.wma'       => esc_html__('wma', 'wp-guppy'),
			
        );
		
		$list = apply_filters('wpguppy_audio_types_filter', $list);
        if( !empty($key) ){
            $list   = !empty($list[$key]) ? $list[$key] : '';
        }
		
		return $list;
    }
    add_filter( 'wpguppy_audio_types', 'wpguppy_audio_types', 10, 1 );
}

/**
 * Return video type
 *
 * @throws error
 * @author WP Guppy<wpguppy@gmail.com>
 * @return 
 */
if( !function_exists( 'wpguppy_video_types' ) ) {
    function wpguppy_video_types($key='') {
        $list = array(
			'.mp4'       => esc_html__('mp4', 'wp-guppy'),
            '.ogv'       => esc_html__('ogv', 'wp-guppy'),
            '.webm'      => esc_html__('webm', 'wp-guppy'),
            '.wmv'       => esc_html__('wmv', 'wp-guppy'),
            '.avi'       => esc_html__('avi', 'wp-guppy'),
            '.mov'       => esc_html__('mov', 'wp-guppy'),
            '.flv'       => esc_html__('flv', 'wp-guppy'),
            '.f4v'       => esc_html__('f4v', 'wp-guppy'),
            '.mpeg'      => esc_html__('mpeg', 'wp-guppy'),
			'.3gp'		 => esc_html__('3gp', 'wp-guppy'),
			'.mkv'		 => esc_html__('mkv', 'wp-guppy')
			
        );
		
		$list = apply_filters('wpguppy_video_types_filter', $list);
        if( !empty($key) ){
            $list   = !empty($list[$key]) ? $list[$key] : '';
        }
		
		return $list;
    }
    add_filter( 'wpguppy_video_types', 'wpguppy_video_types', 10, 1 );
}

/**
 * Return file type
 *
 * @throws error
 * @author WP Guppy<wpguppy@gmail.com>
 * @return 
 */
if( !function_exists( 'wpguppy_file_types' ) ) {
    function wpguppy_file_types($key='') {
        $list = array(
            '.pdf'       => esc_html__('pdf','wp-guppy'),
            '.doc'       => esc_html__('doc','wp-guppy'),
            '.docx'      => esc_html__('docx','wp-guppy'),
            '.xls'       => esc_html__('xls','wp-guppy'),
            '.xlsx'      => esc_html__('xlsx','wp-guppy'),
            '.ppt'       => esc_html__('ppt','wp-guppy'),
            '.pptx'      => esc_html__('pptx','wp-guppy'),
            '.zip'       => esc_html__('zip','wp-guppy'),
            '.7zip'       => esc_html__('7zip','wp-guppy'),
            '.csv'       => esc_html__('csv','wp-guppy'),
            '.txt'       => esc_html__('txt','wp-guppy')
        );
		
		$list = apply_filters('wpguppy_file_types_filter', $list);
        if( !empty($key) ){
            $list   = !empty($list[$key]) ? $list[$key] : '';
        }
		
		return $list;
    }
    add_filter( 'wpguppy_file_types', 'wpguppy_file_types', 10, 1 );
}

/**
 * @init users online status
  *
 * @throws error
 * @author WP Guppy<wpguppy@gmail.com>
 * @return 
 */
if (!function_exists('wpguppy_OnlineInit')) {
	add_action('init', 'wpguppy_OnlineInit');
	add_action('admin_init', 'wpguppy_OnlineInit');
	function wpguppy_OnlineInit(){
		$logged_in_users = get_transient('wpguppy_online_status'); 
		$user = wp_get_current_user(); //Get the current user's data
		if(empty($logged_in_users)){
			$query_meta_args = array(
				array(
					'key'     	=> 'wpguppy_user_online',                 
					'compare' 	=> '=',
					'value' 	=> '1',
				)
			);
			$query_args = array(
				'fields' 			=> array('id'),
				'number'			=> -1,
				'meta_query' 		=> $query_meta_args
			);
			$all_logined_users = get_users( $query_args );
			if(!empty($all_logined_users)){
				foreach( $all_logined_users as $single ) {
					delete_user_meta($single->id, 'wpguppy_user_online');
				}
			}
		}
		if ($user->ID > 0 && (!isset($logged_in_users[$user->ID]['last']) || $logged_in_users[$user->ID]['last'] <= time() - 900) ){
			$logged_in_users[$user->ID] = array(
				'id' 		=> $user->ID,
				'last' 		=> time(),
			);
			set_transient('wpguppy_online_status', $logged_in_users, 900);
			update_user_meta($user->ID,'wpguppy_user_online','1');
		}
		if(!empty($logged_in_users)){
			foreach($logged_in_users as $single){
				if($single['last'] < time() - 900){
					delete_user_meta($single['id'], 'wpguppy_user_online');
				}
			}
		}
	}
}

/**
 * @logout users online status update
  *
 * @throws error
 * @author WP Guppy<wpguppy@gmail.com>
 * @return 
 */
if (!function_exists('wpguppy_LogoutInit')) {
	add_action('wp_logout', 'wpguppy_LogoutInit');
	function wpguppy_LogoutInit($userId){
		$logged_in_users = get_transient('wpguppy_online_status'); 
		if( !empty( $userId ) ){
			if( !empty( $logged_in_users[$userId] ) ){
				unset($logged_in_users[$userId]);
				set_transient('wpguppy_online_status', $logged_in_users, 900);
			}
			delete_user_meta($userId,'wpguppy_user_online');
		}
	}
}

/**
 * @Check if user is online
  *
 * @throws error
 * @author WP Guppy<wpguppy@gmail.com>
 * @return 
 */
if (!function_exists('wpguppy_UserOnline')) {
	add_filter('wpguppy_UserOnline','wpguppy_UserOnline',10,1);
	function wpguppy_UserOnline($id){	
		$logged_in_users = get_transient('wpguppy_online_status'); 
		return isset($logged_in_users[$id]['last']) && $logged_in_users[$id]['last'] > time() - 900;
	}
}

/**
 * @get user last login
  *
 * @throws error
 * @author WP Guppy<wpguppy@gmail.com>
 * @return 
 */
if (!function_exists('wpguppy_UserLastLogin')) {
	add_action('wpguppy_UserLastLogin','wpguppy_UserLastLogin',10,1);
	function wpguppy_UserLastLogin($id){
		$logged_in_users = get_transient('wpguppy_online_status'); 

		if ( isset($logged_in_users[$id]['last']) ){
			return $logged_in_users[$id]['last'];
		} else {
			return false;
		}
	}	
}

/**
 * @get send message to guppy user
  *
 * @throws error
 * @author Amentotech <wpguppy@gmail.com>
 * @return 
 */
if (!function_exists('wpguppy_send_message_to_user')) {
	add_action('wpguppy_send_message_to_user','wpguppy_send_message_to_user', 10, 3);
	function wpguppy_send_message_to_user($senderId=0, $receiverId=0, $message = ''){
		$guppyModel     = WPGuppy_Model::instance();
        $fetchResults 	= $guppyModel->getGuppyFriend($senderId, $receiverId, false);
        $send_message   = false;
		
        if(empty($fetchResults)){
            $data 	= array(
                'send_by' 				=> $senderId,
                'send_to' 				=> $receiverId,
                'friend_status'			=> '1',
                'friend_created_date' 	=> date('Y-m-d H:i:s'),
            );
            $guppyModel->insertData('wpguppy_friend_list', $data, false);
            $send_message = true;
        }elseif($fetchResults['friend_status'] == 1){
            $send_message = true;
        }
		
        if($send_message && !empty($message)){
            $messageData        = array();
            $messageSentTime 	= date('Y-m-d H:i:s');
			$timestamp 			= strtotime($messageSentTime);
			
			$messageData['sender_id'] 			= $senderId; 
			$messageData['receiver_id'] 		= $receiverId; 
			$messageData['user_type'] 			= 1;  
			$messageData['message'] 			= wp_strip_all_tags(sanitize_text_field($message)); 
			$messageData['chat_type'] 			= 1; 
			$messageData['message_type'] 		= 0; 
			$messageData['timeStamp'] 			= $timestamp; 
			$messageData['message_sent_time'] 	= $messageSentTime;

            $guppyModel->insertData('wpguppy_message' , $messageData, false);

			$messageData['messageType'] 		= 0;
			//Message sent for themes comptibility
			do_action('wpguppy_on_message_sent',$messageData,'',$senderId,$receiverId);
        }
	}	
}

/**
 * @get send message to guppy user
  *
 * @throws error
 * @author Amentotech <wpguppy@gmail.com>
 * @return 
 */
if ( !function_exists('wpguppy_update_user_information') ) {
	add_action('wpguppy_update_user_information','wpguppy_update_user_information', 10, 5);
	function wpguppy_update_user_information($name = '', $phone='', $email = '', $user_id = '', $user_image = ''){
		$guppyModel     = WPGuppy_Model::instance();
		$where 		 	= "user_id=".$user_id; 
		$fetchResults 	= $guppyModel->getData('id','wpguppy_users',$where );
		$data 	= array(
			'user_id' 		=> $user_id,
			'user_name' 	=> $name,
			'user_email'	=> $email,
			'user_image'	=> $user_image,
			'user_phone'	=> $phone,
		);

        if( empty( $fetchResults ) ) {
            $guppyModel->insertData('wpguppy_users', $data, false );
        } else {
			$where = array( 'user_id' => $user_id );
			$guppyModel->updateData('wpguppy_users', $data, $where );
		}
	}	
}

/**
 * @get check already friend
  *
 * @throws error
 * @author Amentotech <wpguppy@gmail.com>
 * @return 
 */
if (!function_exists('wpguppy_is_already_friend')) {
	add_filter('wpguppy_is_already_friend','wpguppy_is_already_friend', 10, 2);
	function wpguppy_is_already_friend($senderId=0, $receiverId=0){
		$guppyModel     = WPGuppy_Model::instance();
        $fetchResults 	= $guppyModel->getGuppyFriend($senderId, $receiverId, false);
        return !empty($fetchResults) && $fetchResults['friend_status'] == 1 ? true : false;
	}	
}

/**
 * @get count of unread messages
  *
 * @throws error
 * @author Amentotech <wpguppy@gmail.com>
 * @return 
 */
if (!function_exists('wpguppy_count_all_unread_messages')) {
	add_filter('wpguppy_count_all_unread_messages','wpguppy_count_all_unread_messages', 10, 1);
	function wpguppy_count_all_unread_messages($userId = 0){
		
        $guppyModel     = WPGuppy_Model::instance();
		$restApiObj 	= new WPGuppy_RESTAPI('wp-guppy', WPGUPPY_VERSION);
		$filterData =  array();
        $filterData['receiverId'] = $userId;

		// get one to one chat message unread count
		$filterData['chatType'] = '1';
		$filterData['receiverId'] = $userId;
		$onetoOneChatCount = $guppyModel->getUnreadCount($filterData);
		
		// get posts message unread count
		$filterData['chatType'] = '0';
		$unseenPostMsgCount 	= $guppyModel->getUnreadCount($filterData);

		// get group message unread count
		$groupCount = 0;
		$userGroups = $guppyModel->getUserGroups($userId);
		
		if(!empty($userGroups)){
			$filterData 				= array();
			$filterData['senderId'] 	= $userId;	
			$filterData['chatType'] 	= '2';
			$filterData['actionBy'] 	= $userId;
			$filterData['orderBy'] 		= 'action_type';
			$filterData['actionType'] 	= array('3','4','5'); // group left or removed from group
			foreach($userGroups as $single){
				$filterData['groupId'] 			= $single['group_id'];
				$filterData['memberAddedDate'] 	= $single['member_added_date'];
				$statusActions = array();
				$filterData['groupAction'] = array();
				$chatActions = $restApiObj->getGuppyChatAction($filterData);
				$exitGroupTime = $deleteGroupTime = '';
				if(!empty($chatActions)){
					foreach($chatActions as $action){
						if($action['action_type'] == '5'){
							$deleteGroupTime = $action['action_updated_time'];
						}else{
							if($deleteGroupTime != ''){
								if(strtotime($action['action_time']) >= strtotime($deleteGroupTime)){
									$statusActions[] = array(
										'statusActionTime' 		=> $action['action_time'],
										'statusUpdatedTime' 	=> $action['action_updated_time'],
									);
								}
							}else{
								$statusActions[] = array(
									'statusActionTime' 		=> $action['action_time'],
									'statusUpdatedTime' 	=> $action['action_updated_time'],
								);
							}	
							if($single['member_status'] == '2' || $single['member_status'] == '0'){
								if(strtotime($action['action_time']) >= strtotime($exitGroupTime)){
									$exitGroupTime = $action['action_time'];
								}
							}
						}
					}
					if($exitGroupTime!=''){
						$filterData['groupAction']['exitGroupTime'] = $exitGroupTime;
					}
					if($deleteGroupTime!=''){
						$filterData['groupAction']['deleteGroupTime'] = $deleteGroupTime;
					}
					$filterData['groupAction']['status'] = $statusActions;
				}
				$count = $guppyModel->getUnreadCount($filterData);
				$groupCount = $groupCount + $count; 
			}
		}
		return ( intval( $groupCount ) + intval( $onetoOneChatCount ) + intval($unseenPostMsgCount));
	}	
}

/**
 * @get count of unread messages of a user
  *
 * @throws error
 * @author Amentotech <wpguppy@gmail.com>
 * @return 
 */
if (!function_exists('wpguppy_count_specific_user_unread_messages')) {
	add_filter('wpguppy_count_specific_user_unread_messages','wpguppy_count_specific_user_unread_messages', 10, 2);
	function wpguppy_count_specific_user_unread_messages($senderId=0, $receiverId=0){
		$filterData =  array();
        $filterData['chatType'] 	= '1';
        $filterData['senderId'] 	= $senderId;
        $filterData['receiverId'] 	= $receiverId;
        $guppyModel     = WPGuppy_Model::instance();
        $unreadCount 	= $guppyModel->getUnreadCount($filterData);
        return ! empty( $unreadCount ) ? intval( $unreadCount ) : 0;
	}	
}

/**
 * @Edit user information
  *
 * @throws error
 * @author Amentotech <wpguppy@gmail.com>
 * @return 
 */
if (!function_exists('wpguppy_custom_user_profile_fields')) {
	function wpguppy_custom_user_profile_fields($user){
		$settings 	= !empty($user->ID) ?  get_user_meta( $user->ID, 'wpguppy_user_settings', true ) : array();
		$name 		= !empty($settings['name']) ? $settings['name'] : '';
		$email 		= !empty($settings['email']) ? $settings['email'] : '';
		$phone 		= !empty($settings['phone']) ? $settings['phone'] : '';
		?>
		<h3><?php esc_html_e('WP Guppy user settings','wp-guppy');?></h3>
		<span class="description"><?php esc_html_e('This settings will be added to WP plugin. You may leave this infromation empty','wp-guppy');?></span>
		<table class="form-table">
			<tr>
				<th><label><?php esc_html_e('Name','wp-guppy');?></label></th>
				<td><input class="regular-text" type="text" value="<?php echo esc_attr($name);?>" name="guppy[name]"></td>
			</tr>
			<tr>
				<th><label><?php esc_html_e('Email','wp-guppy');?></label></th>
				<td><input class="regular-text" type="text" value="<?php echo esc_attr($email);?>" name="guppy[email]"></td>
			</tr>
			<tr>
				<th><label><?php esc_html_e('Phone','wp-guppy');?></label></th>
				<td><input class="regular-text" type="text" value="<?php echo esc_attr($phone);?>" name="guppy[phone]"></td>
			</tr>
		</table>
	  <?php
	}
	add_action( "user_new_form", "wpguppy_custom_user_profile_fields" );
	add_action( 'show_user_profile', 'wpguppy_custom_user_profile_fields' );
	add_action( 'edit_user_profile', 'wpguppy_custom_user_profile_fields' );
}

/**
 * @Update user information when new user created
 * @type create
 */
if (!function_exists('wpguppy_create_wp_user')) {
	add_action( 'user_register', 'wpguppy_create_wp_user',10,1 );
    function wpguppy_create_wp_user($user_id) {
		$settings	= !empty($_POST['guppy']) ? $_POST['guppy'] : array();
		if( !empty( $settings['name'] ) || !empty( $settings['email'] ) || !empty( $settings['phone'] ) ) {
			$name 	= !empty($settings['name']) ? $settings['name'] : '';
			$phone 	= !empty($settings['phone']) ? $settings['phone'] : '';
			$email 	= !empty($settings['email']) ? $settings['email'] : '';
			do_action('wpguppy_update_user_information',$name,$phone,$email,$user_id, '');

			//update user meta
			update_user_meta($user_id,'wpguppy_user_settings',$settings);
		}

		//Add guppy admins with new user chat
		$users = get_users(array(
			'meta_key'     => 'is_guppy_admin',
			'meta_value'   => 1,
			'meta_compare' => '=',
			'fields'	   => 'ID'
		));

		if(!empty($users)){
			foreach($users as $key => $senderID){
				do_action('wpguppy_send_message_to_user',$senderID,$user_id,'');
			}
		}
	}
}

/**
 * @Add new column in user listing wp
 * @type create
 */
if (!function_exists('wpguppy_add_wp_user_column')) {
	function wpguppy_add_wp_user_column( $column ) {
		$column['is_guppy_admin'] = esc_html__('WP Guppy admin','wp-guppy');
		return $column;
	}
	add_filter( 'manage_users_columns', 'wpguppy_add_wp_user_column' );
}

/**
 * @Display admin column value
 * @type create
 */
if (!function_exists('wpguppy_display_admin_column_data')) {
	function wpguppy_display_admin_column_data( $val, $column_name, $user_id ) {
		switch ($column_name) {
			case 'is_guppy_admin' :
				$is_admin	= get_user_meta( $user_id, 'is_guppy_admin', true );
				$icon 		= 'dashicons-no-alt';
				if(!empty($is_admin)){
					$icon 		= 'dashicons-yes';
					$checked 	= 'checked';
				}
				ob_start();
				?>
				<span class="wpguppy-is-admin guppy-<?php echo esc_attr($icon);?>" data-id="<?php echo esc_attr($user_id);?>">
					<input type="checkbox" <?php checked( $is_admin, 1 ); ?> name="is_guppy_admin">
					<code><i class="dashicons <?php echo esc_attr($icon);?>"></i></code> 
				</span>
				<?php
				return ob_get_clean();

			default:
		}
		return $val;
	}
	add_filter( 'manage_users_custom_column', 'wpguppy_display_admin_column_data', 10, 3 );
}

/**
 * @Query filter for Dokan plugin compatibility
 * @type add
 */
if(!function_exists('wpguppy_load_document_menu')){
	add_filter( 'dokan_query_var_filter', 'wpguppy_load_document_menu' );
	function wpguppy_load_document_menu( $query_vars ) {
		$query_vars['guppychat'] = 'guppychat';
		return $query_vars;
	}
}

/**
 * @Add menu for Dokan plugin compatibility
 * @type add
 */
if(!function_exists('wpguppy_dukan_dashboard_menu')){
	add_filter('dokan_get_dashboard_nav', 'wpguppy_dukan_dashboard_menu');
	function wpguppy_dukan_dashboard_menu($url){
		$url['guppychat'] = array(
			'title'      => esc_html__( 'Inbox', 'wp-guppy' ),
			'icon'       => '<i class="guppy-message-square"></i>',
			'url'        => dokan_get_navigation_url( 'guppychat' ),
			'pos'        => 70,
		);
		return $url;
	}
}

/**
 * @load content template for Dokan plugin compatibility
 * @type load
 */
if(!function_exists('wpguppy_load_dokan_template')){
	add_action( 'dokan_load_custom_template', 'wpguppy_load_dokan_template' );
	function wpguppy_load_dokan_template( $query_vars ) {
		if ( isset( $query_vars['guppychat'] ) ) {
			require_once(WPGuppy_GlobalSettings::get_plugin_path().'includes/dokan-guppy-chat.php');
		}
	}
}

/**
 * @Add RTL support
 * @type load
 */
if(!function_exists('wpguppy_add_rtl_support')){
	function wpguppy_add_rtl_support( $classes ) {
		if ( is_rtl() ) {
			$classes[] = 'wpguppy-rtl';
		}
		
		return $classes;
	}
	add_filter( 'body_class','wpguppy_add_rtl_support' );
}

