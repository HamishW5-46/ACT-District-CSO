<?php
/* Template Name: Beginners Sign-Up Form */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main aa-template aa-template-beginners">

	<div class="aa-container aa-beginners-form">

		<div class="aa-page-content">
			<?php echo do_shortcode( '[aa_form id="beginners"]' ); ?>
		</div>

	</div>

</main>

<?php
get_footer();