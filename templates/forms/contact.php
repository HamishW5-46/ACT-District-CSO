<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<form class="aa-form" data-aa-form data-form-id="<?php echo esc_attr( $form_id ); ?>" novalidate>
	<input type="hidden" name="form_id" value="<?php echo esc_attr( $form_id ); ?>">
	<input type="hidden" name="opened_at" value="<?php echo esc_attr( time() ); ?>" data-aa-form-opened-at>

	<div class="aa-form__honeypot" aria-hidden="true">
		<label>
			Leave this field empty
			<input type="text" name="website" value="" tabindex="-1" autocomplete="off">
		</label>
	</div>

	<div class="aa-form__field">
		<label for="aa-contact-name">First Name + Initial</label>
		<input id="aa-contact-name" name="contact_name" type="text" maxlength="100" autocomplete="name" required>
	</div>

	<div class="aa-form__field">
		<label for="aa-contact-phone">Contact Number</label>
		<input id="aa-contact-phone" name="phone" type="tel" maxlength="80" autocomplete="tel">
	</div>

	<div class="aa-form__field">
		<label for="aa-contact-email">Contact Email</label>
		<input id="aa-contact-email" name="email" type="email" maxlength="254" autocomplete="email" required>
	</div>

	<div class="aa-form__field">
		<label for="aa-contact-subject">Subject</label>
		<input id="aa-contact-subject" name="subject" type="text" maxlength="150" required>
	</div>

	<div class="aa-form__field">
		<label for="aa-contact-message">Your message</label>
		<textarea id="aa-contact-message" name="message" rows="7" maxlength="5000" required></textarea>
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
