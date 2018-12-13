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

class Option {

	public function __construct( $optionset, $option ) {

		$this->optionset	= $optionset;
		$this->option		= $option;

		register_setting( $this->optionset, $this->option->name, $this->option->sanitize );

	}

	public function add_ui( $settings_section, $attr = [], $ui_cb = null ) {
		if ( is_null( $ui_cb ) ) {
			$ui_cb = [ $this, 'ui' ];
		}

		if ( $this->option instanceOf Core\Option\Absint ) {

			$attr += [
				'type'	=> 'number',
				'step'	=> '1',
				'min'	=> '0',
			];

		} else if ( $this->option instanceOf Core\Option\Boolean ) {

			$ui_cb = [ $this, 'ui_boolean' ];

		} else if ( $this->option instanceOf Core\Option\Email ) {

			$attr += [
				'type'	=> 'email',
			];

		} else if ( $this->option instanceOf Core\Option\Integer ) {

			$attr += [
				'type'	=> 'number',
				'step'	=> '1',
			];

		} else if ( $this->option instanceOf Core\Option\ChoiceRadio ) {

			$ui_cb = [ $this, 'ui_radio' ];

		} else if ( $this->option instanceOf Core\Option\ChoiceSelect ) {

			$ui_cb = [ $this, 'ui_select' ];

		}

		add_settings_field(
			$this->option->name,
			$this->option->label,
			$ui_cb,
			$this->optionset,
			$settings_section,
			[
				'label_for'	=> '',
				'class'		=> '',
				'attr'		=> $attr,
			]
		);
	}


	/**
	 *	Render UI
	 */
	public function ui( $args = [ 'attr' => [] ] ) {
		$atts = [];
		$attr = wp_parse_args( $args['attr'], [
			'type'			=> 'text',
			'name'			=> $this->option->name,
			'value'			=> $this->option->value,
			'placeholder'	=> $this->option->default,
		]);
		foreach ( $attr as $key => $val ) {
			$atts[] = sprintf(' %s="%s"', $key, esc_attr( $val ));
		}

		?>
		<label>
			<input <?php echo implode( ' ', $atts ); ?> />
		</label>
		<?php

		$this->ui_description();

	}

	public function ui_boolean() {

		$option_value = $this->option->value;

		?><label>
			<input type="hidden" name="<?php echo $this->option->name ?>" value="0" />
			<input type="checkbox" <?php checked( boolval( $option_value ), true, true ); ?> name="<?php echo $this->option->name ?>" value="1" />
			<?php echo $this->option->label ?>
		</label>
		<?php

		$this->ui_description();

	}

	/**
	 *
	 */
	public function ui_radio() {

		$option_value = $this->option->value;

		foreach ( $this->option->choices as $value => $choice ) {
			?>
			<label>
				<input type="radio" <?php checked( $option_value, $value, true ); ?> name="<?php echo $this->option->name ?>" value="<?php echo $value ?>" />
				<?php echo $choice ?>
			</label>
			<?php
		}
		$this->ui_description();

	}


	/**
	 *	@inheritdoc
	 */
	public function ui_select() {

		$option_value = $this->option->value;

		?>
		<select name="<?php echo $this->option->name ?>" />
			<?php

		foreach ( $this->choices as $value => $choice ) {
			?>
			<option value="<?php esc_attr_e( $value ); ?>" <?php selected( $option_value, $value, true ); ?>>
				<?php echo $choice ?>
			</option>
			<?php

		}
			?>
		</select>
		<?php

		$this->ui_description();

	}

	/**
	 *	@inheritdoc
	 */
	public function ui_description() {
		?>
		<?php
			if ( ! empty( $this->option->description ) ) {
				printf( '<p class="description">%s</p>', $this->option->description );
			}
		?>
		<?php
	}

}
