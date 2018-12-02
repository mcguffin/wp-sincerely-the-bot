<?php
/**
 *	@package TheBot\Compat
 *	@version 1.0.0
 *	2018-09-22
 */

namespace TheBot\Compat;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}


use TheBot\Core;
use TheBot\Settings;


class WPMU extends Core\PluginComponent {
	private $options = array(
	);
	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		$settings = Settings\SettingsMailer::instance();
		foreach ( $settings->get_options() as $opt ) {
			add_filter( "pre_option_{$opt}", array( $this, 'pre_option' ), 10, 2 );
		}
		add_action( 'load-settings_page_mailer', array( $this, 'update_options' ) );
		add_action( 'network_admin_menu', array( $this, 'network_admin_menu' ));

		remove_action( 'admin_menu', array( $settings, 'admin_menu' ) );

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
		if ( ! empty( $_POST ) ) {
			check_admin_referer( 'mailer-options' );
			if ( ! current_user_can( 'manage_network_options' ) ) {
				wp_die( __('This is not allowd.','wp-the-bot') );
			}

			$settings = Settings\SettingsMailer::instance();

			foreach ( $settings->get_options() as $option ) {

				$option = trim( $option );
				$value = null;
				if ( isset( $_POST[ $option ] ) ) {
					$value = $_POST[ $option ];
					if ( ! is_array( $value ) ) {
						$value = trim( $value );
					}
					$value = wp_unslash( $value );
				}
				update_site_option( $option, $value );
			}

			if ( isset( $_POST['test' ] ) ) {
				$settings->send_testmail();
			}
		}
	}


	/**
	 *	@action network_admin_menu
	 */
	public function network_admin_menu() {
		$settings = Settings\SettingsMailer::instance();
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
