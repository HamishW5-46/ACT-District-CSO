<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load individual form definitions.
 */
function aa_forms_load_definitions() {
	$definition_files = array(
		'/inc/forms/contact-directory.php',
		'/inc/forms/contact.php',
		'/inc/forms/pipa.php',
		'/inc/forms/beginners.php',
	);

	foreach ( $definition_files as $definition_file ) {
		require_once get_stylesheet_directory() . $definition_file;
	}
}
aa_forms_load_definitions();

/**
 * Return all registered AA Canberra forms.
 */
function aa_forms_get_registry() {
	$forms = apply_filters( 'aa_forms_registry', array() );

	return is_array( $forms ) ? $forms : array();
}

/**
 * Return a single registered form definition.
 */
function aa_forms_get_form( $form_id ) {
	$form_id = sanitize_key( $form_id );
	$forms   = aa_forms_get_registry();

	return isset( $forms[ $form_id ] ) && is_array( $forms[ $form_id ] )
		? $forms[ $form_id ]
		: null;
}

/**
 * Return the configured Cloudflare Turnstile site key.
 */
function aa_forms_turnstile_site_key() {
	$site_key = defined( 'CF_TURNSTILE_SITE_KEY' ) ? CF_TURNSTILE_SITE_KEY : '';

	return trim( (string) apply_filters( 'aa_forms_turnstile_site_key', $site_key ) );
}

/**
 * Return the configured Cloudflare Turnstile secret key.
 */
function aa_forms_turnstile_secret_key() {
	$secret_key = defined( 'CF_TURNSTILE_SECRET_KEY' ) ? CF_TURNSTILE_SECRET_KEY : '';

	return trim( (string) apply_filters( 'aa_forms_turnstile_secret_key', $secret_key ) );
}

/**
 * Return whether Turnstile has both required keys configured.
 */
function aa_forms_turnstile_enabled() {
	return aa_forms_turnstile_site_key() !== '' && aa_forms_turnstile_secret_key() !== '';
}

/**
 * Return the Turnstile action for a registered form.
 */
function aa_forms_turnstile_action( $form_id ) {
	return 'aa_form_' . sanitize_key( $form_id );
}

/**
 * Return the best available visitor IP address for anti-spam checks.
 */
function aa_forms_client_ip() {
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

	return 'unknown';
}

/**
 * Increment and check a transient-backed rate limit bucket.
 */
function aa_forms_check_rate_limit( $scope, $value, $limit, $window ) {
	$value = trim( (string) $value );

	if ( $value === '' ) {
		$value = 'unknown';
	}

	$key = sprintf(
		'aa_forms_rate_%s_%s',
		sanitize_key( $scope ),
		hash( 'sha256', strtolower( $value ) )
	);

	$now    = time();
	$bucket = get_transient( $key );

	if ( ! is_array( $bucket ) || empty( $bucket['expires'] ) || (int) $bucket['expires'] <= $now ) {
		$bucket = array(
			'count'   => 0,
			'expires' => $now + (int) $window,
		);
	}

	if ( (int) $bucket['count'] >= (int) $limit ) {
		return false;
	}

	$bucket['count'] = (int) $bucket['count'] + 1;

	set_transient( $key, $bucket, max( 1, (int) $bucket['expires'] - $now ) );

	return true;
}

/**
 * Check configured form rate limits.
 */
