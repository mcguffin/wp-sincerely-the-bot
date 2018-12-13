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

abstract class SettingsPage extends Settings {

	protected $optionset = null;

	private $tabs = [];

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		parent::__construct();

	}

	/**
	 *	@return string
	 */
	protected function current_tab() {
		if ( empty( $this->tabs ) ) {
			return null;
		}
		foreach ( $this->tabs as $tab ) {
			if ( isset($_GET['tab']) && $_GET['tab'] === $tab->id ) {
				return $tab;
			}
		}
		return $this->tabs[0];
	}

	public function add_tab( $tab ) {
		$this->tabs[] = $tab;
	}

	public function remove_tab( $tab ) {
		foreach ( array_keys( $this->tabs ) as $i ) {
			if ( $this->tabs[$i] === $tab ) {
				unset( $this->tabs[$i] );
			}
		}
	}

	/**
	 *	Add Settings page
	 *
	 *	@action admin_menu
	 */
	public function admin_menu() {

		add_options_page( __('WP Mailer' , 'wp-the-bot' ),__('WP Mailer' , 'wp-the-bot'), 'manage_options', $this->optionset, array( $this, 'settings_page' ) );

	}

	/**
	 *	@inheritdoc
	 */
	public function register_settings() {

		if ( $tab = $this->current_tab() ) {
			$tab->register_settings();
		}
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
			<h1><?php _e('WP Mailer', 'wp-the-bot') ?></h1>
			<?php
			if ( ! empty( $this->tabs ) ) {
				?>
				<h2 class="nav-tab-wrapper">
					<?php
						foreach ( $this->tabs as $tab ) {

							printf( '<a class="nav-tab %s" href="%s">%s</a>',
								$tab === $this->current_tab() ? 'nav-tab-active' : '',
								add_query_arg( 'tab', $tab->id ),
								$tab->label
							);
						}
					?>
				</h2>
				<?php

			}
			?>

			<form method="post">
				<?php
				settings_fields(  $this->optionset );
				do_settings_sections( $this->optionset );
				?>
				<p class="submit">
					<?php
					submit_button( __( 'Save Settings' , 'wp-the-bot' ), 'primary', 'submit', false );
					do_action( 'thebot_settings_submit_button' );
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

	public function deinit() {
		remove_action( 'admin_menu', array( $this, 'admin_menu' ) );
		parent::deinit();
	}



}
