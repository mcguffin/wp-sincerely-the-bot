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

use TheBot\Ajax;
use TheBot\Core;
use TheBot\Mail;

class SettingsTabMessages extends SettingsTab {

	protected $optionset = 'mailer';

	protected $id = 'messages';

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		$this->label = __( 'Messages', 'wp-the-bot' );
		$defaults = apply_filters( 'thebot_mail_defaults', [] );

		add_option( 'thebot_messages', $defaults, '', true ); // admin url...?

		$args = func_get_args();
		parent::__construct( ...$args );


		add_action( 'load-settings_page_mailer', array( $this, 'enqueue_assets' ) );

		$this->ajax_handler = new Ajax\AjaxHandler('thebot-add-to-theme',array(
			'capability'	=> 'edit_themes',
			'callback'		=> [ $this, 'ajax_add_to_theme' ],
		));
	}


	/**
	 *	Ajax Callback
	 */
	public function ajax_add_to_theme( $params ) {

		$core = Core\Core::instance();
		$mail = Mail\Mail::instance();

		$template = $params['message_id'];

		$path = $mail->get_theme_path( $template );

		if ( ! file_exists( $path ) ) {
			wp_mkdir_p( dirname( $path ) );
			copy( $mail->get_plugin_file( $template ), $path );
		}
		$dest = get_stylesheet_directory();
		return [
			'success'	=> true,
			'html'		=> $this->get_theme_ui( $template ),
		];
	}


	/**
	 *	Enqueue settings Assets
	 *
	 *	@action load-options-{$this->optionset}.php
	 */
	public function enqueue_assets() {

		$core = Core\Core::instance();

		wp_enqueue_style('thebot-settings-mailer', $core->get_asset_url( 'css/admin/settings/mailer.css' ), array( 'jquery' ) );
		wp_enqueue_script('thebot-settings-mailer', $core->get_asset_url( 'js/admin/settings/mailer.js' ), array( 'jquery' ) );
		wp_localize_script('thebot-settings-mailer','thebot_mailer',[
			'ajax'	=> [
				'url' => admin_url( 'admin-ajax.php' ),
				'data' => [
					'action' => $this->ajax_handler->action,
					'nonce' => $this->ajax_handler->nonce,
				],
			],
		]);
	}



	/**
	 *	Setup options.
	 *
	 *	@action admin_init
	 */
	public function register_settings() {

		$mail = Mail\Mail::instance();

		$settings_section	= 'thebot_mailer_general';

		add_settings_section( $settings_section, __( 'Message Settings',  'wp-the-bot' ), '__return_empty_string', $this->optionset );

		$messages = $mail->get_messages( is_network_admin() ? 'network' : 'blog' );

		foreach ( $messages as $message ) {

			foreach ( $message->get_options() as $option ) {

				$settings_opt = new Option( $this->optionset, $option );
				add_settings_field(
					$message->id,
					$message->title,
					array( $this, 'mail_ui' ),
					$this->optionset,
					$settings_section,
					[
						'label_for'	=> '',
						'class'		=> '',
						'message'	=> $message,
					]
				);

			}

		}

	}

	public function mail_ui( $args ) {

		extract($args); /*  */

		$mail = Mail\Mail::instance();

		?>
		<div>
			<?php

			if ( $message->supports('disable') ) {
				$opt = new Option( $this->optionset, $message->get_option('disabled') );
				$opt->ui_boolean();
			}


			if ( $message->supports('custom_subject') ) {
				$opt = new Option( $this->optionset, $message->get_option('custom_subject') );
				$opt->ui_boolean();

				$opt = new Option( $this->optionset, $message->get_option('subject') );
				$opt->ui();
			}
			?>
		</div>
		<?php


		?>
		<div>
			<?php

			$opt = new Option( $this->optionset, $message->get_option('html') );
			$opt->ui_boolean();

			?>


			<?php if ( $message->supports( 'custom_template' ) ) { ?>

				<!-- if in theme: show path, and edit link -->
				<?php


				$opt = new Option( $this->optionset, $message->get_option('custom_template') );
				$opt->ui_boolean();

				echo $this->get_theme_ui( $message->id );

				?>
			<?php } ?>

		</div>
		<?php
		$message->settings_ui( $this->optionset );

		printf( '<p class="description">%s</p>', $message->description );

	}

	/**
	 *	Display copy to theme button or edit link to file
	 */
	protected function get_theme_ui( $message_id ) {
		$mail = Mail\Mail::instance();
		$out = '';
		if ( $theme_file = $mail->get_theme_file( $message_id , true) ) {
			if ( current_user_can( 'edit_themes' ) ) {
				// edit theme file link
				list( $stylesheet, $tree ) = explode( '/', $theme_file, 2 );

				$url = add_query_arg(
					array(
						'file' => rawurlencode( $tree ),
						'theme' => rawurlencode( $stylesheet ),
					),
					is_multisite() ? network_admin_url( 'theme-editor.php' ) : admin_url( 'theme-editor.php' )
				);
				$out .= sprintf( '<a href="%s">%s</a>', $url, __('Edit File','wp-the-bot') );
			} else {
				$out .= sprintf( '%s<code>%s</code>',
					_e('Theme File to edit:','wp-the-bot'),
					$mail->get_rel_file( $message_id )
				);
			}
		} else {
			$out .= sprintf('<button data-ajax-message-id="%s" class="button-secondary">%s</button>',
				esc_attr( $message_id ),
				__('Copy to Theme','wp-the-bot')
			);
		}
		return $out;

	}

}
