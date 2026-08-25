<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce already labels the visible product tab. Suppress the repeated
 * panel heading so single product descriptions do not show "Description" twice.
 */
function aac_woocommerce_product_description_heading() {
	return '';
}
add_filter( 'woocommerce_product_description_heading', 'aac_woocommerce_product_description_heading' );

