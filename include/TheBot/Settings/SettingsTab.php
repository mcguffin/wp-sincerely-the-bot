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

abstract class SettingsTab extends Settings {

	protected $label = null;

	protected $id = null;


	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		// don't parent::__construct() here!
	}

	/**
	 *	Magic getter.
	 *
	 *	@param string $what
	 *	@return mixed
	 */
	public function __get( $what ) {
		switch ( $what ) {
			case 'id':
			case 'label':
				return $this->$what;
		}
	}
}
