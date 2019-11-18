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

class SettingsPageMailer extends SettingsPage {

	protected $optionset = 'mailer';

	protected function __construct() {

		parent::__construct();

		//$this->add_tab( SettingsTabMessages::instance() );
		$this->add_tab( SettingsTabMailer::instance() );


	}


}
