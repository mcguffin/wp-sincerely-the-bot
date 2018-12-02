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

	/**
	 *	@inheritdoc
	 */
	protected function __construct(){

		add_action( 'admin_init' , array( $this, 'register_settings' ) );

		parent::__construct();

	}


	abstract function register_settings();


	/**
	 *	Print a checkbox
	 *
	 *	@param $args	array( $option_name, $label )
	 */
	public function checkbox_ui( $args ) {
		@list( $option_name, $label, $description ) = array_values( $args );

		$option_value = get_option( $option_name );

		?><label>
			<input type="hidden" name="<?php echo $option_name ?>" value="0" />
			<input type="checkbox" <?php checked( boolval( $option_value ), true, true ); ?> name="<?php echo $option_name ?>" value="1" />
			<?php echo $label ?>
		</label>
		<?php
			if ( ! empty( $description ) ) {
				printf( '<p class="description">%s</p>', $description );
			}
		?>
		<?php

	}


	/**
	 *	Print a checkbox
	 *
	 *	@param $args	array( $option_name, $label )
	 */
	public function choice_ui( $args ) {
		@list( $option_name, $label, $description, $choices ) = array_values( $args );

		$option_value = get_option( $option_name );

		foreach ( $choices as $value => $choice ) {
			?>
			<label>
				<input type="radio" <?php checked( $option_value, $value, true ); ?> name="<?php echo $option_name ?>" value="<?php echo $value ?>" />
				<?php echo $choice ?>
			</label>
			<?php

		}


		?>
		<?php
			if ( ! empty( $description ) ) {
				printf( '<p class="description">%s</p>', $description );
			}
		?>
		<?php

	}



	/**
	 * Output text input
	 */
	public function text_ui( $args ) {

		@list( $option_name, $label, $description ) = array_values( $args );

		$option_value = get_option( $option_name );

		?>
			<label for="<?php echo $option_name ?>">
				<input class="regular-text" type="text" id="<?php echo $option_name ?>" name="<?php echo $option_name ?>" value="<?php esc_attr_e( $option_value ) ?>" />
			</label>
			<?php
			if ( ! empty( $description ) ) {
				printf( '<p class="description">%s</p>', $description );
			}
			?>
		<?php
	}

	/**
	 *	Sanitize checkbox input
	 *
	 *	@param $value
	 *	@return boolean
	 */
	public function sanitize_checkbox( $value ) {
		return boolval( $value );
	}

}
