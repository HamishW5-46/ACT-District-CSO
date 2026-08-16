<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Return available contact recipients.
 *
 * Email addresses remain server-side and are never rendered into the page.
 */
function aa_get_contact_directory()
{
    return [
        'chair' => [
            'name'        => 'Chair',
            'email'       => 'chair@aacanberra.org',
            'description' => 'General Central Service Office and committee matters.',
        ],

        'secretary' => [
            'name'        => 'Secretary',
            'email'       => 'secretary@aacanberra.org',
            'description' => 'Committee correspondence, records and administrative matters.',
        ],

        'treasurer' => [
            'name'        => 'Treasurer',
            'email'       => 'treasurer@aacanberra.org',
            'description' => 'Contributions, payments, accounts and financial enquiries.',
        ],

        'information' => [
            'name'        => 'Information Coordinator',
            'email'       => 'info@aacanberra.org',
            'description' => 'Website, meeting information and general information enquiries.',
        ],

        'literature' => [
            'name'        => 'Literature Coordinator',
            'email'       => 'literature@aacanberra.org',
            'description' => 'Literature and related enquiries.',
        ],

        'detox' => [
            'name'        => 'Detox Coordinator',
            'email'       => 'detoxmeetings@aacanberra.org',
            'description' => 'Detox service enquiries.',
        ],

        'beginners' => [
            'name'        => 'Beginners Coordinator',
            'email'       => 'beginners@aacanberra.org',
            'description' => 'Beginners meeting and related enquiries.',
        ],
    ];
}


/**
 * Register frontend assets.
 */
