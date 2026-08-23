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

	wp_enqueue_style(
		'aac-account-auth',
		get_stylesheet_directory_uri() . '/assets/css/account-auth.css',
		array( 'ACT-District-CSO-Child-components' ),
		act_district_cso_child_asset_version( '/assets/css/account-auth.css' )
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
