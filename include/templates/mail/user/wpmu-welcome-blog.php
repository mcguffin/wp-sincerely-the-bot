<?php
/**
 *	Sent when the user requests a password reset on the login page
 *
 *	Available vars:
 *	- $site_name
 *	- $site_url
 *	- $blog_name
 *	- $blog_url
 *	- $user_login
 *	- $user_email
 *	- $password
 *	- $confirmation_url
 */


?>
<h2><?php printf(__('Howdy %s','wp-the-bot'), $user_login ); ?></h2>
<p><?php _e( 'A brand new Site has just been created for you:', 'wp-the-bot'); ?></p>
<p>
	<?php printf( __( 'Site Name: %s','wp-the-bot'), sprintf( '<strong>%s</strong>', $blog_name ) ) ?><br />
	<?php printf( __( 'Site URL: %s','wp-the-bot'), sprintf( '<strong>%s</strong>', $blog_url ) ) ?><br />
	<?php printf( __( 'Your Username: %s','wp-the-bot'), sprintf( '<strong>%s</strong>', $user_login ) ) ?>
	<?php
	if ( ! empty( $password ) ) {
		?>
		<br />
		<?php
		printf( __( 'Password: %s','wp-the-bot'), sprintf( '<strong>%s</strong>', $password ) );
	}
	?>
</p>
<?php
if ( ! empty( $confirmation_url ) ) {
	?>
	<p>
		<?php _e( 'To set up your password, visit the following address:', 'wp-the-bot' ); ?><br />
		<?php printf('<a href="%1$s">%1$s</a>', $confirmation_url ); ?>
	</p>
	<?php
}

?>
