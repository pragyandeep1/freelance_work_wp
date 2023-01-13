<?php
/**
 * WP Bakery shortcode
 *
 *
 * @package    WP Guppy
 * @subpackage WP Guppy/admin
 */

if (class_exists('Vc_Manager', false)) {
	class WPGuppy_ChatInit extends WPBakeryShortCode{

		function __construct() {
			add_action('vc_before_init', array(&$this, 'wpguppy_shortcode_init'));
			add_shortcode( 'wpguppy_chat_init', array(&$this, 'wpguppy_chat_init') );
		}

		/**
		 *
		 * @since    1.0.0
		 * @access   Shortcode init
		 * @var      base
		 */
		public function wpguppy_shortcode_init()  {
			vc_map(
				array(
					'name'          => esc_html__('WP Guppy Chat','wp-guppy'),
					'base'          => 'wpguppy_chat_init',
					'description'   => esc_html__('WP Guppy Chat','wp-guppy'),
					'category'      => esc_html__('WP Guppy Chat','wp-guppy'),
					'params'        => array(),
				)
			);

		}

		public function wpguppy_chat_init($atts) {?>
			<div class="guppy-wrapper">
				<?php echo do_shortcode('[getGuppyConversation]'); ?>
			</div>
		<?php
		}

	}

	new WPGuppy_ChatInit();
}