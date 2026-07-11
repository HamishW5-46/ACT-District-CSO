<?php
/**
 * Template Name: AA Resources Page
 *
 * For Members, Public Information resources, Safety resources, Service documents, etc.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main aa-template aa-template-resources">

	<div class="aa-container">
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</div>

</main>

<?php
get_footer();