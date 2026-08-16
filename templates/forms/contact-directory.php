<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contacts = aa_get_contact_directory();
?>

<div class="aa-contact-directory" data-aa-form-scope>
	<div class="aa-contact-grid">
		<?php foreach ( $contacts as $key => $contact ) : ?>
			<article class="aa-contact-card">
				<div class="aa-contact-card__content">
					<h3 class="aa-contact-card__title">
						<?php echo esc_html( $contact['name'] ); ?>
					</h3>

					<?php if ( ! empty( $contact['description'] ) ) : ?>
						<p class="aa-contact-card__description">
							<?php echo esc_html( $contact['description'] ); ?>
						</p>
					<?php endif; ?>
				</div>

				<button
					type="button"
					class="aa-form-button"
					data-aa-contact-open
					data-recipient="<?php echo esc_attr( $key ); ?>"
					data-recipient-name="<?php echo esc_attr( $contact['name'] ); ?>"
				>
					Contact
				</button>
			</article>
		<?php endforeach; ?>
	</div>

	<div class="aa-contact-modal" data-aa-contact-modal aria-hidden="true">
		<div class="aa-contact-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="aa-contact-modal-title">
			<button type="button" class="aa-contact-modal__close" data-aa-contact-close aria-label="Close contact form">
				&times;
			</button>

			<div class="aa-contact-modal__header">
				<p class="aa-contact-modal__eyebrow">Contact</p>
				<h2 id="aa-contact-modal-title" class="aa-contact-modal__title" data-aa-contact-title>Send a message</h2>
			</div>

			<form class="aa-form aa-form--modal" data-aa-form data-form-id="<?php echo esc_attr( $form_id ); ?>" novalidate>
				<input type="hidden" name="form_id" value="<?php echo esc_attr( $form_id ); ?>">
				<input type="hidden" name="recipient" value="" data-aa-contact-recipient>
				<input type="hidden" name="opened_at" value="" data-aa-form-opened-at>

				<div class="aa-form__honeypot" aria-hidden="true">
					<label>
						Leave this field empty
						<input type="text" name="website" value="" tabindex="-1" autocomplete="off">
					</label>
				</div>

				<div class="aa-form__field">
					<label for="aa-contact-directory-name">Your name</label>
					<input id="aa-contact-directory-name" name="name" type="text" maxlength="100" autocomplete="name" required>
				</div>

				<div class="aa-form__field">
					<label for="aa-contact-directory-email">Your email</label>
					<input id="aa-contact-directory-email" name="email" type="email" maxlength="254" autocomplete="email" required>
				</div>

				<div class="aa-form__field">
					<label for="aa-contact-directory-subject">Subject</label>
					<input id="aa-contact-directory-subject" name="subject" type="text" maxlength="150" required>
				</div>

				<div class="aa-form__field">
					<label for="aa-contact-directory-message">Message</label>
					<textarea id="aa-contact-directory-message" name="message" rows="7" maxlength="5000" required></textarea>
				</div>

				<?php if ( aa_forms_turnstile_enabled() ) : ?>
					<div class="aa-form__turnstile">
						<div data-aa-form-turnstile></div>
					</div>
				<?php endif; ?>

				<div class="aa-form__status" data-aa-form-status role="status" aria-live="polite"></div>

				<div class="aa-form__actions">
					<button type="button" class="aa-form-button aa-form-button--secondary" data-aa-contact-close>Cancel</button>
					<button type="submit" class="aa-form-button aa-form-button--primary" data-aa-form-submit>Send message</button>
				</div>
			</form>
		</div>
	</div>
</div>