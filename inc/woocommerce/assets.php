<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check whether the current request is a WooCommerce-owned screen.
 */
function aac_is_woocommerce_screen() {
	return function_exists( 'is_woocommerce' )
		&& (
			is_woocommerce()
			|| ( function_exists( 'is_cart' ) && is_cart() )
			|| ( function_exists( 'is_checkout' ) && is_checkout() )
			|| ( function_exists( 'is_account_page' ) && is_account_page() )
		);
}

/**
 * Enqueue the unified WooCommerce theme assets.
 */
function aac_enqueue_woocommerce_assets() {
	if ( ! aac_is_woocommerce_screen() ) {
		return;
	}

	$base_dependency = wp_style_is( 'ACT-District-CSO-Child-components', 'enqueued' )
		? 'ACT-District-CSO-Child-components'
		: 'ACT-District-CSO-Child-style';

	$styles = array(
		'aac-woocommerce'  => array(
			'path'         => '/assets/css/woocommerce/base.css',
			'dependencies' => array( $base_dependency ),
		),
		'aac-shop-filters' => array(
			'path'         => '/assets/css/woocommerce/shop-filters.css',
			'dependencies' => array( 'aac-woocommerce' ),
			'condition'    => function_exists( 'aac_is_shop_filter_screen' ) && aac_is_shop_filter_screen(),
		),
		'aac-account-auth' => array(
			'path'         => '/assets/css/woocommerce/account-auth.css',
			'dependencies' => array( 'aac-woocommerce' ),
			'condition'    => function_exists( 'aac_is_account_auth_screen' ) && aac_is_account_auth_screen(),
		),
	);

	foreach ( $styles as $handle => $style ) {
		if ( isset( $style['condition'] ) && ! $style['condition'] ) {
			continue;
		}

		$asset_path = get_stylesheet_directory() . $style['path'];

		if ( ! file_exists( $asset_path ) ) {
			continue;
		}

		wp_enqueue_style(
			$handle,
			get_stylesheet_directory_uri() . $style['path'],
			$style['dependencies'],
			(string) filemtime( $asset_path )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'aac_enqueue_woocommerce_assets', 25 );

/**
 * Prevent the "Update cart" button from flashing before the main WooCommerce
 * stylesheet finishes loading. It is revealed by script once the cart changes.
 */
function aac_print_cart_critical_styles() {
	if ( ! function_exists( 'is_cart' ) || ! is_cart() ) {
		return;
	}
	?>
	<style id="aac-cart-critical-css">
		.woocommerce-cart form.woocommerce-cart-form:not(.aac-cart-is-dirty) table.cart td.actions button[name="update_cart"],
		.woocommerce-cart form.woocommerce-cart-form:not(.aac-cart-is-dirty) table.cart td.actions input[name="update_cart"] {
			display: none !important;
		}

		.woocommerce-cart form.woocommerce-cart-form:not(.aac-cart-is-dirty) table.cart td.actions:not(:has(.coupon)) {
			display: none !important;
		}

		.woocommerce-cart form.woocommerce-cart-form.aac-cart-is-dirty table.cart td.actions button[name="update_cart"]:disabled,
		.woocommerce-cart form.woocommerce-cart-form.aac-cart-is-dirty table.cart td.actions button[name="update_cart"]:disabled[disabled],
		.woocommerce-cart form.woocommerce-cart-form.aac-cart-is-dirty table.cart td.actions input[name="update_cart"]:disabled,
		.woocommerce-cart form.woocommerce-cart-form.aac-cart-is-dirty table.cart td.actions input[name="update_cart"]:disabled[disabled] {
			display: none !important;
		}

		.woocommerce-cart form.woocommerce-cart-form.aac-cart-is-dirty table.cart td.actions:has(> button[name="update_cart"]:disabled):not(:has(.coupon)),
		.woocommerce-cart form.woocommerce-cart-form.aac-cart-is-dirty table.cart td.actions:has(> input[name="update_cart"]:disabled):not(:has(.coupon)) {
			display: none !important;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'aac_print_cart_critical_styles', 1 );

/**
 * Reveal the cart update button only after the customer changes the cart form.
 */
function aac_print_cart_update_button_script() {
	if ( ! function_exists( 'is_cart' ) || ! is_cart() ) {
		return;
	}
	?>
	<script id="aac-cart-update-button-js">
		document.addEventListener('DOMContentLoaded', function () {
			var form = document.querySelector('.woocommerce-cart form.woocommerce-cart-form');

			if (!form) {
				return;
			}

			var markCartDirty = function () {
				form.classList.add('aac-cart-is-dirty');
			};

			form.addEventListener('change', markCartDirty);
			form.addEventListener('input', function (event) {
				if (event.target && event.target.matches('input.qty, input[name^="cart"][name$="[qty]"]')) {
					markCartDirty();
				}
			});
		});
	</script>
	<?php
}
add_action( 'wp_footer', 'aac_print_cart_update_button_script', 20 );

/**
 * Add a body class for the standalone WooCommerce theme layer.
 */
function aac_woocommerce_body_classes( $classes ) {
	if ( aac_is_woocommerce_screen() ) {
		$classes[] = 'aac-woocommerce-page';
	}

	return $classes;
}
add_filter( 'body_class', 'aac_woocommerce_body_classes' );
