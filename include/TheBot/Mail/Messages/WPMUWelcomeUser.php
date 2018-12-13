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

class WPMUWelcomeUser extends Mail\Message {

	/**
	 *	@inheritdoc
	 */
	protected $id = 'user/wpmu-welcome-user';

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
			->add_support('disable')
			->add_support('custom_subject')
			->add_support('custom_template');

		$this
			->add_option( new Core\Option\Boolean( 'disabled', false, __('Disabled','wp-the-bot'), $this->id ) )
			->add_option( new Core\Option\Boolean( 'no_password', false, __('Send Password Reset Link instead of plain Password','wp-the-bot'), $this->id ) )
			->add_option( new Core\Option\Boolean( 'custom_template', false, __('Custom Template','wp-the-bot'), $this->id ) )
			->add_option( new Core\Option\Boolean( 'custom_subject', false, __('Custom Subject','wp-the-bot'), $this->id ) )
			->add_option( new Core\Option\Text( 'subject', __( 'New %1$s User: %2$s' ), __('Subject','wp-the-bot'), $this->id ) );

		$this->title = __('New User Welcome','wp-the-bot');
		$this->description = __('Welcome Mail for a new Network User Account.','wp-the-bot');

	}

	/**
	 *	@inheritdoc
	 */
	public function init() {

		if ( $this->get_option('disabled')->value ) {
			add_filter( 'wpmu_welcome_user_notification', '__return_false' );
		}

		if ( $this->get_option('custom_subject')->value ) {
			add_filter('update_welcome_user_subject', [ $this, 'subject' ] );
		}

		if ( $this->get_option('custom_template')->value ) {
			add_filter('update_welcome_user_email', [ $this, 'body' ], 10, 4 );
		}

	}

	/**
	 *	@inheritdoc
	 */
	public function settings_ui( $optionset ) {

		$opt = new Settings\Option( $optionset, $this->get_option('disabled') );
		$opt->ui_boolean();

		$opt = new Settings\Option( $optionset, $this->get_option('no_password') );
		$opt->ui_boolean();

	}

	/**
	 *	@filter retrieve_password_title
	 */
	public function subject( $title, $user_login, $user_data ) {
		return $this->get_option('custom_subject')->value;
	}

	/**
	 *	@filter retrieve_password_message
	 */
	public function body( $message, $user_id, $password, $meta ) {

		$user = get_userdata($user_id);

		$pw_reset_url = false;

		if ( $this->get_option('no_password')->value ) {

			$message = str_replace('PASSWORD',__('Please reset under the following URL:','wp-the-bot'), $message );

			$password = false;

			$pw_reset_url = add_query_arg([
				'action'	=> 'rp',
				'key'		=> get_password_reset_key( $user ),
				'login'		=> rawurlencode( $user->user_login ),
			], network_site_url( 'wp-login.php' ) );

			$message = str_replace('LOGINLINK', $pw_reset_url, $message );
		}

		if ( ! $this->get_option('html')->value ) {
			return $message;
		}

		$vars = [
			'user_login'		=> $user->user_login,
			'user_email'		=> $user->user_email,
			'user_id'			=> $user_id,
			'password'			=> $password,
			'confirmation_url'	=> $pw_reset_url,
			'meta'				=> $meta,
		];

		$mails = Mail\Mail::instance();

		$mails->set_html();

		if ( $this->get_option('custom_template')->value ) {
			return $mails->render_email( $this->id, $vars );
		}

		return $mails->wrap_email( $message );

	}



}
