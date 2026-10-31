<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aac_site_render_logo() {
	if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
		ob_start();
		the_custom_logo();
		return ob_get_clean();
	}

	return sprintf(
		'<a href="%1$s" class="custom-logo-link aa-custom-logo" rel="home"><img src="%2$s" class="aa-logo-mark" alt="%3$s"><span class="aa-logo-text"><span>Canberra &amp; District Central Service Office<br></span><span>Alcoholics Anonymous</span></span></a>',
		esc_url( home_url( '/' ) ),
		esc_url( aac_site_theme_file_uri( 'assets/img/aa-logo.svg' ) ),
		esc_attr__( 'Alcoholics Anonymous logo', 'ACT_District_CSO_Child' )
	);
}

function aac_site_primary_menu_fallback() {
	echo '<ul id="primary-menu" class="aa-header-menu nav-links">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home Page', 'ACT_District_CSO_Child' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/meetings/' ) ) . '">' . esc_html__( 'Meetings', 'ACT_District_CSO_Child' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/about-aa/' ) ) . '">' . esc_html__( 'About A.A.', 'ACT_District_CSO_Child' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/members/' ) ) . '">' . esc_html__( 'Members', 'ACT_District_CSO_Child' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'Contact', 'ACT_District_CSO_Child' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/shop/' ) ) . '">' . esc_html__( 'Literature Shop', 'ACT_District_CSO_Child' ) . '</a></li>';
	echo '</ul>';
}

function aac_site_render_header_block() {
	ob_start();
	?>
	<header class="site-header aa-site-header" role="banner">
		<div class="aa-header-nav">
			<div class="aa-header-nav__inner aa-header-container">
				<div class="site-branding aa-site-branding">
					<?php echo aac_site_render_logo(); ?>
				</div>

				<button class="aa-menu-toggle" type="button" aria-controls="primary-menu" aria-expanded="false">
					<?php esc_html_e( 'Menu', 'ACT_District_CSO_Child' ); ?>
				</button>

				<nav id="site-navigation" class="main-navigation aa-main-navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'ACT_District_CSO_Child' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu_id'        => 'primary-menu',
							'menu_class'     => 'aa-header-menu nav-links',
							'container'      => false,
							'depth'          => 2,
							'fallback_cb'    => 'aac_site_primary_menu_fallback',
						)
					);
					?>
				</nav>
			</div>
		</div>
	</header>
	<?php
	return ob_get_clean();
}

function aac_site_footer_nav( $location ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location'  => $location,
				'container'       => 'nav',
				'container_class' => 'footer-navigation',
				'menu_class'      => 'footer-menu-links',
				'depth'           => 1,
			)
		);
	}
}

