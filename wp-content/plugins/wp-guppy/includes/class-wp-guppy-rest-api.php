<?php
global $guppySetting;
if(!empty($guppySetting['rt_chat_settings']) 
	&& $guppySetting['rt_chat_settings'] == 'pusher'
	&& $guppySetting['pusher']=='enable'){
	require_once(WPGuppy_GlobalSettings::get_plugin_path().'libraries/pusher/vendor/autoload.php');
}

require_once(WPGuppy_GlobalSettings::get_plugin_path().'libraries/jwt/vendor/autoload.php');
/** Requiere the JWT library. */
use Firebase\JWT\JWT;

if (!class_exists('WPGuppy_RESTAPI')) {
    /**
     * REST API Module
     * 
     * @package WP Guppy
    */

	/**
	 * Register all rest api routes & function
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

	class WPGuppy_RESTAPI  extends WP_REST_Controller{

		/**
		 * The unique identifier of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
		*/
		private $plugin_name;

		/**
		 * The rest api url
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $restapiurl    rest api ajax url
		 */

		private $restapiurl = 'guppy';


		/**
		 * The restapiversion
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $restapiversion    rest api version
		*/
		private $restapiversion = 'v2';

		/**
		 * The current version of the plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of the plugin.
		 */
		private $version;

		/**
		 * private key for jwt.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $secretKey   secret key for jwt.
		 */
		private $secretKey;

		/**
		 * database object
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of the plugin.
		 */
		private $guppyModel;
		
		/**
		 * Guppy Setting
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of the plugin.
		 */

		private $guppySetting;

		/**
		 * Show Record By Default
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of the plugin.
		 */

		private $showRec;
		/**
		 * private pusher
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of the plugin.
		 */

		private  $pusher;

		/**
		 * Initialize the collections used to maintain the rest api routes.
		 *
		 * @since    1.0.0
		 */
		public function __construct($plugin_name, $version) {

			$this->plugin_name 		= $plugin_name;
			$this->version 			= $version;
			$this->secretKey 		= ',%]txNv^9J~,?-&EH-n;xy),LjN6*Zi/_YXKxTU_SkQ8F[q|du@/4DH*_v4qwJ}A';
			$guppyModel = WPGuppy_Model::instance();
			$this->guppyModel = $guppyModel;

			add_action('wp_enqueue_scripts', array(&$this,'registerGuppyConstant'),90);
			$this->registerRestRoutes();
			global $guppySetting;
			$this->guppySetting = $guppySetting;
			$this->showRec 		= !empty($this->guppySetting['showRec']) ? $this->guppySetting['showRec'] : 20;
			if(!empty($this->guppySetting['rt_chat_settings']) 
				&& $this->guppySetting['rt_chat_settings'] == 'pusher'
				&& $this->guppySetting['pusher']=='enable'){
				$appId 					= $this->guppySetting['option']['app_id'];
				$publicKey 				= $this->guppySetting['option']['app_key'];
				$secretKey 				= $this->guppySetting['option']['app_secret'];
				$appCluster 			= $this->guppySetting['option']['app_cluster'];
				if(!empty($appId) 
				&& !empty($publicKey) 
				&& !empty($secretKey) 
				&& !empty($appCluster)){
					$options = array(
						'useTLS'    => false,
						'cluster'   => $appCluster
					);
					$this->pusher = new Pusher\Pusher($publicKey, $secretKey, $appId, $options);
				}
			}
		}

		/**
		 * Register Guppy Constants
		 *
		 * @since    1.0.0
		*/

		public function registerRestRoutes(){

			add_action('rest_api_init', function() {

				register_rest_route($this->restapiurl.'/'. $this->restapiversion , 'channel-authorize' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'guppyChannelAuthorize'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'. $this->restapiversion , 'load-guppy-users' , array(
					'methods'    			=>  WP_REST_Server::READABLE,
					'callback'   			=> array(&$this, 'getGuppyUsers'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));
				register_rest_route($this->restapiurl.'/'. $this->restapiversion , 'load-guppy-friend-requests' , array(
					'methods'    			=>  WP_REST_Server::READABLE,
					'callback'   			=> array(&$this, 'getGuppyFriendRequests'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'. $this->restapiversion , 'register-guppy-account' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'registerGuppyGuestAccount'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'. $this->restapiversion , 'get-app-guppy-setting' , array(
					'methods'    			=>  WP_REST_Server::READABLE,
					'callback'   			=> array(&$this, 'getGuppySettings'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'. $this->restapiversion , 'user-login' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'userAuth'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'. $this->restapiversion , 'load-profile-info' , array(
					'methods'    			=>  WP_REST_Server::READABLE,
					'callback'   			=> array(&$this, 'getProfileInfo'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'. $this->restapiversion , 'load-unread-count' , array(
					'methods'    			=>  WP_REST_Server::READABLE,
					'callback'   			=> array(&$this, 'getUnreadCount'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'. $this->restapiversion , 'update-profile-info' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'updateProfileInfo'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));
				
				register_rest_route($this->restapiurl.'/'. $this->restapiversion , 'load-guppy-contacts' , array(
					'methods'    			=>  WP_REST_Server::READABLE,
					'callback'   			=> array(&$this, 'getGuppyContactList'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'send-guppy-invite' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'sendGuppyInvite'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));
				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'load-guppy-messages-list' , array(
					'methods'    			=>  WP_REST_Server::READABLE,
					'callback'   			=> array(&$this, 'getUserMessageslist'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'load-guppy-chat' , array(
					'methods'    			=>  WP_REST_Server::READABLE,
					'callback'   			=> array(&$this, 'getGuppyChat'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'get-guppy-group-users' , array(
					'methods'    			=>  WP_REST_Server::READABLE,
					'callback'   			=> array(&$this, 'getGuppyGroupUsers'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'update-guppy-group' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'updateGuppyGroup'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));
				
				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'delete-guppy-group-member' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'deleteGuppyGroupMember'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'delete-guppy-group' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'deleteGuppyGroup'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'leave-guppy-group' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'leaveGuppyGroup'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'send-guppy-message' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'sendMessage'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'delete-guppy-message' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'deleteGuppyMessage'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'update-guppy-message' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'updateGuppyMessage'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'get-guppy-attachments' , array(
					'methods'    			=>  WP_REST_Server::READABLE,
					'callback'   			=> array(&$this, 'loadMediaAttachments'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'download-guppy-attachments' , array(
					'methods'    			=>  WP_REST_Server::READABLE,
					'callback'   			=> array(&$this, 'downloadGuppyAttachments'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'update-user-status' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'updateGuppyUserStatus'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'update-post-user-status' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'updateGuppyPostUserStatus'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'clear-guppy-chat' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'clearGuppyChat'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'report-guppy-chat' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'reportGuppyChat'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));
				
				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'mute-guppy-notifications' , array(
					'methods'    			=>  WP_REST_Server::CREATABLE,
					'callback'   			=> array(&$this, 'muteGuppyNotification'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'load-guppy-post-messages-list' , array(
					'methods'    			=>  WP_REST_Server::READABLE,
					'callback'   			=> array(&$this, 'getUserPostMessageslist'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'get-post-info' , array(
					'methods'    			=>  WP_REST_Server::READABLE,
					'callback'   			=> array(&$this, 'getPostInfo'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

				register_rest_route($this->restapiurl.'/'.$this->restapiversion , 'get-messanger-chat-info' , array(
					'methods'    			=>  WP_REST_Server::READABLE,
					'callback'   			=> array(&$this, 'messangerChatInfo'),
					'args' 					=> array(),
					'permission_callback' 	=> '__return_true', 
				));

			});
		}

		/**
		 * Register Guppy Constants
		 *
		 * @since    1.0.0
		*/
		public function registerGuppyConstant(){
			
			$loginedUser 	= '0';
			$postId 		= 0;
			if(is_user_logged_in()){
				global $current_user, $post;
				$loginedUser 	= $current_user->ID;
				$postId 		= !empty($post->ID) ? $post->ID : 0;
			}
			$settings  	= $this->getGuppySettings();
			$token  	= $this->getGuppyAuthToken($loginedUser);
			$authToken	= $token['authToken'];
			wp_localize_script($this->plugin_name, 'wpguppy_scripts_vars', array(
				'restapiurl'    		=> get_rest_url( null, $this->restapiurl.'/'.$this->restapiversion.'/') ,
				'rest_nonce'			=> wp_create_nonce('wp_rest'),
				'showRec'				=> $this->showRec, 
				'maxFileUploads'		=> ini_get("max_file_uploads"),
				'userId'				=> $loginedUser,
				'postId'				=> $postId,
				'logoutUrl' 			=> esc_url(wp_logout_url(home_url('/'))), 
				'isSingle'				=> $settings['isSingle'],
				'friendListStatusText'	=> $settings['textSetting'],
				'chatSetting' 			=> $settings['chatSetting'],
				'authToken' 			=> $authToken,
			));
		}

		/**
		 * Get guppy auth token
		 *
		 * @since    1.0.0
		*/
		public function getGuppyAuthToken($loginedUser , $ismobApp = false){
			$jwt 		= array();
			$issuedAt 	= time();
			$notBefore 	= $issuedAt + 10;
			$expire 	= $issuedAt + (DAY_IN_SECONDS * 1);
			if($ismobApp){
				$expire 	= $issuedAt + (DAY_IN_SECONDS * 60);
			}
			$token = array(
				'iss' => get_bloginfo('url'),
				'iat' => $issuedAt,
				'nbf' => $notBefore,
				'exp' => $expire,
				'data' => array(
					'user' => array(
						'id' => $loginedUser,
					),
				),
			);
			$authToken = JWT::encode($token, $this->secretKey , 'HS256');
			$jwt['authToken'] 		= $authToken;
			return $jwt;
		}

		/**
		 * Get guppy settings
		 *
		 * @since    1.0.0
		*/
		public function getGuppySettings($data = false){
			$settings 		= $json = array();
			$loginedUser 	= 0;
			$isSingle 		= false;
			$postType 		= false;
			$postAuthor 	= false;
			if(is_user_logged_in()){
				global $current_user, $post;
				$loginedUser = $current_user->ID;
				$postId 		= !empty($post->ID) ? $post->ID : 0;
				if($postId > 0){
					$postType 		= get_post_type($postId);
					$postAuthor		= get_post_field('post_author', $postId, true);
				}
			}
			if($data){
				$headers    		= $data->get_headers();
				$params     		= !empty($data->get_params()) 	? $data->get_params() 	: '';
				$loginedUser 		= !empty($params['userId']) ? intval($params['userId']) 	: 0; 
			}
			$default_bell_url    	= WPGuppy_GlobalSettings::get_plugin_url().'public/media/notification-bell.wav';
			$messangerPageId 		= !empty($this->guppySetting['messanger_page_id']) ? $this->guppySetting['messanger_page_id'] 	: 0;
			$notificationBellUrl 	= !empty($this->guppySetting['notification_bell_url']) ? $this->guppySetting['notification_bell_url'] 	: $default_bell_url;
			$primaryColor 			= !empty($this->guppySetting['primary_color']) 		? $this->guppySetting['primary_color'] 		: '#FF7300';
			$secondaryColor 		= !empty($this->guppySetting['secondary_color']) 	? $this->guppySetting['secondary_color'] 	: '#0A0F26';
			$textColor 				= !empty($this->guppySetting['text_color']) 		? $this->guppySetting['text_color'] 		: '#000000';
			$maxImageSize 			= !empty($this->guppySetting['image_size']) 		? $this->guppySetting['image_size'] 		: '5000';
			$maxFileSize 			= !empty($this->guppySetting['file_size']) 			? $this->guppySetting['file_size'] 			: '5000';
			$maxAudioSize 			= !empty($this->guppySetting['audio_size']) 		? $this->guppySetting['audio_size'] 		: '5000';
			$maxVideoSize 			= !empty($this->guppySetting['video_size']) 		? $this->guppySetting['video_size'] 		: '5000';
			$imgExt 				= !empty($this->guppySetting['allow_img_ext']) 		? $this->guppySetting['allow_img_ext'] 		: array();
			$audioExt 				= !empty($this->guppySetting['allow_audio_ext']) 	? $this->guppySetting['allow_audio_ext'] 	: array();
			$videoExt 				= !empty($this->guppySetting['allow_video_ext']) 	? $this->guppySetting['allow_video_ext'] 	: array();
			$enabledTabs 			= !empty($this->guppySetting['enabled_tabs']) 		? $this->guppySetting['enabled_tabs'] 		: array();
			$defaultActiveTab 		= !empty($this->guppySetting['default_active_tab']) ? $this->guppySetting['default_active_tab'] : 'contacts';
			$fileExt 				= !empty($this->guppySetting['allow_file_ext']) 	? $this->guppySetting['allow_file_ext'] 	: array();
			$shareLocation 			= !empty($this->guppySetting['location_sharing']) 	&& $this->guppySetting['location_sharing'] == 'enable' 	? true 	: false;
			$shareEmoji 			= !empty($this->guppySetting['emoji_sharing']) 		&& $this->guppySetting['emoji_sharing'] == 'enable' 	? true 	: false;
			$shareVoiceNote 		= !empty($this->guppySetting['voicenote_sharing']) 	&& $this->guppySetting['voicenote_sharing'] == 'enable' 	? true 	: false;
			$realTimeOption 		= !empty($this->guppySetting['rt_chat_settings'])  	? $this->guppySetting['rt_chat_settings'] 	: '';
			$pusherEnable 			= !empty($this->guppySetting['pusher'])  			&& $this->guppySetting['pusher'] == 'enable' 	? true 	: false;
			$socketEnable 			= !empty($this->guppySetting['socket'])  			&& $this->guppySetting['socket'] == 'enable' 	? true 	: false;
			$groupChatEnable 		= !empty($this->guppySetting['group_chat'])  		&& $this->guppySetting['group_chat'] == 'disable' 	? false 	: true;
			$floatingWindowEnable 	= !empty($this->guppySetting['floating_window'])  	&& $this->guppySetting['floating_window'] == 'disable' 	? false 	: true;
			$appCluster 			= !empty($this->guppySetting['option']['app_cluster']) 		? $this->guppySetting['option']['app_cluster'] 	: '';
			$appKey 				= !empty($this->guppySetting['option']['app_key']) 			? $this->guppySetting['option']['app_key'] 	: '';
			$socketHost 			= !empty($this->guppySetting['option']['socket_host_url']) 	? $this->guppySetting['option']['socket_host_url'] 	: '';
			$socketPort 			= !empty($this->guppySetting['option']['socket_port_id']) 	? $this->guppySetting['option']['socket_port_id'] 	: '';
			$translations 			= !empty($this->guppySetting['translations']) 				? $this->guppySetting['translations'] 		: array();
			$reportingReasons 		= !empty($this->guppySetting['reporting_reasons']) 			? $this->guppySetting['reporting_reasons'] 	: apply_filters('wpguppy_reporting_reasons','');
			$floatingIcon 			= !empty($this->guppySetting['dock_layout_image']) 			? $this->guppySetting['dock_layout_image'] 	: '';
			$deleteMessageOption 	= !empty($this->guppySetting['delete_message']) 	&& $this->guppySetting['delete_message'] == 'disable' 	? false : true;
			$clearChatOption 		= !empty($this->guppySetting['clear_chat']) 		&& $this->guppySetting['clear_chat'] == 'disable' 	? false 	: true;
			$reportUserOption 		= !empty($this->guppySetting['report_user']) 		&& $this->guppySetting['report_user'] == 'disable' 	? false 	: true;
			$hideAccSettings 		= !empty($this->guppySetting['hide_acc_settings']) 	&& $this->guppySetting['hide_acc_settings'] == 'yes' ? true 	: false;
			$default_translations 	= wp_list_pluck(apply_filters( 'wpguppy_default_text','' ),'default');
			$createGroup 	= $autoInvite 	=	false;
			$userMeta  		= get_userdata($loginedUser);
			$userRoles 		= !empty($userMeta) ? $userMeta->roles : array();
			if(!empty($userRoles)){
				foreach($userRoles as $role){
					if(!empty($this->guppySetting['post_type']) 
						&& !empty($this->guppySetting['post_type'][$role])
						&& is_singular($postType)
						&& in_array($postType, $this->guppySetting['post_type'][$role])
						&& intval($postAuthor) != intval($loginedUser)
					){
						$isSingle = true;
						break;
					}
				}
			}
			$roles =  $this->getUserRoles($loginedUser);
			if(!empty($roles) && $roles['createGroup']){
				$createGroup = true;
			} 
			if(!empty($roles) && $roles['autoInvite']){
				$autoInvite = true;
			}
			foreach($default_translations as $key=> &$value){
				if(!empty($translations[$key])){
					$default_translations[$key] = $translations[$key];
				}
			}
			
			
			$chatSetting 	= array(
				'notificationBellUrl'	=> $notificationBellUrl,
				'reportingReasons'		=> $reportingReasons,
				'translations'			=> $default_translations,
				'defaultActiveTab'		=> $defaultActiveTab,
				'enabledTabs'			=> $enabledTabs,
				'primaryColor' 			=> $primaryColor,	
				'secondaryColor' 		=> $secondaryColor,	
				'textColor' 			=> $textColor,	
				'createGroup' 			=> $createGroup,	
				'autoInvite' 			=> $autoInvite,	
				'maxImageSize' 			=> $maxImageSize,	
				'maxAudioSize' 			=> $maxAudioSize,	
				'maxVideoSize' 			=> $maxVideoSize,	
				'maxFileSize' 			=> $maxFileSize,	
				'imgExt' 				=> $imgExt,	
				'audioExt' 				=> $audioExt,	
				'videoExt' 				=> $videoExt,	
				'fileExt' 				=> $fileExt,	
				'shareLocation' 		=> $shareLocation,	
				'shareEmoji' 			=> $shareEmoji,
				'shareVoiceNote' 		=> $shareVoiceNote,
				'realTimeOption'		=> $realTimeOption,	
				'pusherEnable'			=> $pusherEnable,	
				'socketEnable'			=> $socketEnable,	
				'groupChatEnable'		=> $groupChatEnable,	
				'floatingWindowEnable'	=> $floatingWindowEnable,	
				'pusherKey'				=> $appKey,	
				'pusherCluster'			=> $appCluster,	
				'socketHost'			=> $socketHost,	
				'socketPort'			=> $socketPort,	
				'isRtl'					=> is_rtl(),
				'typingIcon'			=> WPGuppy_GlobalSettings::get_plugin_url().'public/images/typing.gif',
				'videoThumbnail'		=> WPGuppy_GlobalSettings::get_plugin_url().'public/images/video-thumbnail.jpg',
				'floatingIcon'			=> !empty($floatingIcon) ? $floatingIcon : WPGuppy_GlobalSettings::get_plugin_url().'public/images/floating-logo.gif',
				'messangerPage'			=> get_the_permalink($messangerPageId),
				'deleteMessageOption'	=> $deleteMessageOption,
				'clearChatOption'		=> $clearChatOption,
				'reportUserOption'		=> $reportUserOption,
				'hideAccSettings'		=> $hideAccSettings,
			);
			
			$textSetting	= array( 
				'sent' 				=> esc_html__( $default_translations['sent'], 'wp-guppy'), 
				'invite' 			=> esc_html__($default_translations['invite'], 'wp-guppy'),
				'blocked' 			=> esc_html__($default_translations['blocked'], 'wp-guppy'),
				'respond' 			=> esc_html__($default_translations['respond_invite'], 'wp-guppy'),
				'resend' 			=> esc_html__($default_translations['resend_invite'], 'wp-guppy'),
			);

			$settings['textSetting'] 	= $textSetting;
			$settings['chatSetting'] 	= $chatSetting;
			$settings['isSingle'] 		= $isSingle;
			if($data){
				$json['settings']	= $settings;	
				return new WP_REST_Response($json, 200);
			}else{
				return $settings;
			}
		}

		/**
		 * Get guppy user Roles
		 *
		 * @since    1.0.0
		*/
		public function getUserRoles($userId) {
			$roles = array(
				'userRoles' 	=> array(),
				'autoInvite' 	=> false,
				'createGroup'	=> false
			);
			$userRoles = array();
			if(!empty($userId)){
				$user_meta  		= get_userdata($userId);
				$user_roles 		= $user_meta->roles;
				$autoInvitesRoles 	= !empty($this->guppySetting['auto_invite']) ? $this->guppySetting['auto_invite'] : array();
				$allowGroup 		= !empty($this->guppySetting['create_group']) ? $this->guppySetting['create_group'] : array();
				$autoInvite 		= false;
				$createGroup		= false;
				if(!empty($user_roles)){
					foreach($user_roles as $single){
						$allroles =  !empty($this->guppySetting['user_role'][$single]) ? $this->guppySetting['user_role'][$single] : array();
						if($allroles){
							foreach($allroles as $role){
								if(!in_array($role, $userRoles)){
									$userRoles[] = $role;
								}
							}
						}
						if(in_array($single, $autoInvitesRoles)){
							$autoInvite = true;
						}
						if(!empty($allowGroup[$single]) && $allowGroup[$single] == 'yes'){
							$createGroup = true;
						}
					}
					$roles['userRoles'] 	= $userRoles;
					$roles['autoInvite'] 	= $autoInvite;
					$roles['createGroup'] 	= $createGroup;
				}
			}
			return $roles;
		}

		/**
         * Login user for guppy mobile application
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Request
		*/
		public function userAuth($data) {
			
			$headers    	= $data->get_headers();
			$params     	= !empty($data->get_params()) 		? $data->get_params() 		: '';
			$json 			= $userInfo = array();
			$username		= !empty( $params['username'] ) 		? $params['username'] : '';
			$userpassword	= !empty( $params['userpassword'] ) 	?  $params['userpassword']  : '';
			$isMobApp		= !empty( $params['isMobApp'] ) 		?  intval($params['isMobApp'])  : 0;
            if (!empty($username) && !empty($userpassword)) {
                
				$creds = array(
                    'user_login' 			=> $username,
                    'user_password' 		=> $userpassword,
                    'remember' 				=> true
                );
                
                $user = wp_signon($creds, false);
				
				if (is_wp_error($user)) {
                    $json['type']		= 'error';
                    $json['message']	= esc_html__('user name or password is not correct', 'wp-guppy');
					return new WP_REST_Response($json, 203);
                } else {
					
					unset($user->allcaps);
					unset($user->filter);

					$where 		 = "user_id=".$user->data->ID; 
					$fetchResults = $this->guppyModel->getData('*','wpguppy_users',$where );
					
					if(!empty($fetchResults)){
						$info 					= $fetchResults[0];
						$userInfo['userId'] 	= $user->data->ID;
						$userInfo['userName'] 	= $info['user_name'];
						$userInfo['userEmail'] 	= $info['user_email'];
						$userInfo['userPhone'] 	= $info['user_phone'];
					}else{
						$userInfo['userId'] 	= $user->data->ID;
						$userInfo['userName'] 	= $this->guppyModel->getUserInfoData('username', $user->data->ID , array());
						$userInfo['phoneNo'] 	= $this->guppyModel->getUserInfoData('userphone', $user->data->ID, array());
						$userInfo['email'] 		= $this->guppyModel->getUserInfoData('useremail', $user->data->ID, array());
					}
					$token 					= $this->getGuppyAuthToken($user->data->ID, true);
					update_user_meta($user->data->ID, 'wpguppy_app_auth_token', $token['authToken']);
					$json['type']			= 'success';
					$json['message'] 		= esc_html__('You are logged in', 'wp-guppy');
					$json['userInfo'] 		= $userInfo;
					$json['authToken'] 		= $token['authToken'];
					$json['refreshToken'] 	= $token['refreshToken'];
					
					return new WP_REST_Response($json, 200);
                }                
            }else{
				$json['type']		= 'error';
				$json['message']	= esc_html__('user name and password are required fields.', 'wp-guppy');
				return new WP_REST_Response($json, 203);
			}
        }

		/**
         * Authorize Pusher Guppy Channel
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Request
		*/
		public function guppyChannelAuthorize($data){
			$headers    	= $data->get_headers();
			$params     	= !empty($data->get_params()) 		? $data->get_params() 		: '';
			$socketId		= ! empty( $params['socket_id'] ) 		? $params['socket_id'] : 0;
			$channelName	= ! empty( $params['channel_name'] ) 	?  $params['channel_name']  : '';
			if($this->pusher){
				wp_send_json(json_decode( $this->pusher->socket_auth($channelName,$socketId)));
			}
		}

		/**
         * Guppy Authentication
         *
         * @param WP_REST_Request $request Full data about the request.
         * @return WP_Error|WP_REST_Request
		*/

		public function guppyAuthentication($params , $authtoken){
			
			$json 		= array();
			$type 		= 'success';
			$message 	= '';
			if(empty($params['userId']) 
				|| empty(get_userdata($params['userId'])) ){
				$message   	= esc_html__('You are not allowed to perform this action!', 'wp-guppy');
				$type 		= 'error';	
			}else{
				list($token) = sscanf($authtoken, 'Bearer %s');
				if(!$token){
					$message   	= esc_html__('Authorization Token does not found!', 'wp-guppy');
					$type 		= 'error';
				}else{
					try {
						JWT::$leeway = 60;
						$token 	= JWT::decode($token, $this->secretKey, array('HS256'));
						$now 	= time();
						if ($token->iss != get_bloginfo('url') 
							|| !isset($token->data->user->id)
							|| $token->data->user->id != $params['userId']
							|| $token->exp < $now) {
							$message   	= esc_html__('You are not allowed to perform this action!', 'wp-guppy');
							$type 		= 'error';
							
						}
						
					}catch(Exception $e){
						$message   	= $e->getMessage();
						$type 		= 'error';
						
						if($e->getMessage() == 'Expired token' && $params['isMobApp'] == '1'){
							$userToken = get_user_meta($params['userId'], 'wpguppy_app_auth_token', true);
							if($userToken == $token){
								$token 		= $this->getGuppyAuthToken($params['userId'], true);
								update_user_meta($params['userId'], 'wpguppy_app_auth_token', $token['authToken']);
								$json['authToken']	= $token['authToken'];
							}
						}
					}
				}
			}
			$json['type'] 			= $type;
			$json['message_desc']   = $message; 
			return $json;
		}

		/**
		 * Get timeFormat
		 *
		 * @since    1.0.0
		*/
		public function getTimeFormat($time){
			$time_offset 	= (float) get_option( 'gmt_offset' );
			$seconds 		= intval( $time_offset * HOUR_IN_SECONDS );
			$timestamp 		= strtotime( $time ) + $seconds;
			$timeFormat 	= !empty($time) ?  human_time_diff( $timestamp,current_time( 'timestamp' ) ).' '.esc_html__('ago','wp-guppy') : '';
			return $timeFormat;
		}
			
		/**
		 * send guppy invite
		 *
		 * @since    1.0.0
		*/
		public function sendGuppyInvite( $data ) {
			
			$headers    	= $data->get_headers();
			$params     	= !empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  	= ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$json       	=  $friendData = array();
			
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}

			$loginedUser 	= !empty($params['userId']) ? intval($params['userId']) 			: 0; 
			$sendTo			= !empty( $params['sendTo'] ) ? intval($params['sendTo']) 			: 0;
			$startChat		= !empty( $params['startChat'] ) ? intval($params['startChat']) 	: 0;
			$autoInvite 	=	false;
			if($startChat == 1){
				$roles 	= $this->getUserRoles($loginedUser);
				if(!empty($roles) && $roles['autoInvite']){
					$autoInvite = true;
				}
			}
			$fetchResults 	= $this->guppyModel->getGuppyFriend($sendTo,$loginedUser,false);
			$response 		= false;
			if( empty(get_userdata($sendTo)) || $loginedUser == $sendTo){
				$json['type'] = 'error';
				$json['message_desc']   = esc_html__('You are not allowed to perform this action!', 'wp-guppy');
				return new WP_REST_Response($json , 203);
			} elseif(!empty( $fetchResults) && ($fetchResults['friend_status'] == '1' || $fetchResults['friend_status'] == '3')){
				$messageData 	= $messagelistData = array();
				// get receiver user info 
				$receiverUserName 	= $receiverUserAvatar = '';
				$userData 			= $this->getUserInfo('1', $sendTo);
				if(!empty($userData)){
					$receiverUserAvatar 	= $userData['userAvatar'];
					$receiverUserName 		= $userData['userName'];
				}
				$isOnline 			= wpguppy_UserOnline($sendTo);
				$userStatus = $this->getUserStatus($loginedUser, $sendTo, '1');
				$friendData = array(
					'userId' 		=> $sendTo,
					'chatType' 		=> 1,
					'chatId' 		=> $this->getChatKey('1',$sendTo),
					'friendStatus' 	=> $fetchResults['friend_status'],
					'userName' 	   	=> $receiverUserName,
					'userAvatar' 	=> $receiverUserAvatar,
					'blockedId' 	=> !empty($userStatus['blockedId']) ? $userStatus['blockedId'] : '',
					'isOnline' 		=> $isOnline,
					'isBlocked' 	=> $fetchResults['friend_status'] == 3 ? true : false,
				);
				$fetchResults 	= $this->guppyModel->getUserLatestMessage($loginedUser, $sendTo);  // senderId, receiverId
				$messageResult = ! empty($fetchResults) ? $fetchResults[0] : array();
				
				// check chat is cleard or not
				$chatClearTime  = '';
				$clearChat = false;
				$filterData = array();
				$filterData['actionBy'] 	= $loginedUser;
				$filterData['userId'] 		= $sendTo;
				$chatType = 1;
				$filterData['actionType'] 	= '0';
				$filterData['chatType']     = $chatType;
				$chatActions = $this->getGuppyChatAction($filterData);
				
				if(!empty($chatActions)){
					$chatClearTime = $chatActions['chatClearTime'];
				}

				$chatNotify = array();
				$chatNotify['actionBy'] 	= $loginedUser;
				$chatNotify['actionType'] 	= '2';
				$chatNotify['userId'] 		= $sendTo;
				$chatNotify['chatType'] 	= $chatType;
				$muteNotification = $this->getGuppyChatAction($chatNotify);
				
				if(!empty($muteNotification)){
					$muteNotification = true;
				}else{
					$muteNotification = false;
				}
				
				$message = $messageResult['message'];
				if(!empty($chatClearTime) && strtotime($chatClearTime) > strtotime($messageResult['message_sent_time'])){
					$clearChat 	= true;
					$message 	= '';
				}
				
				$messagelistData['messageId'] 			= $messageResult['id'];
				$messagelistData['message'] 			= $message;	
				$messagelistData['timeStamp'] 			= $messageResult['timestamp'];	
				$messagelistData['messageType'] 		= $messageResult['message_type'];
				$messagelistData['chatType'] 			= $messageResult['chat_type'];
				$messagelistData['isSender'] 			= $messageResult['sender_id'] == $loginedUser ? true : false;
				$messagelistData['messageStatus'] 		= $messageResult['message_status'];
				$messagelistData['userName'] 			= $receiverUserName;
				$messagelistData['userAvatar'] 			= $receiverUserAvatar;
				$messagelistData['chatId']				= $this->getChatKey('1',$sendTo);
				$messagelistData['blockedId'] 			= !empty($userStatus['blockedId']) ? $userStatus['blockedId'] : '';
				$messagelistData['clearChat'] 			= $clearChat;
				$messagelistData['isBlocked'] 			= !empty($userStatus['isBlocked']) ? $userStatus['isBlocked'] : false;
				$messagelistData['isOnline'] 			= !empty($userStatus['isOnline'])  ? $userStatus['isOnline'] : false;
				$messagelistData['UnreadCount'] 		= 0;
				$messagelistData['muteNotification'] 	= $muteNotification;
			
				$json['autoInvite']			= $autoInvite;
				$json['messagelistData']	= $messagelistData;
				$json['friendData']			= $friendData;
				$json['resendRequest']		= true;
				$json['type']				= 'success';
				$json['status']				= 200;
				return new WP_REST_Response($json , $json['status']);

			}elseif(!empty( $fetchResults) && ($fetchResults['friend_status'] == '2' || $fetchResults['friend_status'] == '0')){

				if($startChat == 1 &&  $autoInvite){
					$current_date 	= date('Y-m-d H:i:s');
					$friend_status 	= 1;
				}else{
					$current_date = $fetchResults['friend_created_date'];
					$friend_status = 0;
				}
				$where = array(
					'send_by' 				=> $loginedUser,
					'send_to' 				=> $sendTo,
				);

				$data 	= array(
					'friend_status'			=> $friend_status,
					'send_by' 				=> $loginedUser,
					'friend_created_date' 	=> $current_date,
					'send_to' 				=> $sendTo,
				);

				if($fetchResults['send_by'] != $loginedUser){
					$where = array(
						'send_by' 				=> $sendTo,
						'send_to' 				=> $loginedUser,
					);

					$data 	= array(
						'friend_status'			=> $friend_status,
						'send_by' 				=> $loginedUser,
						'friend_created_date' 	=> $current_date,
						'send_to' 				=> $sendTo,
					);
				}
				
				$response 	= $this->guppyModel->updateData( 'wpguppy_friend_list' , $data, $where);
			}elseif(empty($fetchResults)){
				if($startChat == 1 &&  $autoInvite){
					$current_date 	= date('Y-m-d H:i:s');
					$friend_status 	= 1;
				}else{
					$current_date = NULL;
					$friend_status = 0;
				}
				$data 	= array(
					'send_by' 				=> $loginedUser,
					'send_to' 				=> $sendTo,
					'friend_created_date' 	=> $current_date,
					'friend_status' 		=> $friend_status,
				);
				
				$response = $this->guppyModel->insertData('wpguppy_friend_list' , $data);	
			}
			
			$inviteStatus = esc_html__('Sent', 'wp-guppy');
			if ( $response) {
				if($startChat == 1 &&  $autoInvite){
					$isOnline 			= wpguppy_UserOnline($sendTo);
					// get receiver user info 
					$receiverUserName 	= $receiverUserAvatar = '';
					$userData 				= $this->getUserInfo('1', $sendTo);
					if(!empty($userData)){
						$receiverUserAvatar 	= $userData['userAvatar'];
						$receiverUserName 		= $userData['userName'];
					}
					// get sender user info 
					$senderUserName = $senderUserAvatar = '';
					$senderUserData 	= $this->getUserInfo(1, $loginedUser);
					if(!empty($senderUserData)){
						$senderUserName 	= $senderUserData['userName'];
						$senderUserAvatar 	= $senderUserData['userAvatar'];
					}
					$friendData = array(
						'userId' 		=> $sendTo,
						'chatType' 		=> 1,
						'chatId' 		=> $this->getChatKey('1',$sendTo),
						'friendStatus' 	=> 1,
						'userName' 	   	=> $receiverUserName,
						'userAvatar' 	=> $receiverUserAvatar,
						'blockedId' 	=> false,
						'isOnline' 		=> $isOnline,
						'isBlocked' 	=> false,
					);
					$messageData 	= $messagelistData = array();
					$messageSentTime 		= date('Y-m-d H:i:s');
					$timestamp 				= strtotime($messageSentTime);
					
					$messageData['sender_id'] 			= $loginedUser; 
					$messageData['receiver_id'] 		= $sendTo; 
					$messageData['user_type'] 			= 1; 
					$messageData['chat_type'] 			= 1; 
					$messageData['message_type'] 		= 4;
					$messageData['timestamp'] 			= $timestamp; 
					$messageData['message_sent_time'] 	= $messageSentTime;
					$data = array();
					$data['type'] = 1;
					$messageData['message'] = serialize($data); 
					$messageId = $this->guppyModel->insertData('wpguppy_message',$messageData);
					
					$messagelistData['messageId'] 			= $messageId;	
					$messagelistData['message'] 			= $data;	
					$messagelistData['timeStamp'] 			= $messageData['timestamp'];	
					$messagelistData['messageType'] 		= 4;
					$messagelistData['chatType'] 			= 1;
					$messagelistData['isSender'] 			= false;
					$messagelistData['messageStatus'] 		= '0';
					$messagelistData['userName'] 			= $senderUserName;
					$messagelistData['userAvatar'] 			= $senderUserAvatar;
					$messagelistData['chatId']				= $this->getChatKey('1',$loginedUser);
					$messagelistData['blockedId'] 			= false;
					$messagelistData['isBlocked'] 			= false;
					$messagelistData['isOnline'] 			= $isOnline;
					$messagelistData['UnreadCount'] 		= 0;
					$messagelistData['muteNotification'] 	= false;
					$messagelistData['isStartChat'] 		= true;
					
					$chatData = array(
						'chatType' 				=> 1,
						'chatId' 				=> $this->getChatKey('1',$sendTo),
						'messageId' 			=> $messageId,	
						'message' 				=> $data,	
						'timeStamp' 			=> $messageData['timestamp'],	
						'messageType' 			=> 4,
						'userType' 				=> 1,
						'messageStatus' 		=> '0',	
						'attachmentsData' 		=> NULL,	
						'replyMessage' 			=> NULL,	
						'isOnline' 				=> $isOnline,	
						'metaData'				=> false,
						'userName'				=> $senderUserName,
						'userAvatar'			=> $senderUserAvatar,
						'clearChat'				=> false
					);
					$json['chatData']				= $chatData;
					$json['messagelistData']		= $messagelistData;
					$json['userName'] 				= $receiverUserName;
					$json['userAvatar'] 			= $receiverUserAvatar;
					
					if($this->pusher){
						$batchRequests = array();
						// send to receiver
						$pusherData = array(
							'chatId' 			=> $this->getChatKey('1',$loginedUser),
							'chatData'			=> $chatData,
							'chatType'			=> 1,
							'messagelistData' 	=> $messagelistData
						);
						$batchRequests[] = array(
							'channel' 	=> 'private-user-' . $sendTo,
							'name' 		=> 'recChatData',
							'data'		=> $pusherData,
						);

						// send to sender
						$chatData['isSender']				= true;
						$messagelistData['isSender'] 		= true;
						$messagelistData['userName'] 		= $receiverUserName;
						$messagelistData['userAvatar'] 		= $receiverUserAvatar;
						$messagelistData['UnreadCount'] 	= 0;
						$messagelistData['chatId']			= $this->getChatKey('1',$sendTo);
						$pusherData = array(
							'chatId' 			=> $this->getChatKey('1',$sendTo),
							'chatType'			=> 1,
							'chatData'			=> $chatData,
							'messagelistData' 	=> $messagelistData,
						);
						$batchRequests[] = array(
							'channel' 	=> 'private-user-' . $loginedUser,
							'name' 		=> 'senderChatData',
							'data'		=> $pusherData,
						);
						$this->pusher->triggerBatch($batchRequests);
					}
				}	
				$json['inviteStatus'] 	= $inviteStatus;
				$json['autoInvite']		= $autoInvite;
				$json['friendData']		= $friendData;
				$json['type']			= 'success';
				$json['resendRequest']	= false;
				$json['status']			= 200;
			} else {
				$json['type']	= 'error';
				$json['status']	= 203;
			}
			return new WP_REST_Response($json , $json['status']);
		}

		/**
		 * update guppy user status
		 *
		 * @since    1.0.0
		*/
		public function updateGuppyUserStatus( $data ) {
			$headers    	= $data->get_headers();
			$params     	= ! empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  	= ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$json       	= array();
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$statusType		= ! empty( $params['statusType'] ) 	? intval($params['statusType'] ) : 0;
			$actionTo		= ! empty( $params['actionTo'] ) 	?  intval($params['actionTo'])  : 0 ;
			$loginedUser 	= !empty(  $params['userId']) 		? intval($params['userId']) : 0; 
			$response 		= false;
			if(!empty($statusType)){
				$user_meta  	= get_userdata($actionTo);
				$user_roles 	= $user_meta->roles;
				$allowed_roles = array( 'administrator');
				if (array_intersect( $allowed_roles, $user_roles ) ) {
					$json['type'] = 'error';
					$json['message_desc']   = esc_html__('You are not allowed to block admin', 'wp-guppy');
					return new WP_REST_Response($json , 203);
				}
				$fetchResults 	= $this->guppyModel->getGuppyFriend($actionTo,$loginedUser,false);
				if(!empty($fetchResults)){
					$userData 	= $this->getUserInfo('1', $actionTo);
					$userAvatar = $userData['userAvatar'];
					$userName 	= $userData['userName'];
					$chatNotify = array();
					$chatNotify['actionBy'] 	= $loginedUser;
					$chatNotify['actionType'] 	= '2';
					$chatNotify['userId'] 		= $actionTo;
					$chatNotify['chatType'] 	= 1;
					$muteNotification = $this->getGuppyChatAction($chatNotify);
					if(!empty($muteNotification)){
						$muteNotification = true;
					}else{
						$muteNotification = false;
					}
					$updateData = array(
						'friend_status'	=> $statusType,
						'send_by' 		=> $actionTo,
						'send_to' 		=> $loginedUser,
					);
					if($statusType == 1){
						$updateData['friend_created_date'] = date('Y-m-d H:i:s');
						$response 	= $this->guppyModel->updateData( 'wpguppy_friend_list', $updateData, array('id' => $fetchResults['id']));	
					}elseif($statusType == 2 || $statusType == 3){
						$response 	= $this->guppyModel->updateData( 'wpguppy_friend_list', $updateData, array('id' => $fetchResults['id']));
						if($statusType ==3){
							if($this->pusher){
								$batchRequests = array();
								// send to sender
								$pusherData = array(
									'chatId' 			=> $this->getChatKey('1', $actionTo),
									'status' 			=> $statusType,
									'chatType' 			=> 1,
									'muteNotification'	=> $muteNotification,
									'userName' 	   		=> $userName,
									'userAvatar' 		=> $userAvatar,
									'clearChat' 		=> false,
									'blockedId' 		=> $actionTo,
									'blockerId' 		=> $loginedUser,
									'isBlocked' 		=> true,
									'isOnline' 			=> wpguppy_UserOnline($actionTo),
								);
								$batchRequests[] = array(
									'channel' 	=> 'private-user-' . $loginedUser,
									'name' 		=> 'updateUser',
									'data'		=> $pusherData,
								);

								// send to receiver
								$userData 					= $this->getUserInfo('1', $loginedUser);
								$senderUserAvatar 			= $userData['userAvatar'];
								$senderUserName 			= $userData['userName'];
								$chatNotify = array();
								$chatNotify['actionBy'] 	= $actionTo;
								$chatNotify['actionType'] 	= '2';
								$chatNotify['userId'] 		= $loginedUser;
								$chatNotify['chatType'] 	= 1;
								$senderMuteNotification = $this->getGuppyChatAction($chatNotify);
								if(!empty($senderMuteNotification)){
									$senderMuteNotification = true;
								}else{
									$senderMuteNotification = false;
								}
								$pusherData = array(
									'chatId' 			=> $this->getChatKey('1', $loginedUser),
									'status' 			=> $statusType,
									'chatType' 			=> 1,
									'muteNotification'	=> $senderMuteNotification,
									'userName' 	   		=> $senderUserName,
									'userAvatar' 		=> $senderUserAvatar,
									'clearChat' 		=> false,
									'blockedId' 		=> $actionTo,
									'blockerId' 		=> $loginedUser,
									'isBlocked' 		=> true,
									'isOnline' 			=> wpguppy_UserOnline($loginedUser),
								);
								$batchRequests[] = array(
									'channel' 	=> 'private-user-' . $actionTo,
									'name' 		=> 'updateUser',
									'data'		=> $pusherData,
								);
								$this->pusher->triggerBatch($batchRequests);
							}	
						}
					}elseif($statusType == 4 && empty($fetchResults['friend_created_date'])){
						$response 	=	$this->guppyModel->deleteData( 'wpguppy_friend_list',  array('id' => $fetchResults['id']));
						$statusType = 0;
					}elseif($statusType == 4 && !empty($fetchResults['friend_created_date'])){
						$statusType = 1;
						$updateData = array(
							'friend_status'	=> $statusType
						); 
						$response 	= $this->guppyModel->updateData( 'wpguppy_friend_list', $updateData, array('id' => $fetchResults['id']));
						if($this->pusher){
							$batchRequests = array();
							// send to sender
							$pusherData = array(
								'chatId' 			=> $this->getChatKey('1', $actionTo),
								'status' 			=> $statusType,
								'chatType' 			=> 1,
								'blockedId' 		=> $actionTo,
								'blockerId' 		=> $loginedUser,
								'muteNotification'	=> $muteNotification,
								'userName' 	   		=> $userName,
								'userAvatar' 		=> $userAvatar,
								'clearChat' 		=> false,
								'isBlocked' 		=> false,
								'isOnline' 			=> wpguppy_UserOnline($actionTo),
							);
							$batchRequests[] = array(
								'channel' 	=> 'private-user-' . $loginedUser,
								'name' 		=> 'updateUser',
								'data'		=> $pusherData,
							);
							// send to receiver
							$userData 	= $this->getUserInfo('1', $loginedUser);
							$senderUserAvatar 	= $userData['userAvatar'];
							$senderUserName 	= $userData['userName'];
							$chatNotify = array();
							$chatNotify['actionBy'] 	= $actionTo;
							$chatNotify['actionType'] 	= '2';
							$chatNotify['userId'] 		= $loginedUser;
							$chatNotify['chatType'] 	= 1;
							$senderMuteNotification = $this->getGuppyChatAction($chatNotify);
							if(!empty($senderMuteNotification)){
								$senderMuteNotification = true;
							}else{
								$senderMuteNotification = false;
							}
							$pusherData = array(
								'chatId' 			=> $this->getChatKey('1', $loginedUser),
								'status' 			=> $statusType,
								'chatType' 			=> 1,
								'blockedId' 		=> $actionTo,
								'blockerId' 		=> $loginedUser,
								'muteNotification'	=> $senderMuteNotification,
								'userName' 	   		=> $senderUserName,
								'userAvatar' 		=> $senderUserAvatar,
								'clearChat' 		=> false,
								'isBlocked' 		=> false,
								'isOnline' 			=> wpguppy_UserOnline($loginedUser),
							);
							$batchRequests[] = array(
								'channel' 	=> 'private-user-' . $actionTo,
								'name' 		=> 'updateUser',
								'data'		=> $pusherData,
							);
							$this->pusher->triggerBatch($batchRequests);
						}	
					}
				}
			}
			if (!empty($response)) {
				
				$userStatus = $this->getUserStatus($loginedUser, $actionTo, '1');
				$data = array(
					'muteNotification'	=> $muteNotification,
					'blockerId' 		=> $loginedUser,
					'chatId' 			=> $this->getChatKey('1',$actionTo),
					'chatType' 			=> 1,
					'status' 			=> $statusType,
					'userName' 	   		=> $userName,
					'userAvatar' 		=> $userAvatar,
					'blockedId' 		=> $userStatus['blockedId'],
					'isOnline' 			=> $userStatus['isOnline'],
					'isBlocked' 		=> $userStatus['isBlocked'],
					'clearChat' 		=> false,
				);
				$json['type']			= 'success';
				$json['userData']		= $data;
				$json['status']			= 200;
			}else{
				$json['type']	= 'error';
				$json['status']	= 203;
			}
			return new WP_REST_Response($json , $json['status']);
		}

		/**
		 * update guppy post user status
		 *
		 * @since    1.0.0
		*/
		public function updateGuppyPostUserStatus( $data ) {
			$headers    	= $data->get_headers();
			$params     	= ! empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  	= ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$json       	= array();
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$statusType		= ! empty( $params['statusType'] ) 	? intval($params['statusType'] ) 	: 0;
			$actionTo		= ! empty( $params['actionTo'] ) 	? intval( $params['actionTo'] ) 	: 0 ;
			$loginedUser 	= ! empty(  $params['userId']) 		? intval($params['userId']) 		: 0; 
			$postId 		= ! empty(  $params['postId']) 		? intval($params['postId']) 		: 0; 
			$blockType 		= ! empty(  $params['blockType']) 	? intval($params['blockType']) 		: 0; 
			$actionType 	= ! empty(  $params['actionType']) 	? intval($params['actionType']) 	: 0; 
			$response 		= false;
			if(!empty($statusType)){
				$where 				= " (sender_id =".$loginedUser.' OR receiver_id='.$loginedUser.") AND (sender_id =".$actionTo.' OR receiver_id='.$actionTo.")"; 
				$where 				.= " AND post_id =".$postId." AND chat_type = '0' Limit 1"; 
				$fetchResults 		= $this->guppyModel->getData('id','wpguppy_message',$where );
				
				if(!empty($fetchResults)){
					$userData 	= $this->getUserInfo('1', $actionTo);
					$userAvatar = $userData['userAvatar'];
					$userName 	= $userData['userName'];
					$chatNotify = array();
					$chatNotify['actionBy'] 	= $loginedUser;
					$chatNotify['actionType'] 	= '2';
					$chatNotify['actionTo'] 	= $actionTo;
					$chatNotify['postId'] 		= $postId;
					$muteNotification 	= $this->getGuppyPostChatAction($chatNotify);
					
					$postImage 			= $this->getPostImage($postId);
					$postTitle 			= get_the_title($postId);
					if(!empty($muteNotification)){
						$muteNotification = true;
					}else{
						$muteNotification = false;
					}
					if($statusType == 3){
						if($blockType == 0){
							$this->guppyModel->deleteData( 'wpguppy_postchat_action',  array('action_by' => $loginedUser, 'action_to' => $actionTo));
							$this->guppyModel->deleteData( 'wpguppy_postchat_action',  array('action_by' => $actionTo, 'action_to' => $loginedUser));
						}else{
							$where 		 	= "((action_by=". $actionTo." OR action_to =".$actionTo.") AND (action_by=". $loginedUser." OR action_to =".$loginedUser.")) AND action_type = 1 AND post_id=".$postId; 
							$chatAction 	= $this->guppyModel->getData('id','wpguppy_postchat_action',$where );
						}
						if(empty($chatAction)){
							$insertData = array(
								'action_by'				=> $loginedUser,
								'action_to' 			=> $actionTo,
								'post_id' 				=> $blockType == 0 ? NULL : $postId ,
								'action_type' 			=> $blockType == 0 ? 0 : 1,
								'action_time' 			=> date('Y-m-d H:i:s'),
							);
							$response 	= $this->guppyModel->insertData( 'wpguppy_postchat_action', $insertData);
							if($this->pusher){
								$batchRequests = array();
								// send to sender
								$pusherData = array(
									'chatId' 			=> $this->getChatKey('0',$postId, $actionTo),
									'status' 			=> $statusType == 4 ? 1 : 3,
									'blockType' 		=> $blockType,
									'chatType' 			=> 0,
									'blockedId' 		=> $actionTo,
									'blockerId' 		=> $loginedUser,
									'muteNotification'	=> $muteNotification,
									'userName' 	   		=> $userName,
									'userAvatar' 		=> $userAvatar,
									'clearChat' 		=> false,
									'isBlocked' 		=> true,
									'postImage' 		=> $postImage,
									'postTitle' 		=> $postTitle,
									'isOnline' 			=> wpguppy_UserOnline($actionTo),
								);
								$batchRequests[] = array(
									'channel' 	=> 'private-user-' . $loginedUser,
									'name' 		=> 'updateUser',
									'data'		=> $pusherData,
								);

								// send to receiver
								$userData 	= $this->getUserInfo('1', $loginedUser);
								$senderUserAvatar 	= $userData['userAvatar'];
								$senderUserName 	= $userData['userName'];
								$senderChatNotify = array();
								$senderChatNotify['actionBy'] 		= $actionTo;
								$senderChatNotify['actionType'] 	= '2';
								$senderChatNotify['actionTo'] 		= $loginedUser;
								$senderChatNotify['postId'] 		= $postId;

								$senderMuteNotification = $this->getGuppyPostChatAction($senderChatNotify);
								if(!empty($senderMuteNotification)){
									$senderMuteNotification = true;
								}else{
									$senderMuteNotification = false;
								}
								$pusherData = array(
									'chatId' 			=> $this->getChatKey('0',$postId, $loginedUser),
									'status' 			=> $statusType == 4 ? 1 : 3,
									'blockType' 		=> $blockType,
									'chatType' 			=> 0,
									'blockedId' 		=> $actionTo,
									'blockerId' 		=> $loginedUser,
									'muteNotification'	=> $senderMuteNotification,
									'userName' 	   		=> $senderUserName,
									'userAvatar' 		=> $senderUserAvatar,
									'clearChat' 		=> false,
									'isBlocked' 		=> true,
									'postImage' 		=> $postImage,
									'postTitle' 		=> $postTitle,
									'isOnline' 			=> wpguppy_UserOnline($loginedUser),
								);
								$batchRequests[] = array(
									'channel' 	=> 'private-user-' . $actionTo,
									'name' 		=> 'updateUser',
									'data'		=> $pusherData,
								);
								$this->pusher->triggerBatch($batchRequests);
							}
						}
					}elseif($statusType == 4){
						$where 		 	= "((action_by=". $actionTo." OR action_to =".$actionTo.") AND (action_by=". $loginedUser." OR action_to =".$loginedUser.")) AND action_type = 1 AND post_id=".$postId; 
						$chatAction 	= $this->guppyModel->getData('id','wpguppy_postchat_action',$where );
						if(empty($chatAction)){
							$blockType 		= 0;
							$where 		 	= "((action_by=". $actionTo." OR action_to =".$actionTo.") AND (action_by=". $loginedUser." OR action_to =".$loginedUser.")) AND action_type=0"; 
							$chatAction 	= $this->guppyModel->getData('id','wpguppy_postchat_action',$where );
						}else{
							$blockType 		= 1;
						}
						if(!empty($chatAction)){
							$response 	= $this->guppyModel->deleteData( 'wpguppy_postchat_action',  array('id' => $chatAction[0]['id']));
							if($this->pusher){
								$batchRequests = array();
								// send to sender
								$pusherData = array(
									'chatId' 			=> $this->getChatKey('0',$postId, $actionTo),
									'status' 			=> $statusType == 4 ? 1 : 3,
									'blockType' 		=> $blockType,
									'chatType' 			=> 0,
									'blockedId' 		=> $actionTo,
									'blockerId' 		=> $loginedUser,
									'muteNotification'	=> $muteNotification,
									'userName' 	   		=> $userName,
									'userAvatar' 		=> $userAvatar,
									'clearChat' 		=> false,
									'isBlocked' 		=> false,
									'postImage' 		=> $postImage,
									'postTitle' 		=> $postTitle,
									'isOnline' 			=> wpguppy_UserOnline($actionTo),
								);
								$batchRequests[] = array(
									'channel' 	=> 'private-user-' . $loginedUser,
									'name' 		=> 'updateUser',
									'data'		=> $pusherData,
								);

								// send to receiver
								$userData 	= $this->getUserInfo('1', $loginedUser);
								$senderUserAvatar 	= $userData['userAvatar'];
								$senderUserName 	= $userData['userName'];
								$chatNotify = array();
								$chatNotify['actionBy'] 	= $actionTo;
								$chatNotify['actionType'] 	= '2';
								$chatNotify['actionTo'] 	= $loginedUser;
								$chatNotify['postId'] 		= $postId;
								$senderMuteNotification = $this->getGuppyPostChatAction($chatNotify);
								if(!empty($senderMuteNotification)){
									$senderMuteNotification = true;
								}else{
									$senderMuteNotification = false;
								}
								$pusherData = array(
									'chatId' 			=> $this->getChatKey('0',$postId, $loginedUser),
									'status' 			=> $statusType == 4 ? 1 : 3,
									'blockType' 		=> $blockType,
									'chatType' 			=> 0,
									'blockedId' 		=> $actionTo,
									'blockerId' 		=> $loginedUser,
									'muteNotification'	=> $senderMuteNotification,
									'userName' 	   		=> $senderUserName,
									'userAvatar' 		=> $senderUserAvatar,
									'clearChat' 		=> false,
									'isBlocked' 		=> false,
									'postImage' 		=> $postImage,
									'postTitle' 		=> $postTitle,
									'isOnline' 			=> wpguppy_UserOnline($loginedUser),
								);
								$batchRequests[] = array(
									'channel' 	=> 'private-user-' . $actionTo,
									'name' 		=> 'updateUser',
									'data'		=> $pusherData,
								);
								$this->pusher->triggerBatch($batchRequests);
							}	
						}
					}
				}
			}

			if ($response) {
				$data = array(
					'muteNotification'	=> $muteNotification,
					'blockerId' 		=> $loginedUser,
					'chatId' 			=> $this->getChatKey('0',$postId, $actionTo),
					'chatType' 			=> 0,
					'status' 			=> $statusType == 4 ? 1 : 3,
					'userName' 	   		=> $userName,
					'userAvatar' 		=> $userAvatar,
					'blockedId' 		=> $actionTo,
					'isOnline' 			=> wpguppy_UserOnline($actionTo),
					'isBlocked' 		=> $statusType == 4 ? false : true,
					'blockType' 		=> $blockType,
					'postImage' 		=> $postImage,
					'postTitle' 		=> $postTitle,
					'clearChat' 		=> false,
				);
				$json['type']			= 'success';
				$json['userData']		= $data;
				$json['status']			= 200;
			}else{
				$json['type']	= 'error';
				$json['status']	= 203;
			}
			return new WP_REST_Response($json , $json['status']);
		}

		/**
		 * clear guppy chat
		 *
		 * @since    1.0.0
		*/
		public function clearGuppyChat( $data ) {
			$headers    	= $data->get_headers();
			$params     	= ! empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  	= ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$json       	= array();
			$response 		= $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}

			$actionTo		= ! empty( $params['actionTo'] ) 	? intval( $params['actionTo'] ) : 0 ;
			$groupId		= ! empty( $params['groupId'] ) 	? intval( $params['groupId'] ) 	: 0 ;
			$postId			= ! empty( $params['postId'] ) 		? intval( $params['postId'] ) 	: 0 ;
			$loginedUser 	= ! empty(  $params['userId']) 		? intval($params['userId']) 	: 0; 
			$chatType 		= ! empty(  $params['chatType']) 	? $params['chatType'] 			: 0; 
			$chatId 		= ! empty(  $params['chatId']) 		? $params['chatId'] 			: ''; 
			$response 		= false;
			if($chatType == 0){
				$where 		 	= " (action_by =". $loginedUser." AND action_to =". $actionTo.") AND action_type=3 AND post_id=".$postId; 
				$chatActions 	= $this->guppyModel->getData('id', 'wpguppy_postchat_action', $where );
				if(!empty($chatActions)){
					$response 	= $this->guppyModel->updateData( 'wpguppy_postchat_action', array('action_time' => date('Y-m-d H:i:s')), array('id' => $chatActions[0]['id']));
				}else{
					$insertData = array(
						'action_by'				=> $loginedUser,
						'action_to' 			=> $actionTo,
						'post_id' 				=> $postId,
						'action_type' 			=> '3',
						'action_time' 			=> date('Y-m-d H:i:s'),
					);
					$response 	= $this->guppyModel->insertData( 'wpguppy_postchat_action', $insertData);
				}
			}else{
				if(!empty($groupId)){
					$actionTo = NUll;
				}else{
					$groupId = NUll;
				}
				
				$filterData = array();
				$filterData['actionBy'] 	= $loginedUser;
				$filterData['chatType'] 	= $chatType;
				if(!empty($groupId) && $chatType==2){
					$filterData['groupId'] 		= $groupId;
					$corresponding_id = $groupId;
				}elseif(!empty($actionTo) && $chatType==1){
					$filterData['userId'] 		= $actionTo;
					$corresponding_id 			= $actionTo;
				}
				$filterData['actionType'] 	= '0';
				$chatActions = $this->getGuppyChatAction($filterData);
	
				if(!empty($chatActions)){
					$updateData = array(
						'action_by'				=> $loginedUser,
						'corresponding_id' 		=> $corresponding_id,
						'chat_type' 			=> $chatType,
						'action_type' 			=> '0',
						'action_time' 			=> date('Y-m-d H:i:s'),
						'action_updated_time' 	=> date('Y-m-d H:i:s'),
					);
					$response 	= $this->guppyModel->updateData( 'wpguppy_chat_action', $updateData, array('id' => $chatActions['chatActionId']));	
				
				}else{
					$insertData = array(
						'action_by'				=> $loginedUser,
						'corresponding_id' 		=> $corresponding_id,
						'chat_type' 			=> $chatType,
						'action_type' 			=> '0',
						'action_time' 			=> date('Y-m-d H:i:s'),
						'action_updated_time' 	=> date('Y-m-d H:i:s')
					);
					$response 	= $this->guppyModel->insertData( 'wpguppy_chat_action', $insertData);
				}
			}
			if($response) {
				if($this->pusher){
					$pusherData = array(
						'actionTo' 	=> $actionTo,
						'chatId' 	=> $chatId,
						'chatType'  => $chatType,
						'groupId'  	=> $groupId,
						'postId'  	=> $postId,
						'userId' 	=> $loginedUser,
					);
					$this->pusher->trigger('private-user-'.$loginedUser, 'clearChat', $pusherData);
				}

				$json['type']		= 'success';
				$json['status']		= 200;
			}else{
				$json['type']	= 'error';
				$json['status']	= 203;
			}
			return new WP_REST_Response($json , $json['status']);
		}

		/**
		 * report an issue guppy user
		 *
		 * @since    1.0.0
		*/
		public function reportGuppyChat( $data ) {
			$headers    	= $data->get_headers();
			$params     	= ! empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  	= ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$json       	= array();
			
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}

			$reportDesc		= ! empty( $params['reportDesc'] ) 		? sanitize_text_field($params['reportDesc']) : '' ;
			$reportReason	= ! empty( $params['reportReason'] ) 	? sanitize_text_field($params['reportReason']) : '' ;
			$reportAgainst	= ! empty( $params['reportAgainst'] ) 	? sanitize_text_field($params['reportAgainst']) : '' ;
			$loginedUser 	= ! empty(  $params['userId']) 			? intval($params['userId']) : 0; 
			$response 		= false;
			
			if(!empty($reportDesc) 
				&& !empty($reportReason)
				&& !empty($loginedUser)){
					$reportAdminEmail 	= !empty($this->guppySetting['report_admin_email']) ? $this->guppySetting['report_admin_email'] : get_option('admin_email');
					$reportSubject 		= !empty($this->guppySetting['report_subject']) 	? $this->guppySetting['report_subject'] : esc_html__( "User reported against {{reason}}", 'wp-guppy' );
					$reportHeaderlogo 	= !empty($this->guppySetting['report_header_logo']) ? $this->guppySetting['report_header_logo'] : "";
					$emailContent 		= !empty($this->guppySetting['report_content']) 	? $this->guppySetting['report_content'] : wp_kses( __( "Hey,\n\n“{{who_report}}” reported a complaint of “{{report_against}}” against “{{reason}}”. Please read the description below. \n\n{{user_content}} \n\nThanks & regards \n“{{who_report}}”", 'wp-guppy' ), array(
						'a' => array(
							'href' => array(),
							'title' => array()
						),
						'br' => array(),
						'em' => array(),
						'strong' => array(),
					) );
					$where 			= "user_id=".$loginedUser; 
					$userInfo 		= $this->guppyModel->getData('*','wpguppy_users',$where );
					if(!empty($userInfo)){
						$userName = $userInfo[0]['user_name'];
					}else{
						$userName 		= $this->guppyModel->getUserInfoData('username', $loginedUser, array());
					}
					$emailContent 		= str_replace("{{who_report}}", '<strong>'.$userName.'</strong>', $emailContent);

					$reportSubject 		= str_replace("{{reason}}", $reportReason, $reportSubject);
					$emailContent 		= str_replace("{{report_against}}", '<strong>'.$reportAgainst.'</strong>', 	  $emailContent);
					$emailContent 		= str_replace("{{reason}}", 		'<strong>'.$reportReason.'</strong>', $emailContent);
					$emailContent 		= str_replace("{{user_content}}", $reportDesc, $emailContent);
					
					if (class_exists('WpguppyEmailhelper')) { 
						$email_helper   = new WpguppyEmailhelper();
						$emailData      = array();
						$emailData['emailContent']   = esc_html($emailContent);
						$body = '';
						$body .= $email_helper->emailHeaders($reportHeaderlogo);
						$body .= $email_helper->reportChatEmailContent($emailData);
						$body .= $email_helper->emailFooter();
						wp_mail($reportAdminEmail, $reportSubject, $body);
						$response 		= true;
					}
				}
			if ($response) {
				$json['type']			= 'success';
				$json['status']			= 200;
			} else {
				$json['type']	= 'error';
				$json['status']	= 203;
			}
			return new WP_REST_Response($json , $json['status']);
		}

		/**
		 * Get guppy users
		 *
		 * @since    1.0.0
		*/
		public function getGuppyUsers($data){
			$headers    = $data->get_headers();
			$params     = ! empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  = ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$guppyUsers = $json = $meta_query_args = array();
			$requestCount = 0;

			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$offset 		= !empty($params['offset']) ? intval($params['offset']) : 0; 
			$searchQuery 	= !empty($params['search']) ? sanitize_text_field($params['search']) : ''; 
			$loginedUser 	= !empty($params['userId']) ? intval($params['userId']) : 0; 

			$roles = $this->getUserRoles($loginedUser);
			$userRoles 		= $roles['userRoles'];
			$autoInvite 	= $roles['autoInvite'];
			$excludeIds 	= array($loginedUser);
			$fetchResults 	= $this->guppyModel->getGuppyFriend(false, $loginedUser, true);
			if(!empty($fetchResults)){
				foreach( $fetchResults as $result ) {
					if ( $result['friend_status'] == '3') {
						if($result['send_by'] == $loginedUser){
							$excludeIds[] = $result['send_to'];
						}else{
							$excludeIds[] = $result['send_by'];
						}
					}elseif($result['friend_status'] == '1'){
						if($result['send_by'] == $loginedUser){
							$excludeIds[] = $result['send_to'];
						}else{
							$excludeIds[] = $result['send_by'];
						}
					}elseif($result['friend_status'] == '0' && !$autoInvite && $result['send_to'] == $loginedUser ){
						$excludeIds[] = $result['send_by'];
					}
				}
			}
			$query_meta_args = array(
				'relation' => 'OR',
				array(
					'key'     => 'wpguppy_user_online',                 
					'compare' => 'NOT EXISTS'
				),
				array(
					'key'     => 'wpguppy_user_online',                 
					'compare' => 'EXISTS'
				)
			);
			$query_args = array(
				'fields' 			=> array('id'),
				'orderby' 			=> 'meta_value',
				'order' 			=> 'DESC',
				'offset' 			=> $offset,
				'number'			=> $this->showRec,
				'exclude'			=> $excludeIds,
				'meta_query' 		=> $query_meta_args
			);
			if(!empty($userRoles)){
				$query_args['role__in'] = $userRoles;
			}
			if( !empty($searchQuery) ){
				$query_args['search']	=  '*'.$searchQuery.'*';
			}
			$query_args = apply_filters('wpguppy_filter_user_params', $query_args);
			$allusers = get_users( $query_args );
			if(!empty($allusers)){
				foreach( $allusers as $user ) {

					$fetchResults 	= $this->guppyModel->getGuppyFriend($user->id, $loginedUser, false);
					
					$userData 	= $this->getUserInfo('1', $user->id);
					$userAvatar = $userData['userAvatar'];
					$userName 	= $userData['userName'];
					
					$friend_status	= !empty($fetchResults) ? $fetchResults['friend_status'] : '';
					$sendTo			= !empty($fetchResults) ? $fetchResults['send_to'] : ''; 
					$send_by		= !empty($fetchResults) ? $fetchResults['send_by'] : ''; 
					
					$inviteStatusText 	= 'invite';
					if ( $friend_status == '0' && $send_by == $loginedUser ) {
						$inviteStatusText = 'sent';
					}elseif ( $friend_status == '2' && $sendTo == $loginedUser ) {
						$inviteStatusText = 'invite';
					} elseif ( $friend_status == '2' ) {
						$inviteStatusText = 'resend';
					} elseif ( $friend_status == '3' && $send_by == $loginedUser) {
						$inviteStatusText = 'blocked';
					}

					$isOnline 		= wpguppy_UserOnline($user->id);
					$key 			= $this->getChatKey('1',$user->id);
					$guppyUsers[$key] = array(
						'chatType'		 => 1,
						'chatId'		 => $key,
						'statusText' 	 => $inviteStatusText,
						'friendStatus' 	 => intval( $friend_status ),
						'userName' 	   	 => $userName,
						'userAvatar' 	 => $userAvatar,
						'isOnline'		 => $isOnline,
					);	
				}
			}
			$json['type'] 					= 'success';
			$json['guppyUsers']     		= (Object)$guppyUsers;
			$json['autoInvite']     		= $autoInvite;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * Get guppy Friend requests
		 *
		 * @since    1.0.0
		*/
		public function getGuppyFriendRequests($data){
			$headers    = $data->get_headers();
			$params     = ! empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  = ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$guppyFriendRequests = $json  = array();
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$offset 		= !empty($params['offset']) ? intval($params['offset']) : 0; 
			$searchQuery 	= !empty($params['search']) ? sanitize_text_field($params['search']) : ''; 
			$loginedUser 	= !empty($params['userId']) ? intval($params['userId']) : 0; 
			$fetchResults 	= $this->guppyModel->getGuppyFriendRequests($this->showRec, $offset, $searchQuery, $loginedUser);
			if(!empty($fetchResults)){
				foreach( $fetchResults as $user ) {
					$userData 	= $this->getUserInfo('1', $user['send_by']);
					$userAvatar = $userData['userAvatar'];
					$userName 	= $userData['userName'];
					$inviteStatusText = 'respond';
					$key = $user['send_by'].'_1';
					$guppyFriendRequests[$key] = array(
						'userId'		 => intval( $user['send_by']),
						'statusText' 	 => $inviteStatusText,
						'userName' 	   	 => $userName,
						'userAvatar' 	 => $userAvatar,
					);	
				}
			}
			$json['type'] 					= 'success';
			$json['guppyFriendRequests']    = (Object)$guppyFriendRequests;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * get User Profile info
		 *
		 * @since    1.0.0
		*/
		public function getProfileInfo( $data ){
			
			$headers    = $data->get_headers();
			$params     = ! empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  = ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$userInfo  	= $json = $userInfo = array();

			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$loginedUser 	= !empty($params['userId']) ? intval($params['userId']) : 0; 
			// check mute notification
			$muteNotification = false;
			$where 				= "action_by=".$loginedUser; 
			$where 				.= " AND action_type='1'"; 
			$checknotification 	= $this->guppyModel->getData('id','wpguppy_chat_action',$where );
			if(!empty($checknotification)){
				$muteNotification = true;
			}
			$userInfo['userId'] 			= $loginedUser;
			$userInfo['muteNotification'] 	= $muteNotification;
			$userInfo['userAvatar'] 		= $this->guppyModel->getUserInfoData('avatar', $loginedUser, array('width' => 150, 'height' => 150));
			// get user information
			$where 		 	= "user_id=".$loginedUser; 
			$fetchResults 	= $this->guppyModel->getData('*','wpguppy_users',$where );
			
			if(!empty($fetchResults)){
				$info 					= $fetchResults[0];
				$userInfo['userName'] 	= $info['user_name'];
				$userInfo['userEmail'] 	= $info['user_email'];
				$userInfo['userPhone'] 	= $info['user_phone'];
				if(!empty($info['user_image'])){
					$userImage 				= unserialize($info['user_image']);
					$avatar_url 			= $userImage['attachments'][0]['thumbnail'];
					$userInfo['userAvatar']	= $avatar_url;
				}
			}else{
				$userInfo['userName'] 		= $this->guppyModel->getUserInfoData('username', $loginedUser, array());
				$userInfo['userPhone'] 		= $this->guppyModel->getUserInfoData('userphone', $loginedUser, array());
				$userInfo['userEmail'] 		= $this->guppyModel->getUserInfoData('useremail', $loginedUser, array());
			}
			$json['type'] 					= 'success';
			$json['userInfo']   			= $userInfo;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * get unread Count 
		 *
		 * @since    1.0.0
		*/
		public function getUnreadCount( $data ){
			
			$headers    = $data->get_headers();
			$params     = ! empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  = ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$userInfo  	= $json = $userInfo = $filterData = $unReadContent =  array();

			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$loginedUser 	= !empty($params['userId']) ? intval($params['userId']) : 0; 
			
			// get one to one chat message unread count
			$filterData['chatType'] = '1';
			$filterData['receiverId'] = $loginedUser;
			$onetoOneChatCount = $this->guppyModel->getUnreadCount($filterData);

			// get posts message unread count
			$filterData['chatType'] = '0';
			$unseenPostMsgCount 	= $this->guppyModel->getUnreadCount($filterData);
			
			
			// get group message unread count
			$groupCount = 0;
			$userGroups = $this->guppyModel->getUserGroups($loginedUser);
			if(!empty($userGroups)){
				$filterData 				= array();
				$filterData['senderId'] 	= $loginedUser;	
				$filterData['chatType'] 	= '2';
				$filterData['actionBy'] 	= $loginedUser;
				$filterData['orderBy'] 		= 'action_type';
				$filterData['actionType'] 	= array('3','4','5'); // group left or removed from group
				foreach($userGroups as $single){
					$filterData['groupId'] 			= $single['group_id'];
					$filterData['memberAddedDate'] 	= $single['member_added_date'];
					$statusActions = array();
					$filterData['groupAction'] = array();
					$chatActions = $this->getGuppyChatAction($filterData);
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
					$count = $this->guppyModel->getUnreadCount($filterData);
					$groupCount = $groupCount + $count; 
				}
			}

			// get invite request count
			$where 	= "send_to=".$loginedUser; 
			$where .= " AND friend_status='0'";
			$fetchResults = $this->guppyModel->getData('id','wpguppy_friend_list',$where );
			if(!empty($fetchResults)){
				$requestCount = count($fetchResults);
			}

			$unReadContent['requestCount']   		= 	! empty( $requestCount ) ? intval( $requestCount ) : 0;
			$unReadContent['postmessagesCount']   	= 	intval( $unseenPostMsgCount );
			$unReadContent['privateChatCount']   	=  	intval( $onetoOneChatCount );
			$unReadContent['groupChatCount']   		= 	intval( $groupCount );
			$json['type'] 							= 'success';
			$json['unReadContent']					= $unReadContent;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * update user profile Info
		 *
		 * @since    1.0.0
		*/
		public function updateProfileInfo( $data ){
			$headers    = $data->get_headers();
			$params     = ! empty($data->get_params()) 		? $data->get_params() 		: '';
			$userImage  = !empty($data->get_file_params()) ? $data->get_file_params() 	: '';
			$authToken  = ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$userInfo  	= $json = array();
			$avatar_url = '';
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}

			$userName 		= !empty($params['userName']) 	? sanitize_text_field($params['userName']) : ''; 
			$userEmail 		= !empty($params['userEmail']) 	? sanitize_text_field($params['userEmail']) : ''; 
			$userPhone 		= !empty($params['userPhone']) 	? sanitize_text_field($params['userPhone']) : ''; 
			$loginedUser 	= !empty($params['userId']) ? intval($params['userId']) : 0;
			$removeAvatar 	= !empty($params['removeAvatar']) ? intval($params['removeAvatar']) : 0;

			if(!empty($userImage)){
				$filterData = array();
				$filterData['userId'] 		= $loginedUser;
				$filterData['isProfile'] 	= true;
				$attachmentData =$this->uploadAttachments('1', $userImage, $filterData);
				if(!empty($attachmentData)){
					$userInfo['user_image'] = serialize($attachmentData);
					$avatar_url 	= $attachmentData['attachments'][0]['thumbnail'];
				}
			}elseif($removeAvatar=='1'){
				$userInfo['user_image'] = NULL;
			}
			
			$where 			= "user_id=".$loginedUser; 
			$fetchResults 	= $this->guppyModel->getData('*','wpguppy_users',$where );
			$userInfo['user_name'] = $userName;
			$userInfo['user_email'] = $userEmail;
			$userInfo['user_phone'] = $userPhone;
			wp_update_user(array('ID' => $loginedUser, 'display_name' => $userName));
			if(!empty($fetchResults)){
				$updateWhere = array(
					'user_id' 	=> $loginedUser,
				);
				$response = $this->guppyModel->updateData('wpguppy_users',$userInfo, $updateWhere);
			}else{
				$userInfo['user_id'] = $loginedUser;
				$response = $this->guppyModel->insertData('wpguppy_users',$userInfo);
			}
			$info = array(); 
			$info['userName'] 	= $userName;
			$info['userPhone'] 	= $userPhone;
			$info['userEmail'] 	= $userEmail;
			$info['userAvatar'] = $avatar_url;

			$json['type'] 		= 'success'; 
			$json['userInfo']   = $info;
			$json['response']   = $response;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * mute guppy notifications
		 *
		 * @since    1.0.0
		*/
		public function muteGuppyNotification( $data ){
			$headers    = $data->get_headers();
			$params     = ! empty($data->get_params()) 		? $data->get_params() 	: '';
			$authToken  = ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$json = array();

			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			
			$muteType 		= !empty($params['muteType']) 	? intval($params['muteType']) 	: 0; 
			$actionTo 		= !empty($params['actionTo']) 	? intval($params['actionTo']) 	: 0; 
			$groupId 		= !empty($params['groupId']) 	? intval($params['groupId']) 	: 0; 
			$postId 		= !empty($params['postId']) 	? intval($params['postId']) 	: 0; 
			$loginedUser 	= !empty($params['userId']) 	? intval($params['userId']) 	: 0;
			$chatType 		= !empty($params['chatType']) 	? intval($params['chatType']) 	: 0;
			$chatId 		= !empty($params['chatId']) 	? $params['chatId'] 	: '';
			$type 			= 'error';
			$muteAll 		= '';
			$response 		= false;
			if($muteType == '1'){
				$where 			= "action_by=".$loginedUser; 
				$where 			.= " AND action_type='1'"; 
				$fetchResults 	= $this->guppyModel->getData('id','wpguppy_chat_action',$where );
				if(!empty($fetchResults)){
					$where = array(
						'action_by' 	=> $loginedUser,
						'action_type' 	=> '1',
					);
					$response = $this->guppyModel->deleteData('wpguppy_chat_action',$where);
					$muteAll = '0';
				}else{

					$data =array(
						'action_by' 			=> $loginedUser,
						'action_type' 			=> '1',
						'action_time' 			=> date('Y-m-d H:i:s'),
						'action_updated_time' 	=> date('Y-m-d H:i:s'),
					);
					$response = $this->guppyModel->insertData('wpguppy_chat_action',$data);
					$muteAll = '1';
				}
			}else{
				if($chatType == 0){
					$where 		 	= " (action_by =". $loginedUser." AND action_to =". $actionTo.") AND action_type=2 AND post_id=".$postId; 
					$chatNotify 	= $this->guppyModel->getData('id', 'wpguppy_postchat_action', $where );					
					if(!empty($chatNotify)){
						$response = $this->guppyModel->deleteData('wpguppy_postchat_action', array('id'=> $chatNotify[0]['id']));
						$muteAll = '0';
					}else{
						$data =array(
							'action_by' 			=> $loginedUser,
							'action_to' 			=>  $actionTo,
							'action_type' 			=> '2',
							'post_id'				=> $postId,
							'action_time' 			=> date('Y-m-d H:i:s'),
						);
						$response = $this->guppyModel->insertData('wpguppy_postchat_action',$data);
						$muteAll = '1';
					}
				}else{
					if(!empty($actionTo) && $chatType == 1){
						$corresponding_id = $actionTo;
					}elseif(!empty($groupId) && $chatType == 2){
						$corresponding_id = $groupId;
					}
					$where 			= "action_by=".$loginedUser; 
					$where 			.= " AND corresponding_id=".$corresponding_id; 
					$where 			.= " AND chat_type = ".$chatType; 
					$where 			.= " AND action_type = '2'"; 
					$fetchResults 	= $this->guppyModel->getData('id','wpguppy_chat_action',$where );
					if(!empty($fetchResults)){
						$response = $this->guppyModel->deleteData('wpguppy_chat_action', array('id'=> $fetchResults[0]['id']));
						$muteAll = '0';
					}else{
						$data =array(
							'action_by' 			=> $loginedUser,
							'corresponding_id' 		=>  $corresponding_id,
							'action_type' 			=> '2',
							'chat_type'				=> $chatType,
							'action_time' 			=> date('Y-m-d H:i:s'),
							'action_updated_time' 	=> date('Y-m-d H:i:s'),
						);
						$response = $this->guppyModel->insertData('wpguppy_chat_action',$data);
						$muteAll = '1';
					}
				}
			}
			if($response){
				$type = 'success';
				if($this->pusher){
					$pusherData = array(
						'chatId' 	=> $chatId,
						'chatType'  => $chatType,
						'isMute' 	=> $muteAll == '1' ? true : false,
						'muteType' 	=> $muteType,
						'userId' 	=> $loginedUser,
					);
					$this->pusher->trigger('private-user-'.$loginedUser, 'updateMuteChatNotify', $pusherData);
				}
			}
			$json['type'] 			= $type;
			$json['actionTo'] 		= $actionTo;
			$json['groupId'] 		= $groupId;
			$json['chatType'] 		= $chatType;
			$json['muteAll'] 		= $muteAll;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * delete guppy Message
		 *
		 * @since    1.0.0
		*/
		public function deleteGuppyMessage( $data ){
			$headers    = $data->get_headers();
			$params     = ! empty($data->get_params()) 		? $data->get_params() 	: '';
			$authToken  = ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$json 		= $memberinfo = $groupMembers = array();
			$chatType	= '';
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$type = 'error';
			$messageId 		= !empty($params['messageId']) 	? intval($params['messageId']) 	: 0; 
			$loginedUser 	= !empty($params['userId']) 	? intval($params['userId']) 	: 0; 
			
			if(!empty($messageId)){
				
				$where 			= "id =".$messageId; 
				$where 			.= " AND sender_id =".$loginedUser; 
				$where 			.= " AND message_status ='0'"; 
				$fetchResults 	= $this->guppyModel->getData('chat_type,group_id,post_id,receiver_id,sender_id','wpguppy_message',$where );
				if(!empty($fetchResults)){
					$response = $this->guppyModel->updateData('wpguppy_message',array('message_status' => '2'), array('id' => $messageId));
					if($response){
						$type = 'success';
						$chatId = '';
						$chatType = $fetchResults[0]['chat_type'];
						if( $fetchResults[0]['chat_type'] == '0' ){
							$chatId 				= $this->getChatKey('0', $fetchResults[0]['post_id'], $fetchResults[0]['sender_id']);
							$json['chatId'] 		=  $chatId;
							$json['receiverId'] 	=  $fetchResults[0]['receiver_id'];
						}else if( $fetchResults[0]['chat_type'] == '1' ) {
							$chatId 				= $this->getChatKey('1', $fetchResults[0]['sender_id']);
							$json['chatId'] 		=  $chatId;
							$json['receiverId'] 	=  $fetchResults[0]['receiver_id'];
						} else if( $fetchResults[0]['chat_type'] == '2' ) {
							$chatId 	= $this->getChatKey('2',$fetchResults[0]['group_id']);
							$json['chatId'] 		=  $chatId;
						}
						$json['chatType'] 		= $chatType;
						if($chatType == '0'){
							if($this->pusher){
								$batchRequests = array();
								$pusherData = array(
									'chatId' 	=> $chatId,
									'chatType' 	=> $chatType,
									'messageId' => $messageId,
								);
								$batchRequests[] = array(
									'channel' 	=> 'private-user-' . $fetchResults[0]['receiver_id'],
									'name' 		=> 'deleteMessage',
									'data'		=> $pusherData,
								);
								// send to sender
								$chatId 				= $this->getChatKey('0', $fetchResults[0]['post_id'], $fetchResults[0]['receiver_id']);
								$pusherData['chatId'] 	= $chatId;
								$batchRequests[] = array(
									'channel' 	=> 'private-user-' . $fetchResults[0]['sender_id'],
									'name' 		=> 'deleteMessage',
									'data'		=> $pusherData,
								);
								$this->pusher->triggerBatch($batchRequests);
							}
						}elseif($chatType == '1'){
							if($this->pusher){
								$batchRequests = array();
								$pusherData = array(
									'chatId' 	=> $chatId,
									'chatType' 	=> $chatType,
									'messageId' => $messageId,
								);
								$batchRequests[] = array(
									'channel' 	=> 'private-user-' . $fetchResults[0]['receiver_id'],
									'name' 		=> 'deleteMessage',
									'data'		=> $pusherData,
								);
								// send to sender
								$chatId 				= $this->getChatKey('1', $fetchResults[0]['receiver_id']);
								$pusherData['chatId'] 	= $chatId;
								$batchRequests[] = array(
									'channel' 	=> 'private-user-' . $fetchResults[0]['sender_id'],
									'name' 		=> 'deleteMessage',
									'data'		=> $pusherData,
								);
								$this->pusher->triggerBatch($batchRequests);
							}
						}elseif($chatType == '2'){
							$where 			= "group_id=". $fetchResults[0]['group_id']." AND member_status='1' AND group_status='1' AND member_id<>".$loginedUser; 
							$memberinfo 	= $this->guppyModel->getData('member_id', 'wpguppy_group_member', $where );
							if(!empty($memberinfo)){
								foreach($memberinfo as $info){
									$groupMembers[] = $info['member_id'];
								}
							}	
							$json['groupMembers'] 	= $groupMembers;
							$json['chatId'] 		= $this->getChatKey('2', $fetchResults[0]['group_id']);
							if($this->pusher){
								$batchRequests = array();
								$pusherData = array(
									'chatId' 	=> $this->getChatKey('2', $fetchResults[0]['group_id']),
									'chatType' 	=> $chatType,
									'messageId' => $messageId,
								);
								if(!empty($groupMembers)){
									foreach($groupMembers as $id){
										$batchRequests[] = array(
											'channel' 	=> 'private-user-' . $id,
											'name' 		=> 'deleteMessage',
											'data'		=> $pusherData,
										);
									}
								}
								$batchRequests[] = array(
									'channel' 	=> 'private-user-' . $loginedUser,
									'name' 		=> 'deleteMessage',
									'data'		=> $pusherData,
								);
								$this->pusher->triggerBatch($batchRequests);
							}
						}
					}
				}
			}	
			$json['type'] 			= $type;
			$json['messageId'] 		= $messageId;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * update guppy Message
		 *
		 * @since    1.0.0
		*/
		public function updateGuppyMessage( $data ){
			$headers    = $data->get_headers();
			$params     = ! empty($data->get_params()) 		? $data->get_params() 	: '';
			$authToken  = ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$json 		= $messageIds 	= $messageSenders = array();
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$type = 'error';
			$chatId 		= !empty($params['chatId']) 	? $params['chatId'] 	: ''; 
			$receiverId 	= !empty($params['userId']) 	? intval($params['userId']) 	: 0; 
			$chatType 		= !empty($params['chatType']) 	? intval($params['chatType']) 	: 0; 
			$messageCounter = 0;
			$chatkey 		= explode('_', $chatId);
			if(!empty($chatId) && !empty($receiverId) &&  $chatType == '0'){
				$postId 		= $chatkey[0];
				$senderId 		= $chatkey[1];
				$where 			 = " receiver_id =".$receiverId; 
				$where 			.= " AND sender_id =".$senderId; 
				$where 			.= " AND post_id =".$postId; 
				$where 			.= " AND chat_type ='0'"; 
				$where 			.= " AND message_status ='0'"; 
				$fetchResults 	= $this->guppyModel->getData('id','wpguppy_message',$where );
				if(!empty($fetchResults)){
					foreach($fetchResults as $result){
						$this->guppyModel->updateData('wpguppy_message',array('message_status' => '1', 'message_seen_time' => date('Y-m-d H:i:s')), array('id' => $result['id']));
						$messageIds[$result['id']] = true;
						$messageCounter++;
					}
				}
				$chatId = $this->getChatKey('0',$postId, $receiverId); 
				if($this->pusher){
					$batchRequests = array();
					$pusherData = array(
						'chatId' 			=> $chatId,
						'chatType' 			=> $chatType,
						'messageIds' 		=> $messageIds,
						'isSender'			=> true,
						'messageCounter'	=> $messageCounter,
					);
					$batchRequests[] = array(
						'channel' 	=> 'private-user-' . $senderId,
						'name' 		=> 'updateMessage',
						'data'		=> $pusherData,
					);
					$pusherData = array(
						'chatId' 			=> $chatId,
						'chatType' 			=> $chatType,
						'messageIds' 		=> $messageIds,
						'isSender'			=> false,
						'senderId'			=> $senderId,
						'messageCounter'	=> $messageCounter,
					);
					$batchRequests[] = array(
						'channel' 	=> 'private-user-' . $receiverId,
						'name' 		=> 'updateMessage',
						'data'		=> $pusherData,
					);
					$this->pusher->triggerBatch($batchRequests);
				}
				$json['senderId'] 		= $senderId;
			}elseif(!empty($chatId) && !empty($receiverId) &&  $chatType == '1'){
				$senderId 		= $chatkey[0];
				$where 			 = " receiver_id =".$receiverId; 
				$where 			.= " AND sender_id =".$senderId; 
				$where 			.= " AND chat_type =".$chatType; 
				$where 			.= " AND message_status ='0'"; 
				$fetchResults 	= $this->guppyModel->getData('id','wpguppy_message',$where );
				
				if(!empty($fetchResults)){
					foreach($fetchResults as $result){
						$this->guppyModel->updateData('wpguppy_message',array('message_status' => '1', 'message_seen_time' => date('Y-m-d H:i:s')), array('id' => $result['id']));
						$messageIds[$result['id']] = true;
						$messageCounter++;
					}
				}
				$chatId = $this->getChatKey('1', $receiverId);
				if($this->pusher){
					$batchRequests = array();
					$pusherData = array(
						'chatId' 			=> $chatId,
						'chatType' 			=> $chatType,
						'messageIds' 		=> $messageIds,
						'isSender'			=> true,
						'messageCounter'	=> $messageCounter,
					);
					$batchRequests[] = array(
						'channel' 	=> 'private-user-' . $senderId,
						'name' 		=> 'updateMessage',
						'data'		=> $pusherData,
					);
					$pusherData = array(
						'chatId' 			=> $chatId,
						'chatType' 			=> $chatType,
						'messageIds' 		=> $messageIds,
						'isSender'			=> false,
						'senderId'			=> $senderId,
						'messageCounter'	=> $messageCounter,
					);
					$batchRequests[] = array(
						'channel' 	=> 'private-user-' . $receiverId,
						'name' 		=> 'updateMessage',
						'data'		=> $pusherData,
					);
					$this->pusher->triggerBatch($batchRequests);
				}
				$json['senderId'] 		= $senderId;	
			}elseif($chatType== '2' && !empty($chatId)){
				$chatId = $chatkey[0];
				$where 		 	 = " group_id		=". $chatId; 
				$where 		 	.= " AND member_id	=". $receiverId; 
				
				$memberVerify 	= $this->guppyModel->getData('id,member_status', 'wpguppy_group_member', $where );
				if(!$memberVerify){
					$json['type'] 			= 'error';
					$json['message_desc']   = esc_html__('You are not allowed to perform this action!', 'wp-guppy');
					return new WP_REST_Response($json , 203);
				}
				// get group members
				$where 			= '';
				$where 		 	= " group_id =". $chatId." AND member_status ='1'"; 
				$groupMembers 	= $this->guppyModel->getData('member_id,group_role,member_status', 'wpguppy_group_member', $where );

				// get group  members chat actions
				$statusActions = array();	
				$filterData['actionBy'] 	= $receiverId;
				$filterData['chatType'] 	= $chatType;
				$filterData['groupId'] 		= $chatId;
				$filterData['actionType'] 	= array('3','4'); // group left or removed from group
				
				$chatActions = $this->getGuppyChatAction($filterData);

				$exitGroupTime =  '';
				if(!empty($chatActions)){
					foreach($chatActions as $action){
						
						$statusActions[] = array(
							'statusActionTime' 		=> $action['action_time'],
							'statusUpdatedTime' 	=> $action['action_updated_time'],
						);
						if($memberVerify[0]['member_status'] == '2' || $memberVerify[0]['member_status'] == '0'){
							if(strtotime($action['action_time']) >= strtotime($exitGroupTime)){
								$exitGroupTime = $action['action_time'];
							}
						}
					}
					$filterData['groupAction'] = array();
					if($exitGroupTime!=''){
						$filterData['groupAction']['exitGroupTime'] = $exitGroupTime;
					}
					$filterData['groupAction']['status'] = $statusActions;
				}
				
				// get group unseen messages
				$where 		 	 = " group_id			=". $chatId; 
				$where 		 	.= " AND chat_type		=". $chatType; 
				$where 		 	.= " AND message_status	= '0'"; 
				$where 		 	.= " AND message_type 	<> '4'"; 
				if(!empty($filterData['groupAction'])){
					if(!empty($filterData['groupAction']['exitGroupTime'])){
						$where .=" AND message_sent_time <'".$filterData['groupAction']['exitGroupTime']."'";
					}
					foreach($filterData['groupAction']['status'] as $action){
						$where .=" AND (message_sent_time NOT BETWEEN '".$action['statusActionTime']."'  AND  '".$action['statusUpdatedTime']."')";
					}
				}
				$chatDetail 	= $this->guppyModel->getData('id, sender_id, group_msg_seen_id', 'wpguppy_message', $where );

				if(!empty($chatDetail)){
					foreach($chatDetail as $row){
						$groupMsgSeenIds =  !empty($row['group_msg_seen_id']) ? $row['group_msg_seen_id'] : array();
						$senderId 		 =  $row['sender_id'];
						if($senderId != $receiverId){
							$messageSenders[$senderId][$row['id']] = array();
							if(empty($groupMsgSeenIds)){
								$groupMsgSeenIds[] = $receiverId;
								$messageSenders[$senderId][$row['id']]['seen'] = false;
								$messageCounter++;
							}else{
								$groupMsgSeenIds 	= unserialize($row['group_msg_seen_id']);
								if(!in_array($receiverId, $groupMsgSeenIds)){
									$groupMsgSeenIds[] = $receiverId;
									$messageSenders[$senderId][$row['id']]['seen'] = false;
									$messageCounter++;
								}
							}
							$messageStatus = 1;
							if(!empty($groupMembers)){
								foreach($groupMembers as $member){
									if(!in_array($member['member_id'], $groupMsgSeenIds)
										&& $member['member_id'] != $senderId){
										$messageStatus = 0;
										break;
									}
								}
							}
							$messageSenders[$senderId][$row['id']]['seenids'] = $groupMsgSeenIds;
							$json['messageSeenIds'] =  $groupMsgSeenIds;
							$updateData = array();
							$updateData['group_msg_seen_id'] = serialize($groupMsgSeenIds);
							if($messageStatus){
								$updateData['message_status'] = 1;
								$messageSenders[$senderId][$row['id']]['seen'] = true;
							}
							$this->guppyModel->updateData( 'wpguppy_message', $updateData, array('id' => $row['id']));
						}
					}
					$chatId = $this->getChatKey('2', $chatId);
					if($this->pusher){
						$batchRequests = array();
						if(!empty($messageSenders)){
							$pusherData = array(
								'chatId' 			=> $chatId,
								'chatType' 			=> $chatType,
								'isSender'			=> true,
								'messageCounter'	=> $messageCounter,
							);
							foreach($messageSenders as $sender => $value){
								$pusherData['detail'] = $value;
								$batchRequests[] = array(
									'channel' 	=> 'private-user-' . $sender,
									'name' 		=> 'updateMessage',
									'data'		=> $pusherData,
								);
							}
							$pusherData = array(
								'chatId' 			=> $chatId,
								'chatType' 			=> $chatType,
								'isSender'			=> false,
								'messageCounter'	=> $messageCounter,
							);
							$batchRequests[] = array(
								'channel' 	=> 'private-user-' . $receiverId,
								'name' 		=> 'updateMessage',
								'data'		=> $pusherData,
							);
							$this->pusher->triggerBatch($batchRequests);
						}
						
					}
				}
			}
			
			$type = 'success';
			$json['type'] 				= $type;
			$json['messageIds'] 		= $messageIds;
			$json['messageCounter'] 	= $messageCounter;
			$json['messageSenders'] 	= $messageSenders;
			$json['chatId'] 			= $chatId;
			$json['chatType'] 			= $chatType;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * download guppy Message
		 *
		 * @since    1.0.0
		*/
		public function downloadGuppyAttachments( $data ){
			$headers    = $data->get_headers();
			$params     = ! empty($data->get_params()) 		? $data->get_params() 	: '';
			$authToken  = ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$json 		= array();
			$response = $this->guppyAuthentication($params, $authToken);
			
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}

			$type 			= 'error';
			$downloadUrl 	= '';
			$filename 		= '';
			$messageId 		= !empty($params['messageId']) 	? intval($params['messageId']) : 0; 
			$loginedUser 	= !empty($params['userId']) 	? intval($params['userId']) : 0; 
			$actionTo 		= !empty($params['actionTo']) 	? intval($params['actionTo']) : 0; 
			$groupId 		= !empty($params['groupId']) 	? intval($params['groupId']) : 0; 
			$postId 		= !empty($params['postId']) 	? intval($params['postId']) : 0; 

			if(!empty($messageId)){
				$where 			= "id =".$messageId; 
				$where 			.= " AND message_status <> '2' "; 
				$fetchResults 	= $this->guppyModel->getData('*','wpguppy_message',$where );
				if(!empty($fetchResults)){
					$attachmentData = !empty($fetchResults[0]['attachments']) ? unserialize($fetchResults[0]['attachments']) : array();
					if(!empty($attachmentData)){
						$attachments = $attachmentData['attachments'];
						$zip = new ZipArchive();
                    	$uploadspath = wp_upload_dir();
						$folderRalativePath = $uploadspath['baseurl'] . "/download-temp";
						$folderAbsolutePath = $uploadspath['basedir'] . "/download-temp";
                    	wp_mkdir_p($folderAbsolutePath);
						$filename = round(microtime(true)) . '.zip';
						$zip_name = $folderAbsolutePath . '/' . $filename;
						$zip->open($zip_name, ZipArchive::CREATE);
						$downloadUrl = $folderRalativePath . '/' . $filename;
						foreach ($attachments as $file) {
							$response         	= wp_remote_get($file['file']);
							$filedata        	= wp_remote_retrieve_body($response);
							$zip->addFromString(basename($file['file']), $filedata);
							
						}
						$zip->close();
						$type = 'success';
					}

				}	
			}else{
				$filterData = array();
				$filterData['userId'] 	= $actionTo;
				$filterData['groupId'] 	= $groupId;
				$filterData['postId'] 	= $postId;
				$chatMedia = $this->getChatMedia($loginedUser, $filterData);
				if(!empty($chatMedia)){
					$zip = new ZipArchive();
					$uploadspath = wp_upload_dir();
					$folderRalativePath = $uploadspath['baseurl'] . "/download-temp";
					$folderAbsolutePath = $uploadspath['basedir'] . "/download-temp";
					wp_mkdir_p($folderAbsolutePath);
					$filename = round(microtime(true)) . '.zip';
					$zip_name = $folderAbsolutePath . '/' . $filename;
					$zip->open($zip_name, ZipArchive::CREATE);
					$downloadUrl = $folderRalativePath . '/' . $filename;
					foreach($chatMedia as $file){	
						$response         	= wp_remote_get($file['file']);
						$filedata        	= wp_remote_retrieve_body($response);
						$zip->addFromString(basename($file['file']), $filedata);	
					}
					$zip->close();
					$type = 'success';
				}
			}

			$json['type'] 			= $type;
			$json['fileName'] 		= $filename;
			$json['downloadUrl'] 	= $downloadUrl;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * Load  guppy contact list
		 *
		 * @since    1.0.0
		*/
		public function getGuppyContactList($data){
			
			$headers    = $data->get_headers();
			$params     = ! empty($data->get_params()) 			? $data->get_params() 		: '';
			$authToken  = ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$json       = array();

			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			
			$offset 		= !empty($params['offset']) ? 		intval($params['offset']) : 0; 
			$searchQuery 	= !empty($params['search']) 		? sanitize_text_field($params['search']) : ''; 
			$friendStatus 	= !empty($params['friendStatus']) 	? intval($params['friendStatus']) : ''; 
			$loginedUser 	= !empty($params['userId']) 		? intval($params['userId']) : 0;
			$fetchResults 	= $this->guppyModel->getGuppyContactList($this->showRec, $offset, $searchQuery, $loginedUser, $friendStatus);
			$guppyUsers 	= array();
			if ( !empty( $fetchResults )) {
				foreach( $fetchResults as $result ) {
					if($result['send_by'] == $loginedUser){
						$friendId = intval( $result['send_to'] );
					}else{
						$friendId = intval( $result['send_by'] );
					}
					$userData 	= $this->getUserInfo('1', $friendId);
					$userAvatar = $userData['userAvatar'];
					$userName 	= $userData['userName'];

					$chatNotify = array();
					$chatNotify['actionBy'] 	= $loginedUser;
					$chatNotify['actionType'] 	= '2';
					$chatNotify['userId'] 		= $friendId;
					$chatNotify['chatType'] 	= 1;
					$muteNotification = $this->getGuppyChatAction($chatNotify);
					if(!empty($muteNotification)){
						$muteNotification = true;
					}else{
						$muteNotification = false;
					}
					
					$userStatus = $this->getUserStatus($loginedUser, $friendId, '1');
					$key 			= $this->getChatKey('1', $friendId);
					$guppyUsers[$key] = array(
						'chatId' 			=> $key,
						'muteNotification'	=> $muteNotification,
						'friendStatus' 		=> $friendStatus,
						'userName' 	   		=> $userName,
						'userAvatar' 		=> $userAvatar,
						'blockedId' 		=> $userStatus['blockedId'],
						'isOnline' 			=> $userStatus['isOnline'],
						'isBlocked' 		=> $userStatus['isBlocked'],
					);
				}
			}
			$json['type'] 		= 'success'; 
			$json['contacts']  	= (Object)$guppyUsers;
			
			return new WP_REST_Response($json , 200);
		}

		/**
		 * Get user status
		 *
		 * @since    1.0.0
		*/
		public function getUserStatus($loginedUser=0, $userId=0, $chatType = false, $postId=false){
			$isOnline = $isBlocked = $blockedId = false;
			$userType = '0';
			if(get_userdata($loginedUser)){
				$userType = '1';
				$isOnline 		= wpguppy_UserOnline($userId);
				if(!empty($userId) && $chatType == '1'){
					$fetchResults 	= $this->guppyModel->getGuppyFriend($userId,$loginedUser,false);
					if(!empty($fetchResults) && $fetchResults['friend_status']=='3'){
						$isBlocked = true;
						$blockedId = $fetchResults['send_by'];
					}
				}elseif(!empty($userId) && $chatType == '0' && !empty($postId)){
					$where 		 	= "((action_by=". $loginedUser." OR action_to =".$loginedUser.") AND (action_by=". $userId." OR action_to =".$userId.")) AND action_type = 1 AND post_id=".$postId; 
					$chatAction 	= $this->guppyModel->getData('action_by,action_to','wpguppy_postchat_action',$where );
					if(!empty($chatAction)){
						$isBlocked = true;
						$blockedId = $chatAction[0]['action_to'];
					}else{
						$where 		 	= "((action_by=". $loginedUser." OR action_to =".$loginedUser.") AND (action_by=". $userId." OR action_to =".$userId.")) AND action_type = 0"; 
						$chatAction 	= $this->guppyModel->getData('action_by,action_to','wpguppy_postchat_action',$where );
						if(!empty($chatAction)){
							$isBlocked = true;
							$blockedId = $chatAction[0]['action_to'];
						}
					}
				}
			}
			return array(
				'isOnline' 	=> $isOnline,
				'isBlocked' => $isBlocked,
				'blockedId' => $blockedId,
				'userType' 	=> $userType,
			);
		}

		/**
		 * Load user messages
		 *
		 * @since    1.0.0
		*/
		public function getUserMessageslist($data){
			$headers    = $data->get_headers();
			$params     = ! empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  = ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$guppyMessageList  = $json  = array();
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$offset 		= !empty($params['offset']) ? intval($params['offset']) : 0; 
			$searchQuery 	= !empty($params['search']) ? sanitize_text_field($params['search']) : '';
			$loginedUser 	= !empty($params['userId']) ? intval($params['userId']) : 0;
			$chatType 		= !empty($params['chatType']) ? ($params['chatType']) : '1';
			$fetchResults   = $this->guppyModel->getUserMessageslist($loginedUser, $this->showRec, $offset, $searchQuery, $chatType);
			if(!empty($fetchResults)){
				foreach($fetchResults as $result){

					$messageData = array();
					if($result['sender_id'] != $loginedUser){
						$receiverId = $result['sender_id'];
					}else{
						$receiverId = $result['receiver_id']; 
					}
					$message 						= $result['message'];
					$messageType 					= $result['message_type'];
					$timestamp 						= $result['timestamp'];
					$clearChat 						= false;
					$unreadCount					= 0;
					$filterData =  array();
					$filterData['chatType'] = $result['chat_type'];
					$userData 	= $this->getUserInfo('1', !empty($result['group_id']) ? $result['sender_id'] : $receiverId);
					if(!empty($result['group_id'])){
						
						$groupDetail = $this->guppyModel->getGroupDetail($result['group_id']);
						$messageData['groupTitle'] 		= $result['group_title'];
						$messageData['groupDetail']		= NULL;
						$messageData['groupImage'] 		= '';
						$memberInfo 					= $groupDetail['memberAvatars'];
						$memberDisable = $userDisableReply = false;
						
						if($memberInfo[$loginedUser]['memberStatus'] == '2'){
							$memberDisable = true;
							$message = array('type' => 3, 'memberIds' => array($loginedUser));
							$messageType = '4';	
							$timestamp = false;	
							$messageData['groupImage'] 		= WPGuppy_GlobalSettings::get_plugin_url().'public/images/group.jpg';
						}elseif($memberInfo[$loginedUser]['memberStatus'] == '0'){
							$memberDisable = true;
							$message 	= array('type' => 4, 'memberIds' => array($loginedUser));
							$messageType = '4';
							$timestamp = false;
							$messageData['groupImage'] 		= WPGuppy_GlobalSettings::get_plugin_url().'public/images/group.jpg';
						}else{
							if(!empty($result['group_image'])){
								$messageData['groupImage'] = $result['group_image'];
							}
							$messageData['groupDetail']	= $groupDetail;	
							$messageData['userName'] 	= $userData['userName'];
							$messageData['userAvatar'] 	= $userData['userAvatar'];
						} 
						
						if($result['disable_reply'] == '1' && $memberInfo[$loginedUser]['groupRole'] == '0'){
							$userDisableReply = true;
						}
						$messageData['memberDisable'] 		= $memberDisable;
						$messageData['userDisableReply']  	= $userDisableReply;

						if($messageType == '4'){
							$message = is_serialized($message) ? unserialize($message) : $message;
							$membersUpdate = array();
							if(in_array($message['type'], array('2','3','4','5'))){
								foreach($message['memberIds'] as $single){
									$memberName = !empty($memberInfo[$single]['userName']) ? $memberInfo[$single]['userName'] : '';
									$membersUpdate[$single] = $memberName;
								}
								$messageData['membersUpdate'] = $membersUpdate;
							}
						}
						$filterData['groupId'] 			= $result['group_id'];
						$filterData['senderId'] 		= $loginedUser;
						$filterData['memberAddedDate'] 	= $memberInfo[$loginedUser]['memberAddedDate'];
						// get group member chat actions
						$statusActions = $params = array();	
						$params['actionBy'] 	= $loginedUser;
						$params['chatType'] 	= $result['chat_type'];
						$params['groupId'] 		= $result['group_id'];
						$params['actionType'] 	= array('3','4'); // group left or removed from group
						$chatActions = $this->getGuppyChatAction($params);
						$filterData['groupAction'] = array();
						$exitGroupTime = '';
						if(!empty($chatActions)){
							foreach($chatActions as $action){

								$statusActions[] = array(
									'statusActionTime' 		=> $action['action_time'],
									'statusUpdatedTime' 	=> $action['action_updated_time'],
								);
								if($memberInfo[$loginedUser]['memberStatus'] == '2' || $memberInfo[$loginedUser]['memberStatus'] == '0'){
									if(strtotime($action['action_time']) >= strtotime($exitGroupTime)){
										$exitGroupTime = $action['action_time'];
									}
								}
							}
							if($exitGroupTime!=''){
								$filterData['groupAction']['exitGroupTime'] = $exitGroupTime;
							}
							$filterData['groupAction']['status'] = $statusActions;
						}
					}else{
						$userStatus = $this->getUserStatus($loginedUser, $receiverId, '1');
						$messageData['blockedId'] 		= $userStatus['blockedId'];
						$messageData['isOnline'] 		= $userStatus['isOnline'];
						$messageData['isBlocked'] 		= $userStatus['isBlocked'];
						$messageData['userAvatar']		= $userData['userAvatar'];	
						$messageData['userName'] 		= $userData['userName'];
						$filterData['senderId'] 		= $receiverId;
						$filterData['receiverId'] 		= $loginedUser;	
					}
					$unreadCount = $this->guppyModel->getUnreadCount($filterData);
					if($result['message_status'] == 2 ){
						$message = '';
					}
					$isSender = true;
					if($result['sender_id'] != $loginedUser){
						$isSender= false; 
					}

					// check chat is cleard or not
					$chatClearTime  = '';
					$filterData = array();
					$filterData['actionBy'] 	= $loginedUser;
					if(!empty($result['group_id'])){
						$filterData['groupId'] 		= $result['group_id'];
						$chatType = 2;
					}else{
						$filterData['userId'] 		= $receiverId;
						$chatType = 1;
					}
					$filterData['actionType'] 	= '0';
					$filterData['chatType']     = $chatType;

					$chatActions = $this->getGuppyChatAction($filterData);
					if(!empty($chatActions)){
						$chatClearTime = $chatActions['chatClearTime'];
					}

					$chatNotify = array();
					$chatNotify['actionBy'] 	= $loginedUser;
					$chatNotify['actionType'] 	= '2';
					$chatNotify['userId'] 		= $receiverId;
					$chatNotify['chatType'] 	= $chatType;
					$chatNotify['groupId'] 		= $result['group_id'];

					$muteNotification = $this->getGuppyChatAction($chatNotify);
					if(!empty($muteNotification)){
						$muteNotification = true;
					}else{
						$muteNotification = false;
					}
					if(!empty($chatClearTime) && strtotime($chatClearTime) > strtotime($result['message_sent_time'])){
						$clearChat 	= true;
						$message 	= '';
					}

					$chatId = $receiverId;
					if(!empty($result['group_id'])){
						$chatId = $result['group_id'];
					}
					if($message!=''){
						if($messageType == '0'){
							$message = html_entity_decode( stripslashes($message),ENT_QUOTES );
						}elseif($messageType == '2' || $messageType == '4'){
							$message = is_serialized($message) ? unserialize($message) : $message;
						}
					}
					$key 								= $this->getChatKey($result['chat_type'], $chatId);
					$messageData['chatId']				= $key;
					$messageData['isSender'] 			= $isSender;
					$messageData['message'] 	   		= $message;
					$messageData['messageType'] 		= $messageType;
					$messageData['clearChat'] 			= $clearChat;
					$messageData['messageStatus'] 		= $result['message_status'];
					$messageData['chatType'] 			= intval($result['chat_type']);
					$messageData['UnreadCount'] 		= intval( $unreadCount );
					$messageData['timeStamp'] 			= $timestamp;
					$messageData['muteNotification']	= $muteNotification;
					$messageData['messageId']			= Intval($result['id']);
					$guppyMessageList[$key] 			= $messageData;
				}
			}
			
			$json['type'] 				= 'success';
			$json['guppyMessageList']   = (Object)$guppyMessageList;
			return new WP_REST_Response($json , 200);		 
		}

		/**
		 * get user chat
		 *
		 * @since    1.0.0
		*/

		public  function getGuppyChat($data){
			
			$headers    	= $data->get_headers();
			$params     	= ! empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  	= ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$chatMessages  	= $json  = $memberInfo = $filterData = array();
			$memberBlocked  = $userDisableReply = false;
			
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}

			$offset 		= !empty($params['offset']) 	? intval($params['offset']) 	: 0; 
			$receiverId 	= !empty($params['receiverId']) ? intval($params['receiverId']) : 0;
			$chatType 		= !empty($params['chatType']) 	? intval($params['chatType']) 	: 0;
			$groupId 		= !empty($params['groupId']) 	? intval($params['groupId']) 	: 0;
			$postId 		= !empty($params['postId']) 	? intval($params['postId']) 	: 0;
			$loginedUser 	= !empty($params['userId']) 	? intval($params['userId']) 	: 0;
			
			$filterData = array();
			$filterData['actionBy'] 	= $loginedUser;
			$filterData['chatType'] 	= $chatType;
			$filterData['groupId'] 		= $groupId;
			$filterData['userId'] 		= $receiverId;
			$filterData['postId'] 		= $postId;

			if($chatType == '2' && !empty($groupId)){
				$where 		 	 = "group_id =". $groupId. " AND member_id	=". $loginedUser; 
				$memberVerify 	= $this->guppyModel->getData('id,member_status', 'wpguppy_group_member', $where );
				if(!$memberVerify){
					$json['type'] 			= 'error';
					$json['message_desc']   = esc_html__('You are not allowed to perform this action!', 'wp-guppy');
					return new WP_REST_Response($json , 203);
				}
				// get group members
				$where 			= '';
				$where 		 	= " group_id =". $groupId; 
				$groupMembers 	= $this->guppyModel->getData('member_id,group_role,member_status', 'wpguppy_group_member', $where );

				// get group  member chat actions
				$statusActions = array();	
				$filterData['orderBy'] 	= 'action_type'; 
				$filterData['actionType'] 	= array('3','4','5'); // group left or removed from group
				
				$chatActions = $this->getGuppyChatAction($filterData);
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
							if($memberVerify[0]['member_status'] == '2' || $memberVerify[0]['member_status'] == '0'){
								if(strtotime($action['action_time']) >= strtotime($exitGroupTime)){
									$exitGroupTime = $action['action_time'];
								}
							}
						}
					}
					$filterData['groupAction'] = array();
					if($exitGroupTime!=''){
						$filterData['groupAction']['exitGroupTime'] = $exitGroupTime;
					}
					if($deleteGroupTime!=''){
						$filterData['groupAction']['deleteGroupTime'] = $deleteGroupTime;
					}
					$filterData['groupAction']['status'] = $statusActions;
				}
				// group members info
				if(!empty($groupMembers)){
					$userName = $userAvatar = '';
					foreach($groupMembers as $member){
						$userData 	= $this->getUserInfo('1', $member['member_id']);
						$userAvatar = $userData['userAvatar'];
						$userName 	= $userData['userName'];
						$memberInfo[$member['member_id']] = array(
							'groupRole' 	=> $member['group_role'],
							'memberStatus'  => $member['member_status'],
							'userName' 		=> $userName,
							'userAvatar' 	=> $userAvatar,
						);
					}
				}
			}

			// add filter to check clear chat 
			if($chatType == '0' && !empty($postId)){
				$where 		 	= " (action_by =". $loginedUser." AND action_to =". $receiverId.") AND action_type=3 AND post_id=".$postId; 
				$chatActions 	= $this->guppyModel->getData('action_time', 'wpguppy_postchat_action', $where );
				if(!empty($chatActions)){
					$filterData['chatClearTime'] = $chatActions[0]['action_time'];
				}
			}else{
				$filterData['actionType'] 	= '0';
				$chatActions = $this->getGuppyChatAction($filterData);
				if(!empty($chatActions)){
					$chatClearTime = $chatActions['chatClearTime'];
					$filterData['chatClearTime'] = $chatClearTime;
				}
			}
			
			// add filter for  pagination
			$filterData['limit'] 		= $this->showRec;
			$filterData['offset'] 		= $offset;
			$fetchResults = $this->guppyModel->getGuppyChat($filterData);
			
			if(!empty($fetchResults)){
				$userName = $userAvatar  = '';
				$userData 	= $this->getUserInfo('1', $receiverId);
				if(!empty($userData)){
					$userName 	= $userData['userName'];
					$userAvatar = $userData['userAvatar'];
				}
				foreach($fetchResults as $result){
					$message =  '';
					$messageData = $attachmentsData = array();
					$isSender = true;
					if($result['sender_id'] != $loginedUser){
						$senderId = $result['sender_id'];
						$isSender= false; 
					}else{
						$senderId = $result['receiver_id'];
					}

					$messageData['messageId'] 	= $result['id'];
					$messageData['isSender'] 	= $isSender;
					$messageData['userAvatar'] 	= $userAvatar;
					$messageData['userName'] 	= $userName;

					if(!empty($result['group_id']) && $result['chat_type'] == '2'){
						if(!empty($memberInfo[$loginedUser])){
							$memberDisabled = false;
							if($memberInfo[$loginedUser]['memberStatus'] == '2' || $memberInfo[$loginedUser]['memberStatus'] == '0'){
								$memberDisabled = true;
							} 
							if(($result['disable_reply'] == '1' && $memberInfo[$loginedUser]['groupRole'] == '0') || $memberDisabled){
								$userDisableReply = true;
							}
						}
						$messageData['userAvatar'] 	= $userAvatar;
						$messageData['userName'] 	= $userName;
						if(!empty($memberInfo[$result['sender_id']])){
							$messageData['userAvatar'] 	= $memberInfo[$result['sender_id']]['userAvatar'];
							$messageData['userName'] 	= $memberInfo[$result['sender_id']]['userName'];
						}
						$messageData['messageSeenIds'] = array();
						if(!empty($result['group_msg_seen_id'])){
							$messageData['messageSeenIds'] = unserialize($result['group_msg_seen_id']);
						}
					}

					if($result['message_type'] == '0'){
						$message = html_entity_decode( stripslashes($result['message']),ENT_QUOTES );
					}elseif($result['message_type'] == '1' || $result['message_type'] == '3'){
						$attachmentsData 	= unserialize($result['attachments']);
					}elseif($result['message_type'] == '2'){
						$message = unserialize($result['message']);
					}elseif($result['message_type'] == '4'){
						$message = unserialize($result['message']);
						if($result['chat_type'] == '2' && !empty($result['group_id'])){
							$membersUpdate = array();
							if(in_array($message['type'], array('2','3','4','5'))){
								foreach($message['memberIds'] as $single){
									$memberName = !empty($memberInfo[$single]['userName']) ? $memberInfo[$single]['userName'] : '';
									$membersUpdate[$single] = $memberName;
									
								}
								$messageData['membersUpdate'] = $membersUpdate;
							}
						}	
					}

					$messageData['message'] 			= ($result['message_status'] !='2' ? $message : false);
					$messageData['attachmentsData'] 	= $attachmentsData;
					$messageData['replyMessage'] 		= !empty($result['reply_message']) ? unserialize($result['reply_message']) : NULL;
					$messageData['chatType'] 			= $result['chat_type'];
					$messageData['messageType'] 		= $result['message_type'];
					$messageData['messageStatus'] 		= $result['message_status'];
					$messageData['timeStamp'] 			= $result['timestamp'];

					$chatMessages[] = $messageData;
				}
			}

			$mediaAttachments = array();
			if($offset=='0'){
				if($chatType == '0'){
					$where 		 	= " (action_by =". $loginedUser." AND action_to =". $receiverId.") AND action_type=2 AND post_id=".$postId; 
					$chatNotify 	= $this->guppyModel->getData('id', 'wpguppy_postchat_action', $where );					
					if(!empty($chatNotify)){
						$muteNotification = true;
					}else{
						$muteNotification = false;
					}
				}else{
					$filterData['actionType'] 	= '2';
					$muteNotification = $this->getGuppyChatAction($filterData);
				}
				if(!empty($muteNotification)){
					$json['muteNotfication'] = true;
				}else{
					$json['muteNotfication'] = false;
				}
				$mediaAttachments = $this->getChatMedia($loginedUser, $filterData);
			}

			if($chatType== '0'){
				$chatId = $this->getChatKey('0', $postId, $receiverId);
			}elseif($chatType== '1'){
				$chatId = $receiverId;
				$userStatus = $this->getUserStatus($loginedUser, $chatId, '1');
				$json['isOnline'] 		= $userStatus['isOnline'];
				$json['isBlocked'] 		= $userStatus['isBlocked'];
				$json['blockedId'] 		= $userStatus['blockedId'];
				$json['userType']   	= $userStatus['userType'];
				$chatId = $this->getChatKey('1', $chatId);
			}elseif($chatType== '2' && !empty($groupId)){
				$chatId = $this->getChatKey('2', $groupId);
				$json['userDisableReply'] 		= $userDisableReply;
				$json['isBlocked']    			= $memberBlocked;
				$json['userType']     			= '1';
			}
			$json['type'] 				= 'success';
			$json['chatId']   			= $chatId;
			$json['chatType']   		= $chatType;
			$json['chatMessages']   	= $chatMessages;
			$json['mediaAttachments']   = $mediaAttachments;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * load Media Attachments
		 *
		 * @since    1.0.0
		*/
		public function loadMediaAttachments($data){
			$headers    	= $data->get_headers();
			$params     	= !empty($data->get_params()) 		? $data->get_params() 		: '';
			$files     		= !empty($data->get_file_params()) ? $data->get_file_params() 	: '';
			$authToken  	= ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$mediaAttachments = $json  = $filterData = array();
			
			$response = $this->guppyAuthentication($params, $authToken);

			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$offset 		= !empty($params['offset']) 	? intval($params['offset']) : 0; 
			$actionTo 		= !empty($params['actionTo']) ? intval($params['actionTo']) : 0;
			$chatType 		= !empty($params['chatType']) 	? intval($params['chatType']) : 0;
			$groupId 		= !empty($params['groupId']) 	? intval($params['groupId']) : 0;
			$postId 		= !empty($params['postId']) 	? intval($params['postId']) : 0;
			$loginedUser 	= !empty($params['userId']) 	? intval($params['userId']) : 0;
			
			$filterData['limit'] 	= $this->showRec;
			$filterData['offset'] 	= $offset;
			$filterData['userId'] 	= $actionTo;
			$filterData['chatType'] = $chatType;
			$filterData['groupId'] 	= $groupId;
			$filterData['postId'] 	= $postId;

			if(!empty($groupId) && $chatType == '2'){
				// get group members
				$where 		 	 = "group_id =". $groupId. " AND member_id	=". $loginedUser; 
				$member 	= $this->guppyModel->getData('id, member_status', 'wpguppy_group_member', $where );
				// get group  member chat actions
				$statusActions = array();	
				$filterData['actionBy'] 	= $loginedUser;
				$filterData['orderBy'] 		= 'action_type';
				$filterData['actionType'] 	= array('3','4','5'); // group left or removed from group
				
				$chatActions = $this->getGuppyChatAction($filterData);
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
							if($member[0]['member_status'] == '2' || $member[0]['member_status'] == '0'){
								if(strtotime($action['action_time']) >= strtotime($exitGroupTime)){
									$exitGroupTime = $action['action_time'];
								}
							}
						}
					}
					$filterData['groupAction'] = array();
					if($exitGroupTime!=''){
						$filterData['groupAction']['exitGroupTime'] = $exitGroupTime;
					}
					if($deleteGroupTime!=''){
						$filterData['groupAction']['deleteGroupTime'] = $deleteGroupTime;
					}
					$filterData['groupAction']['status'] = $statusActions;
				}
			}
			$mediaAttachments = $this->getChatMedia($loginedUser, $filterData);
			$json['type'] 				= 'success';
			$json['mediaAttachments'] 	= $mediaAttachments;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * get Chat Media
		 *
		 * @since    1.0.0
		*/
		public function getChatMedia($loginedUser, $filterData){
			$mediaAttachments = array();
			$chatMedia = $this->guppyModel->getChatMedia($loginedUser, $filterData);
			if(!empty($chatMedia)){
				foreach($chatMedia as $single){
					$media_data = unserialize($single['attachments']);
					foreach($media_data['attachments'] as $attach){
						$mediaAttachments[] = array(
							'type' 		=> $media_data['attachmentType'],
							'fileName' 	=> $attach['fileName'],
							'file' 		=> $attach['file'],
							'thumbnail' => $attach['thumbnail'],
						);
					}
				}
			}
			return $mediaAttachments;
		}


		/**
		 * get post Chat Actions 
		 *
		 * @since    1.0.0
		*/
		public function getGuppyPostChatAction($filterData){
			$where 		= " (action_by =". $filterData['actionBy']." AND action_to =". $filterData['actionTo'].") AND action_type=".$filterData['actionType']." AND post_id=".$filterData['postId']; 
			$result 	= $this->guppyModel->getData('id', 'wpguppy_postchat_action', $where );
			return $result;
		}
		/**
		 * get Chat Actions 
		 *
		 * @since    1.0.0
		*/
		public function getGuppyChatAction($filterData){
			
			$result = array();
			
			$where 	= "action_by=".$filterData['actionBy']; 
			if(is_array($filterData['actionType'])){
				$actionType = implode(',', $filterData['actionType']);
				$where .= " AND action_type IN(".$actionType.")";
			}else{
				$where .= " AND action_type=".$filterData['actionType'];
			}
			if(!empty($filterData['groupId']) && $filterData['chatType'] == '2'){
				$where .= " AND corresponding_id=". $filterData['groupId'];
				$where .= " AND chat_type=". $filterData['chatType'];
			}elseif(!empty($filterData['userId']) && $filterData['chatType'] == '1'){
				$where .= " AND corresponding_id=". $filterData['userId'];
				$where .= " AND chat_type=". $filterData['chatType'];
			}
			if(!empty($filterData['orderBy'])){	
				$where .= " ORDER BY ". $filterData['orderBy']." DESC";
			}
			$chatActions = $this->guppyModel->getData('*','wpguppy_chat_action',$where );
			
			if(!empty($chatActions)){
				if($filterData['actionType']=='0'){
					$result = array(
						'chatActionId' 				=> $chatActions[0]['id'],
						'chatActionType' 			=> $chatActions[0]['action_type'],
						'chatClearTime' 			=> $chatActions[0]['action_time']
					);
				}elseif($filterData['actionType']=='1' || $filterData['actionType']=='2'){
					$result = array(
						'chatActionId' 				=> $chatActions[0]['id'],
						'chatActionType' 			=> $chatActions[0]['action_type'],
						'muteActionTime' 			=> $chatActions[0]['action_time']
					);
				} elseif(is_array($filterData['actionType']) 
					&& (in_array('3', $filterData['actionType']) || in_array('4', $filterData['actionType']))){
					$result = $chatActions;
				}
			}
			return $result;
		}

		/**
		 * get  group users for creation group
		 *
		 * @since    1.0.0
		*/
		public function getGuppyGroupUsers($data){
			$headers    	= $data->get_headers();
			$params     	= !empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  	= ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			
			$guppyGroupUsers  = $json  = array();
			
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}

			$offset 		= !empty($params['offset']) ? intval($params['offset']) : 0; 
			$searchQuery 	= !empty($params['search']) ? sanitize_text_field($params['search']) : '';
			$loginedUser 	= !empty($params['userId']) ? intval($params['userId']) : 0;
			$user_meta  	= get_userdata($loginedUser);
			$user_roles 	= $user_meta->roles;
			$allowed_roles = array( 'administrator');
			if (array_intersect( $allowed_roles, $user_roles ) ) {
				$query_args = array(
					'fields' 			=> array('id'),
					'orderby' 			=> 'display_name',
					'order'   			=> 'ASC',
					'offset' 			=> $offset,
					'number'			=> $this->showRec,
				);

				if( !empty($searchQuery) ){
					$query_args['search']	=  '*'.$searchQuery.'*';
				}

				$query_args = apply_filters('wpguppy_filter_user_params', $query_args);
				
				$allusers = get_users( $query_args );
				
				if(!empty($allusers)){
					foreach( $allusers as $user ) {
						$userName = $userAvatar = '';
						$userData 			= $this->getUserInfo('1', $user->id);
						if(!empty($userData)){
							$userName 			= $userData['userName'];
							$userAvatar 		= $userData['userAvatar'];
						}
						$guppyGroupUsers[] = array(
							'userId'		 => intval( $user->id),
							'userName' 	   	 => $userName,
							'userAvatar' 	 => $userAvatar,
						);	
					}
				}
			}else{
				$fetchResults 	= $this->guppyModel->getGuppyContactList($this->showRec, $offset, $searchQuery, $loginedUser, 1);
				if ( !empty( $fetchResults )) {
					foreach( $fetchResults as $result ) {

						if($result['send_by'] == $loginedUser){
							$friendId = intval( $result['send_to'] );
						}else{
							$friendId = intval( $result['send_by'] );
						}

						$userName = $userAvatar = '';
						$userData 			= $this->getUserInfo('1', $friendId);
						if(!empty($userData)){
							$userName 			= $userData['userName'];
							$userAvatar 		= $userData['userAvatar'];
						}
						$guppyGroupUsers[] = array(
							'userId'		 => intval( $friendId),
							'userName' 	   	 => $userName,
							'userAvatar' 	 => $userAvatar,
						);
					}
				}
			}
			$json['type'] 				= 'success';
			$json['guppyGroupUsers'] 	= $guppyGroupUsers;
			return new WP_REST_Response($json , 200);	
		}

		/**
		 * create/update guppy group
		 *
		 * @since    1.0.0
		*/
		public function updateGuppyGroup($data){

			$headers    	= $data->get_headers();
			$params     	= !empty($data->get_params()) 	   ? $data->get_params() 		: '';
			$files     		= !empty($data->get_file_params()) ? $data->get_file_params() 	: '';
			$authToken  	= ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$json =  $userMessages = $newMembers = $existingMembers = $newlyAdded  = $messages = array();
			$conversationList = $messagelistData = $groupRoleIds =  $removedIds = array();
			$groupImage = '';
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}

			$memberIds 			= !empty($params['memberIds']) 		? explode(',',$params['memberIds']) 			: array(); 
			$groupTitle 		= !empty($params['groupTitle']) 	? sanitize_text_field($params['groupTitle']) 	: 'Group-'.time();
			$adminIds 			= !empty($params['adminIds']) 		? explode(',',$params['adminIds']) 				: array();
			$disableReply 		= !empty($params['disableReply']) 	? $params['disableReply'] 		: false; 
			$loginedUser 		= !empty($params['userId']) 		? intval($params['userId']) 	: 0; 
			$groupId 			= !empty($params['groupId']) 		? intval($params['groupId']) 	: 0; 
			$removeImage 		= !empty($params['removeImage']) 	? intval($params['removeImage']): 0; 
			$existingGroupImage = !empty($params['groupImage']) 	? $params['groupImage'] : '';
			$isEdit				= false;
			$sec				= 2;
			if($disableReply == 'true'){
				$disableReply = 1;
			}

			$groupdata = array(
				'group_title' 			=> $groupTitle,
				'group_description' 	=> '',
				'disable_reply' 		=> $disableReply,
				'group_updated_date'	=> date('Y-m-d H:i:s')
			);

			if(empty($groupId)){
				$createGroup = false;
				$roles 	= $this->getUserRoles($loginedUser);
				if(!empty($roles) && $roles['createGroup']){
					$createGroup = true;
				}
				if(!$createGroup){
					$json['type'] 			= 'error';
					$json['message_desc']   = esc_html__('You are not allowed to perform this action!', 'wp-guppy');
					return new WP_REST_Response($json , 203);
				}
				$groupdata['group_image']	= $groupImage;
				$groupdata['group_created_date'] = date('Y-m-d H:i:s');
			}
			
			if(!empty($groupId)){
				$where 		 	= "group_id=". $groupId." AND member_status ='1' AND (group_role='1' OR group_role='2') AND member_id=".$loginedUser; 
				$checkInfo 		= $this->guppyModel->getData('id','wpguppy_group_member',$where );
				if(empty($checkInfo)){
					$json['type'] 			= 'error';
					$json['message_desc']   = esc_html__('You are not allowed to perform this action!', 'wp-guppy');
					return new WP_REST_Response($json , 203);
				}
				if($removeImage == 1){
					$groupdata['group_image']	= $groupImage;
				}else{
					$json['groupImage']	= $existingGroupImage;
				}
				$isEdit = $this->guppyModel->updateData( 'wpguppy_group', $groupdata, array('id' => $groupId));
			}else{
				$groupId = $this->guppyModel->insertData('wpguppy_group',$groupdata);
			}

			if($groupId){
				if(!empty($memberIds)){
					if($isEdit){
						$where 		 	= "group_id=". $groupId." AND group_role <> '1'  AND member_id <>".$loginedUser; 
						$memberInfo 	= $this->guppyModel->getData('*','wpguppy_group_member',$where );
						if(!empty($memberInfo)) {
							foreach($memberInfo as $single){
								$existingMembers[$single['member_id']] = array(
									'id'			=>	$single['id'],
									'member_status'	=>	$single['member_status'],
									'group_status'	=>	$single['group_status'],
									'group_role'	=>	$single['group_role'],
								);
							}
						}
					}else{ 
						$data = array();
						$data['group_id']		= $groupId;		
						$data['member_id']		= $loginedUser;		
						$data['group_role']		= 1;	// for creator of group
						$data['member_status']	= 1;		
						$data['group_status']	= 1;
						$data['member_added_date'] = date('Y-m-d H:i:s');
						$this->guppyModel->insertData('wpguppy_group_member',$data);
					}
					foreach( $memberIds as $id ){
						$groupRole = '0';
						if(in_array($id,$adminIds)){
							$groupRole = '2';
						}
						if(!empty($existingMembers) && isset($existingMembers[$id])){
							$actionType = '0';
							if($existingMembers[$id]['group_status'] == '0'){
								$actionType = '5';
								$update_col = 'action_updated_time';
							}elseif($existingMembers[$id]['member_status'] == '0'){
								$actionType = '3';
								$update_col = 'action_updated_time';
							}elseif($existingMembers[$id]['member_status'] == '2'){
								$actionType = '4';
								$update_col = 'action_updated_time';
							}
							if($actionType!='0'){ 			// update chat action
								$where 		 	= "corresponding_id=". $groupId." AND chat_type = '2' AND action_type= '".$actionType."' AND action_by='".$id."' ORDER BY id desc limit 1"; 
								$chatAction 	= $this->guppyModel->getData('id','wpguppy_chat_action',$where );
								if(!empty($chatAction)){ 
									$this->guppyModel->updateData( 'wpguppy_chat_action', array($update_col => date('Y-m-d H:i:s')), array('id' => $chatAction[0]['id']));	
								}
								$newlyAdded[] = $id;
							}
							if($groupRole != $existingMembers[$id]['group_role']){
								$groupRoleIds[] = $id;
							}	
							$updateId = $existingMembers[$id]['id'];
							$this->guppyModel->updateData( 'wpguppy_group_member', array('group_role' => $groupRole, 'member_status'=> 1 , 'group_status' => 1), array('id' => $updateId));
							unset($existingMembers[$id]);
						}else{

							$data = array();
							$data['group_id']			= $groupId;		
							$data['member_id']			= $id;		
							$data['group_role']			= $groupRole;
							$data['member_status']		= 1;		
							$data['group_status']		= 1;
							$data['member_added_date']	= date('Y-m-d H:i:s');
							$newlyAdded[] 	= $id;
							if($groupRole != '0'){
								$groupRoleIds[] = $id;
							}
							$this->guppyModel->insertData('wpguppy_group_member',$data);
						}
					}
					if($isEdit && !empty($existingMembers)){
						foreach($existingMembers as $id => $value){
							if($value['member_status'] == '1'){
								$sec = 0;
								$removedIds[] = $id;
								$this->guppyModel->updateData( 'wpguppy_group_member', array('member_status'=> 2), array('id' => $value['id']));
								
								// insert chat action
								$actionTime = date('Y-m-d H:i:s');
								$data = array();
								$data['corresponding_id']		= $groupId;		
								$data['chat_type']				= 2;		
								$data['action_by']				= $id;
								$data['action_type']			= 4;
								$data['action_time']			= $actionTime;
								$data['action_updated_time']	= $actionTime;
								$this->guppyModel->insertData('wpguppy_chat_action', $data);
							}
						}
					}	
				}
				if(!empty($files)){
					$filterData = array();
					$filterData['groupId'] 	= $groupId;
					$attachmentData = $this->uploadAttachments(1, $files, $filterData);
					if(!empty($attachmentData['attachments'])){
						$groupImage = $attachmentData['attachments'][0]['thumbnail'];
						$json['groupImage']	= $groupImage;
						$messagelistData['groupImage'] 	= $groupImage;
						$this->guppyModel->updateData( 'wpguppy_group', array('group_image' => $groupImage), array('id' => $groupId));
					}
				}

				

				$messageData 			= array();
				$messageSentTime 		= date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s"))+$sec);
				$timestamp 				= strtotime($messageSentTime);
				
				$messageData['sender_id'] 			= $loginedUser; 
				$messageData['user_type'] 			= 1; 
				$messageData['group_id'] 			= $groupId; 
				$messageData['chat_type'] 			= 2; 
				$messageData['message_type'] 		= 4;
				$messageData['timestamp'] 			= $timestamp; 
				$messageData['message_sent_time'] 	= $messageSentTime; 

				
				// prepare data for live chat
				$groupDetail 	= $this->guppyModel->getGroupDetail($groupId);
				$memberInfo 	= $groupDetail['memberAvatars'];
				$chatData = array(
					'chatType' 				=> 2,
					'timeStamp' 			=> $timestamp,	
					'messageType' 			=> 4,
					'userType' 				=> 1,
					'messageStatus' 		=> '0',	
					'attachmentsData' 		=> NULL,	
					'replyMessage' 			=> NULL,	
					'metaData'				=> false,
					'chatId'				=> $this->getChatKey('2', $groupId),
					'userName'				=> $memberInfo[$loginedUser]['userName'], 
				);
				$messagelistData['timeStamp'] 			= $timestamp;
				$messagelistData['messageType'] 		= 4;
				$messagelistData['chatType'] 			= 2;
				$messagelistData['isSender'] 			= false;
				$messagelistData['messageStatus'] 		= '0';
				$messagelistData['chatId'] 				= $this->getChatKey('2', $groupId);	
				$messagelistData['UnreadCount'] 		= 0;
				$messagelistData['muteNotification'] 	= false;
				$messagelistData['memberDisable'] 		= false;
				$messagelistData['userDisableReply'] 	= false;
				$messagelistData['groupTitle'] 			= $groupTitle;
				$messagelistData['userName']			= $memberInfo[$loginedUser]['userName']; 
				$messagelistData['userAvatar']			= $memberInfo[$loginedUser]['userAvatar']; 
				$messagelistData['groupDetail'] 		= $groupDetail;
				// prepare data for live chat

				if($isEdit){

					// for group updated
					$messages[] = array(
						'type'=> 6,     		
					);
					$data = array();
					$data['type'] = 6;
					$messageData['message'] = serialize($data); 
					$this->guppyModel->insertData('wpguppy_message',$messageData);
					
					// for newly add members
					if(!empty($newlyAdded)){
						$data = $membersUpdate = array();
						$data['type'] = 2; 
						$data['memberIds'] = $newlyAdded; 
						$messageData['message'] = serialize($data); 
						$this->guppyModel->insertData('wpguppy_message',$messageData);
						
						foreach($newlyAdded as $id){
							$memberName = !empty($memberInfo[$id]['userName']) ? $memberInfo[$id]['userName'] : '';
							$membersUpdate[$id] = $memberName;
						}
						$messages[] = array(
							'type'				=> 2,
							'membersUpdate'		=> $membersUpdate,
							'memberIds'			=> $newlyAdded, 	
						);
					}
					// for remove  members
					if(!empty($removedIds)){
						$data = $membersUpdate =array();
						$data['type'] = 3; 
						$data['memberIds'] = $removedIds; 
						$messageData['message'] = serialize($data); 
						$this->guppyModel->insertData('wpguppy_message',$messageData);
						foreach($removedIds as $id){
							$memberName = !empty($memberInfo[$id]['userName']) ? $memberInfo[$id]['userName'] : '';
							$membersUpdate[$id] = $memberName;
						}
						$messages[] = array(
							'type'				=> 3,
							'membersUpdate'		=> $membersUpdate,
							'memberIds'			=> $removedIds, 
						);
					}

					// for update  members role
					if(!empty($groupRoleIds)){
						$data = $membersUpdate =array();
						$data['type'] = 5; 
						$data['memberIds'] = $groupRoleIds; 
						$messageData['message'] = serialize($data); 
						$this->guppyModel->insertData('wpguppy_message',$messageData);
						foreach($groupRoleIds as $id){
							$memberName = !empty($memberInfo[$id]['userName']) ? $memberInfo[$id]['userName'] : '';
							$membersUpdate[$id] = $memberName;
						}
						$messages[] = array(
							'type'			=> 5,
							'membersUpdate'	=> $membersUpdate,
							'memberIds'		=> $groupRoleIds, 
						);
					}
					if(!empty($memberInfo)) {
						foreach($memberInfo as $single){
							if($single['memberStatus'] == '1' || in_array($single['userId'], $removedIds)){
								$statusActions = $params = $filterData = array();	
								$params['actionBy'] 	= $single['userId'];
								$params['chatType'] 	= 2;
								$params['groupId'] 		= $groupId;
								$params['orderBy'] 		= 'action_type';
								$params['actionType'] 	= array('3','4','5'); // group left or removed from group
								$chatActions = $this->getGuppyChatAction($params);
								$filterData['memberAddedDate'] 	= $single['memberAddedDate'];
								$filterData['senderId'] 		= $single['userId'];
								$filterData['groupId'] 			= $groupId;
								$filterData['chatType'] 		= 2;
								$filterData['groupAction'] 		= array();
								$deleteGroupTime = '';
								$unreadCount = 0;
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
										}
									}
									if($deleteGroupTime!=''){
										$filterData['groupAction']['deleteGroupTime'] = $deleteGroupTime;
									}
									$filterData['groupAction']['status'] = $statusActions;
								}
								$unreadCount = $this->guppyModel->getUnreadCount($filterData);
								$messagelistData['UnreadCount'] 		= $unreadCount;
								if($groupDetail['disableReply']  && $single['groupRole'] == '0'){
									$messagelistData['userDisableReply'] = true;
								}
								
								$chatNotify = array();
								$chatNotify['actionBy'] 	= $single['userId'];
								$chatNotify['actionType'] 	= '2';
								$chatNotify['chatType'] 	= 2;
								$chatNotify['groupId'] 		= $groupId;

								$muteNotification = $this->getGuppyChatAction($chatNotify);
								if(!empty($muteNotification)){
									$muteNotification = true;
								}else{
									$muteNotification = false;
								}
								$messagelistData['muteNotification'] 	= $muteNotification;
								if(in_array($single['userId'], $removedIds)){
									$memberName = $single['userName'];
									$userMessages[$single['userId']][] = array(
										'type' 			=> 3,
										'membersUpdate' => array($single['userId'] => $memberName),
										'memberIds'		=> array($single['userId']),
									);
									$messagelistData['memberDisable'] 	= true;
									$messagelistData['timestamp'] 		= false;
									$messagelistData['groupDetail'] 	= NULL;
									$messagelistData['groupImage'] 		= WPGuppy_GlobalSettings::get_plugin_url().'public/images/group.jpg';
								}else{
									if($removeImage == 1){
										$messagelistData['groupImage']	= '';
									}elseif(!empty($groupImage)){
										$messagelistData['groupImage']	= $groupImage;
									}else{
										$messagelistData['groupImage']	= $existingGroupImage;
									}
									$messagelistData['memberDisable'] 		= false;
									$messagelistData['timestamp']			= $timestamp; 
									$messagelistData['groupDetail'] 		= $groupDetail;
									$userMessages[$single['userId']] 		= $messages;
								}
								$conversationList[$single['userId']] 	= $messagelistData;
								$groupMembers[] = $single['userId'];
							}
						}
					}
				}else{
					
					$data = array();
					$data['type'] = 1; 
					$messageData['message'] = serialize($data); 
					$this->guppyModel->insertData('wpguppy_message',$messageData);
					$messages[] = array(
						'type'=> 1,
					);
					if(!empty($memberInfo)) {
						foreach($memberInfo as $single){
							if($groupDetail['disableReply']  && $single['groupRole'] == '0'){
								$messagelistData['userDisableReply'] = true;
							}
							$conversationList[$single['userId']] = $messagelistData;
							$groupMembers[] = $single['userId'];
							$userMessages[$single['userId']] = $messages;
						}
					}
				}

				// send data to pushers
				if($this->pusher){
					if(!empty($groupMembers)){
						$batchRequests = array();
						foreach($groupMembers as $id){
							$messagelistData 	= $conversationList[$id];
							$messageData 		= $chatData;
							if($id == $loginedUser){
								$messagelistData['isSender'] 			= true;
								$messageData['isSender'] 				= true;
								$messagelistData['userDisableReply'] 	= false;
							}
							$pusherData = array(
								'chatId' 	=> $this->getChatKey('2', $groupId),
								'chatType'  => 2,
							);
							if(!empty($userMessages[$id])){
								$allMessages = $userMessages[$id];
								foreach($allMessages as $single){
									$message = array();
									if($single['type'] ==  1 || $single['type'] ==  6){
										$message['type'] 				= $single['type'];
										$messageData['message'] 		= $message;
										$messagelistData['message'] 	= $message;
										$pusherData['chatData'] 		= $messageData;
									}else{
										$message['type'] 					= $single['type'];
										$message['memberIds'] 				= $single['memberIds'];
										$messageData['message'] 			= $message;
										$messagelistData['message'] 		= $message;
										$messageData['membersUpdate'] 		= $single['membersUpdate'];
										$messagelistData['membersUpdate'] 	= $single['membersUpdate'];
										$pusherData['chatData'] 			= $messageData;
										if($single['type'] ==  3 &&  in_array($id,$single['memberIds'])){
											unset($pusherData['chatData']);
										}
									}
									$pusherData['messagelistData'] 	= $messagelistData;
								}
							}
							$batchRequests[] = array(
								'channel' 	=> 'private-user-' . $id,
								'name' 		=> 'groupChatData',
								'data'		=> $pusherData,
							);
						}
						$this->pusher->triggerBatch($batchRequests);
					}
				}
				$type = 'success'; 
				$json['userMessages'] 		= $userMessages;	
				$json['chatData'] 			= $chatData;
				$json['chatType'] 			= 2;	
				$json['messagelistData'] 	= $conversationList;	
				$json['groupMembers'] 		= $groupMembers;
					
			}

			$json['type'] 	= $type;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * delete guppy group member
		 *
		 * @since    1.0.0
		*/
		public function deleteGuppyGroupMember($data){
			$headers    	= $data->get_headers();
			$params     	= !empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  	= ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$groupMembers  	= $json  = array();
			
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$loginedUser 	= !empty($params['userId']) 		? intval($params['userId']) 	: 0; 
			$memberId 		= !empty($params['memberId']) 		? intval($params['memberId']) 	: 0;
			$groupId 		= !empty($params['groupId']) 		? intval($params['groupId']) 	: 0;
			$type 			= 'error';
			$where 		 	= "group_id=". $groupId." AND member_status ='1' AND (group_role='1' OR group_role='2') AND member_id=".$loginedUser; 
			$validateUser 	= $this->guppyModel->getData('id','wpguppy_group_member',$where );
			if(empty($validateUser)){
				$json['type'] 			= 'error';
				$json['message_desc']   = esc_html__('You are not allowed to perform this action!', 'wp-guppy');
				return new WP_REST_Response($json , 203);
			}
			$where 		 	= "group_id=". $groupId." AND member_status='1' AND member_id=".$memberId; 
			$verifyInfo 	= $this->guppyModel->getData('id','wpguppy_group_member',$where );
			if($memberId != $loginedUser && !empty($verifyInfo)){
				
				$this->guppyModel->updateData( 'wpguppy_group_member', array('member_status'=> 2), array('id' => $verifyInfo[0]['id']));
				
				// insert chat action
				$actionTime = date('Y-m-d H:i:s');
				$data = array();
				$data['corresponding_id']		= $groupId;		
				$data['chat_type']				= 2;		
				$data['action_by']				= $memberId;
				$data['action_type']			= 4;
				$data['action_time']			= $actionTime;
				$data['action_updated_time']	= $actionTime;
				$this->guppyModel->insertData('wpguppy_chat_action', $data);
				
				$messageData 			= array();
				$messageSentTime 		= date('Y-m-d H:i:s');
				$timestamp 				= strtotime($messageSentTime);
				
				$messageData['sender_id'] 			= $loginedUser; 
				$messageData['user_type'] 			= 1; 
				$messageData['group_id'] 			= $groupId; 
				$messageData['chat_type'] 			= 2; 
				$messageData['message_type'] 		= 4;
				$messageData['timeStamp'] 			= $timestamp; 
				$messageData['message_sent_time'] 	= $messageSentTime;  
				$data = array();
				$data['type'] = 3; 
				$data['memberIds'] 		= array($memberId); 
				$messageData['message'] = serialize($data); 
				$this->guppyModel->insertData('wpguppy_message',$messageData);

				// prepare data for live chat
				$groupMembers = $conversationList = $messagelistData = $membersUpdate = $userMessages = array();
				$groupDetail 	= $this->guppyModel->getGroupDetail($groupId);
				$memberInfo 	= $groupDetail['memberAvatars'];
				$memberName = !empty($memberInfo[$memberId]['userName']) ? $memberInfo[$memberId]['userName'] : '';
				$membersUpdate[$memberId] = $memberName;
				$messages[] = array(
					'type'			=> 3,
					'membersUpdate'	=> $membersUpdate,
					'memberIds'		=> array($memberId), 
				);
				$chatData = array(
					'chatType' 				=> 2,
					'timeStamp' 			=> $timestamp,	
					'messageType' 			=> 4,
					'userType' 				=> 1,
					'messageStatus' 		=> '0',	
					'attachmentsData' 		=> NULL,	
					'replyMessage' 			=> NULL,	
					'metaData'				=> false,
					'chatId'				=> $this->getChatKey('2', $groupId),
					'userName'				=> $memberInfo[$loginedUser]['userName'], 
				);
				$messagelistData['timeStamp'] 			= $timestamp;
				$messagelistData['messageType'] 		= 4;
				$messagelistData['chatType'] 			= 2;
				$messagelistData['isSender'] 			= false;
				$messagelistData['messageStatus'] 		= '0';
				$messagelistData['chatId'] 				= $this->getChatKey('2', $groupId);	
				$messagelistData['UnreadCount'] 		= 0;
				$messagelistData['muteNotification'] 	= false;
				$messagelistData['userDisableReply'] 	= false;
				$messagelistData['memberDisable'] 		= false;
				$messagelistData['groupTitle'] 			= $groupDetail['groupTitle'];
				$messagelistData['userName']			= $memberInfo[$loginedUser]['userName']; 
				$messagelistData['userAvatar']			= $memberInfo[$loginedUser]['userAvatar']; 
				$messagelistData['groupDetail'] 		= $groupDetail;
				if(!empty($memberInfo)) {
					foreach($memberInfo as $single){
						if($single['memberStatus'] == '1' ||  ($single['userId'] == $memberId) ){
							$statusActions = $params = $filterData = array();	
							$params['actionBy'] 	= $single['userId'];
							$params['chatType'] 	= 2;
							$params['groupId'] 		= $groupId;
							$params['orderBy'] 		= 'action_type';
							$params['actionType'] 	= array('3','4','5'); // group left or removed from group
							$chatActions = $this->getGuppyChatAction($params);
							$filterData['memberAddedDate'] 	= $single['memberAddedDate'];
							$filterData['senderId'] 		= $single['userId'];
							$filterData['groupId'] 			= $groupId;
							$filterData['chatType'] 		= 2;
							$filterData['groupAction'] 		= array();
							$deleteGroupTime = '';
							$unreadCount = 0;
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
									}
								}
								if($deleteGroupTime!=''){
									$filterData['groupAction']['deleteGroupTime'] = $deleteGroupTime;
								}
								$filterData['groupAction']['status'] = $statusActions;
							}
							$unreadCount = $this->guppyModel->getUnreadCount($filterData);
							$messagelistData['UnreadCount'] 		= $unreadCount;
							if($groupDetail['disableReply'] == '1' && $single['groupRole'] == '0'){
								$messagelistData['userDisableReply'] = true;
							}
							$chatNotify = array();
							$chatNotify['actionBy'] 	= $single['userId'];
							$chatNotify['actionType'] 	= '2';
							$chatNotify['chatType'] 	= 2;
							$chatNotify['groupId'] 		= $groupId;
	
							$muteNotification = $this->getGuppyChatAction($chatNotify);
							if(!empty($muteNotification)){
								$muteNotification = true;
							}else{
								$muteNotification = false;
							}
							$messagelistData['muteNotification'] 	= $muteNotification;
							if($single['userId'] == $memberId){
								$memberName = $single['userName'];
								$userMessages[$single['userId']][] = array(
									'type' 			=> 3,
									'membersUpdate' => array($single['userId'] => $memberName),
									'memberIds'		=> array($single['userId']),
								);
								$messagelistData['memberDisable'] 	= true;
								$messagelistData['timestamp'] 		= false;
								$messagelistData['groupDetail'] 	= NULL;
								$messagelistData['groupImage'] 		= WPGuppy_GlobalSettings::get_plugin_url().'public/images/group.jpg';
							}else{
								$messagelistData['memberDisable'] 		= false;
								$messagelistData['timestamp']			= $timestamp; 
								$messagelistData['groupDetail'] 		= $groupDetail;
								$messagelistData['groupImage']			= $groupDetail['groupImage'];
								$userMessages[$single['userId']] 		= $messages;
							}
							$conversationList[$single['userId']] 	= $messagelistData;
							$groupMembers[] = $single['userId'];
						}
					}
				}
	
				// send data to pushers
				if($this->pusher){
					if(!empty($groupMembers)){
						$batchRequests = array();
						foreach($groupMembers as $id){
							$messagelistData 	= $conversationList[$id];
							$messageData 		= $chatData;
							if($id == $loginedUser){
								$messagelistData['isSender'] 			= true;
								$messageData['isSender'] 				= true;
							}
							$pusherData = array(
								'chatId' 	=> $this->getChatKey('2', $groupId),
								'chatType'  => 2,
							);
							if(!empty($userMessages[$id])){
								$allMessages = $userMessages[$id];
								foreach($allMessages as $single){
									$message = array();
									$message['type'] 					= $single['type'];
									$message['memberIds'] 				= $single['memberIds'];
									$messageData['message'] 			= $message;
									$messagelistData['message'] 		= $message;
									$messageData['membersUpdate'] 		= $single['membersUpdate'];
									$messagelistData['membersUpdate'] 	= $single['membersUpdate'];
									$pusherData['chatData'] 			= $messageData;
									if($single['type'] ==  3 &&  in_array($id,$single['memberIds'])){
										unset($pusherData['chatData']);
									}
									$pusherData['messagelistData'] 	= $messagelistData;
								}
							}
							$batchRequests[] = array(
								'channel' 	=> 'private-user-' . $id,
								'name' 		=> 'groupChatData',
								'data'		=> $pusherData,
							);	
						}
						$this->pusher->triggerBatch($batchRequests);
					}
				}
				$type = 'success';
			}
			$json['type'] 				= $type;
			$json['groupId'] 			= $groupId;
			$json['userMessages'] 		= $userMessages;	
			$json['chatData'] 			= $chatData;	
			$json['chatType'] 			= 2;	
			$json['messagelistData'] 	= $conversationList;	
			$json['groupMembers'] 		= $groupMembers;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * delete guppy group
		 *
		 * @since    1.0.0
		*/
		public function deleteGuppyGroup($data){
			$headers    	= $data->get_headers();
			$params     	= !empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  	= ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$groupMembers  	= $json  = array();
			
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$loginedUser 	= !empty($params['userId']) 		? intval($params['userId']) 	: 0; 
			$groupId 		= !empty($params['groupId']) 		? intval($params['groupId']) 	: 0;
			$type 			= 'error';
			$where 		 	= "group_id=". $groupId." AND (member_status ='0' OR member_status ='2') AND group_status = '1' AND member_id=".$loginedUser; 
			$validateUser 	= $this->guppyModel->getData('id','wpguppy_group_member',$where );
			if(empty($validateUser)){
				$json['type'] 			= 'error';
				$json['message_desc']   = esc_html__('You are not allowed to perform this action!', 'wp-guppy');
				return new WP_REST_Response($json , 203);
			}

			$this->guppyModel->updateData( 'wpguppy_group_member', array('group_status'=> 0), array('id' => $validateUser[0]['id']));
			$where 		 	= "corresponding_id=". $groupId." AND chat_type = '2' AND action_type= '5' AND action_by=".$loginedUser; 
			$chatAction 	= $this->guppyModel->getData('id','wpguppy_chat_action',$where );
			if(!empty($chatAction)){
				$this->guppyModel->updateData( 'wpguppy_chat_action', array('action_time' => date('Y-m-d H:i:s'), 'action_updated_time' => date('Y-m-d H:i:s')), array('id' => $chatAction[0]['id']));
			}else{
				// insert chat action
				$actionTime = date('Y-m-d H:i:s');
				$data = array();
				$data['corresponding_id']		= $groupId;		
				$data['chat_type']				= 2;		
				$data['action_by']				= $loginedUser;
				$data['action_type']			= 5;
				$data['action_time']			= $actionTime;
				$data['action_updated_time']	= $actionTime;
				$this->guppyModel->insertData('wpguppy_chat_action', $data);
			}

			if($this->pusher){
				$pusherData = array(
					'chatId' 	=> $this->getChatKey('2', $groupId),
					'userId' 	=> $loginedUser,
				);
				$this->pusher->trigger('private-user-'.$loginedUser, 'deleteGroup', $pusherData);
			}
			$json['type'] 		= 'success';
			$json['chatId'] 	= $this->getChatKey('2', $groupId);
			$json['chatType'] 	= 2;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * leave guppy group
		 *
		 * @since    1.0.0
		*/
		public function leaveGuppyGroup($data){
			$headers    	= $data->get_headers();
			$params     	= !empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  	= ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$groupMembers  	= $json  = array();
			
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$loginedUser 	= !empty($params['userId']) 		? intval($params['userId']) 	: 0; 
			$groupId 		= !empty($params['groupId']) 		? intval($params['groupId']) 	: 0;
			$adminIds 		= !empty($params['adminIds']) 		? $params['adminIds'] 			: [];
			$type 			= 'error';
			$where 		 	= "group_id=". $groupId." AND member_status ='1' AND group_status = '1' AND member_id=".$loginedUser; 
			$userinfo 		= $this->guppyModel->getData('id,group_role','wpguppy_group_member',$where );
			$updatedAdminIds = array();
			if(empty($userinfo)){
				$json['type'] 			= 'error';
				$json['message_desc']   = esc_html__('You are not allowed to perform this action!', 'wp-guppy');
				return new WP_REST_Response($json , 203);
			}

			if($userinfo[0]['group_role']== '1' || $userinfo[0]['group_role']== '2'){

				if( !empty($adminIds) ) {
					foreach( $adminIds as $id ){
						$where 		 	= "group_id=". $groupId." AND member_status ='1' AND group_status = '1' AND member_id=".$id; 
						$userVerfiy 	= $this->guppyModel->getData('id','wpguppy_group_member',$where );
						if(!empty($userVerfiy)){
							$updatedAdminIds[] = $id;
							$this->guppyModel->updateData( 'wpguppy_group_member', array( 'group_role' => '2' ), array('id' => $userVerfiy[0]['id'] ) );
						}
					}
				}	
				$where 		 	= "group_id=". $groupId." AND  group_role IN ('1','2') AND member_status ='1' AND group_status = '1' AND member_id <>".$loginedUser; 
				$verify 		= $this->guppyModel->getData('id','wpguppy_group_member',$where );
				if( empty( $verify ) ) {
					$where 		 	= "group_id=". $groupId." AND group_role = '0' AND member_status ='1' AND group_status = '1'"; 
					$getMembers 	= $this->guppyModel->getData('member_id','wpguppy_group_member', $where );
					if( ! empty( $getMembers ) ){
					
						foreach( $getMembers as $member ) {
							$memberData 	= $this->getUserInfo('1', $member['member_id']);
							$userName 		= $userAvatar = '';

							if(!empty($memberData)){
								$userName 	= $memberData['userName'];
								$userAvatar = $memberData['userAvatar'];
							}

							$groupMembers[] = array(
								'userId'		 => intval( $member['member_id']),
								'userName' 	   	 => $userName,
								'userAvatar' 	 => $userAvatar,
							);	
						}

						$json['groupMembers'] = $groupMembers;
						$json['type']		  = 'suggested';
						return new WP_REST_Response($json , 200);
					}
				}
			}

			$this->guppyModel->updateData( 'wpguppy_group_member', array( 'member_status' => 0, 'group_role'=> 0 ), array('member_id' => $loginedUser, 'group_id' => $groupId ) );

			// prepare data for live chat
			$messageSentTime 		= date('Y-m-d H:i:s');
			$timestamp 				= strtotime($messageSentTime);


			$groupDetail 	= $this->guppyModel->getGroupDetail($groupId);
			$memberInfo 	= $groupDetail['memberAvatars'];
			$chatData = array(
				'chatType' 				=> 2,
				'timeStamp' 			=> $timestamp,	
				'messageType' 			=> 4,
				'userType' 				=> 1,
				'messageStatus' 		=> '0',	
				'attachmentsData' 		=> NULL,	
				'replyMessage' 			=> NULL,	
				'metaData'				=> false,
				'chatId'				=> $this->getChatKey('2', $groupId),
				'userName'				=> $memberInfo[$loginedUser]['userName'], 
			);
			$messagelistData['timeStamp'] 			= $timestamp;
			$messagelistData['messageType'] 		= 4;
			$messagelistData['chatType'] 			= 2;
			$messagelistData['isSender'] 			= false;
			$messagelistData['messageStatus'] 		= '0';
			$messagelistData['chatId'] 				= $this->getChatKey('2', $groupId);	
			$messagelistData['UnreadCount'] 		= 0;
			$messagelistData['muteNotification'] 	= false;
			$messagelistData['memberDisable'] 		= false;
			$messagelistData['groupTitle'] 			= $groupDetail['groupTitle'];
			$messagelistData['userName']			= $memberInfo[$loginedUser]['userName']; 
			$messagelistData['userAvatar']			= $memberInfo[$loginedUser]['userAvatar']; 
			$messagelistData['groupDetail'] 		= $groupDetail;
			
			// insert entry in messsage table
			$messageData = $messages		= array();
			$messageData['sender_id'] 			= $loginedUser; 
			$messageData['user_type'] 			= 1; 
			$messageData['group_id'] 			= $groupId; 
			$messageData['chat_type'] 			= 2; 
			$messageData['message_type'] 		= 4;
			$messageData['timeStamp'] 			= $timestamp; 
			$messageData['message_sent_time'] 	= $messageSentTime; 

			if( !empty( $updatedAdminIds ) ){
				$data = $membersUpdate =  array();
				$data['type'] = 5; 
				$data['memberIds'] 		= $updatedAdminIds; 
				$messageData['message'] = serialize($data); 
				$this->guppyModel->insertData('wpguppy_message',$messageData);
				foreach($updatedAdminIds as $id){
					$memberName = !empty($memberInfo[$id]['userName']) ? $memberInfo[$id]['userName'] : '';
					$membersUpdate[$id] = $memberName;
				}
				$messages[] = array(
					'type'			=> 5,
					'membersUpdate'	=> $membersUpdate,
					'memberIds'		=> $updatedAdminIds, 
				);
			}
			
			$data 					= array();
			$data['type'] 			= 4;
			$data['memberIds'] 		= array($loginedUser); 
			$messageData['message'] = serialize($data); 
			$this->guppyModel->insertData('wpguppy_message',$messageData);

			$memberName = !empty($memberInfo[$loginedUser]['userName']) ? $memberInfo[$loginedUser]['userName'] : '';
			$membersUpdate[$loginedUser] = $memberName;
			$messages[] = array(
				'type'			=> 4,
				'membersUpdate'	=> $membersUpdate,
				'memberIds'		=> array($loginedUser), 
			);

			// insert entry in chat action Table
			$data =	$groupMembers	= $conversationList =  array();
			$actionTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s")));
			$data['corresponding_id']		= $groupId;		
			$data['chat_type']				= 2;		
			$data['action_by']				= $loginedUser;
			$data['action_type']			= 3;
			$data['action_time']			= $actionTime;
			$data['action_updated_time']	= $actionTime;
			$this->guppyModel->insertData('wpguppy_chat_action', $data);
			
			if(!empty($memberInfo)) {
				foreach($memberInfo as $single){
					if($single['memberStatus'] == '1' ||  ($single['userId'] == $loginedUser) ){
						$statusActions = $params = $filterData = array();	
						$params['actionBy'] 	= $single['userId'];
						$params['chatType'] 	= 2;
						$params['groupId'] 		= $groupId;
						$params['orderBy'] 		= 'action_type';
						$params['actionType'] 	= array('3','4','5'); // group left or removed from group
						$chatActions = $this->getGuppyChatAction($params);
						$filterData['memberAddedDate'] 	= $single['memberAddedDate'];
						$filterData['senderId'] 		= $single['userId'];
						$filterData['groupId'] 			= $groupId;
						$filterData['chatType'] 		= 2;
						$filterData['groupAction'] 		= array();
						$deleteGroupTime = '';
						$unreadCount = 0;
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
								}
							}
							if($deleteGroupTime!=''){
								$filterData['groupAction']['deleteGroupTime'] = $deleteGroupTime;
							}
							$filterData['groupAction']['status'] = $statusActions;
						}
						$unreadCount = $this->guppyModel->getUnreadCount($filterData);
						$messagelistData['UnreadCount'] 		= $unreadCount;
						if($groupDetail['disableReply'] == '1' && $single['groupRole'] == '0'){
							$messagelistData['userDisableReply'] = true;
						}
						$chatNotify = array();
						$chatNotify['actionBy'] 	= $single['userId'];
						$chatNotify['actionType'] 	= '2';
						$chatNotify['chatType'] 	= 2;
						$chatNotify['groupId'] 		= $groupId;

						$muteNotification = $this->getGuppyChatAction($chatNotify);
						if(!empty($muteNotification)){
							$muteNotification = true;
						}else{
							$muteNotification = false;
						}
						$messagelistData['muteNotification'] 	= $muteNotification;
						if($single['userId'] == $loginedUser){
							$memberName = $single['userName'];
							$userMessages[$single['userId']][] = array(
								'type' 			=> 4,
								'membersUpdate' => array($single['userId'] => $memberName),
								'memberIds'		=> array($single['userId']),
							);
							$messagelistData['memberDisable'] 	= true;
							$messagelistData['timestamp'] 		= false;
							$messagelistData['groupDetail'] 	= NULL;
							$messagelistData['groupImage'] 		= WPGuppy_GlobalSettings::get_plugin_url().'public/images/group.jpg';
						}else{
							$messagelistData['memberDisable'] 		= false;
							$messagelistData['timestamp']			= $timestamp; 
							$messagelistData['groupDetail'] 		= $groupDetail;
							$messagelistData['groupImage']			= $groupDetail['groupImage'];
							$userMessages[$single['userId']] 		= $messages;
						}
						$conversationList[$single['userId']] 	= $messagelistData;
						$groupMembers[] = $single['userId'];
					}
				}
			}

			// send data to pushers
			if($this->pusher){
				if(!empty($groupMembers)){
					$batchRequests = array();
					foreach($groupMembers as $id){
						$messagelistData 	= $conversationList[$id];
						$messageData 		= $chatData;
						if($id == $loginedUser){
							$messagelistData['isSender'] 			= true;
							$messageData['isSender'] 				= true;
							$messagelistData['userDisableReply'] 	= true;
						}
						$pusherData = array(
							'chatId' 	=> $this->getChatKey('2', $groupId),
							'chatType'  => 2,
						);
						if(!empty($userMessages[$id])){
							$allMessages = $userMessages[$id];
							foreach($allMessages as $single){
								$message = array();
								$message['type'] 					= $single['type'];
								$message['memberIds'] 				= $single['memberIds'];
								$messageData['message'] 			= $message;
								$messagelistData['message'] 		= $message;
								$messageData['membersUpdate'] 		= $single['membersUpdate'];
								$messagelistData['membersUpdate'] 	= $single['membersUpdate'];
								$pusherData['chatData'] 			= $messageData;
								if($single['type'] ==  4 &&  in_array($id,$single['memberIds'])){
									unset($pusherData['chatData']);
								}
								$pusherData['messagelistData'] 	= $messagelistData;
							}
						}
						$batchRequests[] = array(
							'channel' 	=> 'private-user-' . $id,
							'name' 		=> 'groupChatData',
							'data'		=> $pusherData,
						);	
					}
					$this->pusher->triggerBatch($batchRequests);
				}
			}
			$type = 'success';
			$json['type'] = $type;
			$json['userMessages'] 		= $userMessages;	
			$json['chatData'] 			= $chatData;	
			$json['chatType'] 			= 2;	
			$json['messagelistData'] 	= $conversationList;	
			$json['groupMembers'] 		= $groupMembers;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * send message
		 *
		 * @since    1.0.0
		*/

		public  function sendMessage($data){
			$headers    	= $data->get_headers();
			$params     	= !empty($data->get_params()) 		? $data->get_params() 		: '';
			$files     		= !empty($data->get_file_params()) ? $data->get_file_params() 	: '';

			$authToken  	= ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$messageData 	= $attachmentData = $userChat = $groupMembers = $replyMessage = $json  = array();
			$verifyMember 	= false;
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$receiverId 	= !empty($params['receiverId']) 	? intval($params['receiverId']) 	: 0; 
			$groupId 		= !empty($params['groupId']) 		? intval($params['groupId']) 		: 0;
			$postId 		= !empty($params['postId']) 		? intval($params['postId']) 		: 0;
			$chatType 		= !empty($params['chatType']) 		? $params['chatType'] 				: 0;
			$messageType 	= !empty($params['messageType']) 	? intval($params['messageType']) 	: 0;
			$message 		= !empty($params['message']) 		? $params['message'] 				: '';
			$replyId 		= !empty($params['replyId']) 		? intval($params['replyId']) 		: 0;
			$latitude 		= !empty($params['latitude']) 		? $params['latitude'] 				: 0;
			$longitude 		= !empty($params['longitude']) 		? $params['longitude'] 				: 0;
			$loginedUser 	= !empty($params['userId']) 		? intval($params['userId']) 		: 0; 
			
			if($chatType == '0'){
				$where 		 	= " ((action_by =". $loginedUser." AND action_to =". $receiverId.") OR( action_by =". $receiverId." AND action_to =". $loginedUser." )) AND action_type=1 AND post_id=".$postId; 
				$verifyMember 	= $this->guppyModel->getData('id', 'wpguppy_postchat_action', $where );
				if(empty($verifyMember)){
					$where 		 	= " ((action_by =". $loginedUser." AND action_to =". $receiverId.") OR( action_by =". $receiverId." AND action_to =". $loginedUser." )) AND action_type=0"; 
					$verifyMember 	= $this->guppyModel->getData('id', 'wpguppy_postchat_action', $where );
					if(empty($verifyMember)){
						$verifyMember = true;
					}
				}
			}elseif($chatType == '1'){
				$where 		 	= " (send_by =". $loginedUser." AND send_to =". $receiverId.") OR( send_by =". $receiverId." AND send_to =". $loginedUser." ) AND friend_status='1'"; 
				$verifyMember 	= $this->guppyModel->getData('id', 'wpguppy_friend_list', $where );
			}elseif($chatType == '2'){
				$where 		 	= " group_id =". $groupId." AND member_id=".$loginedUser." AND member_status ='1' AND group_status='1'"; 
				$verifyMember 	= $this->guppyModel->getData('id', 'wpguppy_group_member', $where );
			}
			if(!$verifyMember){
				$message   				= esc_html__('You are not allowed to perform this action!', 'wp-guppy');
				$json['type'] 			= 'error';
				$json['message_desc']   = $message; 
				return new WP_REST_Response($json , 203);
			}
			$userType = '0';
			$isOnline = false;
			if(get_userdata($loginedUser)){
				$userType = '1';
				if($chatType == '1' || $chatType == '0'){
					$isOnline = wpguppy_UserOnline($receiverId);
				}
			}
			if($messageType == '0'){
				$message = !empty($message) ? wp_strip_all_tags(sanitize_textarea_field($message)) : '';
			}elseif(($messageType=='1' || $messageType=='3') 
				&& !empty($files)){
				$filterData = array();
				$filterData['userId'] 		= $loginedUser;
				$filterData['receiverId'] 	= $receiverId;
				$filterData['groupId'] 		= $groupId;
				$filterData['postId'] 		= $postId;
				$attachmentData = $this->uploadAttachments($messageType, $files, $filterData);
				if(!empty($attachmentData)){
					$messageData['attachments'] = serialize($attachmentData);	
				}
				$message = NULL;
			}elseif($messageType=='2' 
				&& !empty($latitude) 
				&& !empty($longitude)){

				$location = array(
					'latitude' 	=> sanitize_text_field($latitude),
					'longitude' => sanitize_text_field($longitude),
				);
				$message 		= serialize($location);	
			}

			// get message detail if reply message 
			if(!empty($replyId)){
				$where 	= "id=". $replyId; 
				$messageDetail = $this->guppyModel->getData('message,message_type,chat_type,attachments','wpguppy_message', $where );
				if(!empty($messageDetail)){
					$messageDetail = $messageDetail[0];
					$replyMessage['messageId'] 		= $replyId;
					$replyMessage['messageType'] 	= $messageDetail['message_type'];
					$replyMessage['message'] 		= $messageDetail['message_type'] == 2 ?  unserialize($messageDetail['message']) : $messageDetail['message'];
					$replyMessage['chatType'] 		= $messageDetail['chat_type'];
					$replyMessage['attachmentsData'] = !empty($messageDetail['attachments']) ? unserialize($messageDetail['attachments']) : '';
				}
			}

			if($chatType == '0'){
				$messageData['receiver_id'] 	= $receiverId;
				$messageData['post_id'] 		= $postId;
			}elseif($chatType == '1'){
				$messageData['receiver_id'] 	= $receiverId; 
			}elseif($chatType == '2'){
				$messageData['group_id'] 		= $groupId;
			}
			$messageData['sender_id'] 			= $loginedUser; 
			$messageData['user_type'] 			= $userType;  
			$messageData['message'] 			= $message; 
			$messageData['chat_type'] 			= $chatType; 
			$messageData['message_type'] 		= $messageType; 
			$messageData['reply_message'] 		= !empty($replyMessage) ? serialize($replyMessage) : NULL; 
			$messageData['timestamp'] 			= strtotime(date('Y-m-d H:i:s')); 
			$messageData['message_sent_time'] 	= date('Y-m-d H:i:s'); 
			
			$response = $this->guppyModel->insertData('wpguppy_message',$messageData);
			if($response){
				$messagelistData = $filterData = $groupMembers = $chatNotify = $conversationList =  array();

				$filterData['senderId'] 	= $loginedUser;
				$filterData['chatType'] 	= $chatType;
				
				// get sender user info 
				$senderUserName = $senderUserAvatar = '';
				$senderUserData 	= $this->getUserInfo($userType, $loginedUser);
				if(!empty($senderUserData)){
					$senderUserName 	= $senderUserData['userName'];
					$senderUserAvatar 	= $senderUserData['userAvatar'];
				}	
				
				// get receiver user info 
				$receiverUserName = $receiverUserAvatar = '';
				$receiverUserData 			= $this->getUserInfo($userType, $receiverId);
				if(!empty($receiverUserData)){
					$receiverUserName 			= $receiverUserData['userName'];
					$receiverUserAvatar 		= $receiverUserData['userAvatar'];
				}

				$messagelistData['messageId'] 			= $response;	
				$messagelistData['message'] 			= ($messageType=='2' ? unserialize($message) : $message);	
				$messagelistData['timeStamp'] 			= $messageData['timestamp'];	
				$messagelistData['messageType'] 		= $messageType;
				$messagelistData['chatType'] 			= $chatType;
				$messagelistData['isSender'] 			= false;
				$messagelistData['messageStatus'] 		= '0';
				$messagelistData['userName'] 			= $senderUserName;
				$messagelistData['userAvatar'] 			= $senderUserAvatar;
				
				$chatData = array(
					'chatType' 				=> $chatType,
					'messageId' 			=> $response,	
					'message' 				=> ($messageType=='2' ? unserialize($message) : $message),	
					'timeStamp' 			=> $messageData['timestamp'],	
					'messageType' 			=> $messageType,
					'userType' 				=> $userType,
					'messageStatus' 		=> '0',	
					'attachmentsData' 		=> $attachmentData,	
					'replyMessage' 			=> !empty($replyMessage) ? $replyMessage : NULL,	
					'isOnline' 				=> $isOnline,	
					'metaData'				=> !empty($params['metaData']) ? $params['metaData'] : false,
					'userName'				=> $senderUserName,
					'userAvatar'			=> $senderUserAvatar,
				);
				
				if(!empty($postId) && $chatType == '0'){ 
					$postTitle 					= get_the_title($postId);
					$chatData['chatId'] 		= $this->getChatKey('0', $postId, $receiverId);
					
					$filterData['postId'] 		= $postId;
					$filterData['receiverId'] 	= $receiverId;
					
					$chatNotify['postId'] 		= $postId;
					$chatNotify['actionBy'] 	= $receiverId;
					$chatNotify['actionType'] 	= '2';
					$chatNotify['chatType'] 	= '0';
					
					$getUnreadCount = $this->guppyModel->getUnreadCount($filterData);
					// check notification sound
					$muteNotification = $this->getGuppyChatAction($chatNotify);
					if(!empty($muteNotification)){
						$muteNotification = true;
					}else{
						$muteNotification = false;
					}
					$postImage = $this->getPostImage($postId);
					$messagelistData['chatId']				= $this->getChatKey('0', $postId, $loginedUser);
					$messagelistData['postTitle']			= $postTitle;
					$messagelistData['postImage']			= $postImage;
					$messagelistData['postId']				= $postId;
					$messagelistData['postReceiverId']		= $loginedUser;
					$messagelistData['blockedId'] 			= false;
					$messagelistData['isBlocked'] 			= false;
					$messagelistData['isOnline'] 			= $isOnline;
					$messagelistData['UnreadCount'] 		= $getUnreadCount;
					$messagelistData['muteNotification'] 	= $muteNotification;
					$json['messagelistData'] 				= $messagelistData;
				}elseif(!empty($receiverId) && $chatType == '1'){
					
					$filterData['receiverId'] 	= $receiverId;
					$chatNotify['actionBy'] 	= $receiverId;
					$chatNotify['userId'] 		= $loginedUser;
					$chatNotify['actionType'] 	= '2';
					$chatNotify['chatType'] 	= '1';
					$chatData['chatId'] 		= $this->getChatKey('1', $receiverId);

					$getUnreadCount = $this->guppyModel->getUnreadCount($filterData);
					
					// check notification sound
					$muteNotification = $this->getGuppyChatAction($chatNotify);
					if(!empty($muteNotification)){
						$muteNotification = true;
					}else{
						$muteNotification = false;
					}

					$messagelistData['chatId']				= $this->getChatKey('1', $loginedUser);
					$messagelistData['blockedId'] 			= false;
					$messagelistData['isBlocked'] 			= false;
					$messagelistData['isOnline'] 			= $isOnline;
					$messagelistData['UnreadCount'] 		= $getUnreadCount;
					$messagelistData['muteNotification'] 	= $muteNotification;
					$json['messagelistData'] 				= $messagelistData;
				}elseif(!empty($groupId) && $chatType == '2'){

					$filterData['groupId'] 		= $groupId;
					$chatNotify['groupId'] 		= $groupId;
					$chatData['chatId'] 		= $this->getChatKey('2', $groupId);
					$chatNotify['actionType'] 	= '2';
					$chatNotify['chatType'] 	= '2';

					// get group info
					$where 			= "id=". $groupId; 
					$groupInfo 		= $this->guppyModel->getData('group_title,group_image,disable_reply', 'wpguppy_group', $where );
					$groupDetail 	= $this->guppyModel->getGroupDetail($groupId);
					
					if(!empty($groupInfo)){
						$messagelistData['groupTitle'] = $groupInfo[0]['group_title'];
						$messagelistData['groupImage'] = $groupInfo[0]['group_image'];
					}
					$messagelistData['groupDetail'] = $groupDetail;
					$messagelistData['chatId'] 		= $this->getChatKey('2', $groupId);
					if(!empty($groupDetail)){
						foreach($groupDetail['memberAvatars'] as $member){
							if($member['memberStatus'] == '1'){
								$chatNotify['actionBy'] 	= $member['userId'];
								$unreadCount = 0;
								// get group members chat actions
								if($member['userId'] != $loginedUser){
									$groupMembers[] = $member['userId'];
									$statusActions = $params = array();	
									$params['actionBy'] 	= $member['userId'];
									$params['chatType'] 	= $chatType;
									$params['groupId'] 		= $groupId;
									$params['orderBy'] 		= 'action_type';
									$params['actionType'] 	= array('3','4','5'); // group left or removed from group
									$chatActions = $this->getGuppyChatAction($params);
									$filterData['memberAddedDate'] = $member['memberAddedDate'];
									$filterData['senderId'] = $member['userId'];
									$filterData['groupAction'] = array();
									$deleteGroupTime = '';
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
											}
										}
										if($deleteGroupTime!=''){
											$filterData['groupAction']['deleteGroupTime'] = $deleteGroupTime;
										}
										$filterData['groupAction']['status'] = $statusActions;
									}
									$unreadCount = $this->guppyModel->getUnreadCount($filterData);
								}
								$messagelistData['UnreadCount'] 		= $unreadCount;
								$messagelistData['userDisableReply'] 	= false;
								if($groupInfo[0]['disable_reply'] == '1' && $member['groupRole'] == '0'){
									$messagelistData['userDisableReply'] = true;
								}

								// check notification sound
								$muteNotification = $this->getGuppyChatAction($chatNotify);
								if(!empty($muteNotification)){
									$muteNotification = true;
								}else{
									$muteNotification = false;
								}
								$messagelistData['muteNotification'] 	= $muteNotification;
								$conversationList[$member['userId']] 	= $messagelistData;
							}
						}
					}
					$json['groupMembers'] 		= $groupMembers;
					$json['messagelistData'] 	= $conversationList;	
				}
				
				if($this->pusher){
					if($chatType == '0'){
						$batchRequests = array();
						
						// send to receiver
						$pusherData = array(
							'chatId' 			=> $this->getChatKey('0', $postId, $loginedUser),
							'chatData'			=> $chatData,
							'chatType'			=> $chatType,
							'messagelistData' 	=> $messagelistData
						);
						$batchRequests[] = array(
							'channel' 	=> 'private-user-' . $receiverId,
							'name' 		=> 'recChatData',
							'data'		=> $pusherData,
						);

						// send to sender
						$chatNotify['actionBy'] 	= $loginedUser;
						$chatNotify['userId'] 		= $receiverId;
						$chatNotify['actionType'] 	= '2';
						$chatNotify['chatType'] 	= '1';
						// check notification sound
						$muteNotification = $this->getGuppyChatAction($chatNotify);
						if(!empty($muteNotification)){
							$muteNotification = true;
						}else{
							$muteNotification = false;
						}
						$chatData['isSender'] 					= true;
						$messagelistData['isSender'] 			= true;
						$messagelistData['userName'] 			= $receiverUserName;
						$messagelistData['userAvatar'] 			= $receiverUserAvatar;
						$messagelistData['UnreadCount'] 		= 0;
						$messagelistData['chatId']				= $this->getChatKey('0', $postId, $receiverId);
						$messagelistData['postReceiverId']		= $receiverId;
						$messagelistData['muteNotification']	= $muteNotification;
						$pusherData = array(
							'chatId' 			=> $this->getChatKey('0', $postId, $receiverId),
							'chatType'			=> $chatType,
							'chatData'			=> $chatData,
							'messagelistData' 	=> $messagelistData,
						);
						$batchRequests[] = array(
							'channel' 	=> 'private-user-' . $loginedUser,
							'name' 		=> 'senderChatData',
							'data'		=> $pusherData,
						);
						$this->pusher->triggerBatch($batchRequests);
					}elseif($chatType == '1'){
						$batchRequests = array();
						// send to receiver
						$pusherData = array(
							'chatId' 			=> $this->getChatKey('1', $loginedUser),
							'chatData'			=> $chatData,
							'chatType'			=> $chatType,
							'messagelistData' 	=> $messagelistData
						);
						$batchRequests[] = array(
							'channel' 	=> 'private-user-' . $receiverId,
							'name' 		=> 'recChatData',
							'data'		=> $pusherData,
						);

						// send to sender
						$chatNotify['actionBy'] 	= $loginedUser;
						$chatNotify['userId'] 		= $receiverId;
						$chatNotify['actionType'] 	= '2';
						$chatNotify['chatType'] 	= '1';
						// check notification sound
						$muteNotification = $this->getGuppyChatAction($chatNotify);
						if(!empty($muteNotification)){
							$muteNotification = true;
						}else{
							$muteNotification = false;
						}

						$chatData['isSender'] 					= true;
						$messagelistData['isSender'] 			= true;
						$messagelistData['userName'] 			= $receiverUserName;
						$messagelistData['userAvatar'] 			= $receiverUserAvatar;
						$messagelistData['UnreadCount'] 		= 0;
						$messagelistData['chatId']				= $this->getChatKey('1', $receiverId);
						$messagelistData['muteNotification']	= $muteNotification;
						$pusherData = array(
							'chatId' 			=> $this->getChatKey('1', $receiverId),
							'chatType'			=> $chatType,
							'chatData'			=> $chatData,
							'messagelistData' 	=> $messagelistData,
						);
						$batchRequests[] = array(
							'channel' 	=> 'private-user-' . $loginedUser,
							'name' 		=> 'senderChatData',
							'data'		=> $pusherData,
						);
						 $this->pusher->triggerBatch($batchRequests);
						
					}elseif($chatType == '2'){
						// send to all group members
						$pusherData = array(
							'chatId' 			=> $this->getChatKey('2', $groupId),
							'chatType'			=> $chatType,
							'chatData'			=> $chatData,
						);
						if(!empty($groupMembers)){
							$batchRequests = array();
							foreach($groupMembers as $id){
								$pusherData['messagelistData'] = $conversationList[$id];
								$batchRequests[] = array(
									'channel' 	=> 'private-user-' . $id,
									'name' 		=> 'recChatData',
									'data'		=> $pusherData,
								);
							}
							
							// send to sender
							$senderdata = $conversationList[$loginedUser];
							$chatData['isSender'] 		= true;
							$senderdata['isSender'] 	= true;
							$senderdata['UnreadCount'] 	= 0;
							$pusherData = array(
								'chatId' 			=> $this->getChatKey('2', $groupId),
								'chatType'			=> $chatType,
								'chatData'			=> $chatData,
								'messagelistData' 	=> $senderdata,
							);
							$batchRequests[] = array(
								'channel' 	=> 'private-user-' . $loginedUser,
								'name' 		=> 'senderChatData',
								'data'		=> $pusherData,
							);
							$this->pusher->triggerBatch($batchRequests);
						}
					}
				}
				$json['type'] 				= 'success';
				$json['chatData'] 			= $chatData;
				$json['chatType'] 			= $chatType;
			}else{
				$json['type'] 				= 'error';
			}
			return new WP_REST_Response($json , 200);
		}

		/**
		 * get user info
		 *
		 * @since    1.0.0
		*/
		public function getUserInfo($userType,$userId){
			$userName = $userAvatar = '';
			if($userType=='1'){
				$userAvatar 	= $this->guppyModel->getUserInfoData('avatar', $userId, array('width' => 150, 'height' => 150));
				$userName 		= $this->guppyModel->getUserInfoData('username', $userId, array());
				$where 		 	= "user_id=". $userId; 
				$userinfo 		= $this->guppyModel->getData('user_name,user_image','wpguppy_users',$where );
				if(!empty($userinfo)){
					$info 					= $userinfo[0];
					$userName 			= $info['user_name'];
					if(!empty($info['user_image'])){
						$userImage 			= unserialize($info['user_image']);
						$userAvatar 		= $userImage['attachments'][0]['thumbnail'];
					}
				}
			}else{
				$where 		 	= "id=". $userId; 
				$userinfo 		= $this->guppyModel->getData('*','wpguppy_guest_account',$where );
				if(!empty($userinfo)){
					$info 					= $userinfo[0];
					$userName 				= $info['name'];
					$userAvatar 			= $this->guppyModel->getUserInfoData('avatar', '0', array('width' => 150, 'height' => 150));
				}
			}
			if($userName != ''){
				$lastname = '';
				$name =	explode(' ' , $userName);
				if(!empty($name[1])){
					$lastname = ' '. ucfirst(substr($name[1], 0, 1));
				}
				$userName = ucfirst($name[0]).$lastname; 
			}
			return array(
				'userName' 		=> $userName,
				'userAvatar' 	=> $userAvatar,
			);
		}

		/**
		 * upload attachments
		 *
		 * @since    1.0.0
		*/
		public function uploadAttachments($type, $files, $params){
			
			$upload 		= wp_upload_dir();
			$upload_dir 	= $upload['path'].'/';
			$attachmentType = '';
			$attachmentData = $attachments = array();
			$upload_attachments_dir = !empty($this->guppySetting['upload_attachments']) ? $this->guppySetting['upload_attachments'] : 'custom';
			
			if($upload_attachments_dir == 'custom'){
				if(!empty($params['isProfile'])){
					$basedir 	= $upload['basedir'] . "/wpguppy_attachments/profile/";
					$baseurl 	= $upload['baseurl'] . "/wpguppy_attachments/profile/";
					$foldername = $params['userId'] . '/';
					$upload_dir = $basedir.$foldername;
					$upload_url = $baseurl.$foldername;
					wp_mkdir_p($upload_dir);
				}elseif(!empty($params['groupId'])){
					$basedir 	= $upload['basedir'] . "/wpguppy_attachments/group/";
					$baseurl 	= $upload['baseurl'] . "/wpguppy_attachments/group/";
					$foldername = $params['groupId'].'/';
					$upload_dir = $basedir.$foldername;
					$upload_url = $baseurl.$foldername;
					wp_mkdir_p($upload_dir);
				}elseif(!empty($params['postId'])){
					$basedir 	= $upload['basedir'] . "/wpguppy_attachments/posts/";
					$baseurl 	= $upload['baseurl'] . "/wpguppy_attachments/posts/";
					
					$foldername 	= $params['postId'] .'/'. $params['userId'] .'_'.$params['receiverId'].'/';
					$foldername1 	= $params['postId'] .'/'. $params['receiverId'] .'_'.$params['userId'].'/';
					
					if(is_dir($basedir.$foldername)){
						$upload_dir = $basedir.$foldername;
						$upload_url = $baseurl.$foldername;
					}elseif(is_dir($basedir.$foldername1)){
						$upload_dir = $basedir.$foldername1;
						$upload_url = $baseurl.$foldername1;
					}else{
						$upload_dir = $basedir.$foldername;
						$upload_url = $baseurl.$foldername;
						wp_mkdir_p($upload_dir);
					}
				}else{
					$basedir 	= $upload['basedir'] . "/wpguppy_attachments/one_to_one/";
					$baseurl 	= $upload['baseurl'] . "/wpguppy_attachments/one_to_one/";
					
					$foldername = $params['userId'] .'_'.$params['receiverId'].'/';
					$foldername1 = $params['receiverId'] .'_'.$params['userId'].'/';
					
					if(is_dir($basedir.$foldername)){
						$upload_dir = $basedir.$foldername;
						$upload_url = $baseurl.$foldername;
					}elseif(is_dir($basedir.$foldername1)){
						$upload_dir = $basedir.$foldername1;
						$upload_url = $baseurl.$foldername1;
					}else{
						$upload_dir = $basedir.$foldername;
						$upload_url = $baseurl.$foldername;
						wp_mkdir_p($upload_dir);
					}
					
				}
			}else{
				require_once(ABSPATH . 'wp-admin/includes/image.php');
			}
			foreach($files as $file){

				$name 		= sanitize_file_name($file["name"]);
				
				if($type == '3'){
					$name = 'audio-'.time().'.mp3';
				}

				//file type check
				$filetype 			= wp_check_filetype($file['name']);
				$not_allowed_types	= array('php','javascript','js','exe','text/javascript','html');
				$file_ext			= !empty($filetype['ext']) ? $filetype['ext'] : '';
				
				if(empty($file_ext) || in_array($file_ext,$not_allowed_types)){
					$json['type'] = 'error';
					$json['message_desc']   = esc_html__('These file types are not allowed!', 'wp-guppy');
					return new WP_REST_Response($json , 203);
				}
				$i = 0;
				$parts = pathinfo($name);
				while (file_exists($upload_dir . $name)) {
					$i++;
					$name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
				}

				if($type =='3'){
					$attachmentType = 'voice_note';
				}else{
					if(preg_match('/image\/*/', $file['type'])){
						$attachmentType = 'images';
					}elseif(preg_match('/video\/*/', $file['type'])){
						$attachmentType = 'video';
					}elseif(preg_match('/audio\/*/', $file['type'])){
						$attachmentType = 'audio';
					}elseif(preg_match('/pdf\/*/', $file['type']) 
						|| preg_match('/document\/*/', $file['type'])
						|| preg_match('/zip\/*/', $file['type'])
						|| preg_match('/powerpoint\/*/', $file['type'])
						|| preg_match('/text\/*/', $file['type'])
						|| preg_match('/vnd.ms-excel\/*/', $file['type'])
						|| preg_match('/spreadsheet\/*/', $file['type'])
						|| preg_match('/msword\/*/', $file['type'])
						|| preg_match('/octet-stream\/*/', $file['type'])
						){
						$attachmentType = 'file';
					}
				}
				
				$size       	= $file['size'];
				$file_size  	= size_format($size, 2);

				//move file
				$newFile = $upload_dir .$name;
				$is_moved = move_uploaded_file($file["tmp_name"], $newFile);
				
				if($is_moved){
					$filename = basename($newFile);
					$attach_id = 0;
					$thumbnail = '';
					$file = $upload_url.sanitize_file_name($filename);

					if($upload_attachments_dir == 'wpmedia'){
						$file = $upload['url'] .'/'. sanitize_file_name($filename);
						if($type== '1'){
							$attachment = array(
								'post_mime_type' 	=> $filetype['type'],
								'post_title' 		=> sanitize_file_name($filename),
								'post_content' 		=> '',
								'post_status' 		=> 'inherit'
							);
							$attach_id 		= wp_insert_attachment($attachment, $newFile);
						}
						if($attachmentType =='images' || $attachmentType =='file'){
							$attach_data 	= wp_generate_attachment_metadata($attach_id, $newFile);
							wp_update_attachment_metadata($attach_id, $attach_data);
							$thumbnail 		= !empty($attach_data['sizes']['thumbnail']['file']) ? $upload['url'] . '/'. $attach_data['sizes']['thumbnail']['file'] : $upload['url'] . '/'. sanitize_file_name($filename);
							
						}
					}else{
						if($attachmentType == 'images'){
							$thumbnail = $file;
							$image = wp_get_image_editor( $newFile );
							if (!is_wp_error( $image ) ) {
								$registered_sizes = wp_get_registered_image_subsizes();
								$newSize = $image->make_subsize( $registered_sizes['thumbnail'] );
								if(!is_wp_error( $newSize )){
									$thumbnail = $upload_url . $newSize['file'];
								}
							}
						}
					}

					$attachments[] = array(
						'attachmentId' 			=> $attach_id,
						'file' 					=> $file,
						'fileName'				=> sanitize_file_name($filename),
						'thumbnail' 			=> $thumbnail,
						'fileSize' 				=> esc_attr($file_size),
						'fileType' 				=> esc_attr($filetype['ext']),
					);

				}
			}
			if(!empty($attachments)){
				$attachmentData = array(
					'saveTo' 			=> $upload_attachments_dir,
					'attachmentType' 	=> $attachmentType,
					'attachments' 		=> $attachments,
				);
			}
			return $attachmentData;
		}

		/**
		 * Load user post messages
		 *
		 * @since    1.0.0
		*/
		public function getUserPostMessageslist($data){
			$headers    = $data->get_headers();
			$params     = ! empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  = ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$guppyPostMessageList  = $json  = array();
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$offset 		= !empty($params['offset']) ? intval($params['offset']) : 0; 
			$searchQuery 	= !empty($params['search']) ? sanitize_text_field($params['search']) : '';
			$loginedUser 	= !empty($params['userId']) ? intval($params['userId']) : 0;
			$fetchResults 	= $this->guppyModel->getUserPostMessageslist($loginedUser, $this->showRec, $offset, $searchQuery);
			$postImages 	= array();
			if(!empty($fetchResults)){
				foreach($fetchResults as $result){

					$messageData = array();
					if($result['sender_id'] != $loginedUser){
						$receiverId = $result['sender_id'];
					}else{
						$receiverId = $result['receiver_id']; 
					}

					if(empty($postImages) || !isset($postImages[$result['post_id']])){
						$postImage = $this->getPostImage($result['post_id']);
						$postImages[$result['post_id']] = $postImage;	
					}else{
						$postImage = $postImages[$result['post_id']];
					}
					$message 						= $result['message'];
					$messageType 					= $result['message_type'];
					$timestamp 						= $result['timestamp'];
					$clearChat 						= false;
					$unreadCount					= 0;
					
					$userData 	= $this->getUserInfo('1', $receiverId);
					$messageData['userAvatar']		= $userData['userAvatar'];	
					$messageData['userName'] 		= $userData['userName'];
					
					$userStatus = $this->getUserStatus($loginedUser, $receiverId,'0', $result['post_id']);
					$messageData['blockedId'] 		= $userStatus['blockedId'];
					$messageData['isOnline'] 		= $userStatus['isOnline'];
					$messageData['isBlocked'] 		= $userStatus['isBlocked'];
					
					$filterData =  array();
					$filterData['chatType'] 		= '0';
					$filterData['postId'] 			= $result['post_id'];
					$filterData['senderId'] 		= $receiverId;
					$filterData['receiverId'] 		= $loginedUser;	
					
					$unreadCount = $this->guppyModel->getUnreadCount($filterData);
					if($result['message_status'] == 2 ){
						$message = '';
					}
					$isSender = true;
					if($result['sender_id'] != $loginedUser){
						$isSender= false; 
					}

					// check chat is cleard or not
					$where 		 	= " (action_by =". $loginedUser." AND action_to =". $receiverId.") AND action_type=3 AND post_id=".$result['post_id']; 
					$chatActions 	= $this->guppyModel->getData('action_time', 'wpguppy_postchat_action', $where );
					$chatClearTime = !empty($chatActions) ? $chatActions[0]['action_time'] : '';
					if(!empty($chatClearTime) && strtotime($chatClearTime) > strtotime($result['message_sent_time'])){
						$clearChat 	= true;
						$message 	= '';
					}
					$where 		 	= " (action_by =". $loginedUser." AND action_to =". $receiverId.") AND action_type=2 AND post_id=".$result['post_id']; 
					$chatNotify 	= $this->guppyModel->getData('id', 'wpguppy_postchat_action', $where );					
					if(!empty($chatNotify)){
						$muteNotification = true;
					}else{
						$muteNotification = false;
					}
					
					if($message!=''){
						if($messageType == '0'){
							$message = html_entity_decode( stripslashes($message),ENT_QUOTES );
						}elseif($messageType == '2' || $messageType == '4'){
							$message = is_serialized($message) ? unserialize($message) : $message;
						}
					}
					$key 								= $this->getChatKey('0', $result['post_id'], $receiverId);
					$messageData['postId']				= $result['post_id'];
					$messageData['postReceiverId']		= $receiverId;
					$messageData['isSender'] 			= $isSender;
					$messageData['message'] 	   		= $message;
					$messageData['messageType'] 		= $messageType;
					$messageData['clearChat'] 			= $clearChat;
					$messageData['messageStatus'] 		= $result['message_status'];
					$messageData['chatType'] 			= $result['chat_type'];
					$messageData['UnreadCount'] 		= intval( $unreadCount );
					$messageData['timeStamp'] 			= $timestamp;
					$messageData['muteNotification']	= $muteNotification;
					$messageData['messageId']			= $result['id'];
					$messageData['postImage']			= $postImage;
					$messageData['postTitle']			= $result['post_title'];
					$messageData['chatId']				= $key;
					$guppyPostMessageList[$key] 		= $messageData;
					
				}
			}
			
			$json['type'] 					= 'success';
			$json['guppyPostMessageList']   = (Object)$guppyPostMessageList;
			return new WP_REST_Response($json , 200);		 
		}

		/**
		 * get post Image
		 *
		 * @since    1.0.0
		*/
		public function getPostImage($postId){
			$postImage 	= '';
			$thumbId 	= get_post_thumbnail_id( $postId );
			if (!empty( $thumbId)){
				$thumbUrl = wp_get_attachment_image_src( $thumbId, array( 150, 150 ), true );
				if ( $thumbUrl[1] == 150 && $thumbUrl[2] == 150 ) {
					$postImage =  !empty( $thumbUrl[0] ) ? $thumbUrl[0] : '';
				} else {
					$thumbUrl = wp_get_attachment_image_src( $thumbId, 'full', true );
					$postImage =  !empty( $thumbUrl[0] ) ? $thumbUrl[0] : '';
				}
			}else{
				$postImage = WPGuppy_GlobalSettings::get_plugin_url().'public/images/default-post.jpg';
			}
			return $postImage;
		}

		/**
		 * get post chat info
		 *
		 * @since    1.0.0
		*/
		public function getPostInfo($data){
			$headers    = $data->get_headers();
			$params     = ! empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  = ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$postInfo 	= $json  = array();
			$isBlocked 	= $blockerId = $blockedId = false;
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$loginedUser 	= !empty($params['userId']) ? intval($params['userId']) : 0;
			$postId 		= !empty($params['postId']) ? intval($params['postId']) : 0;
			$postAuthor		= get_post_field('post_author', $postId, true);
			$postType 		= get_post_type($postId);
			if($postAuthor != $loginedUser){
				$where 		 	= " ((action_by =". $loginedUser." AND action_to =". $postAuthor.") OR( action_by =". $postAuthor." AND action_to =". $loginedUser." )) AND action_type=1 AND post_id=".$postId; 
				$chatAction 	= $this->guppyModel->getData('action_by,action_to', 'wpguppy_postchat_action', $where );
				if(empty($chatAction)){
					$where 		 	= " ((action_by =". $loginedUser." AND action_to =". $postAuthor.") OR( action_by =". $postAuthor." AND action_to =". $loginedUser." )) AND action_type=0"; 
					$chatAction 	= $this->guppyModel->getData('action_by,action_to', 'wpguppy_postchat_action', $where );
					if(!empty($chatAction)){
						$isBlocked = true;
						$blockerId = $chatAction[0]['action_by'];
						$blockedId = $chatAction[0]['action_to'];
					}
				}else{
					$isBlocked = true;
					$blockerId = $chatAction[0]['action_by'];
					$blockedId = $chatAction[0]['action_to'];
				}
				$userData 	= $this->getUserInfo(1, $postAuthor);
				if(!empty($userData)){
					$userName 	= $userData['userName'];
					$userAvatar = $userData['userAvatar'];
				}
				$filterData =  array();
				$filterData['chatType'] 		= '0';
				$filterData['postId'] 			= $postId;
				$filterData['senderId'] 		= $postAuthor;
				$filterData['receiverId'] 		= $loginedUser;	
				$unreadCount = $this->guppyModel->getUnreadCount($filterData);
				
				$postTitle 						= get_the_title($postId);
				$postInfo['postTitle'] 			= !empty($postTitle) ? $postTitle : '';
				$postInfo['postImage'] 			= $this->getPostImage($postId);
				$postInfo['isOnline']			= wpguppy_UserOnline($postAuthor);
				$postInfo['postId']				= $postId;
				$postInfo['postReceiverId']		= $postAuthor;
				$postInfo['isBlocked']			= $isBlocked;
				$postInfo['blockedId']			= $blockedId;
				$postInfo['blockerId']			= $blockerId;
				$postInfo['userName']			= $userName;
				$postInfo['userAvatar']			= $userAvatar;
				$postInfo['chatId'] 			= $this->getChatKey('0', $postId, $postAuthor);
				$postInfo['chatType'] 			= '0';
				$postInfo['UnreadCount'] 		= intval( $unreadCount );
			}
			$json['type'] 		= 'success';
			$json['postInfo']   = $postInfo;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * get post chat info
		 *
		 * @since    1.0.0
		*/
		public function messangerChatInfo($data){
			$headers    = $data->get_headers();
			$params     = ! empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  = ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$chatInfo 	= $json  = array();
			$isBlocked 	= $blockerId = $blockedId = false;
			$response = $this->guppyAuthentication($params, $authToken);
			if(!empty($response) && $response['type']=='error'){
				return new WP_REST_Response($response , 203);
			}
			$loginedUser 	= !empty($params['userId']) 	? intval($params['userId']) 	: 0;
			$chatId 		= !empty($params['chatId']) 	? $params['chatId'] 			: '';
			$chatType 		= !empty($params['chatType']) 	? intval($params['chatType']) 	: 0;
			$chatId 		= explode('_', $chatId);
			$verifyMember = false;
			if($chatType == 0){
				if(empty($chatId[0]) || empty($chatId[1])){
					$json['type'] = 'error';
					$json['message_desc']   = esc_html__('You are not allowed to perform this action!', 'wp-guppy');
					return new WP_REST_Response($json , 203);
				}
				$postId 		= $chatId[0];
				$postReceiverId = $chatId[1];
				global $guppySetting;
				$postType = get_post_type($postId);
				if(!empty($guppySetting['post_type'])){
					$user_meta  		= get_userdata($postReceiverId);
					$user_roles 		= $user_meta->roles;
					if(!empty($user_roles)){
						foreach($user_roles as $single){
							if(!empty($guppySetting['post_type'][$single]) && in_array($postType, $guppySetting['post_type'][$single])){
								$verifyMember = true;
								break;
							}
						}
					}		
				}
				if(!$verifyMember){
					$json['type'] = 'error';
					$json['message_desc']   = esc_html__('You are not allowed to perform this action!', 'wp-guppy');
					return new WP_REST_Response($json , 203);
				}

				$where 		 	= " (action_by =". $loginedUser." AND action_to =". $postReceiverId.") AND action_type=2 AND post_id=".$postId; 
				$chatNotify 	= $this->guppyModel->getData('id', 'wpguppy_postchat_action', $where );					
				if(!empty($chatNotify)){
					$muteNotification = true;
				}else{
					$muteNotification = false;
				}
				$where 		 	= " ((action_by =". $loginedUser." AND action_to =". $postReceiverId.") OR( action_by =". $postReceiverId." AND action_to =". $loginedUser." )) AND action_type=1 AND post_id=".$postId; 
				$chatAction 	= $this->guppyModel->getData('action_by,action_to', 'wpguppy_postchat_action', $where );
				if(empty($chatAction)){
					$where 		 	= " ((action_by =". $loginedUser." AND action_to =". $postReceiverId.") OR( action_by =". $postReceiverId." AND action_to =". $loginedUser." )) AND action_type=0"; 
					$chatAction 	= $this->guppyModel->getData('action_by,action_to', 'wpguppy_postchat_action', $where );
					if(!empty($chatAction)){
						$isBlocked = true;
						$blockerId = $chatAction[0]['action_by'];
						$blockedId = $chatAction[0]['action_to'];
					}
				}else{
					$isBlocked = true;
					$blockerId = $chatAction[0]['action_by'];
					$blockedId = $chatAction[0]['action_to'];
				}
				$userData 	= $this->getUserInfo(1, $postReceiverId);
				if(!empty($userData)){
					$userName 	= $userData['userName'];
					$userAvatar = $userData['userAvatar'];
				}
				$postTitle 						= get_the_title($postId);
				$chatInfo['postTitle'] 			= !empty($postTitle) ? $postTitle : '';
				$chatInfo['postImage'] 			= $this->getPostImage($postId);
				$chatInfo['isOnline']			= wpguppy_UserOnline($postReceiverId);
				$chatInfo['postId']				= $postId;
				$chatInfo['postReceiverId']		= $postReceiverId;
				$chatInfo['isBlocked']			= $isBlocked;
				$chatInfo['blockedId']			= $blockedId;
				$chatInfo['blockerId']			= $blockerId;
				$chatInfo['userName']			= $userName;
				$chatInfo['userAvatar']			= $userAvatar;
				$chatInfo['chatId'] 			= $this->getChatKey('0', $postId, $postReceiverId);
				$chatInfo['muteNotification'] 	= $muteNotification;
				$chatInfo['chatType'] 			= '0';
			}elseif($chatType == 1){
				$receiverId = $chatId[0];
				$where 		 	= " (send_by =". $loginedUser." AND send_to =". $receiverId.") OR( send_by =". $receiverId." AND send_to =". $loginedUser." ) AND friend_status IN(1,3)"; 
				$verifyMember 	= $this->guppyModel->getData('id', 'wpguppy_friend_list', $where );
				if(!$verifyMember){
					$json['type'] = 'error';
					$json['message_desc']   = esc_html__('You are not allowed to perform this action!', 'wp-guppy');
					return new WP_REST_Response($json , 203);
				}
				$chatNotify = array();
				$chatNotify['userId'] 		= $receiverId;
				$chatNotify['actionBy'] 	= $loginedUser;
				$chatNotify['actionType'] 	= '2';
				$chatNotify['chatType'] 	= $chatType;
				$muteNotification = $this->getGuppyChatAction($chatNotify);
				if(!empty($muteNotification)){
					$muteNotification = true;
				}else{
					$muteNotification = false;
				}
				$userData 	= $this->getUserInfo(1, $receiverId);
				if(!empty($userData)){
					$userName 	= $userData['userName'];
					$userAvatar = $userData['userAvatar'];
				}
				$userStatus = $this->getUserStatus($loginedUser, $receiverId, '1');
				$chatInfo['isOnline']			= $userStatus['isOnline'];
				$chatInfo['isBlocked']			= $userStatus['isBlocked'];
				$chatInfo['blockedId']			= $userStatus['blockedId'];
				$chatInfo['blockerId']			= $userStatus['blockerId'];
				$chatInfo['userName']			= $userName;
				$chatInfo['userAvatar']			= $userAvatar;
				$chatInfo['chatId'] 			= $this->getChatKey('1',$receiverId);
				$chatInfo['chatType'] 			= '1';
				$chatInfo['muteNotification'] 	= $muteNotification;
			}elseif($chatType == 2){
				$groupId = $chatId[0];
				$where 		 	= " group_id =". $groupId." AND member_id=".$loginedUser." AND group_status='1'"; 
				$verifyMember 	= $this->guppyModel->getData('id', 'wpguppy_group_member', $where );
				if(!$verifyMember){
					$json['type'] = 'error';
					$json['message_desc']   = esc_html__('You are not allowed to perform this action!', 'wp-guppy');
					return new WP_REST_Response($json , 203);
				}
				$chatNotify = array();
				$chatNotify['groupId'] 		= $groupId;
				$chatNotify['actionBy'] 	= $loginedUser;
				$chatNotify['actionType'] 	= '2';
				$chatNotify['chatType'] 	= $chatType;
				$muteNotification = $this->getGuppyChatAction($chatNotify);
				if(!empty($muteNotification)){
					$muteNotification = true;
				}else{
					$muteNotification = false;
				}
				$groupDetail = $this->guppyModel->getGroupDetail($groupId);
				$chatInfo['groupDetail']		= NULL;
				$chatInfo['groupImage'] 		= '';
				$memberInfo 					= $groupDetail['memberAvatars'];
				$memberDisable = $userDisableReply = $isSender  = false;
				if($memberInfo[$loginedUser]['memberStatus'] == '2'){
					$memberDisable = true;
					$message = array('type' => 3, 'memberIds' => array($loginedUser));
					$messageType = '4';	
					$chatInfo['message']		= $message;	
					$chatInfo['messageType']	= $messageType;	
					$chatInfo['groupImage'] 		= WPGuppy_GlobalSettings::get_plugin_url().'public/images/group.jpg';
				}elseif($memberInfo[$loginedUser]['memberStatus'] == '0'){
					$memberDisable = true;
					$message 	= array('type' => 4, 'memberIds' => array($loginedUser));
					$messageType = '4';
					$isSender = true;
					$chatInfo['message']		= $message;	
					$chatInfo['messageType']	= $messageType;	
					$chatInfo['groupImage'] 		= WPGuppy_GlobalSettings::get_plugin_url().'public/images/group.jpg';
				}else{
					if(!empty($groupDetail['groupImage'])){
						$chatInfo['groupImage'] = $groupDetail['groupImage'];
					}
					$chatInfo['groupDetail']	= $groupDetail;	
				} 
				if($groupDetail['disableReply'] == '1' && $memberInfo[$loginedUser]['groupRole'] == '0'){
					$userDisableReply = true;
				}
				$chatInfo['memberDisable'] 		= $memberDisable;
				$chatInfo['isSender'] 			= $isSender;
				$chatInfo['userDisableReply']  	= $userDisableReply;
				$chatInfo['groupTitle'] 		= $groupDetail['groupTitle'];
				$chatInfo['chatId'] 			= $this->getChatKey('2',$groupId);
				$chatInfo['chatType'] 			= '2';
				$chatInfo['muteNotification'] 	= $muteNotification;
			}
			$json['type'] 		= 'success';
			$json['chatInfo']   = $chatInfo;
			return new WP_REST_Response($json , 200);
		}

		/**
		 * Register Guest Users
		 *
		 * @since    1.0.0
		*/
		public function registerGuppyGuestAccount($data){
			$headers    = $data->get_headers();
			$params     = ! empty($data->get_params()) 		? $data->get_params() 		: '';
			$authToken  = ! empty($headers['authorization'][0]) ? $headers['authorization'][0] : '';
			$json  = array();
			$type = 'error';
			$status = 203;

			$guestName 		= !empty($params['guestName']) ? sanitize_text_field($params['guestName']) : '';
			$guestEmail 	= !empty($params['guestEmail']) ? sanitize_text_field($params['guestEmail']) : '';
			$ipAddress 		= $_SERVER['REMOTE_ADDR'];
			$userAgent 		= $_SERVER['HTTP_USER_AGENT'];
			if(!empty($guestName) && !empty($guestEmail)){
				$data 	= array(
					'name' 			=> $guestName,
					'email' 		=> $guestEmail,
					'ip_address' 	=> $ipAddress,
					'user_agent' 	=> $userAgent,
				);
				$response = $this->guppyModel->insertData('wpguppy_guest_account' , $data);
				if($response){
					$type 				= 'success';
					$status 			= 200;
					$json['userId']   	= $response;
				}	
			}
			$json['type'] 	= $type;
			return new WP_REST_Response($json , $status);
		}

		/**
		 * Get Chat key
		 *
		 * @since    1.0.0
		*/
		public function getChatKey($chatType = '0', $chatId = 0, $postReceiverId = 0){
			$chatKey = $chatId;
			if($chatType == '0' ){
				$chatKey = $chatId.'_'.$postReceiverId.'_0';
			}elseif($chatType == '1'){
				$chatKey = $chatId.'_1';
			}elseif($chatType == '2'){
				$chatKey = $chatId.'_2';
			}
			return $chatKey;
		}
	}
}