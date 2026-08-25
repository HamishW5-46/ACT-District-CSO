<?php
get_header();
?>

<main id="primary" class="site-main aa-page">
	<section class="aa-page-content">
		<div class="aa-container aa-container--narrow">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'aa-card aa-card-white' ); ?>>
						<h1 class="entry-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h1>
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
