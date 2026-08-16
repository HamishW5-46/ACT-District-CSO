<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return available contact recipients.
 *
 * Email addresses remain server-side and are never rendered into the page.
 */
function aa_get_contact_directory() {
	return apply_filters(
		'aa_contact_directory_recipients',
		array(
			'chair' => array(
				'name'        => 'Chair',
				'email'       => 'chair@aacanberra.org',
				'description' => 'General Central Service Office and committee matters.',
			),
			'secretary' => array(
				'name'        => 'Secretary',
				'email'       => 'secretary@aacanberra.org',
				'description' => 'Committee correspondence, records and administrative matters.',
			),
			'treasurer' => array(
				'name'        => 'Treasurer',
				'email'       => 'treasurer@aacanberra.org',
				'description' => 'Contributions, payments, accounts and financial enquiries.',
			),
			'information' => array(
				'name'        => 'Information Coordinator',
				'email'       => 'info@aacanberra.org',
				'description' => 'Website, meeting information and general information enquiries.',
			),
			'literature' => array(
				'name'        => 'Literature Coordinator',
				'email'       => 'literature@aacanberra.org',
				'description' => 'Literature and related enquiries.',
			),
			'detox' => array(
				'name'        => 'Detox Coordinator',
				'email'       => 'detoxmeetings@aacanberra.org',
				'description' => 'Detox service enquiries.',
			),
			'beginners' => array(
				'name'        => 'Beginners Coordinator',
				'email'       => 'beginners@aacanberra.org',
				'description' => 'Beginners meeting and related enquiries.',
			),
		)
	);
}

/**
 * Register the public contact directory form.
 */
function aa_forms_register_contact_directory( $forms ) {
	$forms['contact_directory'] = array(
		'title'           => 'Contact Directory',
		'email_title'     => 'Website Contact',
		'email_eyebrow'   => 'Contact Directory',
		'template'        => 'contact-directory',
		'email_template'  => 'contact-directory',
		'success_message' => 'Your message has been sent.',
		'fields'          => array(
			array(
				'key'           => 'recipient',
				'label'         => 'Recipient',
				'type'          => 'text',
				'required'      => true,
				'hide_in_email' => true,
			),
			array(
				'key'       => 'name',
				'label'     => 'Your name',
				'type'      => 'text',
				'required'  => true,
				'maxlength' => 100,
			),
			array(
				'key'       => 'email',
				'label'     => 'Your email',
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
				'key'       => 'message',
				'label'     => 'Message',
				'type'      => 'textarea',
				'required'  => true,
				'maxlength' => 5000,
			),
		),
		'validate' => function( $values ) {
			$contacts  = aa_get_contact_directory();
			$recipient = sanitize_key( $values['recipient'] ?? '' );

			if ( ! $recipient || ! isset( $contacts[ $recipient ] ) ) {
				return 'Please select a valid recipient.';
			}

			return '';
		},
		'mail' => array(
			'to' => function( $form, $values ) {
				$contacts  = aa_get_contact_directory();
				$recipient = sanitize_key( $values['recipient'] ?? '' );

				return $contacts[ $recipient ]['email'] ?? '';
			},
			'subject' => function( $form, $values ) {
				return sprintf( 'Website enquiry: %s', $values['subject'] ?? '' );
			},
			'reply_name' => function( $form, $values ) {
				return $values['name'] ?? '';
			},
			'reply_email' => function( $form, $values ) {
				return $values['email'] ?? '';
			},
		),
		'email_data' => function( $values ) {
			$contacts  = aa_get_contact_directory();
			$recipient = sanitize_key( $values['recipient'] ?? '' );
			$contact   = $contacts[ $recipient ] ?? array();

			return array(
				'recipient_name' => $contact['name'] ?? '',
				'sender_name'    => $values['name'] ?? '',
				'sender_email'   => $values['email'] ?? '',
				'message'        => $values['message'] ?? '',
			);
		},
	);

	return $forms;
}
add_filter( 'aa_forms_registry', 'aa_forms_register_contact_directory' );