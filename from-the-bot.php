<?php
/*
Plugin Name: Mail-from-the-Bot
Plugin URI: http://flyingletters.com/mail-from-the-bot/
Description: Sets an arbitrary Email address as From-Address for the WP automailer.
Version: 1.0.1
Author: Joern Lund
Author URI: http://flyingletters.com/
*/

class FromTheBot {
	// --------------------------------------------------
	// Options page
	// --------------------------------------------------
	static function init() {
		add_action( 'plugins_loaded' , array( __CLASS__, 'plugin_loaded' ) );
		
		if ( is_multisite() )
			$from_bot = get_site_option( 'from_the_bot' );
		else 
			$from_bot = get_option( 'from_the_bot' );

		if ( ! is_multisite() && is_admin() ) {
			add_option( 'from_the_bot' , '' , '' , 'yes' );
			add_action( 'admin_menu', array( __CLASS__ , 'create_menu' ));
		} else if ( is_multisite() && is_network_admin() ) {
			add_site_option( 'from_the_bot' , '' , '' , 'yes' );
			add_action( 'network_admin_menu', array( __CLASS__ , 'create_network_menu' ));
		}

		if ((boolean) $from_bot)
			add_filter('wp_mail_from',create_function('$from','return "'.$from_bot.'";')); // automat@podirate.org
		
		
		if ( defined( 'WPBOT_SMTP_HOST' ) )
			add_action( 'phpmailer_init' , array(__CLASS__,'force_smtp') );
		
	}
	
	static function force_smtp( &$phpmailer ) {
		$phpmailer->IsSMTP();
		
		$phpmailer->Host = WPBOT_SMTP_HOST; //'wp194.webpack.hosteurope.de';
		$phpmailer->Port = defined('WPBOT_SMTP_PORT') ? WPBOT_SMTP_PORT : 25 ;
		
		$phpmailer->SMTPAuth = defined('WPBOT_SMTP_AUTH'); 
		$phpmailer->AuthType = defined('WPBOT_SMTP_AUTH') ? WPBOT_SMTP_AUTH : ''; 
		if ( $phpmailer->SMTPAuth ) {
			$phpmailer->Username = WPBOT_SMTP_USER;
			$phpmailer->Password = WPBOT_SMTP_PASS;
		}
		// port: 25 / 587
	}
	
	// --------------------------------------------------
	// general actions
	// --------------------------------------------------
	static function plugin_loaded() {
		load_plugin_textdomain( 'fromthebot' , false, dirname( plugin_basename( __FILE__ )) . '/lang');
	}
	
	
	// --------------------------------------------------
	// Options page
	// --------------------------------------------------
	static function create_network_menu() {
		add_submenu_page( 'settings.php' , __('From the Bot','fromthebot'), __('From the Bot','fromthebot'), 'manage_options', 'from_the_bot', array(__CLASS__,'settings_page'));
		add_action( 'admin_init', array( __CLASS__ , 'register_settings' ) );
	}
	
	
	
	static function create_menu() { // @ admin_menu
		add_options_page(__('From the Bot','fromthebot'), __('From the Bot','fromthebot'), 'manage_options', 'from_the_bot', array(__CLASS__,'settings_page'));
		add_action( 'admin_init', array( __CLASS__ , 'register_settings' ) );
	}
	static function register_settings() { // @ admin_init
		register_setting( 'from_the_bot', 'from_the_bot' );
		add_settings_section('fromthebot_main', __('Main Settings','fromthebot'), '__return_false', 'from_the_bot');
		add_settings_field('plugin_text_string', __('Set WordPress’ email from','fromthebot'), array( __CLASS__ , 'setting_hide_undisclosed'), 'from_the_bot', 'fromthebot_main');
	}
	static function settings_page() {
		?>
		<div class="wrap">
			<h2><?php _e('Mail From the Bot','fromthebot') ?></h2>
			
			<form method="post">
				<?php 
					if ( isset( $_POST['from_the_bot'] )  && wp_verify_nonce($_POST['_wpnonce'],'from_the_bot-options') ) {
						if ( is_multisite() && is_network_admin() ) {
							update_site_option( 'from_the_bot', $_POST['from_the_bot'] );
						} else {
							update_option( 'from_the_bot', $_POST['from_the_bot'] );
						}
					}
					
					?><pre><?php
					settings_fields( 'from_the_bot' );
					?></pre><?php
				?>
				<?php do_settings_sections( 'from_the_bot' );  ?>
				
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				
			</form>
		</div>
		<?php
	}
	static function setting_hide_undisclosed() {
		if ( is_multisite() && is_network_admin() )
			$from_bot = get_site_option( 'from_the_bot' );
		else 
			$from_bot = get_option( 'from_the_bot' );
		?>
		<input type="text" name="from_the_bot" id="hide_undisclosed" value="<?php echo $from_bot ?>" />
		<?php
	}
}

FromTheBot::init();

?>