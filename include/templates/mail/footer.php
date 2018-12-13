<?php
/**
 *	Global Email Footer
 *
 *	Available vars:
 *	- $site_name
 *	- $site_url
 */


?>
		</div>
		<div <?php thebot_style('last-section', 'font-size-small' ) ?>>
			<p><?php
				printf(
					__( 'This email was generated automtically by %s.', 'wp-the-bot' ),
					sprintf( '<a href="%s">%s</a>', $site_url, $site_name )
				);
			?><br />
			<p>
				<?php _e('Please do not reply to this message.', 'wp-the-bot' ); ?>
			</p>
		</div>
	</div>
</body>
</html>
