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

class SettingsMailer extends Settings {

	private $optionset = 'mailer';
	private $_smtp_debug_log = '';


	/**
	 *	@inheritdoc
	 */
	protected function __construct() {


		add_option( 'thebot_mailer_from',			'', 	'', true ); // admin url...?
		add_option( 'thebot_mailer_from_name',		'', 	'', true ); // admin url...?
		add_option( 'thebot_mailer_smtp',			0,		'', true ); // admin url...?
		add_option( 'thebot_mailer_smtp_host',		'' ,	'', true ); // admin url...?
		add_option( 'thebot_mailer_smtp_port', 		587 ,	'', true ); // admin url...?
		add_option( 'thebot_mailer_smtp_secure',	'',		'', true );
		add_option( 'thebot_mailer_smtp_all_certs',	'',		'', true );
		add_option( 'thebot_mailer_smtp_auth', 		0,		'', true ); // admin url...?
		add_option( 'thebot_mailer_smtp_auth_type',	'',		'', true ); // admin url...?
		add_option( 'thebot_mailer_smtp_auth_user',	'',		'', true ); // admin url...?
		add_option( 'thebot_mailer_smtp_auth_pass',	'',		'', true ); // admin url...?

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		parent::__construct();

	}

	public function get_options() {
		return array(
			'thebot_mailer_from',
			'thebot_mailer_from_name',
			'thebot_mailer_smtp',
			'thebot_mailer_smtp_host',
			'thebot_mailer_smtp_port',
			'thebot_mailer_smtp_secure',
			'thebot_mailer_smtp_all_certs',
			'thebot_mailer_smtp_auth',
			'thebot_mailer_smtp_auth_type',
			'thebot_mailer_smtp_auth_user',
			'thebot_mailer_smtp_auth_pass',
		);
	}


	/**
	 *	Add Settings page
	 *
	 *	@action admin_menu
	 */
	public function admin_menu() {
		add_options_page( __('WP Mailer' , 'wp-the-bot' ),__('WP Mailer' , 'wp-the-bot'), 'manage_options', $this->optionset, array( $this, 'settings_page' ) );
	}

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
	 *	Render Settings page
	 */
	public function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		?>
		<div class="wrap">
			<h2><?php _e('WP Mailer', 'wp-the-bot') ?></h2>

			<form method="post">
				<?php
				settings_fields(  $this->optionset );
				do_settings_sections( $this->optionset );
				?>
				<p class="submit">
					<?php
					submit_button( __('Save Settings' , 'wp-the-bot' ), 'primary', 'submit', false );
					submit_button( __('Save and Send Testmail' , 'wp-the-bot' ), 'secondary', 'test', false );
					?>
				</p>
			</form>
			<?php

