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

class NewUserNotification extends Mail\Message {

	/**
	 *	@inheritdoc
	 */
	protected $id = 'user/new-user-notification';

	/**
	 *	@inheritdoc
	 */
	protected $html_support = true;

	/**
	 *	@inheritdoc
	 */
	protected $context = 'network';

	/**
	 *	@inheritdoc
	 */
	protected $capabilities = ['read'];



	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

//		add_filter('thebot_email_placeholders_' . $this->get_id(), [ $this, 'this' ] );

		$args = func_get_args();
		parent::__construct( ...$args );

		$this->add_support('html')
			->add_support('custom_subject')
			->add_support('custom_template');

		/*
		$this->default( 'subject', ; // %s: site name
		/*/
		$this
			->add_option( new Core\Option\Boolean( 'custom_template', false, __('Custom Template','wp-the-bot'), $this->id ) )
			->add_option( new Core\Option\Boolean( 'custom_subject', '', __('Custom Subject','wp-the-bot'), $this->id ) )
			->add_option( new Core\Option\Text( 'subject', __( '[%s] Your username and password info' ), __('Subject','wp-the-bot'), $this->id ) );
		//*/
		if ( is_multisite( ) ) {

			$this->add_option( new Core\Option\Boolean( 'wpmu_disable', false, __('Disable if a site is being created at the same time','wp-the-bot'), $this->id ) );

		}
		$this->title = __('New User Notification','wp-the-bot');
		$this->description = __('Sent to the User on account creation.','wp-the-bot');

		add_filter('wp_new_user_notification_email', [ $this, 'mail_hook' ], 10, 3 );

	}

	/**
	 *	@inheritdoc
	 */
	public function init() {

		if ( is_multisite() && $this->get_option('wpmu_disable')->value ) {

			remove_action( 'network_site_new_created_user',   'wp_send_new_user_notifications' );

//			remove_action( 'network_site_users_created_user', 'wp_send_new_user_notifications' );
//			remove_action( 'network_user_new_created_user',   'wp_send_new_user_notifications' );

		}

	}

	/**
	 *	@inheritdoc
	 */
	public function settings_ui( $optionset ) {

		if ( is_multisite( ) ) {
			$opt = new Settings\Option( $optionset, $this->get_option('wpmu_disable') );
			$opt->ui_boolean();
		}
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
				$key = get_password_reset_key( 20, false );
				$vars = [
					'user_login'		=> $user->user_login,
					'user_email'		=> $user->user_email,
					'admin_email'		=> get_option( 'admin_email' ),
					'confirmation_url'	=> network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login' ),
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
