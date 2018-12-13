<?php
/**
 *	Sent to admin when a user changed his/her password
 *
 *	Available vars:
 *	- $site_name
 *	- $site_url
 *	- $user_login
 *	- $user_email
 */


?>
<h2><?php _x('Howdy,','wp-the-bot'); ?></h2>
<p>
	<?php printf( __( 'The Password of User %s was changed.','wp-the-bot'), sprintf( '<strong>%s</strong>', $user_login ) ) ?>
</p>
