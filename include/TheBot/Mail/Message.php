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

abstract class Message extends Core\PluginComponent {

	/**
	 *	@var array
	 */
	private $options = null;

	/**
	 *	Whether this type of message can be sent in HTML format
	 *
	 *	@var array
	 */
	private $_supports = [];// = true;

	/**
	 *	@var string unique ID for this type of message
	 */
	protected $id = null;

	/**
	 *	@var string context of this mail. 'blog' or 'network' for multisite-specific-mails
	 */
	protected $context = 'blog';

	/**
	 *	@var string title in settings admin
	 */
	protected $title = '';

	/**
	 *	@var string description in settings admin
	 */
	protected $description = '';


	/**
	 *	What kind of user can recieve messages of this type?
	 *
	 *	@var bool
	 */
	protected $capabilities = ['read'];

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {


		$args = func_get_args();
		parent::__construct( ...$args );
		$this
			->add_option( new Core\Option\Boolean( 'html', false, __('HTML','wp-the-bot'), $this->id ) );

		add_filter('thebot_mail_defaults', [ $this, 'add_default_settings'] );
		add_filter('thebot_messages', [ $this, 'add_self' ], 10, 2 );


	}

	/**
	 *	@param string $what
	 */
	public function __get( $what ) {
		switch ( $what ) {
			case 'capabilities':
			case 'context':
			case 'description':
			case 'id':
			case 'title':
				return $this->$what;
		}
	}


	/**
	 *	Check if a certain feature is supported
	 *
	 *	@param string $feature disable|html|custom_subject|custom_content|custom_recipient
	 *	@return bool whether the plugin supports the given feature
	 */
	final public function supports( $feature ) {
		return isset( $this->_supports[ $feature ] ) && $this->_supports[ $feature ];
	}

	/**
	 *	Add feature support
	 */
	final protected function add_support( $feature ) {
		$this->_supports[ $feature ] = true;
		return $this;
	}

	/**
	 *	Remove feature support
	 */
	final protected function remove_support( $feature ) {
		if ( isset( $this->_supports[ $feature ] ) ){
			$this->_supports[ $feature ] = false;
		}
		return $this;
	}

	/**
	 *	@filter thebot_messages
	 */
	public function add_self( $mails, $context = null ) {

		if ( is_null( $context ) || $context === $this->context ) {
			$mails[$this->id] = $this;
		}
		return $mails;
	}

	/**
	 *	Print additional settings UI
	 */
	public function settings_ui($optionset) {
		?>
		<p class="description">
			<?php _e('- no Options -','wp-the-bot'); ?>
		</p>
		<?php
	}

	/**
	 *	@inheritdoc
	 */
	public function activate() {}

	/**
	 *	@inheritdoc
	 */
	public function upgrade( $new_version, $old_version ) {}

	/**
	 *	@inheritdoc
	 */
	public function deactivate() {}

	/**
	 *	@inheritdoc
	 */
	public static function uninstall() {}


}
