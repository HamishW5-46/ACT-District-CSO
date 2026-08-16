<?php

if (!defined('ABSPATH')) {
    exit;
}

$email_data = isset($email_data) && is_array($email_data)
    ? $email_data
    : [];

$recipient_name   = esc_html($email_data['recipient_name'] ?? '');
$sender_name      = esc_html($email_data['sender_name'] ?? '');
$sender_email_raw = sanitize_email($email_data['sender_email'] ?? '');
$sender_email     = esc_html($sender_email_raw);
$subject          = esc_html($email_data['subject'] ?? '');

$message = nl2br(
    esc_html($email_data['message'] ?? '')
);

$site_name = get_bloginfo('name');
$site_url  = home_url('/');

$label_cell_style = 'width:130px; padding:12px 16px; background:#f7f9fb; border-bottom:1px solid #dfe4e8; color:#5c6670; font-size:13px; font-weight:700; vertical-align:top;';
$value_cell_style = 'padding:12px 16px; border-bottom:1px solid #dfe4e8; color:#222222; font-size:14px; line-height:1.5;';
$label_cell_last_style = 'width:130px; padding:12px 16px; background:#f7f9fb; color:#5c6670; font-size:13px; font-weight:700; vertical-align:top;';
$value_cell_last_style = 'padding:12px 16px; color:#222222; font-size:14px; line-height:1.5;';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title><?php echo $subject; ?></title>
</head>

<body
    style="
        margin: 0;
        padding: 0;
        background: #f2f4f7;
        color: #222222;
        font-family: Arial, Helvetica, sans-serif;
    "
>

<table
    role="presentation"
    width="100%"
    cellspacing="0"
    cellpadding="0"
    border="0"
    style="
        width: 100%;
        background: #f2f4f7;
    "
>
    <tr>
        <td
            align="center"
            style="padding: 32px 16px;"
        >

            <table
                role="presentation"
                width="100%"
                cellspacing="0"
                cellpadding="0"
                border="0"
                style="
                    width: 100%;
                    max-width: 640px;
                    background: #ffffff;
                    border-radius: 10px;
                    overflow: hidden;
                "
            >

                <tr>
                    <td
                        style="
                            padding: 28px 32px;
                            background: #1d5f91;
                            color: #ffffff;
                        "
                    >

                        <div
                            style="
                                margin: 0 0 4px;
                                color: #dceaf5;
                                font-size: 12px;
                                font-weight: 700;
                                letter-spacing: 0.08em;
                                text-transform: uppercase;
                            "
                        >
                            Website Contact
                        </div>

                        <div
                            style="
                                margin: 0;
                                color: #ffffff;
                                font-size: 24px;
                                font-weight: 700;
                                line-height: 1.3;
                            "
                        >
                            <?php echo $recipient_name; ?>
                        </div>

                    </td>
                </tr>

                <tr>
                    <td style="padding: 32px;">

                        <p
                            style="
                                margin: 0 0 24px;
                                color: #4b5563;
                                font-size: 15px;
                                line-height: 1.6;
                            "
                        >
                            A message has been submitted through the
                            <?php echo esc_html($site_name); ?> website.
                        </p>

                        <table
                            role="presentation"
                            width="100%"
                            cellspacing="0"
                            cellpadding="0"
                            border="0"
                            style="
                                width: 100%;
                                margin: 0 0 28px;
                                border: 1px solid #dfe4e8;
                                border-collapse: separate;
                                border-radius: 8px;
                                overflow: hidden;
                            "
                        >

                            <tr>
                                <td style="<?php echo $label_cell_style; ?>">
                                    To
                                </td>

                                <td style="<?php echo $value_cell_style; ?>">
                                    <?php echo $recipient_name; ?>
                                </td>
                            </tr>

                            <tr>
                                <td style="<?php echo $label_cell_style; ?>">
                                    From
                                </td>

                                <td style="<?php echo $value_cell_style; ?>">
                                    <?php echo $sender_name; ?>
                                </td>
                            </tr>

                            <tr>
                                <td style="<?php echo $label_cell_style; ?>">
                                    Email
                                </td>

                                <td style="<?php echo $value_cell_style; ?>">
                                    <a
                                        href="mailto:<?php echo esc_attr($sender_email_raw); ?>"
                                        style="
                                            color: #1d5f91;
                                            text-decoration: underline;
                                        "
                                    >
                                        <?php echo $sender_email; ?>
                                    </a>
                                </td>
                            </tr>

                            <tr>
                                <td style="<?php echo $label_cell_last_style; ?>">
                                    Subject
                                </td>

                                <td style="<?php echo $value_cell_last_style; ?>">
                                    <?php echo $subject; ?>
                                </td>
                            </tr>

                        </table>

                        <div
                            style="
                                margin: 0 0 28px;
                                padding: 22px;
                                background: #f7f9fb;
                                border-left: 4px solid #1d5f91;
                                border-radius: 0 6px 6px 0;
                            "
                        >

                            <div
                                style="
                                    margin: 0 0 10px;
                                    color: #5c6670;
                                    font-size: 12px;
                                    font-weight: 700;
                                    letter-spacing: 0.06em;
                                    text-transform: uppercase;
                                "
                            >
                                Message
                            </div>

                            <div
                                style="
                                    color: #222222;
                                    font-size: 15px;
                                    line-height: 1.7;
                                "
                            >
                                <?php echo $message; ?>
                            </div>

                        </div>

                        <p
                            style="
                                margin: 0;
                                color: #667085;
                                font-size: 13px;
                                line-height: 1.6;
                            "
                        >
                            Reply directly to this email to respond to
                            <?php echo $sender_name; ?>.
                        </p>

                    </td>
                </tr>

                <tr>
                    <td
                        style="
                            padding: 20px 32px;
                            background: #f7f9fb;
                            border-top: 1px solid #e5e7eb;
                            text-align: center;
                        "
                    >

                        <p
                            style="
                                margin: 0;
                                color: #7a828a;
                                font-size: 12px;
                                line-height: 1.6;
                            "
                        >
                            Sent through
                            <a
                                href="<?php echo esc_url($site_url); ?>"
                                style="
                                    color: #1d5f91;
                                    text-decoration: none;
                                "
                            >
                                <?php echo esc_html($site_name); ?>
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
