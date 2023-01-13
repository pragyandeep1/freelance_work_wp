<?php
/**
 * Shortcode
 *
 *
 * @package    WP Guppy
 * @subpackage WP Guppy/admin
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( !class_exists('WPGuppy_Chat_Elementor') ){
	class WPGuppy_Chat_Elementor extends Widget_Base {

		public function __construct( $data = array(), $args = null ) {
            parent::__construct( $data, $args );
        }
		/**
		 *
		 * @since    1.0.0
		 * @access   static
		 * @var      base
		 */
		public function get_name() {
			return 'wpguppy_chat';
		}

		/**
		 *
		 * @since    1.0.0
		 * @access   static
		 * @var      title
		 */
		public function get_title() {
			return esc_html__( 'WP Guppy Chat', 'wp-guppy' );
		}

		/**
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      icon
		 */
		public function get_icon() {
			return 'eicon-testimonial';
		}

		/**
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      category of shortcode
		 */
		public function get_categories() {
			return [ 'wp-guppy-elements' ];
		}

		/**
		 * Render shortcode
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function render() {
			?>
			<div class="guppy-wrapper">
				<?php echo do_shortcode('[getGuppyConversation]'); ?>
			</div>
			<?php
		}
	}
	Plugin::instance()->widgets_manager->register_widget_type( new WPGuppy_Chat_Elementor ); 
}