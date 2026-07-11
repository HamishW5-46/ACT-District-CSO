<?php
/**
 * Template Name: AA Information Page
 *
 * For long-form guide pages such as About A.A., New to A.A., Family & Friends, etc.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main aa-template aa-template-guide">

	<div class="aa-guide-page">
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