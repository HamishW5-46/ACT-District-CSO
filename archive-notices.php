<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main aa-page aa-template aa-template-notices-archive">
	<section class="aa-page-hero">
		<div class="aa-container">
			<h1>Notices &amp; Updates</h1>
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
						<p class="post-date"><?php echo esc_html( get_the_date( 'j M Y' ) ); ?></p>
						<div class="entry-content">
							<?php the_excerpt(); ?>
						</div>
					</article>
				<?php endwhile; ?>

				<?php the_posts_navigation(); ?>
			<?php else : ?>
				<p>No notices or updates at the moment.</p>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
