<?php
/* Template Name: PIPA Form */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main aa-template aa-template-pipa">

	<div class="aa-container aa-pipa-form">

		<div class="aa-page-content">
			<?php echo do_shortcode( '[aa_form id="pipa"]' ); ?>
		</div>

	</div>

</main>

<?php
get_footer();