function aa_contact_directory_assets()
{
    wp_register_style(
        'aa-contact-directory',
        get_stylesheet_directory_uri() . '/assets/css/contact-directory.css',
        [],
        '1.0.0'
    );

    wp_register_script(
        'aa-contact-directory',
        get_stylesheet_directory_uri() . '/assets/js/contact-directory.js',
        [],
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'aa_contact_directory_assets');


/**
 * Contact directory shortcode.
 *
 * Usage:
 * [aa_contact_directory]
 */
function aa_contact_directory_shortcode()
{
    $contacts = aa_get_contact_directory();

    wp_enqueue_style('aa-contact-directory');
    wp_enqueue_script('aa-contact-directory');

    wp_localize_script(
        'aa-contact-directory',
        'aaContactDirectory',
        [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('aa_contact_directory'),
        ]
    );

    ob_start();
    ?>

    <div class="aa-contact-directory">

        <div class="aa-contact-grid">

            <?php foreach ($contacts as $key => $contact) : ?>

                <article class="aa-contact-card">

                    <div class="aa-contact-card__content">

                        <h3 class="aa-contact-card__title">
                            <?php echo esc_html($contact['name']); ?>
                        </h3>

                        <?php if (!empty($contact['description'])) : ?>
                            <p class="aa-contact-card__description">
                                <?php echo esc_html($contact['description']); ?>
                            </p>
                        <?php endif; ?>

                    </div>

                    <button
                        type="button"
                        class="aa-contact-button"
                        data-aa-contact-open
                        data-recipient="<?php echo esc_attr($key); ?>"
                        data-recipient-name="<?php echo esc_attr($contact['name']); ?>"
                    >
                        Contact
                    </button>

                </article>

            <?php endforeach; ?>

        </div>


        <div
            class="aa-contact-modal"
            data-aa-contact-modal
            aria-hidden="true"
        >

            <div
                class="aa-contact-modal__dialog"
                role="dialog"
                aria-modal="true"
                aria-labelledby="aa-contact-modal-title"
            >

                <button
                    type="button"
                    class="aa-contact-modal__close"
                    data-aa-contact-close
                    aria-label="Close contact form"
                >
                    &times;
                </button>

                <div class="aa-contact-modal__header">

                    <p class="aa-contact-modal__eyebrow">
                        Contact
                    </p>

                    <h2
                        id="aa-contact-modal-title"
                        class="aa-contact-modal__title"
                        data-aa-contact-title
                    >
                        Send a message
                    </h2>

                </div>

                <form
                    class="aa-contact-form"
                    data-aa-contact-form
                    novalidate
                >

                    <input
                        type="hidden"
                        name="recipient"
                        value=""
                        data-aa-contact-recipient
                    >

                    <input
                        type="hidden"
                        name="opened_at"
                        value=""
                        data-aa-contact-opened-at
                    >

                    <div
                        class="aa-contact-form__honeypot"
                        aria-hidden="true"
                    >
                        <label>
                            Leave this field empty
                            <input
                                type="text"
                                name="website"
                                value=""
                                tabindex="-1"
                                autocomplete="off"
                            >
                        </label>
                    </div>


                    <div class="aa-contact-form__field">

                        <label for="aa-contact-name">
                            Your name
                        </label>

                        <input
                            id="aa-contact-name"
                            name="name"
                            type="text"
                            maxlength="100"
                            autocomplete="name"
                            required
                        >

                    </div>


                    <div class="aa-contact-form__field">

                        <label for="aa-contact-email">
                            Your email
                        </label>

                        <input
                            id="aa-contact-email"
                            name="email"
                            type="email"
                            maxlength="254"
                            autocomplete="email"
                            required
                        >

                    </div>


                    <div class="aa-contact-form__field">

                        <label for="aa-contact-subject">
                            Subject
                        </label>

                        <input
                            id="aa-contact-subject"
                            name="subject"
                            type="text"
                            maxlength="150"
                            required
                        >

                    </div>


                    <div class="aa-contact-form__field">

                        <label for="aa-contact-message">
                            Message
                        </label>

                        <textarea
                            id="aa-contact-message"
                            name="message"
                            rows="7"
                            maxlength="5000"
                            required
                        ></textarea>

                    </div>


                    <div
                        class="aa-contact-form__status"
                        data-aa-contact-status
                        role="status"
                        aria-live="polite"
                    ></div>


                    <div class="aa-contact-form__actions">

                        <button
                            type="button"
                            class="aa-contact-button aa-contact-button--secondary"
                            data-aa-contact-close
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="aa-contact-button aa-contact-button--primary"
                            data-aa-contact-submit
                        >
                            Send message
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <?php

    return ob_get_clean();
}
add_shortcode('aa_contact_directory', 'aa_contact_directory_shortcode');


/**
 * Handle public contact form submissions.
 */
function aa_contact_directory_submit()
{
    check_ajax_referer(
        'aa_contact_directory',
        'nonce'
    );

    $contacts = aa_get_contact_directory();

    $recipient = isset($_POST['recipient'])
        ? sanitize_key(wp_unslash($_POST['recipient']))
        : '';

    $name = isset($_POST['name'])
        ? sanitize_text_field(wp_unslash($_POST['name']))
        : '';

    $email = isset($_POST['email'])
        ? sanitize_email(wp_unslash($_POST['email']))
        : '';

    $subject = isset($_POST['subject'])
        ? sanitize_text_field(wp_unslash($_POST['subject']))
        : '';

    $message = isset($_POST['message'])
        ? sanitize_textarea_field(wp_unslash($_POST['message']))
        : '';

    $honeypot = isset($_POST['website'])
        ? sanitize_text_field(wp_unslash($_POST['website']))
        : '';

    $opened_at = isset($_POST['opened_at'])
        ? absint($_POST['opened_at'])
        : 0;


    /*
     * Honeypot.
     *
     * Return a fake success response so bots do not learn that
     * their submission was detected.
     */
    if ($honeypot !== '') {
        wp_send_json_success([
            'message' => 'Your message has been sent.',
        ]);
    }


    /*
     * Validate recipient.
     */
    if (
        !$recipient ||
        !isset($contacts[$recipient])
    ) {
        wp_send_json_error([
            'message' => 'Please select a valid recipient.',
        ], 400);
    }


    /*
     * Minimum completion time.
     */
    if (
        !$opened_at ||
        (time() - $opened_at) < 3
    ) {
        wp_send_json_error([
            'message' => 'Please wait a moment and try again.',
        ], 400);
    }


    /*
     * Validate fields.
     */
    if ($name === '') {
        wp_send_json_error([
            'message' => 'Please enter your name.',
        ], 400);
    }

    if (
        !$email ||
        !is_email($email)
    ) {
        wp_send_json_error([
            'message' => 'Please enter a valid email address.',
        ], 400);
    }

    if ($subject === '') {
        wp_send_json_error([
            'message' => 'Please enter a subject.',
        ], 400);
    }

    if ($message === '') {
        wp_send_json_error([
            'message' => 'Please enter a message.',
        ], 400);
    }


    /*
     * Enforce lengths server-side as well.
     */
    if (mb_strlen($name) > 100) {
        wp_send_json_error([
            'message' => 'Your name is too long.',
        ], 400);
    }

    if (mb_strlen($subject) > 150) {
        wp_send_json_error([
            'message' => 'Your subject is too long.',
        ], 400);
    }

    if (mb_strlen($message) > 5000) {
        wp_send_json_error([
            'message' => 'Your message is too long.',
        ], 400);
    }


    $contact = $contacts[$recipient];


    /*
     * Construct email.
     */
    $to = $contact['email'];

    $mail_subject = sprintf(
        'Website enquiry: %s',
        $subject
    );

    $email_data = [
        'recipient_name' => $contact['name'],
        'sender_name'    => $name,
        'sender_email'   => $email,
        'subject'        => $subject,
        'message'        => $message,
    ];

    ob_start();
    include get_stylesheet_directory() . '/templates/contact-email-template.php';
    $body = trim(ob_get_clean());

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        sprintf(
            'Reply-To: %s <%s>',
            $name,
            $email
        ),
    ];


    /*
     * Send.
     */
    $sent = wp_mail(
        $to,
        $mail_subject,
        $body,
        $headers
    );


    if (!$sent) {
        wp_send_json_error([
            'message' => 'Your message could not be sent. Please try again.',
        ], 500);
    }


    wp_send_json_success([
        'message' => sprintf(
            'Your message has been sent to the %s.',
            $contact['name']
        ),
    ]);
}

add_action(
    'wp_ajax_aa_contact_directory_submit',
    'aa_contact_directory_submit'
);

add_action(
    'wp_ajax_nopriv_aa_contact_directory_submit',
    'aa_contact_directory_submit'
);
