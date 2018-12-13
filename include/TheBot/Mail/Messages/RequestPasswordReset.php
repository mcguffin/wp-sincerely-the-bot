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

class RequestPasswordReset extends Mail\Message {

	/**
	 *	@inheritdoc
	 */
	protected $id = 'user/request-password-reset';

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
			->add_option( new Core\Option\Text( 'subject', __( '[%s] Password Reset', 'wp-the-bot' ), __('Subject','wp-the-bot'), $this->id ) );
		//*/

		$this->title = __('Lost Password','wp-the-bot');
		$this->description = __('Sent to the User when he/she requests a Password Reset on the WP-Login-Page.','wp-the-bot');

		if ( $this->get_option('custom_subject')->value ) {
			add_filter('retrieve_password_title', [$this,'subject'], 10, 3 );
		}

		if ( $this->get_option('custom_template')->value ) {
			add_filter('retrieve_password_message', [$this,'body'], 10, 4 );
		}
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
	public function body( $message, $key, $user_login, $user_data ) {


		if ( ! $this->get_option('html')->value ) {
			return $message;
		}

		$vars = [
			'user_login'		=> $user_data->user_login,
			'user_email'		=> $user_data->user_email,
			'confirmation_url'	=> network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ),
		];

		$mails = Mail\Mail::instance();

		$mails->set_html();

		if ( $this->get_option('custom_template')->value ) {
			return $mails->render_email( $this->id, $vars );
		}
		return $mails->wrap_email( $message );

	}



}
