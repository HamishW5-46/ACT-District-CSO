<?php
/**
 * Title: Home content
 * Slug: act-district-cso/home-content
 * Categories: featured
 * Block Types: core/template-part
 */
?>

<!-- wp:group {"className":"aa-site-container","layout":{"type":"default"}} -->
<div class="wp-block-group aa-site-container">
	<!-- wp:group {"tagName":"main","className":"site-main aa-template aa-template-home","layout":{"type":"default"}} -->
	<main class="wp-block-group site-main aa-template aa-template-home">
		<!-- wp:html -->
		<section class="hero-overlay" aria-labelledby="home-hero-title">
			<div class="hero-image-wrapper">
				<img fetchpriority="high" src="/wp-content/themes/ACT-District-CSO/assets/img/front-page-vector.jpg" alt="Illustration of a person reflecting with a drink" class="hero-illustration">
				<div class="hero-text">
					<h1 id="home-hero-title">Do you think you have a drinking problem?</h1>
					<p><strong>Is it costing you more than money?</strong></p>
					<p><strong>We could help...</strong></p>
					<p><a class="home-page-phone-link" href="tel:+61262873020">(02) 6287 3020</a></p>
					<a href="/meetings/" class="button">Find a Meeting</a>
				</div>
			</div>
		</section>
		<!-- /wp:html -->

		<!-- wp:group {"className":"home-content container","layout":{"type":"default"}} -->
		<div class="wp-block-group home-content container">
			<!-- wp:group {"tagName":"section","className":"section-preview","layout":{"type":"default"}} -->
			<section class="wp-block-group section-preview">
				<!-- wp:heading -->
				<h2 class="wp-block-heading">New to AA?</h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p>Alcoholics Anonymous is a community of people who share their experience, strength and hope with each other so they can recover from alcoholism.</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph -->
				<p><a href="/about-aa/">Learn more</a></p>
				<!-- /wp:paragraph -->
			</section>
			<!-- /wp:group -->

			<!-- wp:shortcode -->
			[pstn_optin_form]
			<!-- /wp:shortcode -->

			<!-- wp:group {"tagName":"section","className":"section-preview notices","layout":{"type":"default"}} -->
			<section class="wp-block-group section-preview notices">
				<!-- wp:heading -->
				<h2 class="wp-block-heading">Important Notices &amp; Local Updates</h2>
				<!-- /wp:heading -->

				<!-- wp:query {"query":{"perPage":2,"pages":0,"offset":0,"postType":"notices","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"layout":{"type":"default"}} -->
				<div class="wp-block-query">
					<!-- wp:post-template -->
						<!-- wp:group {"className":"preview-item compact-preview","layout":{"type":"default"}} -->
						<div class="wp-block-group preview-item compact-preview">
							<!-- wp:post-title {"isLink":true,"level":3} /-->
							<!-- wp:post-date {"format":"j M Y","className":"post-date"} /-->
							<!-- wp:post-excerpt {"moreText":"","excerptLength":14} /-->
						</div>
						<!-- /wp:group -->
					<!-- /wp:post-template -->

					<!-- wp:query-no-results -->
						<!-- wp:paragraph -->
						<p>No notices or updates at the moment.</p>
						<!-- /wp:paragraph -->
					<!-- /wp:query-no-results -->
				</div>
				<!-- /wp:query -->

				<!-- wp:paragraph -->
				<p><a href="/notices/">View all notices</a></p>
				<!-- /wp:paragraph -->
			</section>
			<!-- /wp:group -->

			<!-- wp:group {"tagName":"section","className":"section-preview","layout":{"type":"default"}} -->
			<section class="wp-block-group section-preview">
				<!-- wp:heading -->
				<h2 class="wp-block-heading">Local &amp; National Events</h2>
				<!-- /wp:heading -->

				<!-- wp:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"tribe_events","order":"asc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"layout":{"type":"default"}} -->
				<div class="wp-block-query">
					<!-- wp:post-template -->
						<!-- wp:group {"className":"preview-item compact-preview","layout":{"type":"default"}} -->
						<div class="wp-block-group preview-item compact-preview">
							<!-- wp:post-title {"isLink":true,"level":3} /-->
							<!-- wp:post-date {"format":"j M Y g:ia","className":"event-date"} /-->
							<!-- wp:post-excerpt {"moreText":"","excerptLength":18} /-->
						</div>
						<!-- /wp:group -->
					<!-- /wp:post-template -->

					<!-- wp:query-no-results -->
						<!-- wp:paragraph -->
						<p>No upcoming local or national events at the moment.</p>
						<!-- /wp:paragraph -->
					<!-- /wp:query-no-results -->
				</div>
				<!-- /wp:query -->

				<!-- wp:paragraph -->
				<p><a href="/events/">View all events</a></p>
				<!-- /wp:paragraph -->
			</section>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</main>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
