<?php
/**
 * Elementor Page builder config
 *
 * This file will include all global settings which will be used in all over the plugin,
 * It have gatter and setter methods
 *
 * @link              https://wp-guppy.com/
 * @since             1.0.0
 * @package           WP Guppy
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die('No kiddies please!');
}

if( !class_exists( 'WPGuppy_Elementor' ) ) {

	final class WPGuppy_Elementor{
		private static $_instance = null;
		
		/**
		 *
		 * @since    1.0.0
		 * @access   static
		 * @var      string    wp guppy
		 */
        public function __construct() {
			add_action( 'elementor/elements/categories_registered', array( &$this, 'wpguppy_init_elementor_widgets' ) );
            add_action( 'init', array( &$this, 'wpguppy_elementor_shortcodes' ),  20 );
        }
		
	
		/**
		 * class init
         * @since 1.1.0
         * @static
         * @var      string    wp guppy
         */
        public static function instance () {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
		
		/**
		 * Add category
		 * @since    1.0.0
		 * @access   static
		 * @var      string    wp guppy
		 */
        public function wpguppy_init_elementor_widgets( $elements_manager ) {
            $elements_manager->add_category(
                'wp-guppy-elements',
                [
                    'title' => esc_html__( 'WP Guppy Chat', 'wp-guppy' ),
                    'icon' => 'fa fa-plug',
				]
			);
        }

        /**
		 * Add widgets
		 * @since    1.0.0
		 * @access   static
		 * @var      string    wp guppy
		 */
        public function wpguppy_elementor_shortcodes() {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'elementor/shortcodes/wp-guppy-chat.php';
        }
		 
	}
}

//Init class
if ( did_action( 'elementor/loaded' ) ) {
    WPGuppy_Elementor::instance();
}