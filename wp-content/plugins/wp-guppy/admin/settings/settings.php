<?php

if (!class_exists('WPGuppy_Plugin_Options')) {
    /**
     *
     * @package WP Guppy
     */
	class WPGuppy_Plugin_Options {
        private static $_instance = null;
		
		/**
         * PRIVATE CONSTRUCTOR
         */
		public function __construct() {
			add_action('admin_menu', array(&$this, 'wpguppy_admin_menu'));
			add_action('wp_ajax_wpguppy_rest_database', array(&$this,'wpguppy_rest_database'));
			add_action('wp_ajax_wpguppy_update_guppy_admin_status', array(&$this,'wpguppy_update_guppy_admin_status'));
		}

		/**
         * Call this method to get singleton
         *
         * @return 
         */
        public static function instance()
        {
            if (self::$_instance === null) {
                self::$_instance = new WPGuppy_Plugin_Options();
            }
            return self::$_instance;
        }

		/**
		 * Is admin, update status
		 *
		 * @link       wp-guppy.com
		 * @since      1.0.0
		 *
		 * @package    wp-guppy
		 * @subpackage wp-guppy/admin
		 */
		function wpguppy_update_guppy_admin_status() {
			global $wpdb;
			$json		= array();
			$user_id	= !empty($_POST['user_id']) ? $_POST['user_id'] : '';
			$status		= !empty($_POST['status']) ? $_POST['status'] : '';

			if( !is_admin() || empty($user_id) ){
				$json['type']		= 'error';
				$json['message']	= esc_html__('You are not allowed to perform this action!','wp-guppy');
				wp_send_json($json);
			}

			update_user_meta($user_id,'is_guppy_admin',$status);
			
			$json['type']		= 'success';
			$json['message']	= esc_html__('Update status','wp-guppy');
			wp_send_json($json);
		}

		/**
		 * Reset database
		 *
		 * @link       wp-guppy.com
		 * @since      1.0.0
		 *
		 * @package    wp-guppy
		 * @subpackage wp-guppy/admin
		 */
		function wpguppy_rest_database() {
			global $wpdb;
			$json				= array();
			
			if( !is_admin(  ) ){
				$json['type']		= 'error';
				$json['message']	= esc_html__('You are not allowed to perform this action!','wp-guppy');
				wp_send_json($json);
			}

			$tables  = array(
				$wpdb->prefix . 'wpguppy_message',
				$wpdb->prefix . 'wpguppy_users',
				$wpdb->prefix . 'wpguppy_guest_account',
				$wpdb->prefix . 'wpguppy_group_member',
				$wpdb->prefix . 'wpguppy_group',
				$wpdb->prefix . 'wpguppy_friend_list',
				$wpdb->prefix . 'wpguppy_chat_action',
			);

			foreach($tables as $table){
				$delete = $wpdb->query("TRUNCATE TABLE $table");
			}
			
			$json['type']		= 'sussess';
			$json['message']	= esc_html__('You have successfully reset database','wp-guppy');
			wp_send_json($json);
		}

		/**
		 * Load plugin menu
		 *
		 * @link       wp-guppy.com
		 * @since      1.0.0
		 *
		 * @package    wp-guppy
		 * @subpackage wp-guppy/admin
		 */
		public function wpguppy_admin_menu() {
			$menu_slug = 'wpguppy_options';
			$messages_page	= add_menu_page(esc_html__('WP Guppy','wp-guppy'), 
											esc_html__('WP Guppy','wp-guppy'), 
											'administrator',
											'wpguppy_settings',
											array( &$this,'wpguppy_settings'),
											WPGuppy_GlobalSettings::get_plugin_url().'/admin/images/guppy.svg'
										);			
			add_action( "load-".$messages_page, array(&$this, 'wpguppy_load_settings') );
		}

		/**
		 * Load settings
		 *
		 * @link       wp-guppy.com
		 * @since      1.0.0
		 *
		 * @package    wp-guppy
		 * @subpackage wp-guppy/admin
		 */
		function wpguppy_load_settings() {
			global $pagenow;
			if ( isset($_POST["wpguppy_submit"]) && $_POST["wpguppy_submit"] == 'yes' ) {
				check_admin_referer( "wpguppy_options" );
				$settings = get_option( "wpguppy_settings" );
				if ($pagenow == 'admin.php' && $_GET['page'] == 'wpguppy_settings') {
					$wpguppy_settings	= !empty($_POST['wpguppy_settings']) ? $_POST['wpguppy_settings'] : array();
					$updated = update_option( "wpguppy_settings", $wpguppy_settings );
				}
				
				$url_params = isset($_GET['tab']) ? 'updated=true&tab='.$_GET['tab'] : 'updated=true';
				wp_redirect(admin_url('admin.php?page=wpguppy_settings&'.$url_params));
				exit;
			}
		}
		
		/**
		 * Guppy settings
		 *
		 * @link       wp-guppy.com
		 * @since      1.0.0
		 *
		 * @package    wp-guppy
		 * @subpackage wp-guppy/admin
		 */
		public function wpguppy_settings() {
			global	$wpguppy_settings;
			$options = get_option( 'epv_verify_settings' );
			$verified	= !empty($options['verified']) ? $options['verified'] : '';
			$tabs = array(
				'general'			=> esc_html__('General','wp-guppy'),
				'tabs'				=> esc_html__('Chat tabs','wp-guppy'),
				'style'				=> esc_html__('Styles','wp-guppy'),
				'media'				=> esc_html__('Media','wp-guppy'),
				'real-time-chat'	=> esc_html__('Real time chat','wp-guppy'),
				'database'			=> esc_html__('Database','wp-guppy'),
				'translation'		=> esc_html__('Translation','wp-guppy'),
				'report'			=> esc_html__('Report a user','wp-guppy'),
				'email'				=> esc_html__('Email templates','wp-guppy'),
			);
			if(!AmentoTech_Envato_Purchase_Verify_User::isLocalhost() && empty($verified)){
				do_action('epv_verify_purchase_section_callback');
			}else{
				if(!empty($verified)){
					$tabs['license'] = esc_html__('License','wp-guppy');
				}
				
				$wpguppy_settings       = get_option( "wpguppy_settings" );
				$current_tab			= !empty($_GET['tab']) ? $_GET['tab'] : 'general';
				?>
				<?php $this->wpguppy_settings_tabs($tabs,$current_tab);?>
				<div id="poststuff">
					<form autocomplete="off" method="post" id="gp-settings-page-form" action="<?php admin_url('admin.php?page=wpguppy_options'); ?>">
						<?php
							foreach($tabs as $key => $tab ){
								$display_class	= !empty($current_tab) && $current_tab == $key ? '' : 'hide-if-js';
								wp_nonce_field( "wpguppy_options" ); 
								?>
								<div class="tab-content gb-tab-content <?php echo esc_attr($display_class);?>" id="tb-content-<?php echo esc_attr($key);?>">
									<div class="wrap">
										<?php require WPGuppy_GlobalSettings::get_plugin_path() . 'admin/settings/templates/'.$key.'-options.php'; ?>
									</div>
									<?php if( !empty($key) && $key !='database' && $key !='license' ){?>
										<input type="hidden" name="wpguppy_submit" value="yes"/>
										<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save changes','wp-guppy');?>"></p>
									<?php }	?>
								</div>
							<?php
							} 
						echo '</div></div>';?>
					</form>
				</div>
			<?php
			}
		}
		
		/**
		 * Guppy Tabs settings
		 *
		 * @link       wp-guppy.com
		 * @since      1.0.0
		 *
		 * @package    wp-guppy
		 * @subpackage wp-guppy/admin
		 */
		public function wpguppy_settings_tabs( $tabs,$current = '' ) {
			echo '<h2 class="nav-tab-wrapper">';
			
				foreach ( $tabs as $tab => $name ) {
					$class = ( $tab == $current ) ? ' nav-tab-active' : '';
					echo "<a id=".esc_attr($tab)."-settings-tab' data-tab_id=".esc_attr( $tab )." href='javascript:;' class='gp-tabs-settings nav-tab".esc_attr($class)."'>".esc_html($name)."</a>";
				}
			
			echo '</h2>';
		}
	}
	
	WPGuppy_Plugin_Options::instance();
}