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

abstract class Settings extends Core\Singleton {

	protected $optionset = null;

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		parent::__construct();

	}

	/**
	 *	@inheritdoc
	 */
	public function deinit() {

		remove_action( 'admin_init' , array( $this, 'register_settings' ) );

	}

	/**
	 *	@action admin_init
	 */
	abstract function register_settings();


}
