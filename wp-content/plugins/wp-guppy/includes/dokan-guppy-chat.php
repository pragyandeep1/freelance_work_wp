<?php
/**
 * Dokan plugin compatibility 
 *
 * This is Multi vendor dokan plugin compatibility file. This template will show the messenger in the vendor dashboard
 *
 * @link       wp-guppy.com
 * @since      1.0.0
 *
 * @package    wp-guppy
 * @subpackage wp-guppy/includes
 */
?>
<div class="dokan-dashboard-wrap wpguppy-chat-compatibility-wrapper">
    <?php do_action( 'dokan_dashboard_content_before' );?>
    <div class="dokan-dashboard-content">
        <?php do_action( 'dokan_help_content_inside_before' );?>
        <article class="help-content-area">
            <?php echo do_shortcode('[getGuppyConversation]');?>
        </article>
        <?php do_action( 'dokan_dashboard_content_inside_after' ); ?>
    </div>
    <?php do_action( 'dokan_dashboard_content_after' ); ?>
</div>