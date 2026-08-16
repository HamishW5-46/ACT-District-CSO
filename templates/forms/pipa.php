<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<form class="aa-form aa-form--standalone" data-aa-form data-form-id="<?php echo esc_attr( $form_id ); ?>" novalidate>
	<input type="hidden" name="form_id" value="<?php echo esc_attr( $form_id ); ?>">
	<input type="hidden" name="opened_at" value="<?php echo esc_attr( time() ); ?>" data-aa-form-opened-at>

	<div class="aa-form__honeypot" aria-hidden="true">
		<label>
			Leave this field empty
			<input type="text" name="website" value="" tabindex="-1" autocomplete="off">
		</label>
	</div>

	<header class="aa-form__header">
		<h1><?php echo esc_html( $form['title'] ); ?></h1>
	</header>

	<div class="aa-form__field">
		<label for="aa-pipa-name">Name</label>
		<input id="aa-pipa-name" name="pipa_requester_name" type="text" maxlength="100" autocomplete="name" required>
	</div>

	<div class="aa-form__field">
		<label for="aa-pipa-phone">Phone Number</label>
		<input id="aa-pipa-phone" name="pipa_requester_tel" type="tel" maxlength="80" autocomplete="tel">
	</div>

	<div class="aa-form__field">
		<label for="aa-pipa-email">Email</label>
		<input id="aa-pipa-email" name="pipa_requester_email" type="email" maxlength="254" autocomplete="email" required>
	</div>

	<div class="aa-form__field">
		<label for="aa-pipa-subject">Subject</label>
		<input id="aa-pipa-subject" name="pipa_subject" type="text" maxlength="150" required>
	</div>

	<div class="aa-form__field">
		<label for="aa-pipa-message">Message</label>
		<textarea id="aa-pipa-message" name="pipa_message" rows="7" maxlength="5000" required></textarea>
	</div>

	<?php if ( aa_forms_turnstile_enabled() ) : ?>
		<div class="aa-form__turnstile">
			<div data-aa-form-turnstile></div>
		</div>
	<?php endif; ?>

	<p class="aa-form__privacy">
		This form uses anti-spam checks to reduce automated submissions.
	</p>

	<div class="aa-form__status" data-aa-form-status role="status" aria-live="polite"></div>

	<div class="aa-form__actions">
		<button type="submit" class="aa-form-button aa-form-button--primary" data-aa-form-submit>Submit</button>
	</div>
</form>
