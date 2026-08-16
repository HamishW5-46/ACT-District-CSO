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
		<label for="aa-beginners-group">What is the name of your Group?</label>
		<input id="aa-beginners-group" name="group_name" type="text" maxlength="140" autocomplete="organization">
	</div>

	<fieldset class="aa-form__field aa-form__radio-field">
		<legend>Can your Group support the Canberra Beginners meeting this year?</legend>
		<label><input type="radio" name="confirmation" value="yes" required> Yes, we can!</label>
		<label><input type="radio" name="confirmation" value="no" required> Sorry, we can't this time.</label>
	</fieldset>

	<div class="aa-form__conditional" data-aa-beginners-if-yes>
		<fieldset class="aa-form__field aa-form__radio-field">
			<legend>If yes, can you host a 3 or 6-week series?</legend>
			<label><input type="radio" name="roster_length" value="3_weeks"> 3 weeks (recommended for small groups)</label>
			<label><input type="radio" name="roster_length" value="6_weeks"> 6 weeks (recommended for larger groups)</label>
			<label><input type="radio" name="roster_length" value="either"> Either 3 or 6 weeks</label>
		</fieldset>
	</div>

	<div class="aa-form__field">
		<label for="aa-beginners-contact-name">Please nominate a Group member we can contact</label>
		<input id="aa-beginners-contact-name" name="group_member_name" type="text" maxlength="100" autocomplete="name" placeholder="Bill W" required>
	</div>

	<div class="aa-form__field">
		<label for="aa-beginners-contact-email">Group contact email</label>
		<input id="aa-beginners-contact-email" name="group_member_email" type="email" maxlength="254" autocomplete="email" placeholder="Bill.Wilson@aa.org" required>
	</div>

	<p class="aa-form__note">
		Beginners is a hybrid meeting, providing a broadcast of the meeting through Zoom. The CSO provides a laptop for this service.
	</p>

	<fieldset class="aa-form__field aa-form__radio-field">
		<legend>Will your Group need any help with Zoom?</legend>
		<label><input type="radio" name="zoom_confirmation" value="yes" required> Yes, that would be great.</label>
		<label><input type="radio" name="zoom_confirmation" value="no" required> No thanks, we are confident Zoomers!</label>
		<label><input type="radio" name="zoom_confirmation" value="maybe" required> Maybe... we need more info about what's involved.</label>
	</fieldset>

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
