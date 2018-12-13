<?php
/**
 *	@package TheBot\Core
 *	@version 1.0.1
 *	2018-09-22
 */

namespace TheBot\Mail;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}

use TheBot\Core;

class Mail extends Core\Singleton {

	private $theme_path = 'the-bot/mail';

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		// @plugins_loaded:10
		$args = func_get_args();
		parent::__construct( ...$args );


		Messages\NewUserNotification::instance();
		Messages\NewUserNotificationAdmin::instance();

		Messages\RequestPasswordReset::instance();
		Messages\PasswordChangeNotification::instance();
		Messages\PasswordEditedNotification::instance();

	}

	public function get_messages( $context = null ) {
		return apply_filters( 'thebot_messages', [], $context );
	}

	public function get_message( $id ) {
		$msg = $this->get_messages();
		if ( isset( $msg[$id] ) ) {
			return $msg[$id];
		}
	}

	/**
	 *	Set mail delivery to text/html
	 */
	public function set_html() {
		if ( ! has_filter( 'wp_mail_content_type', [ $this, 'html' ] ) ) {
			add_filter( 'wp_mail_content_type', [ $this, 'html' ] );
		}
	}

	/**
	 *	Reset mail delivery to default
	 */
	public function unset_html() {
		remove_filter( 'wp_mail_content_type', [ $this, 'html' ] );
	}


	/**
	 *	@filter wp_mail_content_type
	 */
	public function html() {
		return 'text/html';
	}


	/**
	 *	render custom mail tempalte
	 */
	public function render_email( $template, $vars ) {
		ob_start();
		global $thebot_styles;

		$this->set_html();

		$thebot_styles	= include $this->get_template_file( 'styles' );

		$this->render_template( 'header', $vars );
		$this->render_template( $template, $vars );
		$this->render_template( 'footer', $vars );

		return ob_get_clean();

	}
	/**
	 *	Add header and footer to default wp mail
	 */
	public function wrap_email( $text ) {
		ob_start();
		global $thebot_styles;

		$this->set_html();

		$thebot_styles	= include $this->get_template_file( 'styles' );
		$this->render_template( 'header', $vars );
		echo nl2br( $text );
		$this->render_template( 'footer', $vars );
		return ob_get_clean();

	}

	/**
	 *	render a template file
	 */
	public function render_template( $template, $vars = [] ) {
		//$network_name = get_network()->site_name;

		$default_vars = [
			'site_name'		=> wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
			'site_url'		=> home_url(),
		];
		$default_vars = apply_filters( 'thebot_default_template_vars', $default_vars );
		$vars = wp_parse_args( $vars, $default_vars );

		extract( $vars );
		include $this->get_template_file( $template );
	}



	/**
	 *	Get file path of message template inside theme folder
	 *
	 *	@return null|string path of theme file or null
	 */
	public function get_template_file( $template ) {
		if ( $file = apply_filters( "thebot_template_file_{$template}", false, $template ) ) {
			return $file;
		}

		$file = $this->get_theme_file( $template );

		if ( is_null( $file ) ) {
			$file = $this->get_plugin_file( $template );
		}
		$file = apply_filters( 'thebot_template_file', $file, $template );

		return $file;
	}



	/**
	 *	Get file path of message template inside theme folder
	 *
	 *	@return null|string path of theme file or null
	 */
	final public function get_theme_file( $template, $rel = false ) {
		$rel_file = $this->get_rel_file( $template );
		$lookup = [
			get_stylesheet_directory() .  '/' . $rel_file
				=> get_stylesheet() . '/' . $rel_file,
			get_template_directory() .  '/' . $rel_file
				=> get_template() . '/' . $rel_file,
		];
		foreach ( array_unique( $lookup ) as $abspath => $relpath ) {
			if ( file_exists( $abspath ) ) {
				return $rel ? $relpath : $abspath;
			}
		}
		return null;

	}

	/**
	 *	Theme path of message template
	 *
	 *	@return null|string path of theme file or null
	 */
	final public function get_rel_file( $template ) {

		return $this->theme_path . '/'  . $template . '.php';
	}

	/**
	 *	Theme path of message template
	 *
	 *	@return null|string path of theme file or null
	 */
	final public function get_theme_path( $template ) {

		return get_stylesheet_directory() . '/' . $this->theme_path . '/'  . $template . '.php';

	}

	/**
	 *	Theme path of message template
	 *
	 *	@return null|string path of theme file or null
	 */
	final public function get_plugin_file( $template ) {

		return Core\Core::instance()->get_asset_path( 'include/templates/mail/' . $template . '.php' );

	}



}
