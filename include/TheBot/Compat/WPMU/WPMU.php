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
use TheBot\Settings as CoreSettings;
// use TheBot\Mail;


class WPMU extends Core\PluginComponent {

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

		Settings::instance();
		// Users::instance();

		$this->init();

	}

	/**
	 *	@action init
	 */
	public function init() {
		// Mail\Messages\WPMUWelcomeUser::instance();
		// Mail\Messages\WPMUWelcomeBlog::instance();
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
