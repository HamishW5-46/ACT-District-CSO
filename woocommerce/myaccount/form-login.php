<?php
/**
 * Custom WooCommerce customer login and registration form.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$registration_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );

do_action( 'woocommerce_before_customer_login_form' );
?>

<section class="aa-account-auth<?php echo $registration_enabled ? ' aa-account-auth--with-registration' : ''; ?>" aria-labelledby="aa-account-auth-heading">
	<div class="aa-account-auth__intro">
		<p class="aa-eyebrow"><?php esc_html_e( 'Literature Shop Account', 'ACT_District_CSO_Child' ); ?></p>
		<h2 id="aa-account-auth-heading"><?php esc_html_e( 'Access your account', 'ACT_District_CSO_Child' ); ?></h2>
		<p><?php esc_html_e( 'Log in to view orders, manage account details, and make future literature purchases faster.', 'ACT_District_CSO_Child' ); ?></p>
	</div>

	<div class="aa-account-auth__grid" id="customer_login">
		<section class="aa-account-auth__panel aa-account-auth__panel--login" aria-labelledby="aa-account-login-heading">
			<div class="aa-account-auth__panel-header">
				<h2 id="aa-account-login-heading"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></h2>
				<p><?php esc_html_e( 'Use your existing shop account details.', 'ACT_District_CSO_Child' ); ?></p>
			</div>

			<form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>
				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
				</p>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
					<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
				</p>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<div class="aa-account-auth__form-actions">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
					</label>

					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>

					<button type="submit" class="woocommerce-button button woocommerce-form-login__submit<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>">
						<?php esc_html_e( 'Log in', 'woocommerce' ); ?>
					</button>
				</div>

				<p class="woocommerce-LostPassword lost_password">
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
				</p>

				<?php do_action( 'woocommerce_login_form_end' ); ?>
			</form>
		</section>

		<?php if ( $registration_enabled ) : ?>
			<section class="aa-account-auth__panel aa-account-auth__panel--register" aria-labelledby="aa-account-register-heading">
				<div class="aa-account-auth__panel-header">
					<h2 id="aa-account-register-heading"><?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>
					<p><?php esc_html_e( 'Create a customer account for future literature orders.', 'ACT_District_CSO_Child' ); ?></p>
				</div>

				<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
					<?php do_action( 'woocommerce_register_form_start' ); ?>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
							<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
						</p>
					<?php endif; ?>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
						<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
					</p>

					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
							<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
						</p>
					<?php else : ?>
						<p class="aa-account-auth__note"><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>
					<?php endif; ?>

					<?php do_action( 'woocommerce_register_form' ); ?>

					<p class="woocommerce-form-row form-row aa-account-auth__register-submit">
						<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
						<button type="submit" class="woocommerce-Button woocommerce-button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?> woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>">
							<?php esc_html_e( 'Register', 'woocommerce' ); ?>
						</button>
					</p>

					<?php do_action( 'woocommerce_register_form_end' ); ?>
				</form>
			</section>
		<?php endif; ?>
	</div>
</section>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
