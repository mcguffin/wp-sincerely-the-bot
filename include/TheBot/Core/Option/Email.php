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

class Email extends Text {

	/**
	 *	@inheritdoc
	 */
	protected $sanitize = 'sanitize_email';


}
