<?php
/**
 *
 * The template used for displaying freelancer experience
 *
 * @package   Workreap
 * @author    amentotech
 * @link      https://amentotech.com/user/amentotech/portfolio
 * @version 1.0
 * @since 1.0
 */

global $post;
$post_id = $post->ID;

if (function_exists('fw_get_db_post_option')) {
	$experience 	= fw_get_db_post_option($post_id, 'experience', true);
	$remove_experience	= fw_get_db_settings_option('frc_remove_experience', 'no');
} else {
	$experience		= array();
}

if(!empty($remove_experience) && $remove_experience === 'yes' ){return '';}

$count_experience	=	0;
if( !empty( $experience ) && is_array($experience) ){?>
	<div class="wt-experience items-more-wrap-ex">
		<div class="wt-usertitle">
			<h2><?php esc_html_e('Experience','workreap');?></h2>
		</div>
		<div class="wt-experiencelisting-hold data-list">
			<?php 
			foreach( $experience as $key => $item ){
				$start_year = '';
				$end_year 	= '';
				$period 	= '';
				$count_experience++;
				$bg_class	= !empty($count_experience) && intval($count_experience)%2 === 0 ? '' : 'wt-bgcolor';
				
				if (!empty($item['startdate']) || !empty($item['enddate'])) {
					if (!empty($item['startdate'])) {
						$start_year = date_i18n('M Y', strtotime( apply_filters('workreap_date_format_fix',$item['startdate'] ) ));
					}

					if (!empty($item['enddate'])) {
						$end_year = '&nbsp;-&nbsp;'.date_i18n('M Y', strtotime( apply_filters('workreap_date_format_fix',$item['enddate'] )));
					} else{
						$end_year	= '&nbsp;-&nbsp;'.esc_html__('Present','workreap');
					}

					if (!empty($start_year) || !empty($end_year)) {
						$period = $start_year . $end_year;
					}
				}
				?>
				<div class="wt-experiencelisting <?php echo esc_attr($bg_class);?> sp-load-item">
					<?php if( !empty( $item['title'] ) ){?>
						<div class="wt-title"><h3><?php echo esc_html( stripslashes( $item['title'] ) );?></h3></div>
					<?php }?>
					<div class="wt-experiencecontent">
						<ul class="wt-userlisting-breadcrumb">
							<?php if( !empty( $item['company'] ) ){?>
								<li><span><i class="fa fa-building"></i>&nbsp;<?php echo esc_html( stripslashes( $item['company']) );?></span></li>
							<?php }?>
							<?php if (!empty($period)) { ?>
								<li><span><i class="fa fa-calendar"></i>&nbsp;<?php echo esc_html($period); ?></span></li>
							<?php } ?>
						</ul>
						<?php if( !empty( $item['description'] ) ){?>
							<div class="wt-description">
								<p><?php echo do_shortcode( nl2br( stripslashes( $item['description'] ) ) );?></p>
							</div>
						<?php }?>
					</div>
				</div>
			<?php }?>
			<div class="wt-btnarea"><a href="#" onclick="event_preventDefault(event);" class="wt-btn sp-loadMore"><?php esc_html_e('Load More','workreap');?></a></div>
			<div class="divheight"></div>
		</div>
	</div>
<?php }?>