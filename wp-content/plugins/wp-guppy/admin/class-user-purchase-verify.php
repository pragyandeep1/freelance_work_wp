<?php
/**
 * This class used to activate envato license
 *
 *
 * @package    Envato_Purchase_Verify
 * @subpackage Envato_Purchase_Verify/admin
 * @author     Amentotech <amentotech011@amentotech.com>
 */
if ( ! class_exists( 'AmentoTech_Envato_Purchase_Verify_User' ) ) {
	class AmentoTech_Envato_Purchase_Verify_User {
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
		 * The rest api url
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $restapiurl    rest api url
		*/

		private $restApiUrl;
		/**
		 * The rest api version
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $restapiversion    rest api nonce
		*/
		private $restApiVersion ;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param      string    $plugin_name       The name of this plugin.
		 * @param      string    $version    The version of this plugin.
		 */
		public function __construct() {
			$this->epv_init_actions();
			add_action( 'wp_ajax_epv_verifypurchase', array(&$this, 'epv_verifypurchase') );
			add_action( 'wp_ajax_epv_remove_license', array(&$this, 'epv_remove_license') );
			add_action('admin_notices', array(&$this, 'epv_license_activation_notice'));
			add_action('epv_verify_purchase_section_callback', array(&$this, 'epv_verify_purchase_section_callback'));
			add_action('init', array(&$this, 'epv_pluginloaded'));
			
		}

		public function epv_pluginloaded(){
			add_action('plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'epv_settings_link'), 9999);
		}

		// Link to settings page from plugins screen
		public function epv_settings_link ( $links ) {
			$mylinks = array(
				'<a href="' . admin_url( 'options-general.php?page=epv_settings_page' ) . '">' . esc_html__('Settings', 'wp-guppy') . '</a>',
			);
			return array_merge( $mylinks, $links );
		}

		/**
		 * license alert
		 */
		public function epv_license_activation_notice() {
			$options 		= get_option( 'epv_verify_settings' );
			$verified		= !empty($options['verified']) ? $options['verified'] : '';
			$page			= !empty($_GET['page']) ? $_GET['page'] : '';
			if(!AmentoTech_Envato_Purchase_Verify_User::isLocalhost() && empty($verified) && $page !== 'epv_verify_purchase'){?>	
					<div class="notice notice-success is-dismissible">
						<p><?php esc_html_e('Please activate WP Guppy license to see all the settings. Click below button to start activating', 'wp-guppy'); ?></p>
						<p><a class="button button-primary button-hero" href="<?php echo admin_url( 'options-general.php?page=epv_verify_purchase' );?>"><?php esc_html_e('Activate license', 'wp-guppy' );?></a></p>
					</div>		
				<?php 
			}
		}
		

		/**
		 * Remove license
		 */
		public function epv_remove_license(){
			$json = array();
			//security check
			if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
				$json['type'] = 'error';
				$json['message'] = esc_html__('Oops!', 'wp-guppy');
				$json['message_desc'] = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'wp-guppy');
				wp_send_json( $json );
			}

			$purchase_code	= !empty($_POST['purchase_code']) ? sanitize_text_field( $_POST['purchase_code'] ) : '';
			$domain			= get_home_url();
			
			$url = 'https://wp-guppy.com/verification/wp-json/atepv/v2/epvRemoveLicense';
			$args = array(
				'timeout'		=> 45,
				'redirection'	=> 5,
				'httpversion'	=> '1.0',
				'blocking'		=> true,
				'headers'     => array(),
				'body'		=> array(
					'purchase_code'	=> $purchase_code, 
					'domain'	=> $domain 
				),
				'cookies'	=> array()
			);
			$response = wp_remote_post( $url, $args );
			// error check
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				$json['type'] 	 	= 'error';
				$json['title']		= esc_html__('Failed!');
				$json['message']	= $error_message;
				wp_send_json($json);
			} else {
				$response = json_decode(wp_remote_retrieve_body( $response ));			
				if(!empty($response->type) && $response->type == 'success'){
					delete_option('epv_verify_settings');
				}
				wp_send_json($response);
			}
		}

		/**
		 * Verify item purchase code
		 */
		public function epv_verifypurchase(){
			$json = array();
			//security check
			if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
				$json['type'] = 'error';
				$json['message'] = esc_html__('Oops!', 'wp-guppy');
				$json['message_desc'] = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'wp-guppy');
				wp_send_json( $json );
			}
			$purchase_code	= !empty($_POST['purchase_code']) ? sanitize_text_field( $_POST['purchase_code'] ) : '';
			$domain			= get_home_url();
			
			$url = 'https://wp-guppy.com/verification/wp-json/atepv/v2/verifypurchase';
			$args = array(
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(),
				'body'        => array( 'purchase_code' => $purchase_code, 'domain' => $domain ),
				'cookies'     => array()
			);
			$response = wp_remote_post( $url, $args );
			$options = get_option( 'epv_verify_settings' );
			$options['purchase_code']	= $purchase_code;
			// error check
			if ( is_wp_error( $response ) ) {
				update_option('epv_verify_settings', $options);
				$error_message = $response->get_error_message();
				$json['type'] 	 	= 'error';
				$json['title']		= esc_html__('Oops!');
				$json['message']	= $error_message;
				wp_send_json($json);
			} else {
				$response = json_decode(wp_remote_retrieve_body( $response ),true);			
				$options = get_option( 'epv_verify_settings' );
				$options['purchase_code']	= $purchase_code;
				if(!empty($response['type']) && $response['type'] == 'success'){
					if($response['data']['item']['id'] == '34619534'){
						$options['verified']	= true;
						$json['type'] 	 	= 'success';
						$json['title']		= esc_html__('Woohoo!','wp-guppy');
						$json['message']	= esc_html__('Your license has been verified.','wp-guppy');
					}else{
						$json['type'] 	 	= 'error';
						$json['title']		= esc_html__('Oops!','wp-guppy');
						$json['message'] = esc_html__('This code is invalid for this product!','wp-guppy');
					}
				}else{
					$json['type'] 	 	= 'error';
					$json['title']		= esc_html__('Oops!','wp-guppy');
					$json['message']	= $response['message'];
				}
				update_option('epv_verify_settings', $options);
				wp_send_json($json);
			}
		}

		/**
		 * Init all the actions of admin pages
		 */
		public function epv_init_actions() {
			add_action( 'admin_menu', array( $this, 'epv_purchase_verify_menu' ));
			add_action( 'admin_init', array( $this, 'epv_purchase_verify_init' ) );
			
		}

		public static function isLocalhost($whitelist = ['127.0.0.1', '::1']) {
			if($_SERVER['HTTP_HOST'] == 'wp-guppy.com'){
			  return true;
			}elseif(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
			  return true;
			} 
			return false;
		}

		/**
		 * Submenu
		 */
		public function epv_purchase_verify_menu() {

			add_submenu_page(
				'options-general.php',
				esc_html__( 'License activation', 'wp-guppy' ),
				esc_html__( 'License activation', 'wp-guppy' ),
				'manage_options',
				'epv_verify_purchase',
				array( $this, 'epv_verify_purchase_section_callback' )
			);
		}
		
		/**
		 * Purchase code verify menu
		*/
		public function epv_purchase_verify_init(  ) { 
			
			register_setting(
				'epv_verify_settings',
				'epv_verify_settings'
			);	

			add_settings_section(
				'user_purchase_code_verify',
				esc_html__( 'Envato purchase code Verify', 'wp-guppy' ),
				array( $this, 'epv_api_text' ),
				'epv_verify_section'
			);
		
			add_settings_field(
				'purchase_code',
				esc_html__( 'Envato purchase code', 'wp-guppy' ), 
				array( $this, 'epv_purchase_code_field' ),
				'epv_verify_section',
				'user_purchase_code_verify'
			);
		}

		/**
		 * Get purchase code text
		*/
		public function epv_api_text() {
			$options = get_option( 'epv_verify_settings' );
			$verified	= !empty($options['verified']) ? $options['verified'] : '';

			if(empty($verified)){		
				$message	= sprintf( __( '<p>To get all the functionality of WP Guppy, please verify your valid license copy. <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">How, i can find the purchase code</a>.</p>', 'wp-guppy' ) );
			} else {
				$message	= sprintf( __( '<p>One license can only be used for your one live site, you can unlink this license to use our product to another server. You can check the license detail <a href="https://themeforest.net/licenses/standard">here</a> </p>', 'wp-guppy' ) );
			}
			echo wp_kses_post( $message );
		}

		/**
		 * Purchase code text field
		*/
		public function epv_purchase_code_field() {
			$options = get_option( 'epv_verify_settings' );
			$purchase_code	= !empty($options['purchase_code']) ? $options['purchase_code'] : '';
			printf(
				'<input type="text" name="%s" id="epv_purchase_code" value="%s" title="%s" />',
				esc_attr( 'epv_verify_settings[purchase_code]' ),
				esc_attr( $purchase_code ),
				esc_html__( 'Enter purchase code', 'wp-guppy' )
			);
		}

		/**
		 * Purchase code settings form
		 * 
		*/
		public function epv_verify_purchase_section_callback() {
			$options = get_option( 'epv_verify_settings' );
			$verified	= !empty($options['verified']) ? $options['verified'] : '';
			?>
			<div id="at-item-verification" class="at-wrapper">
				<div class="at-content">
					<div class="settings-section">
						<form action='options.php' method='post'>    
							<?php 
								do_action('epv_form_render_before');
								settings_fields( 'epv_verify_settings' );
								do_settings_sections( 'epv_verify_section' ); 
								if(!empty($verified)){?>
										<input type="submit" name="remove" class="button button-primary" id="epv_remove_license_btn" value="<?php esc_attr_e( 'Remove license','wp-guppy' ); ?>" />
								<?php } else {?>
										<input type="submit" name="submit" class="button button-primary" id="epv_verify_btn" value="<?php esc_attr_e( 'Activate license','wp-guppy' ); ?>" />
									<?php
								}
								do_action('epv_form_render_after');
							?>
						</form>
					</div>
				</div> 
			</div>
			<?php
		}

	}
	new AmentoTech_Envato_Purchase_Verify_User();
}

