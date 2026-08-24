<?php
/* Template Name: Home */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$aa_home_preview_text = static function ( $post ) {
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
};
?>

<main id="primary" class="site-main aa-template aa-template-home">
	<section class="hero-overlay" aria-labelledby="home-hero-title">
		<div class="hero-image-wrapper">
			<img fetchpriority="high" src="<?php echo esc_url( get_theme_file_uri( '/assets/img/front-page-vector.jpg' ) ); ?>" alt="Illustration of a person reflecting with a drink" class="hero-illustration">
			<div class="hero-text">
				<h1 id="home-hero-title">Do you think you have a drinking problem?</h1>
				<p><strong>Is it costing you more than money?</strong></p>
				<p><strong>We could help...</strong></p>
				<?php if ( function_exists( 'custom_site_details_phone_link' ) ) : ?>
					<p><?php echo wp_kses_post( custom_site_details_phone_link( array( 'class' => 'home-page-phone-link' ) ) ); ?></p>
				<?php endif; ?>
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

				if ( has_excerpt( $about_post ) ) {
					$content = get_the_excerpt( $about_post );
				} else {
					$content = $about_post->post_content;
					$content = do_blocks( $content );
					$content = strip_shortcodes( $content );
					$content = wp_strip_all_tags( $content );
					$content = trim( preg_replace( '/\s+/', ' ', $content ) );
					$content = wp_trim_words( $content, 25, '...' );
				}
				?>
				<p><?php echo esc_html( $content ); ?></p>
				<p><a href="<?php echo esc_url( get_permalink( $about_page->ID ) ); ?>">Learn more</a></p>
			<?php endif; ?>
		</section>

		<section>
			<?php echo do_shortcode( '[pstn_optin_form]' ); ?>
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
						<p><?php echo esc_html( wp_trim_words( $aa_home_preview_text( get_post() ), 14, '...' ) ); ?></p>
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
			$now = current_time( 'Y-m-d H:i:s' );

			$events_query = new WP_Query(
				array(
					'post_type'      => 'tribe_events',
					'posts_per_page' => 3,
					'meta_key'       => '_EventStartDate',
					'meta_value'     => $now,
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

						<?php
						if ( function_exists( 'tribe_get_start_date' ) ) :
							$date = tribe_event_is_multiday()
								? tribe_get_start_date( null, true, 'j M Y' ) . ' to ' . tribe_get_end_date( null, true, 'j M Y' )
								: tribe_get_start_date( null, true, 'j M Y g:ia' );
							?>
							<p class="event-date"><?php echo esc_html( $date ); ?></p>
						<?php endif; ?>

						<p><?php echo esc_html( wp_trim_words( $aa_home_preview_text( get_post() ), 18, '...' ) ); ?></p>
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

<?php
get_footer();
