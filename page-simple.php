<?php
/**
 * Template Name: AA Simple Page
 *
 * For normal pages that need a consistent wrapper and title.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main aa-template aa-template-simple">

	<div class="aa-container aa-simple-page">

		<header class="aa-page-hero aa-page-hero-simple">
			<h1><?php the_title(); ?></h1>
		</header>

		<div class="aa-page-content">
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