function aa_forms_rate_limit_allows( $form_id, $email ) {
	$limits = apply_filters(
		'aa_forms_rate_limits',
		array(
			'ip' => array(
				'value'  => aa_forms_client_ip(),
				'limit'  => 5,
				'window' => 15 * MINUTE_IN_SECONDS,
			),
			'email' => array(
				'value'  => $email,
				'limit'  => 3,
				'window' => HOUR_IN_SECONDS,
			),
		),
		$form_id,
		$email
	);

	foreach ( $limits as $scope => $limit ) {
		if ( empty( $limit['limit'] ) || empty( $limit['window'] ) ) {
			continue;
		}

		$bucket_scope = sanitize_key( $form_id . '_' . $scope );

		if ( ! aa_forms_check_rate_limit( $bucket_scope, $limit['value'] ?? '', (int) $limit['limit'], (int) $limit['window'] ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Verify a Cloudflare Turnstile token with the Siteverify API.
 */
function aa_forms_verify_turnstile_token( $form_id, $token ) {
	if ( ! aa_forms_turnstile_enabled() ) {
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
				'secret'          => aa_forms_turnstile_secret_key(),
				'response'        => $token,
				'remoteip'        => aa_forms_client_ip(),
				'idempotency_key' => wp_generate_uuid4(),
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		error_log( 'AA form Turnstile validation request failed: ' . $response->get_error_message() );

		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( ! is_array( $body ) || empty( $body['success'] ) ) {
		$codes = isset( $body['error-codes'] ) && is_array( $body['error-codes'] )
			? implode( ', ', array_map( 'sanitize_text_field', $body['error-codes'] ) )
			: 'unknown-error';

		error_log( 'AA form Turnstile validation failed: ' . $codes );

		return false;
	}

	return isset( $body['action'] ) && $body['action'] === aa_forms_turnstile_action( $form_id );
}

/**
 * Register shared form frontend assets.
 */
function aa_forms_register_assets() {
	wp_register_style(
		'aa-forms',
		get_stylesheet_directory_uri() . '/assets/css/forms.css',
		array(),
		act_district_cso_child_asset_version( '/assets/css/forms.css' )
	);

	wp_register_script(
		'aa-forms-turnstile',
		'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit',
		array(),
		null,
		true
	);

	wp_register_script(
		'aa-forms',
		get_stylesheet_directory_uri() . '/assets/js/forms.js',
		array(),
		act_district_cso_child_asset_version( '/assets/js/forms.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'aa_forms_register_assets' );

/**
 * Enqueue the shared frontend and expose form configuration to JavaScript.
 */
function aa_forms_enqueue_assets() {
	wp_enqueue_style( 'aa-forms' );

	if ( aa_forms_turnstile_enabled() ) {
		wp_enqueue_script( 'aa-forms-turnstile' );
	}

	wp_enqueue_script( 'aa-forms' );

	$actions = array();

	foreach ( array_keys( aa_forms_get_registry() ) as $registered_form_id ) {
		$actions[ $registered_form_id ] = aa_forms_turnstile_action( $registered_form_id );
	}

	wp_localize_script(
		'aa-forms',
		'aaForms',
		array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'aa_forms_submit' ),
			'turnstile' => array(
				'enabled' => aa_forms_turnstile_enabled(),
				'siteKey' => aa_forms_turnstile_enabled() ? aa_forms_turnstile_site_key() : '',
				'actions' => $actions,
			),
		)
	);
}

/**
 * Render a template file with scoped variables.
 */
function aa_forms_render_template( $template_path, array $vars = array() ) {
	if ( ! file_exists( $template_path ) ) {
		return '';
	}

	extract( $vars, EXTR_SKIP );

	ob_start();
	include $template_path;

	return ob_get_clean();
}

/**
 * Render a registered form.
 */
function aa_forms_render( $form_id, array $args = array() ) {
	$form_id = sanitize_key( $form_id );
	$form    = aa_forms_get_form( $form_id );

	if ( ! $form ) {
		return '';
	}

	aa_forms_enqueue_assets();

	$template = get_stylesheet_directory() . '/templates/forms/' . $form['template'] . '.php';

	return aa_forms_render_template(
		$template,
		array(
			'form_id' => $form_id,
			'form'    => $form,
			'args'    => $args,
		)
	);
}

/**
 * Generic form shortcode.
 *
 * Usage: [aa_form id="contact"]
 */
function aa_forms_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'id' => '',
		),
		$atts,
		'aa_form'
	);

	return aa_forms_render( sanitize_key( $atts['id'] ) );
}
add_shortcode( 'aa_form', 'aa_forms_shortcode' );

/**
 * Back-compat shortcode for the public contact directory.
 */
function aa_contact_directory_shortcode() {
	return aa_forms_render( 'contact_directory' );
}
add_shortcode( 'aa_contact_directory', 'aa_contact_directory_shortcode' );

/**
 * Read a submitted scalar value.
 */
function aa_forms_post_value( $key ) {
	if ( ! isset( $_POST[ $key ] ) ) {
		return '';
	}

	$value = wp_unslash( $_POST[ $key ] );

	return is_scalar( $value ) ? (string) $value : '';
}

/**
 * Sanitise a submitted field according to its definition.
 */
function aa_forms_sanitize_field_value( array $field, $raw_value ) {
	$type = $field['type'] ?? 'text';

	if ( $type === 'textarea' ) {
		return sanitize_textarea_field( $raw_value );
	}

	if ( $type === 'email' ) {
		return sanitize_email( $raw_value );
	}

	return sanitize_text_field( $raw_value );
}

/**
 * Validate and collect submitted values for a form.
 */
function aa_forms_collect_values( array $form ) {
	$values = array();
	$errors = array();

	foreach ( $form['fields'] ?? array() as $field ) {
		$key   = sanitize_key( $field['key'] ?? '' );
		$label = $field['label'] ?? $key;

		if ( ! $key ) {
			continue;
		}

		$raw_value = aa_forms_post_value( $key );
		$value     = aa_forms_sanitize_field_value( $field, $raw_value );

		if ( ! empty( $field['options'] ) && is_array( $field['options'] ) ) {
			$allowed = array_keys( $field['options'] );

			if ( $value !== '' && ! in_array( $value, $allowed, true ) ) {
				$errors[] = sprintf( 'Please choose a valid option for %s.', $label );
			}
		}

		if ( ! empty( $field['required'] ) && $value === '' ) {
			$errors[] = sprintf( 'Please complete %s.', $label );
		}

		if ( ( $field['type'] ?? '' ) === 'email' && $value !== '' && ! is_email( $value ) ) {
			$errors[] = 'Please enter a valid email address.';
		}

		if ( ! empty( $field['maxlength'] ) && strlen( $value ) > (int) $field['maxlength'] ) {
			$errors[] = sprintf( '%s is too long.', $label );
		}

		$values[ $key ] = $value;
	}

	return array( $values, $errors );
}

/**
 * Return the first submitted email value for rate limiting and Reply-To.
 */
function aa_forms_primary_email( array $form, array $values ) {
	foreach ( $form['fields'] ?? array() as $field ) {
		$key = sanitize_key( $field['key'] ?? '' );

		if ( ( $field['type'] ?? '' ) === 'email' && isset( $values[ $key ] ) ) {
			return $values[ $key ];
		}
	}

	return '';
}

/**
 * Build default display rows for an email template.
 */
function aa_forms_email_rows( array $form, array $values ) {
	$rows = array();

	foreach ( $form['fields'] ?? array() as $field ) {
		if ( ! empty( $field['hide_in_email'] ) ) {
			continue;
		}

		$key   = sanitize_key( $field['key'] ?? '' );
		$value = $values[ $key ] ?? '';

		if ( $value === '' ) {
			continue;
		}

		if ( ! empty( $field['options'] ) && is_array( $field['options'] ) && isset( $field['options'][ $value ] ) ) {
			$value = $field['options'][ $value ];
		}

		$rows[] = array(
			'label' => $field['email_label'] ?? $field['label'] ?? $key,
			'value' => $value,
			'type'  => $field['type'] ?? 'text',
		);
	}

	return $rows;
}

/**
 * Resolve a form mail option value.
 */
function aa_forms_resolve_mail_value( $value, array $form, array $values ) {
	if ( is_callable( $value ) ) {
		return (string) call_user_func( $value, $form, $values );
	}

	return (string) $value;
}

/**
 * Handle public form submissions.
 */
function aa_forms_submit() {
	check_ajax_referer( 'aa_forms_submit', 'nonce' );

	$form_id = sanitize_key( aa_forms_post_value( 'form_id' ) );
	$form    = aa_forms_get_form( $form_id );

	if ( ! $form ) {
		wp_send_json_error( array( 'message' => 'Please select a valid form.' ), 400 );
	}

	$honeypot = sanitize_text_field( aa_forms_post_value( 'website' ) );

	if ( $honeypot !== '' ) {
		wp_send_json_success( array( 'message' => $form['success_message'] ?? 'Your message has been sent.' ) );
	}

	$opened_at = absint( aa_forms_post_value( 'opened_at' ) );

	if ( ! $opened_at || ( time() - $opened_at ) < 3 ) {
		wp_send_json_error( array( 'message' => 'Please wait a moment and try again.' ), 400 );
	}

	list( $values, $errors ) = aa_forms_collect_values( $form );

	if ( $errors ) {
		wp_send_json_error( array( 'message' => $errors[0] ), 400 );
	}

	$email = aa_forms_primary_email( $form, $values );

	if ( ! aa_forms_rate_limit_allows( $form_id, $email ) ) {
		wp_send_json_error( array( 'message' => 'Too many messages have been sent recently. Please try again later.' ), 429 );
	}

	$turnstile_token = sanitize_text_field( aa_forms_post_value( 'cf-turnstile-response' ) );

	if ( ! aa_forms_verify_turnstile_token( $form_id, $turnstile_token ) ) {
		wp_send_json_error( array( 'message' => 'Please complete the verification and try again.' ), 400 );
	}

	if ( ! empty( $form['validate'] ) && is_callable( $form['validate'] ) ) {
		$custom_error = call_user_func( $form['validate'], $values, $form );

		if ( is_string( $custom_error ) && $custom_error !== '' ) {
			wp_send_json_error( array( 'message' => $custom_error ), 400 );
		}
	}

	$mail = $form['mail'] ?? array();
	$to   = sanitize_email( aa_forms_resolve_mail_value( $mail['to'] ?? '', $form, $values ) );

	if ( ! $to ) {
		wp_send_json_error( array( 'message' => 'This form is not configured for delivery.' ), 500 );
	}

	$mail_subject = aa_forms_resolve_mail_value( $mail['subject'] ?? $form['title'], $form, $values );
	$reply_name   = aa_forms_resolve_mail_value( $mail['reply_name'] ?? '', $form, $values );
	$reply_email  = sanitize_email( aa_forms_resolve_mail_value( $mail['reply_email'] ?? $email, $form, $values ) );

	$email_data = array(
		'form_id'  => $form_id,
		'form'     => $form,
		'values'   => $values,
		'rows'     => aa_forms_email_rows( $form, $values ),
		'title'    => $form['email_title'] ?? $form['title'],
		'eyebrow'  => $form['email_eyebrow'] ?? 'Website Contact',
		'subject'  => $mail_subject,
		'reply_to' => array(
			'name'  => $reply_name,
			'email' => $reply_email,
		),
	);

	if ( ! empty( $form['email_data'] ) && is_callable( $form['email_data'] ) ) {
		$email_data = array_merge( $email_data, (array) call_user_func( $form['email_data'], $values, $form ) );
	}

	$email_template = get_stylesheet_directory() . '/templates/emails/' . ( $form['email_template'] ?? 'form' ) . '.php';
	$body           = trim( aa_forms_render_template( $email_template, array( 'email_data' => $email_data ) ) );

	$headers = array( 'Content-Type: text/html; charset=UTF-8' );

	if ( $reply_email ) {
		$headers[] = sprintf( 'Reply-To: %s <%s>', $reply_name ? $reply_name : $reply_email, $reply_email );
	}

	$sent = wp_mail( $to, $mail_subject, $body, $headers );

	if ( ! $sent ) {
		wp_send_json_error( array( 'message' => 'Your message could not be sent. Please try again.' ), 500 );
	}

	wp_send_json_success( array( 'message' => $form['success_message'] ?? 'Your message has been sent.' ) );
}
add_action( 'wp_ajax_aa_forms_submit', 'aa_forms_submit' );
add_action( 'wp_ajax_nopriv_aa_forms_submit', 'aa_forms_submit' );