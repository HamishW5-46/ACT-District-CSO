<?php
/*
Template Name: Default Page Template
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main aa-page aa-template aa-template-default">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>

		<section class="aa-page-hero">
			<div class="aa-container">
				<h1><?php the_title(); ?></h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="aa-subtitle"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</section>

		<section class="aa-page-content">
			<div class="aa-container aa-container--narrow">
				<?php the_content(); ?>
			</div>
		</section>
	<?php endwhile; ?>
</main>

<?php
get_footer();
