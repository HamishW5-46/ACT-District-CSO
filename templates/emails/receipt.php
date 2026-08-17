<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$email_data = isset( $email_data ) && is_array( $email_data ) ? $email_data : array();
$rows       = isset( $email_data['rows'] ) && is_array( $email_data['rows'] ) ? $email_data['rows'] : array();
$receipt    = isset( $email_data['receipt'] ) && is_array( $email_data['receipt'] ) ? $email_data['receipt'] : array();

$submitter_name = esc_html( $receipt['name'] ?? '' );
$title          = esc_html( $email_data['receipt_title'] ?? 'Submission Received' );
$eyebrow        = esc_html( $email_data['receipt_eyebrow'] ?? 'Website Contact' );
$email_subject  = esc_html( $receipt['subject'] ?? $title );
$button_url     = esc_url( $receipt['button_url'] ?? home_url( '/meetings/' ) );
$button_text    = esc_html( $receipt['button_text'] ?? 'Find a meeting' );
$site_name      = get_bloginfo( 'name' );
$site_url       = home_url( '/' );

if ( isset( $email_data['recipient_name'] ) && $email_data['recipient_name'] !== '' ) {
	array_unshift(
		$rows,
		array(
			'label' => 'Sent to',
			'value' => $email_data['recipient_name'],
			'type'  => 'text',
		)
	);
}

$label_cell_style      = 'width:160px; padding:12px 16px; background:#f7f9fb; border-bottom:1px solid #dfe4e8; color:#5c6670; font-size:13px; font-weight:700; vertical-align:top;';
$value_cell_style      = 'padding:12px 16px; border-bottom:1px solid #dfe4e8; color:#222222; font-size:14px; line-height:1.5;';
$label_cell_last_style = 'width:160px; padding:12px 16px; background:#f7f9fb; color:#5c6670; font-size:13px; font-weight:700; vertical-align:top;';
$value_cell_last_style = 'padding:12px 16px; color:#222222; font-size:14px; line-height:1.5;';
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $email_subject; ?></title>
</head>
<body style="margin:0; padding:0; background:#f2f4f7; color:#222222; font-family:Arial, Helvetica, sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; background:#f2f4f7;">
	<tr>
		<td align="center" style="padding:32px 16px;">
			<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; max-width:640px; background:#ffffff; border-radius:10px; overflow:hidden;">
				<tr>
					<td style="padding:28px 32px; background:#1d5f91; color:#ffffff;">
						<div style="margin:0 0 4px; color:#dceaf5; font-size:12px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase;">
							<?php echo $eyebrow; ?>
						</div>
						<div style="margin:0; color:#ffffff; font-size:24px; font-weight:700; line-height:1.3;">
							<?php echo $title; ?>
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding:32px;">
						<p style="margin:0 0 18px; color:#4b5563; font-size:15px; line-height:1.6;">
							Hi<?php echo $submitter_name ? ' <strong>' . $submitter_name . '</strong>' : ''; ?>,
						</p>

						<p style="margin:0 0 24px; color:#4b5563; font-size:15px; line-height:1.6;">
							Thank you for contacting the Canberra Central Service Office of Alcoholics Anonymous.<br><br>
							This inbox is monitored by volunteers and responses may not be immediate.<br><br>
							AA cannot provide emergency assistance. If you or someone you know is in danger or requires urgent help, please call <a href="tel:000" style="color:#1d5f91; text-decoration:underline;"><strong>000</strong></a>.<br><br>
							To reach a member for support or information about AA, volunteers are available 24/7 on <a href="tel:61262873020" style="color:#1d5f91; text-decoration:underline;">(02) 6287 3020</a>.
						</p>

						<div style="margin:0 0 10px; color:#5c6670; font-size:12px; font-weight:700; letter-spacing:0.06em; text-transform:uppercase;">
							Your message details
						</div>

						<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; margin:0 0 28px; border:1px solid #dfe4e8; border-collapse:separate; border-radius:8px; overflow:hidden;">
							<?php foreach ( $rows as $index => $row ) : ?>
								<?php
								$is_last = $index === array_key_last( $rows );
								$type    = $row['type'] ?? 'text';
								$value   = (string) ( $row['value'] ?? '' );
								?>
								<tr>
									<td style="<?php echo $is_last ? $label_cell_last_style : $label_cell_style; ?>">
										<?php echo esc_html( $row['label'] ?? '' ); ?>
									</td>
									<td style="<?php echo $is_last ? $value_cell_last_style : $value_cell_style; ?>">
										<?php if ( $type === 'textarea' ) : ?>
											<?php echo nl2br( esc_html( $value ) ); ?>
										<?php elseif ( $type === 'email' ) : ?>
											<a href="mailto:<?php echo esc_attr( sanitize_email( $value ) ); ?>" style="color:#1d5f91; text-decoration:underline;">
												<?php echo esc_html( $value ); ?>
											</a>
										<?php else : ?>
											<?php echo esc_html( $value ); ?>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>

						<p style="margin:0 0 24px; text-align:center;">
							<a href="<?php echo $button_url; ?>" style="display:inline-block; text-align:center; background:#1d5f91; color:#ffffff; text-decoration:none; font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:700; padding:12px 18px; border-radius:6px;">
								<?php echo $button_text; ?>
							</a>
						</p>

						<p style="margin:0; color:#667085; font-size:13px; line-height:1.6;">
							If you did not submit this request, please ignore and discard this email.
						</p>
					</td>
				</tr>

				<tr>
					<td style="padding:20px 32px; background:#f7f9fb; border-top:1px solid #e5e7eb; color:#7a828a; font-size:12px; line-height:1.6;">
						<p style="margin:0 0 12px;">
							Yours in service,<br><br>
							Canberra Central Service Office<br>
							Grant Cameron Offices<br>
							27 Mulley Street<br>
							HOLDER ACT 2611<br><br>
							Web:
							<a href="<?php echo esc_url( $site_url ); ?>" style="color:#1d5f91; text-decoration:none;">
								<?php echo esc_html( $site_name ); ?>
							</a><br>
							Phone:
							<a href="tel:61262873020" style="color:#1d5f91; text-decoration:none;">
								(02) 6287 3020
							</a>
						</p>

						<p style="margin:0; text-align:center;">
							Sent through
							<a href="<?php echo esc_url( $site_url ); ?>" style="color:#1d5f91; text-decoration:none;">
								<?php echo esc_html( $site_name ); ?>
							</a>
						</p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>