function aac_site_render_footer_block() {
	ob_start();
	?>
	<footer class="aa-footer">
		<div class="aa-footer-container">
			<div class="aa-footer-col aa-footer-brand">
				<?php
				$footer_image_url = function_exists( 'custom_site_details_footer_image_url' ) ? custom_site_details_footer_image_url() : aac_site_theme_file_uri( 'assets/img/aa-logo.svg' );
				if ( $footer_image_url ) :
					?>
					<img class="aa-footer-logo" id="aa-footer-logo" src="<?php echo esc_url( $footer_image_url ); ?>" alt="Footer logo">
				<?php endif; ?>

				<?php
				$footer_text = function_exists( 'custom_site_details_footer_text' ) ? custom_site_details_footer_text() : 'Supporting central service across Canberra and surrounding districts.<br><br>Find meetings, stay connected, and get support - One day at a time.';
				if ( $footer_text ) :
					?>
					<div class="aa-footer-about"><?php echo wp_kses_post( $footer_text ); ?></div>
				<?php endif; ?>
			</div>

			<div class="sitemap-container">
				<div class="aa-footer-col">
					<h4><?php esc_html_e( 'Sitemap', 'ACT_District_CSO_Child' ); ?></h4>
					<?php aac_site_footer_nav( 'ACT-District-CSO-Child-footer-menu-1' ); ?>
				</div>
				<div class="aa-footer-col" id="sitemap-2">
					<?php aac_site_footer_nav( 'ACT-District-CSO-Child-footer-menu-2' ); ?>
				</div>
			</div>

			<div class="aa-footer-col">
				<h4><?php esc_html_e( 'Quick Links', 'ACT_District_CSO_Child' ); ?></h4>
				<?php aac_site_footer_nav( 'ACT-District-CSO-Child-quick-links' ); ?>
			</div>

			<div class="aa-footer-col">
				<h4 class="aa-footer-heading"><?php esc_html_e( 'Contact', 'ACT_District_CSO_Child' ); ?></h4>
				<ul class="aa-footer-contact">
					<?php if ( function_exists( 'custom_site_details_email_link' ) && custom_site_details_email_link( array( 'class' => 'footer-email' ) ) ) : ?>
						<li>Email: <?php echo wp_kses_post( custom_site_details_email_link( array( 'class' => 'footer-email' ) ) ); ?></li>
					<?php else : ?>
						<li>Email: <a class="footer-email" href="mailto:info@aacanberra.org">info@aacanberra.org</a></li>
					<?php endif; ?>

					<?php if ( function_exists( 'custom_site_details_phone_link' ) && custom_site_details_phone_link( array( 'class' => 'footer-phone' ) ) ) : ?>
						<li>Phone: <?php echo wp_kses_post( custom_site_details_phone_link( array( 'class' => 'footer-phone' ) ) ); ?> <strong>(24/7)</strong></li>
					<?php else : ?>
						<li>Phone: <a class="footer-phone" href="tel:+61262873020">(02) 6287 3020</a> <strong>(24/7)</strong></li>
					<?php endif; ?>

					<?php if ( function_exists( 'custom_site_details_opening_hours' ) && custom_site_details_opening_hours() ) : ?>
						<li>Office Hours: <?php echo wp_kses_post( custom_site_details_opening_hours() ); ?></li>
					<?php else : ?>
						<li>Office Hours: 10:00am - 3:00pm<br>Monday, Wednesday, Friday<br>Closed on Public Holidays</li>
					<?php endif; ?>

					<?php if ( function_exists( 'custom_site_details_address' ) && custom_site_details_address() ) : ?>
						<li><?php echo wp_kses_post( custom_site_details_address() ); ?></li>
					<?php else : ?>
						<li>Grant Cameron Offices<br>27 Mulley Street<br>Holder ACT 2611</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>

		<div class="aa-footer-bottom">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> ACT &amp; District Central Service Office of Alcoholics Anonymous. All rights reserved.</p>
			<?php
			$footer_disclaimer = function_exists( 'custom_site_details_footer_disclaimer' ) ? custom_site_details_footer_disclaimer() : 'There are no dues or fees for A.A. membership; we are self-supporting through our own contributions.';
			if ( $footer_disclaimer ) :
				?>
				<div class="aa-footer-note"><?php echo wp_kses_post( $footer_disclaimer ); ?></div>
			<?php endif; ?>
		</div>
	</footer>
	<?php
	return ob_get_clean();
}

function aac_site_preview_text( $post ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return '';
	}

	if ( has_excerpt( $post ) ) {
		$text = get_the_excerpt( $post );
	} else {
		$text = do_blocks( $post->post_content );
		$text = strip_shortcodes( $text );
		$text = preg_replace( '/<\/(p|div|li|h[1-6])>/i', ' ', $text );
		$text = preg_replace( '/<br\s*\/?>/i', ' ', $text );
		$text = wp_strip_all_tags( $text );
		$text = html_entity_decode( $text, ENT_QUOTES, get_bloginfo( 'charset' ) );
	}

	return trim( preg_replace( '/\s+/', ' ', $text ) );
}

