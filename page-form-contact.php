<?php

/**
 * Template Name: CSO Contact Page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main aa-template aa-template-contact">

	<div class="aa-container aa-container--narrow">

		<section class="aa-section">

			<h1><?php the_title(); ?></h1>

			<p class="aa-lead">
				For enquiries about meetings, literature, group information or local AA service,
				please contact the Canberra &amp; District Central Service Office.
			</p>

		</section>

		<div class="aa-grid aa-grid-2 aa-grid-stretch">

			<section class="aa-card aa-card-accent">

				<h2>Contact Details</h2>

				<p class="aa-lead-small">
					Contact is also available via:
				</p>

				<div class="cso-contact-item">

					<span>Phone</span>

					<?php 
					if ( function_exists( 'custom_site_details_phone_link' ) ) {
						echo wp_kses_post( custom_site_details_phone_link() );
					}
					?>

					<div class="cso-contact-badge">
						Available 24/7
					</div>

				</div>

				<div class="cso-contact-item">

					<span>Email</span>

					<?php 
					if ( function_exists( 'custom_site_details_email_link' ) ) { 
						echo wp_kses_post( custom_site_details_email_link() );
					}
					?>

				</div>

				<div class="aa-divider"></div>

				<h3>Need help finding a meeting?</h3>

				<p>
					View the current meeting list online, or contact the CSO if you'd like help finding a meeting suitable for you.
				</p>

				<p>
					<a class="aa-button" href="/meetings/">
						View Meetings
					</a>
				</p>

			</section>

			<section class="aa-card aa-card-accent cso-contact-form">

				<h2>Send a Message</h2>

				<?php echo do_shortcode( '[aa_form id="contact"]' ); ?>

			</section>

		</div>

	</div>

</main>

<?php
get_footer();