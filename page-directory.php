<?php
/**
 * Template Name: AA Card Directory
 *
 * For committee positions, service opportunities, trusted servants, etc.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main aa-template aa-template-directory">

	<div class="aa-container aa-directory-page">

		<header class="aa-page-hero">
			<h1><?php the_title(); ?></h1>

			<?php if ( has_excerpt() ) : ?>
				<p><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</header>

		<div class="aa-directory-content">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>

	</div>

</main>

<?php
get_footer();