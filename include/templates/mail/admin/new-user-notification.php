<?php
/**
 *	Sent when the user requests a password reset on the login page
 *
 *	Available vars:
 *	- $site_name
 *	- $site_url
 *	- $user_login
 *	- $user_email
 */
?>
<h2><?php printf( __('Howdy,','wp-the-bot'), $user_login ); ?></h2>
<p><?php _e( 'a new Account on your site has just been created:', 'wp-the-bot'); ?></p>
<p>
	<?php printf( __( 'Site Name: %s','wp-the-bot'), sprintf( '<strong>%s</strong>', $site_name ) ) ?><br />
	<?php printf( __( 'Username: %s','wp-the-bot'), sprintf( '<strong>%s</strong>', $user_login ) ) ?>
</p>