			if ( $log = get_transient( 'smtp_debug_log' ) ) {
				printf('wp_mail() returned <code>%s</code>', var_export(get_transient('smtp_send_success' ),true) );
				printf('<pre>%s</pre>', htmlentities($log) );
				delete_transient( 'smtp_debug_log' );
			}
			?>
		</div><?php
	}


	/**
	 * Enqueue settings Assets
	 *
	 *	@action load-options-{$this->optionset}.php

	 */
	public function enqueue_assets() {

	}


	/**
	 *	Setup options.
	 *
	 *	@action admin_init
	 */
	public function register_settings() {

		$settings_section	= 'thebot_mailer_general';

		add_settings_section( $settings_section, __( 'Mailer Settings',  'wp-the-bot' ), '__return_empty_string', $this->optionset );



		$option_name		= 'thebot_mailer_from';
		register_setting( $this->optionset , $option_name, 'sanitize_email' );
		add_settings_field(
			$option_name,
			__( 'From Address',  'wp-the-bot' ),
			array( $this, 'text_ui' ),
			$this->optionset,
			$settings_section,
			array(
				'option_name'			=> $option_name,
			)
		);

		$option_name		= 'thebot_mailer_from_name';
		register_setting( $this->optionset , $option_name );
		add_settings_field(
			$option_name,
			__( 'From Name',  'wp-the-bot' ),
			array( $this, 'text_ui' ),
			$this->optionset,
			$settings_section,
			array(
				'option_name'			=> $option_name,
			)
		);
		$settings_section	= 'thebot_mailer_smtp';

		add_settings_section( $settings_section, __( 'SMTP Settings',  'wp-the-bot' ), '__return_empty_string', $this->optionset );


		$option_name		= 'thebot_mailer_smtp';
		register_setting( $this->optionset , $option_name, 'intval' );
		add_settings_field(
			$option_name,
			__( 'Enable',  'wp-the-bot' ),
			array( $this, 'checkbox_ui' ),
			$this->optionset,
			$settings_section,
			array(
				'option_name'			=> $option_name,
				'option_label'			=> __( 'Enable SMTP Delivery',  'wp-the-bot' ),
			)
		);




		$option_name		= 'thebot_mailer_smtp_host';
		register_setting( $this->optionset, $option_name );
		add_settings_field(
			$option_name,
			__( 'Host',  'wp-the-bot' ),
			array( $this, 'text_ui' ),
			$this->optionset,
			$settings_section,
			array(
				'option_name'			=> $option_name,
			)
		);



		$option_name		= 'thebot_mailer_smtp_port';
		register_setting( $this->optionset , $option_name, 'intval' );
		add_settings_field(
			$option_name,
			__( 'Port',  'wp-the-bot' ),
			array( $this, 'text_ui' ),
			$this->optionset,
			$settings_section,
			array(
				'option_name'			=> $option_name,
				'option_label'			=> __( 'Enable SMTP Delivery',  'wp-the-bot' ),
			)
		);

		$option_name		= 'thebot_mailer_smtp_secure';
		register_setting( $this->optionset , $option_name );
		add_settings_field(
			$option_name,
			__( 'SMTP Secure',  'wp-the-bot' ),
			array( $this, 'choice_ui' ),
			$this->optionset,
			$settings_section,
			array(
				'option_name'			=> $option_name,
				'option_label'			=> __( 'SMTP Authentication',  'wp-the-bot' ),
				'option_description'	=> '',
				'choices'				=> array(
					''		=> __( 'None', 'wp-the-bot' ),
					'ssl'	=> __( 'SSL', 'wp-the-bot' ),
					'tls'	=> __( 'TLS', 'wp-the-bot' ),
				),
			)
		);


		$option_name		= 'thebot_mailer_smtp_all_certs';
		register_setting( $this->optionset , $option_name, 'intval' );
		add_settings_field(
			$option_name,
			__( 'Allow Insecure',  'wp-the-bot' ),
			array( $this, 'checkbox_ui' ),
			$this->optionset,
			$settings_section,
			array(
				'option_name'			=> $option_name,
				'option_label'			=> __( 'Allow all Certificates',  'wp-the-bot' ),
			)
		);


		$option_name		= 'thebot_mailer_smtp_auth';
		register_setting( $this->optionset , $option_name, 'intval' );
		add_settings_field(
			$option_name,
			__( 'SMTP Auth',  'wp-the-bot' ),
			array( $this, 'checkbox_ui' ),
			$this->optionset,
			$settings_section,
			array(
				'option_name'			=> $option_name,
				'option_label'			=> __( 'SMTP Authentication',  'wp-the-bot' ),
			)
		);

		$option_name		= 'thebot_mailer_smtp_auth_type';
		register_setting( $this->optionset, $option_name );
		add_settings_field(
			$option_name,
			__( 'SMTP Auth Type',  'wp-the-bot' ),
			array( $this, 'choice_ui' ),
			$this->optionset,
			$settings_section,
			array(
				'option_name'			=> $option_name,
				'option_label'			=> __( 'SMTP Authentication',  'wp-the-bot' ),
				'option_description'	=> '',
				'choices'				=> array(
					''			=> __( 'Try all', 'wp-the-bot' ),
					'CRAM-MD5'	=> __( 'MD5 Challenge-Response', 'wp-the-bot' ),
					'LOGIN'		=> __( 'Login', 'wp-the-bot' ),
					'PLAIN'		=> __( 'Plain', 'wp-the-bot' ),
					'XOAUTH2'	=> __( 'OAuth 2.0', 'wp-the-bot' ),
				),
			)
		);



		$option_name		= 'thebot_mailer_smtp_auth_user';
		register_setting( $this->optionset , $option_name );
		add_settings_field(
			$option_name,
			__( 'SMTP Auth Username',  'wp-the-bot' ),
			array( $this, 'text_ui' ),
			$this->optionset,
			$settings_section,
			array(
				'option_name'			=> $option_name,
				'option_label'			=> __( 'Enable SMTP Delivery',  'wp-the-bot' ),
			)
		);


		$option_name		= 'thebot_mailer_smtp_auth_pass';
		register_setting( $this->optionset , $option_name );
		add_settings_field(
			$option_name,
			__( 'SMTP Auth Password',  'wp-the-bot' ),
			array( $this, 'text_ui' ),
			$this->optionset,
			$settings_section,
			array(
				'option_name'			=> $option_name,
				'option_label'			=> __( 'Enable SMTP Delivery',  'wp-the-bot' ),
			)
		);

	}

}
