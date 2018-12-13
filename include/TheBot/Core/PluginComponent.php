<?php
/**
 *	@package TheBot\Core
 *	@version 1.0.0
 *	2018-09-22
 */

namespace TheBot\Core;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}


abstract class PluginComponent extends Singleton {

	/** @var array */
	private $options = [];

	/**
	 *	@param Option\Option $option
	 *	@return self
	 */
	public function add_option( $option ) {
		$this->options[ $option->id ] = $option;
		return $this;
	}

	/**
	 *	@return array
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 *	@return null|Option\Option
	 */
	public function get_option( $id ) {
		if ( isset( $this->options[$id] ) ) {
			return $this->options[$id];
		}
		return null;
	}


	/**
	 *	Save option if according $_POST value is present.
	 */
	public function maybe_save_options( ) {

		// other options:
		// $mail = Mail\Mail::instance();
		// $options += $mail->get_options();

		foreach ( $this->options as $option ) {

			$value = null;
			if ( isset( $_POST[ $option->name ] ) ) {
				$value = $_POST[ $option->name ];
				if ( ! is_array( $value ) ) {
					$value = trim( $value );
				}
				$value = wp_unslash( $value );
				$option->update( $value );
			}

		}
	}


	/**
	 *	Called on plugin activation
	 *
	 *	@return array(
	 *		'success'	=> bool,
	 *		'messages'	=> array,
	 *	)
	 */
	abstract function activate();

	/**
	 *	Called on plugin upgrade
	 *	@param	string	$new_version
	 *	@param	string	$old_version
	 *	@return array(
	 *		'success'	=> bool,
	 *		'messages'	=> array,
	 *	)
	 */
	abstract function upgrade( $new_version, $old_version );

	/**
	 *	Called on plugin deactivation
	 *	@return array(
	 *		'success'	=> bool,
	 *		'messages'	=> array,
	 *	)
	 */
	abstract function deactivate();

	/**
	 *	Called on plugin uninstall
	 *	@param	string	$new_version
	 *	@param	string	$old_version
	 *	@return array(
	 *		'success'	=> bool,
	 *		'messages'	=> array,
	 *	)
	 */
	abstract static function uninstall();

}
