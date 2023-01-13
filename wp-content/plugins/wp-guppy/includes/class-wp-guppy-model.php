<?php
if (!class_exists('WPGuppy_Model')) {
    /**
     * Database operations Module
     * 
     * @package WP Guppy
    */

	/**
	 * Register all database operations & fucntions
	 *
	 * @link       wp-guppy.com
	 * @since      1.0.0
	 *
	 * @package    wp-guppy
	 * @subpackage wp-guppy/includes
	 */

	/**
	 * Register all actions and filters for the plugin.
	 *
	 * Maintain a list of all hooks that are registered throughout
	 * the plugin, and register them with the WordPress API. Call the
	 * run function to execute the list of actions and filters.
	 *
	 * @package    wp-guppy
	 * @subpackage wp-guppy/includes
	 * @author     wp-guppy <wpguppy@gmail.com>
	 */

	class WPGuppy_Model{


		/**
         * Initialize Singleton
         *
         * @var [void]
         */

        private static $_instance = null;
		
		
		/**
         * Call this method to get singleton
         *
         * @return wp-guppy Instance
         */
        public static function instance(){
            if (self::$_instance === null) {
                self::$_instance = new WPGuppy_Model();
            }
            return self::$_instance;
        }	

		/**
		 * Get guppy users 
		 *
		 * @param string $limit
		 * @param string $offset
		 * @param string $searchQuery
		 * @return void
		*/
		public function getGuppyFriend($userId = 0, $loginedUser = 0, $is_exclude = false){
			global $wpdb;
			$guppyFriends = $wpdb->prefix . 'wpguppy_friend_list';
			
			if($is_exclude){
				$where = " ($guppyFriends.send_by= $loginedUser OR $guppyFriends.send_to= $loginedUser)"; 
				$where .= " AND	($guppyFriends.friend_status= 0 OR $guppyFriends.friend_status= 1 OR  $guppyFriends.friend_status= 3) ";
			}else{
				$where = " ($guppyFriends.send_by= $loginedUser AND $guppyFriends.send_to= $userId)"; 
				$where .= " OR	($guppyFriends.send_by= $userId AND $guppyFriends.send_to= $loginedUser)";
			}

			$query = "SELECT $guppyFriends.*
			FROM $guppyFriends

			WHERE $where";
			if($is_exclude){
				$fetchResults = $wpdb->get_results( $query, ARRAY_A );
			}else{
				$fetchResults = $wpdb->get_row( $query, ARRAY_A );
			}
			return $fetchResults;
		}
		
		/**
		 * Get guppy friends 
		 *
		 * @param string $limit
		 * @param string $offset
		 * @param string $searchQuery
		 * @return void
		*/
		public function getGuppyContactList($limit, $offset, $searchQuery, $loginedUser, $friendStatus){
			global $wpdb;
			$userTable = $wpdb->prefix . 'users';
			$guppyFriends = $wpdb->prefix . 'wpguppy_friend_list';
			
			$searchFriend = '';
			if(!empty($searchQuery)){
				$searchFriend =" AND $userTable.display_name LIKE '%$searchQuery%'"; 	
			}
			if($friendStatus=='1'){
				$query = "SELECT * FROM (
						SELECT $userTable.display_name as userName, $guppyFriends.send_by, $guppyFriends.send_to 
						FROM $guppyFriends 
						INNER JOIN $userTable ON $guppyFriends.send_to = $userTable.ID 
						WHERE $guppyFriends.friend_status = '1'
						AND $guppyFriends.send_by = $loginedUser $searchFriend
					UNION ALL
						SELECT $userTable.display_name as userName, $guppyFriends.send_by, $guppyFriends.send_to 
						FROM $guppyFriends 
						INNER JOIN $userTable ON $guppyFriends.send_by = $userTable.ID 
						WHERE $guppyFriends.friend_status = '1'
						AND $guppyFriends.send_to = $loginedUser $searchFriend
				)as t ORDER BY t.userName  ASC  LIMIT $offset, $limit";
			}else{
				$query = "SELECT * FROM (
					SELECT $userTable.display_name as userName, $guppyFriends.send_by, $guppyFriends.send_to 
					FROM $guppyFriends 
					INNER JOIN $userTable ON $guppyFriends.send_to = $userTable.ID 
					WHERE $guppyFriends.friend_status = '3'
					AND $guppyFriends.send_to = $loginedUser $searchFriend
				)as t ORDER BY t.userName  ASC  LIMIT $offset, $limit";
			}

			$fetchResults = $wpdb->get_results( $query, ARRAY_A );
			return $fetchResults;
		}

		/**
		 * Get guppy friend requests
		 *
		 * @param string $limit
		 * @param string $offset
		 * @param string $searchQuery
		 * @return void
		*/
		public function getGuppyFriendRequests($limit, $offset, $searchQuery, $loginedUser){
			global $wpdb;
			$userTable = $wpdb->prefix . 'users';
			$guppyFriends = $wpdb->prefix . 'wpguppy_friend_list';
			
			$searchRequests = '';
			if(!empty($searchQuery)){
				$searchRequests =" AND $userTable.display_name LIKE '%$searchQuery%'"; 	
			}
			
			$query = "SELECT * FROM (
				SELECT $userTable.display_name as userName, $guppyFriends.send_by
				FROM $guppyFriends 
				INNER JOIN $userTable ON $guppyFriends.send_to = $userTable.ID 
				WHERE $guppyFriends.friend_status = '0'
				AND $guppyFriends.send_to = $loginedUser $searchRequests
			)as t ORDER BY t.userName  ASC  LIMIT $offset, $limit";
			$fetchResults = $wpdb->get_results( $query, ARRAY_A );
			return $fetchResults;
		}

		/**
		 * Get User Information
		 *
		 * @param string $type
		 * @param string $send_by
		 * @return void
		*/
		public  function getUserInfoData($type = '', $userID = '', $sizes = array()){
			$info = '';
			switch ($type) {
				case "avatar":
					$info = get_avatar_url($userID, $sizes);	
				break;

				case "useremail":
					$user_data 	= get_userdata($userID);
					if(!empty($user_data)){
						$info 	= $user_data->user_email;
					}
				break;

				case "username":
					$user_data 	= get_userdata($userID);
					if(!empty($user_data)){
						$info 	= $user_data->display_name;
					}
				break;

				case "url":
					$info = get_author_posts_url($userID);
				break;
			}
			return $info;
		}

		/**
		 * Get  table Information
		 *
		 * @param string $table_name
		 * @param array $where
		 * @param string $order_col
		 * @param string $order_by
		 * @return 
		*/
		public function getData($fields='*', $table_name='', $where='', $order_col=false, $order_by=false, $group_by=false){
			global $wpdb;
			$table_name = $wpdb->prefix .$table_name;
			$query = "SELECT $fields FROM $table_name WHERE $where ";
			if($group_by){
				$query .=" GROUP BY $group_by";
			}
			if($order_col){
				$query .=" ORDER BY $order_col  $order_by";
			}
			$fetchResults  = $wpdb->get_results($query, ARRAY_A);
			return $fetchResults;
		}

		/**
		 * Insert table data
		 *
		 * @param string $table_name
		 * @param array $data
		 * @return 
		*/
		public function insertData( $table_name = '', $data= array(), $lastId = true, $batchInsert = false){
			global $wpdb;
			$table_name = $wpdb->prefix.$table_name;
			if($batchInsert){
				$wpdb->insert_multiple($table_name, $data);
			}else{
				$wpdb->insert($table_name, $data);
			}
			if($lastId){
				return $wpdb->insert_id;
			}
		}
		
		/**
		 * update table data
		 *
		 * @param string $table_name
		 * @param array $data
		 * @return 
		*/
		public function updateData($table_name='', $data= array(), $where= array()){
			global $wpdb;
			$table_name = $wpdb->prefix .$table_name;
			$response = $wpdb->update($table_name, $data, $where);

			return $response;
		}

		/**
		 * delete table data
		 *
		 * @param string $table_name
		 * @param array $data
		 * @return 
		*/
		public function deleteData($table_name='',$where= array()){
			global $wpdb;
			$table_name = $wpdb->prefix .$table_name;
			$response = $wpdb->delete($table_name, $where);
			return $response;
		}

		

		/**
		 * Load user messages
		 *
		 * @since    1.0.0
		*/
		public function getUserMessageslist($loginedUser, $limit, $offset, $searchQuery, $chatType){
			global $wpdb;
			$guppyMessages 		= $wpdb->prefix . 'wpguppy_message';
			$guppyGroupMember 	= $wpdb->prefix . 'wpguppy_group_member';
			$guppyGroup 		= $wpdb->prefix . 'wpguppy_group';
			$userTable 			= $wpdb->prefix . 'users';
			$searchMessage = $searchGroup = $jointable = '';
			
			if(!empty($searchQuery) && $chatType == '1'){
				$jointable = "INNER JOIN $userTable ON $userTable.ID = $guppyMessages.sender_id OR $userTable.ID = $guppyMessages.receiver_id";
				$searchMessage =" AND $guppyMessages.chat_type = '1'  AND ($guppyMessages.message LIKE '%$searchQuery%' OR $userTable.display_name LIKE '%$searchQuery%') group by $guppyMessages.id"; 	
			}elseif(!empty($searchQuery) && $chatType == '2'){
				$searchGroup.=" AND ($guppyGroup.group_title LIKE '%$searchQuery%' OR $guppyMessages.message LIKE '%$searchQuery%' )"; 	
			}
			if($chatType == '1'){
				$maxMessageIds = "SELECT $guppyMessages.id FROM $guppyMessages
				$jointable
				WHERE $guppyMessages.id IN ( 
					SELECT MAX(id) AS id 
					FROM ( 
						SELECT id, sender_id AS guppy_sender 
						FROM $guppyMessages 
						WHERE (receiver_id = $loginedUser OR sender_id = $loginedUser) 
						AND $guppyMessages.chat_type = '1'
						AND $guppyMessages.user_type <>'0'
						UNION ALL
						SELECT id, receiver_id AS guppy_sender 
						FROM $guppyMessages 
						WHERE (sender_id = $loginedUser OR receiver_id = $loginedUser) 
						AND $guppyMessages.chat_type = '1'
						AND $guppyMessages.user_type <>'0'

					) t GROUP BY guppy_sender
				)
				$searchMessage 
				ORDER BY id DESC LIMIT $offset, $limit";
			}elseif($chatType == '2'){
				$maxMessageIds = "SELECT id FROM $guppyMessages
				WHERE id IN ( 
					SELECT MAX($guppyMessages.id) as id
					FROM $guppyMessages
					INNER JOIN $guppyGroupMember ON $guppyMessages.group_id = $guppyGroupMember.group_id
					INNER JOIN $guppyGroup 		ON $guppyMessages.group_id 	= $guppyGroup.id
					WHERE $guppyGroupMember.group_status = '1'
					$searchGroup
					AND $guppyGroupMember.member_id = $loginedUser
					AND $guppyMessages.message_sent_time >= $guppyGroupMember.member_added_date
					AND $guppyMessages.chat_type = '2'
					AND $guppyMessages.user_type <>'0'
					group by $guppyMessages.group_id 
				) 
				ORDER BY id DESC LIMIT $offset, $limit";
			}
			$getResult = $wpdb->get_results($maxMessageIds, ARRAY_A);
			
			$fetchResults = $messageIds = array(); 

			if(!empty($getResult)){
				foreach($getResult as $result){
					$messageIds[] = $result['id'];
				}
				$messageIds = implode(',',$messageIds);
			}
			if(!empty($messageIds) && $chatType == '1'){
				$message_query = "SELECT $guppyMessages.*
				FROM $guppyMessages 
				WHERE $guppyMessages.id IN($messageIds)
				GROUP BY $guppyMessages.id
				ORDER BY $guppyMessages.id  DESC";
				$fetchResults  = $wpdb->get_results($message_query, ARRAY_A);
			}elseif(!empty($messageIds) && $chatType == '2'){
				$message_query = "SELECT $guppyMessages.*, $guppyGroup.id as group_id, $guppyGroup.group_title, $guppyGroup.group_image, $guppyGroup.disable_reply 
				FROM $guppyMessages 
				INNER JOIN $guppyGroup 		ON $guppyMessages.group_id 	= $guppyGroup.id
				INNER JOIN $guppyGroupMember ON $guppyGroup.id 	= $guppyGroupMember.group_id AND $guppyGroupMember.group_status = '1'  AND $guppyGroupMember.member_id = $loginedUser
				WHERE $guppyMessages.id IN($messageIds)
				GROUP BY $guppyMessages.id
				ORDER BY $guppyMessages.id  DESC";
				$fetchResults  = $wpdb->get_results($message_query, ARRAY_A);
			}
			return	$fetchResults;
		}

		/**
		 * Load user last messages
		 *
		 * @since    1.0.0
		*/
		public function getUserLatestMessage( $loginedUser, $userId){
			global $wpdb;
			$guppyMessages 		= $wpdb->prefix . 'wpguppy_message';
			$query = "SELECT * FROM $guppyMessages WHERE (( sender_id = $loginedUser OR receiver_id = $loginedUser ) AND ( sender_id = $userId OR receiver_id = $userId )) AND `chat_type`=1 AND `user_type`=1 ORDER BY ID DESC LIMIT 1";
			$fetchResults = $wpdb->get_results($query, ARRAY_A);
			return	$fetchResults;
		}
		/**
		 * Load user post  messages
		 *
		 * @since    1.0.0
		*/
		public function getUserPostMessageslist($loginedUser, $limit, $offset, $searchQuery){
			global $wpdb;
			$guppyMessages 		= $wpdb->prefix . 'wpguppy_message';
			$guppyPosts 		= $wpdb->prefix . 'posts';
			$searchMessage = '';
			if(!empty($searchQuery)){
				$searchMessage =" AND $guppyMessages.chat_type = '0' AND $guppyMessages.message_type = '0' AND post_id IS NOT NULL AND ($guppyMessages.message LIKE '%$searchQuery%' OR $guppyPosts.post_title LIKE '%$searchQuery%')"; 	
			}
			$query = "SELECT $guppyMessages.*, $guppyPosts.post_title FROM $guppyMessages
			INNER JOIN $guppyPosts ON $guppyMessages.post_id = $guppyPosts.ID
			WHERE $guppyMessages.id IN ( 
				SELECT MAX(id) AS id 
				FROM ( 
				
						SELECT id, post_id AS guppy_post, sender_id AS guppy_sender 
						FROM $guppyMessages 
						WHERE (receiver_id = $loginedUser OR sender_id = $loginedUser) 
						AND $guppyMessages.chat_type = '0'
						AND post_id IS NOT NULL 
					UNION ALL
						SELECT id, post_id AS guppy_post, receiver_id AS guppy_sender 
						FROM $guppyMessages 
						WHERE (sender_id = $loginedUser OR receiver_id = $loginedUser) 
						AND $guppyMessages.chat_type = '0'
						AND post_id IS NOT NULL

				) t GROUP BY guppy_post,guppy_sender
					 
			)
			$searchMessage
			ORDER BY id DESC LIMIT $offset, $limit";
			$fetchResults = $wpdb->get_results($query, ARRAY_A);
			return	$fetchResults;
		}

		/**
		 * get guppy group details
		 *
		 * @since    1.0.0
		*/

		public function getGroupDetail($groupId){

			global $wpdb;
			$guppyGroupMember 	= $wpdb->prefix . 'wpguppy_group_member';
			$guppyGroup 		= $wpdb->prefix . 'wpguppy_group';

			$query = "SELECT  $guppyGroup.disable_reply, $guppyGroup.group_title,$guppyGroup.group_image, $guppyGroupMember.member_id, $guppyGroupMember.member_added_date,$guppyGroupMember.group_role,$guppyGroupMember.member_status
			
			FROM $guppyGroupMember

			INNER JOIN $guppyGroup 	ON $guppyGroupMember.group_id = $guppyGroup.id
			
			WHERE  
			$guppyGroupMember.group_id = $groupId
			AND $guppyGroup.id = $groupId
			ORDER BY $guppyGroupMember.member_id  ASC";
			
			$fetchResults = $wpdb->get_results($query, ARRAY_A);
			$groupDetails = array();
			if(!empty($fetchResults)){
				$totalMembers 	= 0;
				$memberAvatars = array();

				foreach($fetchResults as $result){
					
					$where 		 	= "user_id=". $result['member_id']; 
					$userinfo 		= $this->getData('*','wpguppy_users',$where );
					
					if(!empty($userinfo)){
						$info 					= $userinfo[0];
						$userName 				= $info['user_name'];
						if(!empty($info['user_image'])){
							$userImage 			= unserialize($info['user_image']);
							$userAvatar 		= $userImage['attachments'][0]['thumbnail'];
						}
					}else{
						$userAvatar 	= $this->getUserInfoData('avatar',  $result['member_id'], array('width' => 150, 'height' => 150));
						$userName 		= $this->getUserInfoData('username', $result['member_id'], array());
					}
					if($userName != ''){
						$lastname = '';
						$name =	explode(' ' , $userName);
						if(!empty($name[1])){
							$lastname = ' '. ucfirst(substr($name[1], 0, 1));
						}
						$userName = ucfirst($name[0]).$lastname; 
					}
					$memberAvatars[$result['member_id']] = array(
						'userId' 			=> $result['member_id'],
						'groupRole' 		=> $result['group_role'],
						'userName' 			=> $userName,
						'userAvatar' 		=> $userAvatar,
						'memberStatus' 		=> $result['member_status'],
						'memberAddedDate' 	=> $result['member_added_date'],
					);
					if($result['member_status'] == 1){
						$totalMembers++; 
					}
				}

				$groupDetails = array(
					'groupTitle' 	=> $fetchResults[0]['group_title'],
					'groupImage' 	=> $fetchResults[0]['group_image'],
					'disableReply' 	=> $fetchResults[0]['disable_reply'] == '1' ? true : false,
					'totalMembers' 	=> $totalMembers,
					'memberAvatars' => $memberAvatars,
				);
			}
			return $groupDetails;	
		}

		/**
		 * get guppy unread message count
		 *
		 * @since    1.0.0
		*/

		public function getUnreadCount($filterData){

			global $wpdb;
			$guppyMessages 		= $wpdb->prefix . 'wpguppy_message';
			$unSeenCount 		= 0;
			$where 	= "message_status = '0'";
			$where 	.= " AND message_type <> '4'"; 
			if(!empty($filterData['groupId']) && $filterData['chatType'] == '2'){
				
				$where 	.= " AND group_id 	=" 	.$filterData['groupId']; 
				$where 	.= " AND sender_id 	<>" .$filterData['senderId']; 
				$where 	.= " AND chat_type 	='2'"; 
				$where 	.= " AND user_type 	='1'"; 
				$where 	.= " AND message_sent_time >= '".$filterData['memberAddedDate']."'"; 
				if(!empty($filterData['groupAction'])){
					if(!empty($filterData['groupAction']['deleteGroupTime'])){
						$where .=" AND message_sent_time >'".$filterData['groupAction']['deleteGroupTime']."'";
					}elseif(!empty($filterData['groupAction']['exitGroupTime'])){
						$where .=" AND message_sent_time <'".$filterData['groupAction']['exitGroupTime']."'";
					}
					foreach($filterData['groupAction']['status'] as $action){
						$where .=" AND (message_sent_time NOT BETWEEN '".$action['statusActionTime']."'  AND  '".$action['statusUpdatedTime']."')";
					}
				}
			}elseif(!empty($filterData['postId']) && $filterData['chatType'] == '0'){
				$where 	.= " AND sender_id 		=" 	.$filterData['senderId'];
				$where 	.= " AND receiver_id 	=" 	.$filterData['receiverId']; 
				$where 	.= " AND post_id 		=" 	.$filterData['postId']; 
				$where 	.= " AND chat_type 		=	'0'"; 
			}elseif($filterData['chatType'] == '1' && !empty($filterData['senderId']) && !empty($filterData['receiverId'])){
				$where 	.= " AND sender_id 	=" 	.$filterData['senderId'];
				$where 	.= " AND receiver_id =" 	.$filterData['receiverId']; 
				$where 	.= " AND chat_type 	='1'"; 
				$where 	.= " AND user_type 	='1'"; 
			}elseif(!empty($filterData['receiverId']) && in_array($filterData['chatType'],array('0','1'))){
				$where 	.= " AND receiver_id =" .$filterData['receiverId']; 
				$where 	.= " AND chat_type=".$filterData['chatType']; 
				$where 	.= " AND user_type 	='1'"; 
			}
			$query = "SELECT id,sender_id,group_msg_seen_id FROM $guppyMessages WHERE $where"; 
			$fetchResults = $wpdb->get_results($query, ARRAY_A);
			if(!empty($fetchResults)){
				if($filterData['chatType'] == '0' ||  $filterData['chatType'] == '1'){
					$unSeenCount = count($fetchResults);
				}elseif($filterData['chatType'] == '2'){
					foreach($fetchResults as $res){
						if(empty($res['group_msg_seen_id'])){
							$unSeenCount++;
						}else{
							$groupMsgSeenIds 	= unserialize($res['group_msg_seen_id']);
							if(!in_array($filterData['senderId'], $groupMsgSeenIds)){
								$unSeenCount++;
							}
						}
					}
				}
			}
			return $unSeenCount;	
		}
		
		/**
		 * user Group Ids
		 *
		 * @since    1.0.0
		*/
		public function getUserGroups($loginedUser){

			global $wpdb;
			$guppyGroupMember 	= $wpdb->prefix . 'wpguppy_group_member';
			$guppyMessages 		= $wpdb->prefix . 'wpguppy_message';
			
			$query = "SELECT $guppyMessages.group_id,$guppyGroupMember.member_status,$guppyGroupMember.member_added_date
			FROM $guppyMessages
			INNER JOIN $guppyGroupMember ON $guppyMessages.group_id = $guppyGroupMember.group_id
			AND  $guppyGroupMember.member_id = $loginedUser 
			AND $guppyGroupMember.group_status = '1'
			GROUP BY $guppyMessages.group_id";
			$fetchResults = $wpdb->get_results($query, ARRAY_A);
			return $fetchResults;	
		}

		/**
		 * get user chat
		 *
		 * @since    1.0.0
		*/

		public  function getGuppyChat($filterData){
			global $wpdb;
			$guppyMessages 		= $wpdb->prefix . 'wpguppy_message';
			$guppyGroupMember 	= $wpdb->prefix . 'wpguppy_group_member';
			$guppyGroup 		= $wpdb->prefix . 'wpguppy_group';
			$userChat 	= array();
			$selectFields = " $guppyMessages.*";
			
			if(!empty($filterData['groupId'])){
				$selectFields .= ",$guppyGroup.disable_reply";
			}
			$query = "SELECT * FROM (SELECT $selectFields 
			FROM $guppyMessages ";
			if(!empty($filterData['groupId'])){ 
				$query .=" INNER JOIN $guppyGroup 			ON $guppyMessages.group_id 	= $guppyGroup.id "; 
				$query .=" INNER JOIN $guppyGroupMember 	ON $guppyGroup.id 		= $guppyGroupMember.group_id AND $guppyGroupMember.group_status = '1'" ; 
			}
			
			if(!empty($filterData['groupId'])){
				$query .=" WHERE $guppyGroupMember.member_id = ". $filterData['actionBy'];
				$query .=" AND $guppyMessages.group_id= ".$filterData['groupId']; 
				$query .=" AND $guppyMessages.chat_type = '2'";
				$query .=" AND $guppyMessages.user_type = '1'";
				$query .=" AND $guppyMessages.message_sent_time >= $guppyGroupMember.member_added_date";
				if(!empty($filterData['groupAction'])){
					if(!empty($filterData['groupAction']['deleteGroupTime'])){
						$query .=" AND $guppyMessages.message_sent_time >'".$filterData['groupAction']['deleteGroupTime']."'";
					}elseif(!empty($filterData['groupAction']['exitGroupTime'])){
						$query .=" AND $guppyMessages.message_sent_time <'".$filterData['groupAction']['exitGroupTime']."'";
					}
					foreach($filterData['groupAction']['status'] as $action){
						$query .=" AND ($guppyMessages.message_sent_time NOT BETWEEN '".$action['statusActionTime']."'  AND  '".$action['statusUpdatedTime']."')";
					}
				}
			}elseif(!empty($filterData['postId'])){
				$query .=" WHERE (sender_id =". $filterData['actionBy'] ." OR receiver_id = ". $filterData['actionBy'] .")"; 
				$query .=" AND (receiver_id =" .$filterData['userId']. " OR sender_id =". $filterData['userId'].")"; 
				$query .=" AND $guppyMessages.chat_type = '0'";
				$query .=" AND $guppyMessages.post_id = ".$filterData['postId'];
			}else{
				$query .=" WHERE (sender_id = ". $filterData['actionBy'] ." OR receiver_id = ". $filterData['actionBy'] .")"; 
				$query .=" AND (receiver_id =" .$filterData['userId']. " OR sender_id =". $filterData['userId'].")"; 
				$query .=" AND $guppyMessages.chat_type = '1'";
				$query .=" AND $guppyMessages.user_type = '1'";
			}

			if(!empty($filterData['chatClearTime'])){
				$query .=" AND $guppyMessages.message_sent_time > '".$filterData['chatClearTime']."' "; 
			}
			if(!empty($filterData['offset'])){
				$query .=" AND $guppyMessages.id < ".$filterData['offset'].""; 	
				$query .=" ORDER BY $guppyMessages.id  DESC LIMIT " .$filterData['limit']. ') msg ORDER BY id ASC';
			}else{
				$query .=" ORDER BY $guppyMessages.id  DESC LIMIT ". $filterData['offset'].',' .$filterData['limit'].') msg ORDER BY id ASC';
			}
			$fetchResults  = $wpdb->get_results($query, ARRAY_A);
			return $fetchResults;
		}

		/**
		 * get chat Media
		 *
		 * @since    1.0.0
		*/
		public  function getChatMedia($loginedUser, $filterData){
			global $wpdb;
			$guppyMessages 		= $wpdb->prefix . 'wpguppy_message';
			$guppyGroupMember 	= $wpdb->prefix . 'wpguppy_group_member';
			$guppyGroup 		= $wpdb->prefix . 'wpguppy_group';
			$userChat 	= array();
			
			$query = "SELECT $guppyMessages.attachments
			FROM $guppyMessages ";

			if(!empty($filterData['groupId'])){
				$query .="INNER JOIN $guppyGroupMember 	ON $guppyMessages.group_id 	= $guppyGroupMember.group_id";  
			}

			$query .= " WHERE $guppyMessages.message_type = '1'";
			$query .= " AND $guppyMessages.attachments IS NOT NULL";
			
			if(!empty($filterData['groupId'])){
				$query .=" AND $guppyMessages.group_id= ".$filterData['groupId']; 
				$query .=" AND $guppyMessages.chat_type = '2'";
				$query .=" AND $guppyMessages.user_type = '1'";
				$query .=" AND $guppyMessages.message_status <> '2'";
				$query .=" AND $guppyGroupMember.member_id =".$loginedUser;
				$query .=" AND $guppyMessages.message_sent_time >= $guppyGroupMember.member_added_date";
				if(!empty($filterData['groupAction'])){
					if(!empty($filterData['groupAction']['deleteGroupTime'])){
						$query .=" AND $guppyMessages.message_sent_time >'".$filterData['groupAction']['deleteGroupTime']."'";
					}elseif(!empty($filterData['groupAction']['exitGroupTime'])){
						$query .=" AND $guppyMessages.message_sent_time <'".$filterData['groupAction']['exitGroupTime']."'";
					}
					foreach($filterData['groupAction']['status'] as $action){
						$query .=" AND ($guppyMessages.message_sent_time NOT BETWEEN '".$action['statusActionTime']."'  AND  '".$action['statusUpdatedTime']."')";
					}
				}
			}elseif(!empty($filterData['postId'])){
				$query .=" AND (sender_id = $loginedUser OR receiver_id = $loginedUser)"; 
				$query .=" AND (receiver_id =" .$filterData['userId']. " OR sender_id =". $filterData['userId'].")"; 
				$query .=" AND $guppyMessages.chat_type = '0'";
				$query .=" AND $guppyMessages.post_id = ".$filterData['postId'];
				$query .=" AND $guppyMessages.message_status <> '2'";
			}else{
				$query .=" AND (sender_id = $loginedUser OR receiver_id = $loginedUser)"; 
				$query .=" AND (receiver_id =" .$filterData['userId']. " OR sender_id =". $filterData['userId'].")"; 
				$query .=" AND $guppyMessages.chat_type = '1'";
				$query .=" AND $guppyMessages.user_type = '1'";
				$query .=" AND $guppyMessages.message_status <> '2'";
			}
			$query .=" ORDER BY $guppyMessages.id  DESC";
			if(!empty($filterData['limit'])){
				$query .= " LIMIT ". $filterData['offset'].',' .$filterData['limit'];
			}
			$fetchResults  = $wpdb->get_results($query, ARRAY_A);
			return $fetchResults;
			
		}

		/**
		 * upgrade database
		 *
		 * @since    1.0.0
		*/
		public  function upgradeGuppyDB($version){
			global $wpdb;
			$guppyMessages 		= $wpdb->prefix . 'wpguppy_message';
			$guppyGroupMember 	= $wpdb->prefix . 'wpguppy_group_member';
			$guppyGroup 		= $wpdb->prefix . 'wpguppy_group';
			$chatActions 		= $wpdb->prefix . 'wpguppy_chat_action';
			$addColumns 		=	$dropColumns	= array();
			$addColumns[$guppyGroupMember.'***group_role'] = "ALTER TABLE $guppyGroupMember 		ADD  `group_role` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '(1->creator, 2->admin)' AFTER `member_id`, ADD INDEX (`group_role`);";
			$addColumns[$guppyGroupMember.'***group_status'] = "ALTER TABLE $guppyGroupMember 		ADD  `group_status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '(0->deleted, 1->activated, 2->blocked)' AFTER `member_status`, ADD INDEX (`group_status`);";
			$addColumns[$guppyGroupMember.'***member_added_date'] = "ALTER TABLE $guppyGroupMember 	ADD  `member_added_date`  datetime DEFAULT NULL AFTER `group_status`;";
			$addColumns[$guppyMessages.'***group_msg_seen_id'] = "ALTER TABLE $guppyMessages 		ADD  `group_msg_seen_id` varchar(255) DEFAULT NULL COMMENT 'group member message seen ids' AFTER `message_type`;";
			$addColumns[$chatActions.'***action_updated_time'] = "ALTER TABLE $chatActions 			ADD  `action_updated_time`  datetime DEFAULT NULL AFTER `action_time`;";
			$addColumns[$guppyGroup.'***disable_reply'] = "ALTER TABLE $guppyGroup 					ADD  `disable_reply` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '(1->disabled)' AFTER `group_image`, ADD INDEX (`disable_reply`);";
			
			if(!empty($addColumns)){
				foreach($addColumns as $key=>$query){
					$data = explode('***',$key);
					$tableName 		= $data[0]; 
					$columnName 	= $data[1]; 
					$column = $wpdb->get_results( $wpdb->prepare("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",DB_NAME, $tableName, $columnName) );

					if(empty($column)){
						$wpdb->query($query);
					}
				}
			}
			$dropColumns[$guppyGroup.'***group_owner_id'] = "ALTER TABLE $guppyGroup DROP  group_owner_id;";
			$dropColumns[$guppyGroup.'***group_status'] = "ALTER TABLE $guppyGroup DROP  group_status;";
			if(!empty($dropColumns)){
				foreach($dropColumns as $key=> $query){
					$data = explode('***',$key);
					$tableName 		= $data[0]; 
					$columnName 	= $data[1]; 
					$column = $wpdb->get_results( $wpdb->prepare("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",DB_NAME, $tableName, $columnName) );
					if(!empty($column)){
						$wpdb->query($query);
					}
				}
			}
			update_option('wpguppy_version',$version);
		}
		/**
		 * upgrade database
		 *
		 * @since    1.0.0
		*/
		public function createPostActionTable($version){
			global $wpdb;
			$wpguppy_postchat_action 	= $wpdb->prefix . 'wpguppy_postchat_action';
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
				update_option('wpguppy_version',$version); 
			}
		}	
	}
}