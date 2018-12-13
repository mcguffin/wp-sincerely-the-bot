<?php
/**
 *	Sent when an admin changed a users password.
 *
 *	Available vars:
 *	- $site_name
 *	- $site_url
 *	- $user_login
 *	- $user_email
 *	- $admin_email
 */

?>
<h2><?php printf(__('Howdy %s','wp-the-bot'), $user_login ); ?></h2>
<p><?php printf(__( 'This notice confirms that your password was changed on %s', 'wp-the-bot'), $site_name ); ?></p>
<p>
	<?php printf( __( 'If you did not change your password, please contact the Site Administrator at: %s','wp-the-bot'), $admin_email ) ?>
</p>
<p>
	<?php printf( __( 'This email has been sent to: %s','wp-the-bot'), $user_email ) ?>
</p>
