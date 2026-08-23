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

	if ( act_district_cso_child_login_turnstile_enabled() && act_district_cso_child_login_is_login_action() ) {
		wp_enqueue_script(
			'act-district-cso-child-login-turnstile',
			'https://challenges.cloudflare.com/turnstile/v0/api.js',
			array(),
			null,
			true
		);
	}
}
add_action( 'login_enqueue_scripts', 'act_district_cso_child_login_enqueue_assets' );

/**
 * Return the configured Cloudflare Turnstile site key.
 */
function act_district_cso_child_login_turnstile_site_key() {
	$site_key = defined( 'CF_TURNSTILE_SITE_KEY' ) ? CF_TURNSTILE_SITE_KEY : '';

	return trim( (string) apply_filters( 'act_district_cso_child_login_turnstile_site_key', $site_key ) );
}

/**
 * Return the configured Cloudflare Turnstile secret key.
 */
function act_district_cso_child_login_turnstile_secret_key() {
	$secret_key = defined( 'CF_TURNSTILE_SECRET_KEY' ) ? CF_TURNSTILE_SECRET_KEY : '';

	return trim( (string) apply_filters( 'act_district_cso_child_login_turnstile_secret_key', $secret_key ) );
}

/**
 * Return whether Turnstile has both required keys configured.
 */
function act_district_cso_child_login_turnstile_enabled() {
	return act_district_cso_child_login_turnstile_site_key() !== '' && act_district_cso_child_login_turnstile_secret_key() !== '';
}

/**
 * Return the Turnstile action used for wp-login.php.
 */
function act_district_cso_child_login_turnstile_action() {
	return 'aa_login';
}

/**
 * Return whether the current wp-login.php screen is the credential form.
 */
function act_district_cso_child_login_is_login_action() {
	$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : 'login';

	return $action === 'login';
}

/**
 * Return the best available visitor IP address for Turnstile verification.
 */
function act_district_cso_child_login_client_ip() {
	$headers = array(
		'HTTP_CF_CONNECTING_IP',
		'REMOTE_ADDR',
	);

	foreach ( $headers as $header ) {
		if ( empty( $_SERVER[ $header ] ) ) {
			continue;
		}

		$ip = sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) );

		if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			return $ip;
		}
	}

	return '';
}

/**
 * Render the Turnstile widget inside the login form.
 */
function act_district_cso_child_login_turnstile_field() {
	if ( ! act_district_cso_child_login_turnstile_enabled() ) {
		return;
	}
	?>
	<div class="aa-login-turnstile">
		<div
			class="cf-turnstile"
			data-sitekey="<?php echo esc_attr( act_district_cso_child_login_turnstile_site_key() ); ?>"
			data-action="<?php echo esc_attr( act_district_cso_child_login_turnstile_action() ); ?>"
			data-theme="auto"
		></div>
	</div>
	<?php
}
add_action( 'login_form', 'act_district_cso_child_login_turnstile_field' );

/**
 * Verify a Cloudflare Turnstile token with the Siteverify API.
 */
function act_district_cso_child_login_verify_turnstile_token( $token ) {
	if ( ! act_district_cso_child_login_turnstile_enabled() ) {
		return true;
	}

	$token = trim( (string) $token );

	if ( $token === '' || strlen( $token ) > 2048 ) {
		return false;
	}

	$response = wp_remote_post(
		'https://challenges.cloudflare.com/turnstile/v0/siteverify',
		array(
			'timeout' => 5,
			'body'    => array(
				'secret'          => act_district_cso_child_login_turnstile_secret_key(),
				'response'        => $token,
				'remoteip'        => act_district_cso_child_login_client_ip(),
				'idempotency_key' => wp_generate_uuid4(),
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		error_log( 'AA login Turnstile validation request failed: ' . $response->get_error_message() );

		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! is_array( $body ) || empty( $body['success'] ) ) {
		$codes = isset( $body['error-codes'] ) && is_array( $body['error-codes'] )
			? implode( ', ', array_map( 'sanitize_text_field', $body['error-codes'] ) )
			: 'unknown-error';

		error_log( 'AA login Turnstile validation failed: ' . $codes );

		return false;
	}

	return isset( $body['action'] ) && $body['action'] === act_district_cso_child_login_turnstile_action();
}

/**
 * Require Turnstile verification before processing login credentials.
 */
function act_district_cso_child_login_authenticate_turnstile( $user, $username, $password ) {
	if ( ! act_district_cso_child_login_turnstile_enabled() || ! act_district_cso_child_login_is_login_action() ) {
		return $user;
	}

	if ( $username === '' && $password === '' ) {
		return $user;
	}

	$token = isset( $_POST['cf-turnstile-response'] )
		? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) )
		: '';

	if ( act_district_cso_child_login_verify_turnstile_token( $token ) ) {
		return $user;
	}

	return new WP_Error(
		'aa_login_turnstile_failed',
		__( '<strong>Error:</strong> Please complete the verification and try again.', 'ACT_District_CSO_Child' )
	);
}
add_filter( 'authenticate', 'act_district_cso_child_login_authenticate_turnstile', 5, 3 );

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
