<?php
get_header();
?>

<main id="primary" class="site-main aa-page">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<?php
		$is_woocommerce_page = function_exists( 'aac_is_woocommerce_screen' ) && aac_is_woocommerce_screen();
		$content_classes     = $is_woocommerce_page ? 'aa-container aa-container-wide aa-woocommerce-block-page' : 'aa-container aa-container--narrow';
		?>

		<section class="aa-page-hero">
			<div class="aa-container">
				<h1><?php the_title(); ?></h1>
			</div>
		</section>

		<section class="aa-page-content">
			<div class="<?php echo esc_attr( $content_classes ); ?>">
				<?php the_content(); ?>
			</div>
		</section>
	<?php endwhile; ?>
</main>

<?php
get_footer();
