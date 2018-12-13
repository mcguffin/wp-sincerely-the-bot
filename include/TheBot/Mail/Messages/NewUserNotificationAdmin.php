<?php
/**
 *	@package TheBot\Core
 *	@version 1.0.1
 *	2018-09-22
 */

namespace TheBot\Mail\Messages;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}

use TheBot\Core;
use TheBot\Mail;
use TheBot\Settings;

class NewUserNotificationAdmin extends NewUserNotification {

	/**
	 *	@inheritdoc
	 */
	protected $id = 'admin/new-user-notification';


	/**
	 *	@inheritdoc
	 */
	protected $capabilities = ['manage_users'];



	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

//		add_filter('thebot_email_placeholders_' . $this->get_id(), [ $this, 'this' ] );

		$args = func_get_args();
		parent::__construct( ...$args );

		$this->add_support('html')
			->add_support('custom_subject')
			->add_support('custom_template')
			->add_support('custom_recipients');

		/*
		$this->default( 'subject', ; // %s: site name
		/*/
		$this
			->add_option( new Core\Option\Boolean( 'custom_template', false, __('Custom Template','wp-the-bot'), $this->id ) )
			->add_option( new Core\Option\Boolean( 'custom_subject', '', __('Custom Subject','wp-the-bot'), $this->id ) )
			->add_option( new Core\Option\Text( 'subject', __( '[%s] New User Registration' ), __('Subject','wp-the-bot'), $this->id ) );
		//*/

		$this->title = __('New User Notification (Admin)','wp-the-bot');

		if ( is_multisite( ) ) {
			$this->description = __('Sent to the Admin of the main blog on account creation.','wp-the-bot');
		} else {
			$this->description = __('Sent to the Blog Admin on account creation.','wp-the-bot');
		}

		add_filter('wp_new_user_notification_email_admin', [ $this, 'mail_hook' ], 10, 3 );

	}

	/**
	 *	@inheritdoc
	 */
	public function settings_ui( $optionset ) {
		?>
		<p class="description">
			<?php _e( 'Disabling: If the user does not receive a Message, the blog admin also won‘t get one.', 'wp-the-bot' ); ?>
		</p>
		<?php

	}

	/**
	 *	@action wp_password_change_notification_email
	 */
	public function mail_hook( $email, $user, $blogname ) {

		$mails = Mail\Mail::instance();

		if ( $this->get_option('custom_subject')->value ) {
			$email['subject'] = $this->get_option('subject')->value;
		}

		if ( $this->get_option('html')->value ) {
			$mails->set_html();
			if ( $this->get_option('custom_template')->value ) {
				$vars = [
					'user_login'		=> $user->user_login,
					'user_email'		=> $user->user_email,
					'admin_email'		=> get_option( 'admin_email' ),
				];
				$email['message'] = $mails->render_email( $this->id, $vars );

			} else {
				$email['message'] = $mails->wrap_email( $message );
			}
		}

		// get subscribers
		// if ( $this->option('override_recipient') ) {
		// 	$email['to'] = implode(',', [ $email['to'], $this->option('recipients') ]);
		// }
		//
		return $email;
	}



}
