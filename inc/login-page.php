<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme-owned customisations for wp-login.php.
 */
function act_district_cso_child_login_enqueue_assets() {
	wp_enqueue_style(
		'act-district-cso-child-login',
		get_stylesheet_directory_uri() . '/assets/css/login.css',
		array(),
		act_district_cso_child_asset_version( '/assets/css/login.css' )
	);
}
add_action( 'login_enqueue_scripts', 'act_district_cso_child_login_enqueue_assets' );

/**
 * Point the login logo back to the public website.
 */
function act_district_cso_child_login_header_url() {
	return home_url( '/' );
}
add_filter( 'login_headerurl', 'act_district_cso_child_login_header_url' );

/**
 * Use site-specific accessible text for the login logo.
 */
function act_district_cso_child_login_header_text() {
	return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'act_district_cso_child_login_header_text' );

/**
 * Add a stable class for scoped theme styling.
 */
function act_district_cso_child_login_body_class( $classes ) {
	$classes[] = 'aa-login-page';

	return $classes;
}
add_filter( 'login_body_class', 'act_district_cso_child_login_body_class' );

/**
 * Add a restrained site identity note below the login controls.
 */
function act_district_cso_child_login_footer_note() {
	?>
	<p class="aa-login-footer-note">
		<?php esc_html_e( 'ACT & District Central Service Office', 'ACT_District_CSO_Child' ); ?>
	</p>
	<?php
}
add_action( 'login_footer', 'act_district_cso_child_login_footer_note' );