function aac_site_render_home_block() {
	ob_start();
	?>
	<div class="aa-site-container">
		<main id="primary" class="site-main aa-template aa-template-home">
			<section class="hero-overlay" aria-labelledby="home-hero-title">
				<div class="hero-image-wrapper">
					<img fetchpriority="high" src="<?php echo esc_url( aac_site_theme_file_uri( 'assets/img/front-page-vector.jpg' ) ); ?>" alt="Illustration of a person reflecting with a drink" class="hero-illustration">
					<div class="hero-text">
						<h1 id="home-hero-title">Do you think you have a drinking problem?</h1>
						<p><strong>Is it costing you more than money?</strong></p>
						<p><strong>We could help...</strong></p>
						<p>
							<?php
							if ( function_exists( 'custom_site_details_phone_link' ) ) {
								echo wp_kses_post( custom_site_details_phone_link( array( 'class' => 'home-page-phone-link' ) ) );
							} else {
								echo '<a class="home-page-phone-link" href="tel:+61262873020">(02) 6287 3020</a>';
							}
							?>
						</p>
						<a href="<?php echo esc_url( home_url( '/meetings/' ) ); ?>" class="button">Find a Meeting</a>
					</div>
				</div>
			</section>

			<div class="home-content container">
				<section class="section-preview">
					<h2>New to AA?</h2>
					<?php
					$about_page = get_page_by_path( 'about-aa' );
					if ( $about_page ) :
						$about_post = get_post( $about_page->ID );
						$content    = get_the_excerpt( $about_post );
						if ( ! has_excerpt( $about_post ) ) {
							$content = wp_trim_words( aac_site_preview_text( $about_post ), 25, '...' );
						}
						?>
						<p><?php echo esc_html( $content ); ?></p>
						<p><a href="<?php echo esc_url( get_permalink( $about_page->ID ) ); ?>">Learn more</a></p>
					<?php else : ?>
						<p>Alcoholics Anonymous is a community of people who share their experience, strength and hope with each other so they can recover from alcoholism.</p>
						<p><a href="<?php echo esc_url( home_url( '/about-aa/' ) ); ?>">Learn more</a></p>
					<?php endif; ?>
				</section>

				<section>
					<?php
					if ( shortcode_exists( 'pstn_optin_form' ) ) {
						echo do_shortcode( '[pstn_optin_form]' );
					}
					?>
				</section>

				<?php
				$notices_query = new WP_Query(
					array(
						'post_type'      => 'notices',
						'posts_per_page' => 2,
						'category_name'  => 'notices',
						'orderby'        => 'date',
						'order'          => 'DESC',
					)
				);

				if ( $notices_query->have_posts() ) :
					?>
					<section class="section-preview notices">
						<h2>Important Notices &amp; Local Updates</h2>

						<?php while ( $notices_query->have_posts() ) : ?>
							<?php $notices_query->the_post(); ?>
							<div class="preview-item compact-preview">
								<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<p class="post-date"><?php echo esc_html( get_the_date( 'j M Y' ) ); ?></p>
								<p><?php echo esc_html( wp_trim_words( aac_site_preview_text( get_post() ), 14, '...' ) ); ?></p>
							</div>
						<?php endwhile; ?>

						<p><a href="<?php echo esc_url( home_url( '/notices/' ) ); ?>">View all notices</a></p>
					</section>
					<?php
				endif;
				wp_reset_postdata();
				?>

				<section class="section-preview">
					<h2>Local &amp; National Events</h2>
					<?php
					$events_query = new WP_Query(
						array(
							'post_type'      => 'tribe_events',
							'posts_per_page' => 3,
							'meta_key'       => '_EventStartDate',
							'meta_value'     => current_time( 'Y-m-d H:i:s' ),
							'meta_compare'   => '>=',
							'meta_type'      => 'DATETIME',
							'orderby'        => 'meta_value',
							'order'          => 'ASC',
						)
					);

					if ( $events_query->have_posts() ) :
						while ( $events_query->have_posts() ) :
							$events_query->the_post();
							?>
							<div class="preview-item compact-preview">
								<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<?php if ( function_exists( 'tribe_get_start_date' ) ) : ?>
									<p class="event-date">
										<?php
										echo esc_html(
											function_exists( 'tribe_event_is_multiday' ) && tribe_event_is_multiday()
												? tribe_get_start_date( null, true, 'j M Y' ) . ' to ' . tribe_get_end_date( null, true, 'j M Y' )
												: tribe_get_start_date( null, true, 'j M Y g:ia' )
										);
										?>
									</p>
								<?php endif; ?>
								<p><?php echo esc_html( wp_trim_words( aac_site_preview_text( get_post() ), 18, '...' ) ); ?></p>
							</div>
							<?php
						endwhile;
						wp_reset_postdata();
					else :
						?>
						<p>No upcoming local or national events at the moment.</p>
					<?php endif; ?>

					<p><a href="<?php echo esc_url( home_url( '/events/' ) ); ?>">View all events</a></p>
				</section>
			</div>
		</main>
	</div>
	<?php
	return ob_get_clean();
}

