<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the destination for Beginners roster enquiries.
 */
function aa_forms_beginners_recipient_email() {
	return apply_filters( 'aa_forms_beginners_recipient_email', 'beginners@aacanberra.org' );
}

/**
 * Register the Beginners roster form.
 */
function aa_forms_register_beginners( $forms ) {
	$forms['beginners'] = array(
		'title'           => 'Beginners Sign-Up',
		'email_title'     => 'Beginners Roster Sign-Up',
		'email_eyebrow'   => 'Beginners Roster',
		'template'        => 'beginners',
		'email_template'  => 'beginners',
		'success_message' => 'Thank you. Your Beginners roster response has been sent.',
		'fields'          => array(
			array(
				'key'         => 'group_name',
				'label'       => 'What is the name of your Group?',
				'email_label' => 'Group',
				'type'        => 'text',
				'required'    => false,
				'maxlength'   => 140,
			),
			array(
				'key'         => 'confirmation',
				'label'       => 'Can your Group support the Canberra Beginners meeting this year?',
				'email_label' => 'Can support Beginners?',
				'type'        => 'radio',
				'required'    => true,
				'options'     => array(
					'yes' => 'Yes, we can!',
					'no'  => "Sorry, we can't this time.",
				),
			),
			array(
				'key'         => 'roster_length',
				'label'       => 'If yes, can you host a 3 or 6-week series?',
				'email_label' => 'Preferred roster length',
				'type'        => 'radio',
				'required'    => false,
				'options'     => array(
					'3_weeks' => '3 weeks (recommended for small groups)',
					'6_weeks' => '6 weeks (recommended for larger groups)',
					'either'  => 'Either 3 or 6 weeks',
				),
			),
			array(
				'key'       => 'group_member_name',
				'label'     => 'Group contact name',
				'type'      => 'text',
				'required'  => true,
				'maxlength' => 100,
			),
			array(
				'key'       => 'group_member_email',
				'label'     => 'Group contact email',
				'type'      => 'email',
				'required'  => true,
				'maxlength' => 254,
			),
			array(
				'key'         => 'zoom_confirmation',
				'label'       => 'Will your Group need any help with Zoom?',
				'email_label' => 'Zoom assistance',
				'type'        => 'radio',
				'required'    => true,
				'options'     => array(
					'yes'   => 'Yes, that would be great.',
					'no'    => 'No thanks, we are confident Zoomers!',
					'maybe' => "Maybe... we need more info about what's involved.",
				),
			),
		),
		'validate' => function( $values ) {
			if ( ( $values['confirmation'] ?? '' ) === 'yes' && empty( $values['roster_length'] ) ) {
				return 'Please choose a preferred roster length.';
			}

			return '';
		},
		'mail' => array(
			'to' => function( $form, $values ) {
				return aa_forms_beginners_recipient_email();
			},
			'subject' => function( $form, $values ) {
				$group = trim( $values['group_name'] ?? '' );

				return $group
					? sprintf( 'Beginners roster response: %s', $group )
					: 'Beginners roster response';
			},
			'reply_name' => function( $form, $values ) {
				return $values['group_member_name'] ?? '';
			},
			'reply_email' => function( $form, $values ) {
				return $values['group_member_email'] ?? '';
			},
		),
	);

	return $forms;
}
add_filter( 'aa_forms_registry', 'aa_forms_register_beginners' );