<?php
/**
 *	@package TheBot\Settings
 *	@version 1.0.0
 *	2018-09-22
 */

namespace TheBot\Core\Option;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}

use TheBot\Core;

class Choice extends Option {


	/**
	 *	@param string $what
	 *	@return mixed
	 */
	public function __get( $what ) {
		switch ( $what ) {
			case 'choices':
				return $this->$what;
			default:
				return parent::__get($what);
		}
	}

	/**
	 *	@param array $choices
	 */
	public function set_choices( $choices ) {
		// radio
		$this->choices = $choices;
		return $this;
	}

	/**
	 *	@param mixed $value
	 *	@return mixed
	 */
	public function sanitize( $value ) {
		if ( isset( $this->choices[ $value ] ) ) {
			return $value;
		}
		return null;
	}
}