function aac_site_render_form_block( $attributes ) {
	$form_id = isset( $attributes['formId'] ) ? sanitize_key( $attributes['formId'] ) : '';

	if ( ! $form_id || ! function_exists( 'aa_forms_render' ) ) {
		return '';
	}

	return aa_forms_render( $form_id );
}

function aac_site_render_contact_page_block() {
	ob_start();
	?>
	<main id="primary" class="site-main aa-template aa-template-contact">
		<div class="aa-container">
			<section class="aa-section">
				<h1><?php the_title(); ?></h1>
				<p class="aa-lead">For enquiries about meetings, literature, group information or local AA service, please contact the Canberra &amp; District Central Service Office.</p>
			</section>

			<div class="aa-grid aa-grid-2 aa-grid-stretch">
				<section class="aa-card aa-card-accent">
					<h2>Contact Details</h2>
					<p class="aa-lead-small">Contact is also available via:</p>

					<div class="cso-contact-item">
						<span>Phone</span>
						<?php
						if ( function_exists( 'custom_site_details_phone_link' ) ) {
							echo wp_kses_post( custom_site_details_phone_link() );
						} else {
							echo '<a href="tel:+61262873020">(02) 6287 3020</a>';
						}
						?>
						<div class="cso-contact-badge">Available 24/7</div>
					</div>

					<div class="cso-contact-item">
						<span>Email</span>
						<?php
						if ( function_exists( 'custom_site_details_email_link' ) ) {
							echo wp_kses_post( custom_site_details_email_link() );
						} else {
							echo '<a href="mailto:info@aacanberra.org">info@aacanberra.org</a>';
						}
						?>
					</div>

					<div class="aa-divider"></div>
					<h3>Need help finding a meeting?</h3>
					<p>View the current meeting list online, or contact the CSO if you'd like help finding a meeting suitable for you.</p>
					<p><a class="aa-button" href="<?php echo esc_url( home_url( '/meetings/' ) ); ?>">View Meetings</a></p>
				</section>

				<section class="aa-card aa-card-accent cso-contact-form">
					<h2>Send a Message</h2>
					<?php echo aac_site_render_form_block( array( 'formId' => 'contact' ) ); ?>
				</section>
			</div>
		</div>
	</main>
	<?php
	return ob_get_clean();
}

function aac_site_render_woocommerce_content_block() {
	if ( ! function_exists( 'woocommerce_content' ) ) {
		return '';
	}

	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_category() || is_product_tag() ) ) {
		add_filter( 'woocommerce_show_page_title', '__return_false' );
	}

	ob_start();
	?>
	<main id="primary" class="site-main">
		<div class="aa-woocommerce-container">
			<?php if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_category() || is_product_tag() ) ) : ?>
				<section class="aac-shop-archive-intro">
					<p class="aac-shop-archive-intro__eyebrow">
						<?php echo is_shop() ? esc_html__( 'Online literature catalogue', 'ACT_District_CSO_Child' ) : esc_html__( 'Literature Shop', 'ACT_District_CSO_Child' ); ?>
					</p>
					<h1><?php woocommerce_page_title(); ?></h1>
					<?php if ( is_shop() ) : ?>
						<p><?php esc_html_e( 'Conference-approved and other literature, cards, banners, and service resources for members, groups, and committees across Canberra and the surrounding districts.', 'ACT_District_CSO_Child' ); ?></p>
					<?php endif; ?>
				</section>
			<?php endif; ?>

			<?php woocommerce_content(); ?>
		</div>
	</main>
	<?php
	return ob_get_clean();
}

function aac_site_locate_woocommerce_template( $template, $template_name, $template_path ) {
	$plugin_template = AAC_SITE_PLUGIN_DIR . '/templates/woocommerce/' . ltrim( $template_name, '/' );

	if ( file_exists( $plugin_template ) ) {
		return $plugin_template;
	}

	return $template;
}
add_filter( 'woocommerce_locate_template', 'aac_site_locate_woocommerce_template', 10, 3 );
