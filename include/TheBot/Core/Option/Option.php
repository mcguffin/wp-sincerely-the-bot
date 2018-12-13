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

abstract class Option {

	private $prefix = null;

	/**
	 *	@var boolean
	 */
	public $multisite = false;

	/**
	 *	@var string
	 */
	protected $id;

	/**
	 *	@var mixed
	 */
	protected $default;

	/**
	 *	@var boolean
	 */
	protected $autoload = false;

	/**
	 *	@var callable
	 */
	protected $sanitize = null;

	/**
	 *	@var string
	 */
	protected $label = '';

	/**
	 *	@var string
	 */
	protected $description = '';

	/**
	 *	@var array
	 */
	protected $input_attr = [
		'type'	=> 'text',
	];


	/**
	 *	@param string $id
	 *	@param string $default
	 *	@param string $autoload
	 */
	public function __construct( $id, $default, $label, $prefix = 'thebot_', $description = '', $autoload = false ) {

		$this->id			= $id;
		$this->prefix		= $prefix;
		$this->default		= $default;
		$this->autoload 	= $autoload;
		$this->label		= $label;
		$this->description	= $description;

		if ( is_null( $this->sanitize ) && method_exists( $this, 'sanitize' ) ) {
			$this->sanitize = [ $this, 'sanitize'];
		}

		add_option( $this->name,	$this->default, '', $this->autoload );

		do_action( 'thebot_register_option_' . $this->id, $this );
	}


	/**
	 *	@param string $what
	 *	@return mixed
	 */
	public function __get( $what ) {
		switch ( $what ) {
			case 'id':
			case 'sanitize':
			case 'label':
			case 'description':
			case 'default':
				return $this->$what;
			case 'name':
				return $this->prefix . $this->id;
			case 'value':
				if ( $this->multisite ) {
					return get_site_option( $this->name );
				} else {
					return get_option( $this->name );
				}
		}
	}

	/**
	 *	@param string $what
	 *	@param mixed $value
	 */
	public function update( $value ) {
		if ( $this->multisite ) {
			return update_site_option( $this->name, $value );
		} else {
			return update_option( $this->name, $value );
		}
	}

	public function delete() {
		if ( $this->multisite ) {
			return delete_site_option( $this->name );
		} else {
			return delete_option( $this->name );
		}
	}


}
