<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       wp-guppy.com
 * @since      1.0.0
 *
 * @package    wp-guppy
 * @subpackage wp-guppy/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    wp-guppy
 * @subpackage wp-guppy/public
 * @author     wp-guppy <wpguppy@gmail.com>
 */
class WPGuppy_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The userRoles of current logined users.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $userRoles    The userRoles of current logined users.
	 */
	private $userRoles;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	*/
	 
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name 	= $plugin_name;
		$this->version 		= $version;
		$guppyModel 		= WPGuppy_Model::instance();
		$this->guppyModel 	= $guppyModel;
		$this->wpguppyUpgrade();
		add_action( 'woocommerce_after_shop_loop_item',  	array($this, 'startGuppychat'));
		add_action( 'woocommerce_before_main_content',  	array($this, 'beforeShopLoop'));
		add_action( 'woocommerce_after_main_content',  		array($this, 'afterShopLoop'));
		add_action('wp_footer', 							array($this, 'wpGuppyWidgetChat'));
		add_shortcode('getGuppyConversation',				array(&$this,'wpguppy_getGuppyConversation'));
	}
	
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WPGuppy_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WPGuppy_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style('wpguppy-app-css', WPGuppy_GlobalSettings::get_plugin_url().'guppy-chat/dist/css/app.css',array(), $this->version, 'all');
		wp_enqueue_style('wpguppy-vendors-css', WPGuppy_GlobalSettings::get_plugin_url().'guppy-chat/dist/css/vendors.css',array(), $this->version, 'all');
		wp_enqueue_style( $this->plugin_name.'-guppy-icons', plugin_dir_url( __FILE__ ) . 'css/guppy-icons.css', array(), $this->version, 'all' );
		
	}


	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WPGuppy_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WPGuppy_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class. 
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-guppy-public.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script('wpguppy-app-js', WPGuppy_GlobalSettings::get_plugin_url().'guppy-chat/dist/js/app.js', array(), $this->version, true);
		wp_enqueue_script('wpguppy-vendors-js', WPGuppy_GlobalSettings::get_plugin_url().'guppy-chat/dist/js/vendors.js', array(), $this->version, true);
		// wp_enqueue_script('guppy_cc_vendors', 'http://localhost:8080/js/chunk-vendors.js', array(), '', true);
		// wp_enqueue_script('guppy_cc_app', 'http://localhost:8080/js/app.js', array(), '', true);
	}

	/**
	 * Guppy Conversation shortcode
	 *
	 * @since    1.0.0
	*/
	public function wpguppy_getGuppyConversation(){
		if(is_user_logged_in()){
			return '<div id="wpguppy-messanger-chat"></div>';
		}
	}

	/**
	 * widget chat Initialize
	 *
	 * @since    1.0.0
	*/
	public function wpGuppyWidgetChat(){
		echo do_shortcode('<div id="wpguppy-widget-chat"></div>');
	}
	
	/**
	 *  woocommerce products chat button Initialize
	 *
	 * @since    1.0.0
	*/
	public function startGuppychat(){
		global $product, $guppySetting, $current_user;
		$loginedUser = !empty($current_user->ID) ? $current_user->ID : 0;
		if($loginedUser > 0){
			$postId 	= $product->get_id();
			$postType 	= get_post_type($postId);
			$postAuthor	= get_post_field('post_author', $postId, true);
			if(!empty($guppySetting['post_type'])){
				if(!empty($this->userRoles)){
					foreach($this->userRoles as $single){
						if(!empty($guppySetting['post_type'][$single]) 
							&& in_array($postType, $guppySetting['post_type'][$single]) 
							&& $postAuthor != $loginedUser){
							echo do_shortcode('<wpguppy-woo-chat-button :post_id='.$postId.' />');
						}
					}
				}		
			}
		}
	}
	public function beforeShopLoop(){
		if(is_user_logged_in()){
			global $current_user;
			$userMeta  			= get_userdata($current_user->ID);
			$this->userRoles 	= !empty($userMeta) ? $userMeta->roles : array();
			echo do_shortcode('<div id="wpguppy-woo-products" style="display:contents">');
		}
	}  
	public function afterShopLoop(){
		if(is_user_logged_in()){
			echo do_shortcode('</div>');
		}
	}  

	/**
	 * actions to perform when version is upgraded 
	 *
	 * @since    1.0.0
	*/
	public function wpguppyUpgrade() {
		$wpguppy_version = get_option('wpguppy_version');
		if(empty($wpguppy_version)){
			$this->guppyModel->upgradeGuppyDB($this->version);
		}elseif($wpguppy_version < 2.2 ){
			$this->guppyModel->createPostActionTable($this->version);
		}
	}	
}