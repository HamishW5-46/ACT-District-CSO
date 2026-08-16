<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the destination for PIPA enquiries.
 */
function aa_forms_pipa_recipient_email() {
	return apply_filters( 'aa_forms_pipa_recipient_email', 'pipa@aaareaber.org.au' );
}

/**
 * Register the Public Information and Professional Awareness form.
 */
function aa_forms_register_pipa( $forms ) {
	$forms['pipa'] = array(
		'title'           => 'Public Information & Professional Awareness Committee Contact Form',
		'email_title'     => 'PIPA Contact',
		'email_eyebrow'   => 'Public Information & Professional Awareness',
		'template'        => 'pipa',
		'email_template'  => 'pipa',
		'success_message' => 'Your message has been sent to the Public Information & Professional Awareness committee.',
		'fields'          => array(
			array(
				'key'       => 'pipa_requester_name',
				'label'     => 'Name',
				'type'      => 'text',
				'required'  => true,
				'maxlength' => 100,
			),
			array(
				'key'       => 'pipa_requester_tel',
				'label'     => 'Phone Number',
				'type'      => 'text',
				'required'  => false,
				'maxlength' => 80,
			),
			array(
				'key'       => 'pipa_requester_email',
				'label'     => 'Email',
				'type'      => 'email',
				'required'  => true,
				'maxlength' => 254,
			),
			array(
				'key'       => 'pipa_subject',
				'label'     => 'Subject',
				'type'      => 'text',
				'required'  => true,
				'maxlength' => 150,
			),
			array(
				'key'       => 'pipa_message',
				'label'     => 'Message',
				'type'      => 'textarea',
				'required'  => true,
				'maxlength' => 5000,
			),
		),
		'mail' => array(
			'to' => function( $form, $values ) {
				return aa_forms_pipa_recipient_email();
			},
			'subject' => function( $form, $values ) {
				return sprintf( 'PIPA enquiry: %s', $values['pipa_subject'] ?? '' );
			},
			'reply_name' => function( $form, $values ) {
				return $values['pipa_requester_name'] ?? '';
			},
			'reply_email' => function( $form, $values ) {
				return $values['pipa_requester_email'] ?? '';
			},
		),
	);

	return $forms;
}
add_filter( 'aa_forms_registry', 'aa_forms_register_pipa' );