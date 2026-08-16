<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$email_data = isset( $email_data ) && is_array( $email_data ) ? $email_data : array();
$rows       = isset( $email_data['rows'] ) && is_array( $email_data['rows'] ) ? $email_data['rows'] : array();
$receipt    = isset( $email_data['receipt'] ) && is_array( $email_data['receipt'] ) ? $email_data['receipt'] : array();

$submitter_name = esc_html( $receipt['name'] ?? '' );
$email_subject  = esc_html( $receipt['subject'] ?? 'Submission Received' );
$button_url     = esc_url( $receipt['button_url'] ?? home_url( '/meetings/' ) );
$button_text    = esc_html( $receipt['button_text'] ?? 'Find a meeting' );
$site_url       = home_url( '/' );
$site_host      = wp_parse_url( $site_url, PHP_URL_HOST );
$site_label     = $site_host ? $site_host : $site_url;

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
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $email_subject; ?></title>
</head>
<body style="margin:0; padding:0; background:#f4f6f8; color:#1f2937; font-family:Arial, Helvetica, sans-serif;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f4f6f8; padding:24px 0;">
	<tr>
		<td align="center" style="padding:0 12px;">
			<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="max-width:600px; width:100%; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.06);">
				<tr>
					<td style="padding:24px; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
						<div style="font-size:16px; line-height:1.5; margin:0 0 16px 0;">
							Hi<?php echo $submitter_name ? ' <strong>' . $submitter_name . '</strong>' : ''; ?>,
						</div>

						<div style="font-size:14px; line-height:1.6; margin:0 0 16px 0;">
							Thank you for contacting the Canberra Central Service Office of Alcoholics Anonymous.<br><br>
							This inbox is monitored by volunteers and responses may not be immediate.<br><br>
							AA cannot provide emergency assistance. If you or someone you know is in danger or requires urgent help, please call <a href="tel:000" style="color:#0b3d91; text-decoration:none;"><strong>000</strong></a>.<br><br>
							To reach a member for support or information about AA, volunteers are available 24/7 on <a href="tel:61262873020" style="color:#0b3d91; text-decoration:none;">(02) 6287 3020</a>.
						</div>

						<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:10px; margin:0 0 20px 0;">
							<tr>
								<td style="padding:16px;">
									<div style="font-size:13px; font-weight:700; color:#111827; margin:0 0 10px 0;">
										<strong>Your message details:</strong>
									</div>

									<?php foreach ( $rows as $row ) : ?>
										<?php
										$type  = $row['type'] ?? 'text';
										$value = (string) ( $row['value'] ?? '' );
										?>
										<div style="font-size:13px; line-height:1.6; margin:0 0 6px 0;">
											<strong><?php echo esc_html( $row['label'] ?? '' ); ?>:</strong>
											<?php if ( $type === 'textarea' ) : ?>
												<br><div style="white-space:pre-wrap; margin-top:6px;"><?php echo esc_html( $value ); ?></div>
											<?php elseif ( $type === 'email' ) : ?>
												<a href="mailto:<?php echo esc_attr( sanitize_email( $value ) ); ?>" style="color:#0b3d91; text-decoration:none;"><?php echo esc_html( $value ); ?></a>
											<?php else : ?>
												<?php echo esc_html( $value ); ?>
											<?php endif; ?>
										</div>
									<?php endforeach; ?>
								</td>
							</tr>
						</table>

						<div style="margin:12px 0 0 0; text-align:center;">
							<a href="<?php echo $button_url; ?>" style="display:inline-block; text-align:center; background:#0b3d91; color:#ffffff; text-decoration:none; font-family:Arial, Helvetica, sans-serif; font-size:14px; padding:10px 14px; border-radius:10px;">
								<?php echo $button_text; ?>
							</a>
						</div>

						<div style="margin:18px 0 0 0; font-size:12px; line-height:1.6; color:#6b7280;">
							If you did not submit this request, please ignore and discard this email.
						</div>
					</td>
				</tr>

				<tr>
					<td style="padding:18px 24px; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:1.6; color:#6b7280; background:#ffffff; border-top:1px solid #e5e7eb;">
						<div style="margin:0 0 6px 0;">
							Yours in service,<br><br>
							Canberra Central Service Office<br>
							Grant Cameron Offices<br>
							27 Mulley Street<br>
							HOLDER ACT 2611<br><br>
							Web: <a href="<?php echo esc_url( $site_url ); ?>" style="color:#0b3d91; text-decoration:none;"><?php echo esc_html( $site_label ); ?></a><br>
							Phone: <a href="tel:61262873020" style="color:#0b3d91; text-decoration:none;">(02) 6287 3020</a>
						</div>
						<div style="margin:0;">
							Sent from <a href="<?php echo esc_url( $site_url ); ?>" style="color:#0b3d91; text-decoration:none;"><?php echo esc_html( $site_label ); ?></a>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>
