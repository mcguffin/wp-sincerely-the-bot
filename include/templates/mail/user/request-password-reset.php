<?php
/**
 *	Sent when the user requests a password reset on the login page
 *
 *	Available vars:
 *	- $site_name
 *	- $site_url
 *	- $user_login
 *	- $user_email
 *	- $confirmation_url
 */


?>
<h2><?php printf(__('Howdy %s','wp-the-bot'), $user_login ); ?></h2>
<p><?php _e( 'Someone or something has requested a password reset for the following account:', 'wp-the-bot'); ?></p>
<p>
	<?php printf( __( 'Site Name: %s','wp-the-bot'), sprintf( '<strong>%s</strong>', $site_name ) ) ?><br />
	<?php printf( __( 'Username: %s','wp-the-bot'), sprintf( '<strong>%s</strong>', $user_login ) ) ?>
</p>
<p><?php _e( 'If this was a mistake, just ignore this email and nothing will happen.', 'wp-the-bot' ); ?></p>
<p>
	<?php _e( 'To reset your password, visit the following address:', 'wp-the-bot' ); ?><br />
	<?php printf('<a href="%1$s">%1$s</a>', $confirmation_url ); ?>
</p>
