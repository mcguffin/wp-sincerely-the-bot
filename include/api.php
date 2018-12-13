<?php

function thebot_style( ) {
	$args = func_get_args();
	if ( $css = get_thebot_style( ...$args ) ) {
		printf( ' style="%s"', esc_attr( $css ) );
	}
}

function get_thebot_style( ) {
	global $thebot_styles;
	$css = '';
	foreach ( func_get_args() as $arg ) {
		if ( isset( $thebot_styles[$arg] ) ) {
			if ( is_array( $thebot_styles[$arg] ) ) {
				$css .= thebot_style( ...$thebot_styles[$arg] );
			} else {
				$css .= $thebot_styles[$arg].';';
			}
		} else if ( preg_match('/^([a-z0-9\-]+):/', $arg ) ) {
			// plain css
			$css .= $arg.';';
		}
	}
	return preg_replace('/(;+)/', ';', $css);
}
