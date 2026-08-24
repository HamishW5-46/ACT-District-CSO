<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( is_shop() || is_product_category() || is_product_tag() ) {
	add_filter( 'woocommerce_show_page_title', '__return_false' );
}
?>

<main id="primary" class="site-main">
	<div class="aa-woocommerce-container">
		<?php if ( is_shop() || is_product_category() || is_product_tag() ) : ?>
			<section class="aac-shop-archive-intro">
				<p class="aac-shop-archive-intro__eyebrow">
					<?php
					if ( is_shop() ) {
						esc_html_e( 'Online literature catalogue', 'ACT_District_CSO_Child' );
					} else {
						esc_html_e( 'Literature Shop', 'ACT_District_CSO_Child' );
					}
					?>
				</p>
				<h1><?php woocommerce_page_title(); ?></h1>
				<?php if ( is_shop() ) : ?>
					<p><?php esc_html_e( 'Conference-approved and other literature, cards, banners, and service resources for members, groups, and committees across Canberra and the surrounding districts.', 'ACT_District_CSO_Child' ); ?></p>
				<?php endif; ?>
			</section>
		<?php endif; ?>

		<?php woocommerce_content(); ?>
	</div>
</main>

<?php
get_footer();
