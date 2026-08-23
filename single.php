<?php
get_header();
?>

<main id="primary" class="site-main aa-page">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<section class="aa-page-hero">
				<div class="aa-container">
					<h1><?php the_title(); ?></h1>
				</div>
			</section>

			<section class="aa-page-content">
				<div class="aa-container aa-container--narrow">
					<?php the_content(); ?>
				</div>
			</section>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();
