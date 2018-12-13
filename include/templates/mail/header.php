<?php
/**
 *	Global Email Header.
 *
 *	Available vars:
 *	- $site_name
 *	- $site_url
 */

?>
<html>
<body <?php thebot_style('doc'); ?>>
	<div <?php thebot_style('body') ?>>
		<div <?php thebot_style('section') ?>>
			<h1><?php printf( '<a href="%s">%s</a>', $site_url, $site_name ); ?></h1>
		</div>
		<div <?php thebot_style('section') ?>>
