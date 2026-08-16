<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the destination for the general website contact form.
 */
function aa_forms_contact_recipient_email() {
	return apply_filters( 'aa_forms_contact_recipient_email', 'info@aacanberra.org' );
}

/**
 * Register the general contact form.
 */
function aa_forms_register_contact( $forms ) {
	$forms['contact'] = array(
		'title'           => 'Send a Message',
		'email_title'     => 'General Contact',
		'email_eyebrow'   => 'Website Contact',
		'template'        => 'contact',
		'email_template'  => 'contact',
		'success_message' => 'Your message has been sent to the Central Service Office.',
		'fields'          => array(
			array(
				'key'         => 'contact_name',
				'label'       => 'First Name + Initial',
				'email_label' => 'Name',
				'type'        => 'text',
				'required'    => true,
				'maxlength'   => 100,
			),
			array(
				'key'       => 'phone',
				'label'     => 'Contact Number',
				'type'      => 'text',
				'required'  => false,
				'maxlength' => 80,
			),
			array(
				'key'       => 'email',
				'label'     => 'Contact Email',
				'type'      => 'email',
				'required'  => true,
				'maxlength' => 254,
			),
			array(
				'key'       => 'subject',
				'label'     => 'Subject',
				'type'      => 'text',
				'required'  => true,
				'maxlength' => 150,
			),
			array(
				'key'         => 'message',
				'label'       => 'Your message',
				'email_label' => 'Message',
				'type'        => 'textarea',
				'required'    => true,
				'maxlength'   => 5000,
			),
		),
		'mail' => array(
			'to' => function( $form, $values ) {
				return aa_forms_contact_recipient_email();
			},
			'subject' => function( $form, $values ) {
				return sprintf( 'Website enquiry: %s', $values['subject'] ?? '' );
			},
			'reply_name' => function( $form, $values ) {
				return $values['contact_name'] ?? '';
			},
			'reply_email' => function( $form, $values ) {
				return $values['email'] ?? '';
			},
		),
		'receipt' => array(
			'enabled' => true,
			'to'      => function( $form, $values ) {
				return $values['email'] ?? '';
			},
			'name'    => function( $form, $values ) {
				return $values['contact_name'] ?? '';
			},
			'subject' => function( $form, $values ) {
				return sprintf( 'Submission Received: "%s"', $values['subject'] ?? $form['title'] );
			},
		),
	);

	return $forms;
}
add_filter( 'aa_forms_registry', 'aa_forms_register_contact' );