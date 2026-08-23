<?php
get_header();
?>

<main id="primary" class="site-main aa-page">
	<section class="aa-page-hero">
		<div class="aa-container">
			<h1><?php the_archive_title(); ?></h1>
			<?php the_archive_description( '<p class="aa-subtitle">', '</p>' ); ?>
		</div>
	</section>

	<section class="aa-page-content">
		<div class="aa-container aa-container--narrow">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'aa-card aa-card-white' ); ?>>
						<h2 class="entry-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>
						<div class="entry-content">
							<?php the_excerpt(); ?>
						</div>
					</article>
				<?php endwhile; ?>

				<?php the_posts_navigation(); ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No content found.', 'ACT_District_CSO_Child' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
