<?php
/**
 * Template Name: AA Blank Canvas
 *
 * No title, no wrapper styling. Gutenberg controls the page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main aa-template aa-template-blank">

	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>

</main>

<?php
get_footer();