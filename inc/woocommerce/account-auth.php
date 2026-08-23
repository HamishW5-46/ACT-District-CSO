<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check whether the current request is the guest WooCommerce account screen.
 */
function aac_is_account_auth_screen() {
	return function_exists( 'is_account_page' )
		&& is_account_page()
		&& ! is_user_logged_in();
}

/**
 * Enqueue the custom login and registration page styles.
 */
function aac_enqueue_account_auth_assets() {
	if ( ! aac_is_account_auth_screen() ) {
		return;
	}

	$relative_path = '/assets/css/account-auth.css';
	$version       = act_district_cso_child_asset_version( $relative_path );
	$style_uri     = add_query_arg(
		'aacv',
		rawurlencode( (string) $version ),
		get_stylesheet_directory_uri() . $relative_path
	);

	wp_enqueue_style(
		'aac-account-auth',
		$style_uri,
		array( 'ACT-District-CSO-Child-components' ),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'aac_enqueue_account_auth_assets', 20 );

/**
 * Add a body class for scoped account-auth styling.
 *
 * @param string[] $classes Existing body classes.
 *
 * @return string[]
 */
function aac_account_auth_body_classes( $classes ) {
	if ( aac_is_account_auth_screen() ) {
		$classes[] = 'aac-account-auth-page';
	}

	return $classes;
}
add_filter( 'body_class', 'aac_account_auth_body_classes' );
