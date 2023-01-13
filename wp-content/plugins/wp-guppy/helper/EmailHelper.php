<?php
/**
 * Email Helper For Theme
 * @since    1.0.0
 */
if (!class_exists('WpguppyEmailhelper')) {

    class WpguppyEmailhelper {

        public function __construct() {
            add_filter('wp_mail_content_type', array(&$this, 'wpguppy_set_content_type'));
        }


        /**
         * Email Headers From name
         * @since    1.0.0
         */
        public function wpguppy_wp_mail_from_name($name) {
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			global $theme_settings;
			$email_from_name = !empty($theme_settings['email_from_name']) ? $theme_settings['email_from_name'] : '';

            if (!empty($email_from_name)) {
                return $email_from_name;
            }
        }

        /**
         * Email Content type
         *
         *
         * @since    1.0.0
         */
        public function wpguppy_set_content_type() {
            return "text/html";
		}

		/**
         * Get Email Header
         * Return email header html
         * @since    1.0.0
		*/
		public function prepare_email_before_header() {
			ob_start();
			?>
			<!doctype html>
			<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

				<head>
					<title></title>
					<meta http-equiv="X-UA-Compatible" content="IE=edge">
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
					<meta name="viewport" content="width=device-width, initial-scale=1">
				</head>
				<body>
			<?php
			return ob_get_clean();
		}

        /**
         * Get Email Header
         * Return email header html
         * @since    1.0.0
         */
        public function emailHeaders($header_logo) {
			ob_start();
			echo $this->prepare_email_before_header();
            ?>
			<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" style="width: 100%; background-color: #f4f4f4;">
				<?php if(!empty($header_logo)){ ?>
					<tr>
						<td align="center">
							<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" width="600" style="background: #ffffff;">
								<tbody>
									<tr>
										<td style="direction:ltr;font-size:0px;padding:50px 55px;text-align:center;">
											<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
												<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
													<tbody>
														<tr>
															<td>
																<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
																	<tbody>
																		<tr>
																			<td style="width:130px;">
																				<img  alt="<?php echo esc_attr('header logo') ?>"height="auto" src="<?php echo esc_url( $header_logo ); ?>"  style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;" width="130">
																			</td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
			<?php
				}
			return ob_get_clean();
		}

		/**
         * Get report user email content
         * Return report user email content
         * @since    1.0.0
         */
        public function reportChatEmailContent($params = array()) {
			ob_start();
            ?>
				
				<tr>
					<td align="center">
						<table align="center" role="presentation" border="0" cellspacing="0" cellpadding="0" width="600" style="background: #ffffff;">
							<tbody>
								<tr>
									<td style="font-size: 0px; padding: 0 55px 20px; word-break: break-word;">
										<div style="font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif; font-size: 16px; font-weight: 400; line-height: 24px; color: #676767;">
											<p style="font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif; width: 100%; font-size: 16px; color: #676767; font-weight: 400; line-height: 24px; margin: 0;"><?php echo (nl2br(html_entity_decode($params['emailContent'])));?></p>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			<?php
			return ob_get_clean();
		}

		/**
		 * Get Email Footer
		 *
		 * Return email footer html
		 *
		 * @since    1.0.0
		 */
		public function emailFooter() {
			ob_start();
			?>
				<tr>
					<td align="center">
						<table align="center" role="presentation" border="0" cellspacing="0" cellpadding="0" width="600" style="background: #353648;">
							<tbody>
								<tr>
									<td style="font-size: 0px; padding: 3px;">
										<div style="font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif; font-size: 13px; font-weight: 500; line-height: 20px; color: #bdbdbd; text-align:center;"></div>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			<?php
			echo $this->prepare_email_after_footer();
			return ob_get_clean();
		}
		public function prepare_email_after_footer () {
			ob_start();
			?>
				</table>
					</body>	
				</html>
			<?php
			return ob_get_clean();
		}
	}
}