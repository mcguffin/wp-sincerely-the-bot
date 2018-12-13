<?php
/**
 *	@package TheBot\Compat
 *	@version 1.0.0
 *	2018-09-22
 */

namespace TheBot\Compat\WPMU;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}


use TheBot\Core;
use TheBot\Mail;
use TheBot\Settings as CoreSettings;


class Settings extends Core\PluginComponent {
	private $options = array(
	);
	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		// wpmu-settings
		if ( is_network_admin() ) {


			add_action( 'load-settings_page_mailer', array( $this, 'update_options' ) );
			add_action( 'network_admin_menu', array( $this, 'network_admin_menu' ));

			//remove_action( 'admin_menu', array( $settings, 'admin_menu' ) );

			$core = Core\Core::instance();
			$mail = Mail\Mail::instance();

			foreach ( $core->get_options() as $option ) {
				$option->multisite = true;
			}
			foreach ( $mail->get_messages( 'network' ) as $message ) {
				foreach ( $message->get_options() as $option ) {
					$option->multisite = true;
				}
			}

		} else {
			/*
			CoreSettings\SettingsPageMailer::instance()->deinit();
			/*/
			$s = CoreSettings\SettingsPageMailer::instance();
			remove_action( 'admin_menu', array( $s, 'admin_menu' ) );
			remove_action( 'admin_init', array( $s, 'register_settings' ) );
			//*/


		}
	}

	/**
	 *	Reroute plugin options
	 *
	 *	@filter pre_option_{$option}
	 */
	public function pre_option( $value, $option_name ) {
		if ( $val = get_site_option( $option_name ) ) {
			return $val;
		}
		return '';
	}

	/**
	 *	@action load-settings_page_mailer
	 */
	public function update_options() {
		if ( ! is_network_admin() ) {
			return;
		}
		if ( ! empty( $_POST ) ) {

			check_admin_referer( 'mailer-options' );

			if ( ! current_user_can( 'manage_network_options' ) ) {
				wp_die( __('This is not allowd.','wp-the-bot') );
			}

			$core = Core\Core::instance();
			$mail = Mail\Mail::instance();

			$core->maybe_save_options();
			foreach ( $mail->get_messages( 'network' ) as $message ) {
				$message->maybe_save_options();
			}

			if ( isset( $_POST[ 'test' ] ) ) {
				$settings->send_testmail();
			}
		}
	}


	/**
	 *	@action network_admin_menu
	 */
	public function network_admin_menu() {
		$settings = CoreSettings\SettingsPageMailer::instance();

		add_submenu_page( 'settings.php' , __('WP Mailer','wp-the-bot'), __('WP Mailer','wp-the-bot'), 'manage_network_options', 'mailer', array( $settings, 'settings_page'));
	}


	/**
	 *	@inheritdoc
	 */
	 public function activate(){

	 }

	 /**
	  *	@inheritdoc
	  */
	 public function deactivate(){

	 }

	 /**
	  *	@inheritdoc
	  */
	 public static function uninstall() {
		 // remove content and settings
	 }

	/**
 	 *	@inheritdoc
	 */
	public function upgrade( $new_version, $old_version ) {
	}

}
