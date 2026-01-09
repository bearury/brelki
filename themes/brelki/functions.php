<?php

function brelki_styles() {
	wp_enqueue_style(
		'brelki-general',
		get_template_directory_uri() . '/assets/css/brelki.css',
		[],
		wp_get_theme()->get( 'Version' )
	);
	wp_enqueue_script(
		'brelki-theme-related',
		get_template_directory_uri() . '/assets/js/brelki-theme-related.js',
		[],
		wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'brelki_styles' );

function brelki_google_fonts() {
	$font_url = ''; // https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap
	$font = 'Roboto';
	$font_extra = "wght@400;700";


	if ('off' !== _x( 'on', 'Google font: on or off', 'brelki' ) ) {
		$query_args = [
			'family' => urldecode( $font . ':' . $font_extra ),
			'display' => urldecode('swap'),
		];

		$font_url = add_query_arg( $query_args, '//fonts.googleapis.com/css2' );
	}

	return $font_url;
}


function brelki_google_font_script() {
	wp_enqueue_style( 'brelki-google-fonts', brelki_google_fonts(), [], null );
}

add_action( 'wp_enqueue_scripts', 'brelki_google_font_script' );