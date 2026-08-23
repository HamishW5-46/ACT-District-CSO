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

	$relative_path = '/assets/css/account-auth-page.css';
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

	if ( aac_account_auth_turnstile_enabled() ) {
		wp_enqueue_script(
			'aac-account-auth-turnstile',
			'https://challenges.cloudflare.com/turnstile/v0/api.js',
			array(),
			null,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'aac_enqueue_account_auth_assets', 20 );

/**
 * Return the configured Cloudflare Turnstile site key.
 */
function aac_account_auth_turnstile_site_key() {
	$site_key = defined( 'CF_TURNSTILE_SITE_KEY' ) ? CF_TURNSTILE_SITE_KEY : '';

	return trim( (string) apply_filters( 'aac_account_auth_turnstile_site_key', $site_key ) );
}

/**
 * Return the configured Cloudflare Turnstile secret key.
 */
function aac_account_auth_turnstile_secret_key() {
	$secret_key = defined( 'CF_TURNSTILE_SECRET_KEY' ) ? CF_TURNSTILE_SECRET_KEY : '';

	return trim( (string) apply_filters( 'aac_account_auth_turnstile_secret_key', $secret_key ) );
}

/**
 * Check whether Turnstile has both required keys configured.
 */
function aac_account_auth_turnstile_enabled() {
	return aac_account_auth_turnstile_site_key() !== '' && aac_account_auth_turnstile_secret_key() !== '';
}

/**
 * Return the Turnstile action used for a WooCommerce account form.
 *
 * @param string $form Account form slug.
 */
function aac_account_auth_turnstile_action( $form ) {
	$form = sanitize_key( $form );

	return 'aa_wc_' . ( $form === 'register' ? 'register' : 'login' );
}

/**
 * Return the best available visitor IP address for Turnstile verification.
 */
function aac_account_auth_client_ip() {
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
 * Render a Turnstile widget in a WooCommerce account form.
 *
 * @param string $form Account form slug.
 */
function aac_account_auth_render_turnstile_field( $form ) {
	if ( ! aac_account_auth_turnstile_enabled() ) {
		return;
	}
	?>
	<div class="aa-account-auth__turnstile">
		<div
			class="cf-turnstile"
			data-sitekey="<?php echo esc_attr( aac_account_auth_turnstile_site_key() ); ?>"
			data-action="<?php echo esc_attr( aac_account_auth_turnstile_action( $form ) ); ?>"
			data-theme="auto"
		></div>
	</div>
	<?php
}

/**
 * Render the login Turnstile widget.
 */
function aac_account_auth_render_login_turnstile() {
	aac_account_auth_render_turnstile_field( 'login' );
}
add_action( 'woocommerce_login_form', 'aac_account_auth_render_login_turnstile' );

/**
 * Render the registration Turnstile widget.
 */
function aac_account_auth_render_register_turnstile() {
	aac_account_auth_render_turnstile_field( 'register' );
}
add_action( 'woocommerce_register_form', 'aac_account_auth_render_register_turnstile' );

/**
 * Verify a Cloudflare Turnstile token with the Siteverify API.
 *
 * @param string $form  Account form slug.
 * @param string $token Turnstile response token.
 */
function aac_account_auth_verify_turnstile_token( $form, $token ) {
	if ( ! aac_account_auth_turnstile_enabled() ) {
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
				'secret'          => aac_account_auth_turnstile_secret_key(),
				'response'        => $token,
				'remoteip'        => aac_account_auth_client_ip(),
				'idempotency_key' => wp_generate_uuid4(),
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		error_log( 'AA WooCommerce account Turnstile validation request failed: ' . $response->get_error_message() );

		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! is_array( $body ) || empty( $body['success'] ) ) {
		$codes = isset( $body['error-codes'] ) && is_array( $body['error-codes'] )
			? implode( ', ', array_map( 'sanitize_text_field', $body['error-codes'] ) )
			: 'unknown-error';

		error_log( 'AA WooCommerce account Turnstile validation failed: ' . $codes );

		return false;
	}

	return isset( $body['action'] ) && $body['action'] === aac_account_auth_turnstile_action( $form );
}

/**
 * Return the submitted Turnstile response token.
 */
function aac_account_auth_turnstile_post_token() {
	return isset( $_POST['cf-turnstile-response'] )
		? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) )
		: '';
}

/**
 * Require Turnstile verification before WooCommerce processes account login.
 *
 * @param WP_Error $validation_error Existing validation errors.
 */
function aac_account_auth_validate_login_turnstile( $validation_error ) {
	if ( ! aac_account_auth_verify_turnstile_token( 'login', aac_account_auth_turnstile_post_token() ) ) {
		$validation_error->add(
			'aa_wc_login_turnstile_failed',
			__( 'Please complete the verification and try again.', 'ACT_District_CSO_Child' )
		);
	}

	return $validation_error;
}
add_filter( 'woocommerce_process_login_errors', 'aac_account_auth_validate_login_turnstile', 10, 1 );

/**
 * Require Turnstile verification before WooCommerce creates account registrations.
 *
 * @param WP_Error $errors Existing validation errors.
 */
function aac_account_auth_validate_register_turnstile( $errors ) {
	if ( ! aac_account_auth_verify_turnstile_token( 'register', aac_account_auth_turnstile_post_token() ) ) {
		$errors->add(
			'aa_wc_register_turnstile_failed',
			__( 'Please complete the verification and try again.', 'ACT_District_CSO_Child' )
		);
	}

	return $errors;
}
add_filter( 'woocommerce_registration_errors', 'aac_account_auth_validate_register_turnstile', 10, 1 );

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
