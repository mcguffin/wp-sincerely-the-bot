<?php
/**
 *	@package TheBot\Settings
 *	@version 1.0.0
 *	2018-09-22
 */

namespace TheBot\Settings;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}

use TheBot\Core;

class SettingsTabMailer extends SettingsTab {

	protected $optionset = 'mailer';

	private $_smtp_debug_log = '';

	protected $id = 'mailer';


	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

		$this->label = __( 'Mailer', 'wp-the-bot' );

		parent::__construct();
	}


	/**
	 *
	 */
	public function send_testmail() {
		global $phpmailer;

		add_action( 'phpmailer_init' , array( $this, 'enable_mailer_debug') );

		$this->_smtp_debug_log = sprintf( "# SMTP Debug Log %s\n", date('c') );

		$result = wp_mail( wp_get_current_user()->user_email, __('WP-Mailer Test','wp-the-bot'), __("It Works.\n\n Sincerely,\nThe Bot",'wp-the-bot') );

		set_transient('smtp_send_success', var_export( $result, true ), 60 );
		set_transient('smtp_debug_log', $this->_smtp_debug_log, 60 );
	}

	public function enable_mailer_debug( &$phpmailer ) {

		$phpmailer->SMTPDebug = 4;
		$phpmailer->Timeout = 15; // 15 secs. (default is 300)
		$phpmailer->Debugoutput = array( $this, 'smtp_debug_log' );
	}

	public function smtp_debug_log( $msg, $level ) {
		$this->_smtp_debug_log .= "Level {$level}: $msg\n";
	}



	/**
	 *	Setup options.
	 *
	 *	@action admin_init
	 */
	public function register_settings() {
		$core = Core\Core::instance();
		$settings_section	= 'thebot_mailer_general';

		add_settings_section( $settings_section, __( 'Mailer Settings',  'wp-the-bot' ), '__return_empty_string', $this->optionset );

		foreach ( $core->get_options() as $option ) {
			$settings_opt = new Option( $this->optionset, $option );
			$settings_opt->add_ui( $settings_section, [
				'class' => 'regular-text'
			] );
		}
		add_action( 'thebot_settings_submit_button', [ $this, 'submit_btn' ] );

	}

	public function submit_btn() {
		submit_button( __('Save and Send Testmail' , 'wp-the-bot' ), 'secondary', 'test', false );
	}

}
