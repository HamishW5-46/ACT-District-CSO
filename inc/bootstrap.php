<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register standalone theme support and navigation locations.
 */
function act_district_cso_setup() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'editor-styles' );
	add_editor_style(
		array(
			'style.css',
			'assets/css/components.css',
			'assets/css/contact.css',
			'assets/css/about-aa.css',
			'assets/css/forms.css',
			'assets/css/woocommerce/base.css',
			'assets/css/woocommerce/shop-filters.css',
			'assets/css/woocommerce/account-auth.css',
			'assets/css/editor.css',
		)
	);
	add_theme_support(
		'html5',
		array(
			'caption',
			'comment-form',
			'comment-list',
			'gallery',
			'navigation-widgets',
			'script',
			'search-form',
			'style',
		)
	);
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary'                        => __( 'Primary Menu', 'ACT_District_CSO' ),
			'ACT-District-CSO-footer-menu-1' => __( 'Footer Menu 1', 'ACT_District_CSO' ),
			'ACT-District-CSO-footer-menu-2' => __( 'Footer Menu 2', 'ACT_District_CSO' ),
			'ACT-District-CSO-quick-links'   => __( 'Quick Links', 'ACT_District_CSO' ),
		)
	);
}
add_action( 'after_setup_theme', 'act_district_cso_setup' );

/**
 * Return a cache-busting asset version.
 */
function act_district_cso_asset_version( $relative_path ) {
	$asset_path = get_stylesheet_directory() . $relative_path;

	if ( file_exists( $asset_path ) ) {
		return filemtime( $asset_path );
	}

	return wp_get_theme()->get( 'Version' );
}

/**
 * Add a late cache-busting query arg for theme-owned assets.
 */
function act_district_cso_versioned_asset_src( $src, $handle ) {
	$assets = array(
		'ACT-District-CSO-style'                     => '/style.css',
		'ACT-District-CSO-components'                => '/assets/css/components.css',
		'ACT-District-CSO-navigation'                => '/assets/js/navigation.js',
		'ACT-District-CSO-shop-filters'              => '/assets/js/shop-filters.js',
		'ACT-District-CSO-editor'                    => '/assets/css/editor.css',
		'ACT-District-CSO-page-form-contact-contact' => '/assets/css/contact.css',
		'ACT-District-CSO-page-information-about-aa' => '/assets/css/about-aa.css',
		'ACT-District-CSO-woocommerce-base'          => '/assets/css/woocommerce/base.css',
		'ACT-District-CSO-woocommerce-shop-filters'  => '/assets/css/woocommerce/shop-filters.css',
		'ACT-District-CSO-woocommerce-account-auth'  => '/assets/css/woocommerce/account-auth.css',
	);

	if ( ! isset( $assets[ $handle ] ) ) {
		return $src;
	}

	return add_query_arg(
		'aacv',
		act_district_cso_asset_version( $assets[ $handle ] ),
		remove_query_arg( array( 'ver', 'aacv' ), $src )
	);
}
add_filter( 'style_loader_src', 'act_district_cso_versioned_asset_src', 9999, 2 );
add_filter( 'script_loader_src', 'act_district_cso_versioned_asset_src', 9999, 2 );

/**
 * Keep theme stylesheets out of CSS optimization so filemtime cache busting is visible.
 */
function act_district_cso_style_loader_tag( $html, $handle, $href, $media ) {
	$handles = array(
		'ACT-District-CSO-style',
		'ACT-District-CSO-components',
		'ACT-District-CSO-editor',
		'ACT-District-CSO-page-form-contact-contact',
		'ACT-District-CSO-page-information-about-aa',
		'ACT-District-CSO-woocommerce-base',
		'ACT-District-CSO-woocommerce-shop-filters',
		'ACT-District-CSO-woocommerce-account-auth',
	);

	if ( ! in_array( $handle, $handles, true ) || false !== strpos( $html, 'data-no-optimize=' ) ) {
		return $html;
	}

	return str_replace( '<link ', '<link data-no-optimize="1" ', $html );
}
add_filter( 'style_loader_tag', 'act_district_cso_style_loader_tag', 10, 4 );

/**
 * Return the active page template slug for classic or block templates.
 */
function act_district_cso_current_page_template_slug() {
	if ( ! is_singular( 'page' ) ) {
		return '';
	}

	$template = get_page_template_slug( get_queried_object_id() );

	if ( ! is_string( $template ) || '' === $template ) {
		return '';
	}

	$slug = preg_replace( '/\.php$/', '', $template );

	return is_string( $slug ) ? $slug : '';
}

/**
 * Determine whether the current request is handled by WooCommerce.
 */
function act_district_cso_is_woocommerce_context() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}

	return (
		( function_exists( 'is_woocommerce' ) && is_woocommerce() ) ||
		( function_exists( 'is_cart' ) && is_cart() ) ||
		( function_exists( 'is_checkout' ) && is_checkout() ) ||
		( function_exists( 'is_account_page' ) && is_account_page() )
	);
}

/**
 * Determine whether the current request is a product archive with shop filters.
 */
function act_district_cso_is_shop_filter_context() {
	return (
		( function_exists( 'is_shop' ) && is_shop() ) ||
		is_post_type_archive( 'product' ) ||
		is_tax( array( 'product_cat', 'product_tag' ) )
	);
}

/**
 * Enqueue standalone theme styles and scripts.
 */
function act_district_cso_enqueue_styles() {
	$child_handle      = 'ACT-District-CSO-style';
	$components_handle = 'ACT-District-CSO-components';

	wp_enqueue_style(
		$child_handle,
		get_stylesheet_uri(),
		array(),
		act_district_cso_asset_version( '/style.css' )
	);

	wp_enqueue_style(
		$components_handle,
		get_stylesheet_directory_uri() . '/assets/css/components.css',
		array( $child_handle ),
		act_district_cso_asset_version( '/assets/css/components.css' )
	);

	$template_styles  = array(
		'page-form-contact' => 'contact.css',
		'page-information'  => 'about-aa.css',
	);
	$current_template = act_district_cso_current_page_template_slug();

	foreach ( $template_styles as $template => $stylesheet ) {
		if ( $current_template === $template || is_page_template( $template . '.php' ) ) {
			act_district_cso_enqueue_page_style(
				$template,
				$stylesheet,
				$components_handle
			);
		}
	}

	if ( act_district_cso_is_woocommerce_context() ) {
		wp_enqueue_style(
			'ACT-District-CSO-woocommerce-base',
			get_stylesheet_directory_uri() . '/assets/css/woocommerce/base.css',
			array( $components_handle ),
			act_district_cso_asset_version( '/assets/css/woocommerce/base.css' )
		);
	}

	if ( act_district_cso_is_shop_filter_context() ) {
		wp_enqueue_style(
			'ACT-District-CSO-woocommerce-shop-filters',
			get_stylesheet_directory_uri() . '/assets/css/woocommerce/shop-filters.css',
			array( 'ACT-District-CSO-woocommerce-base' ),
			act_district_cso_asset_version( '/assets/css/woocommerce/shop-filters.css' )
		);

		wp_enqueue_script(
			'ACT-District-CSO-shop-filters',
			get_stylesheet_directory_uri() . '/assets/js/shop-filters.js',
			array( 'jquery' ),
			act_district_cso_asset_version( '/assets/js/shop-filters.js' ),
			true
		);

		wp_localize_script(
			'ACT-District-CSO-shop-filters',
			'aacShopFilters',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'aac_shop_filters' ),
			)
		);
	}

	if ( function_exists( 'is_account_page' ) && is_account_page() && ! is_user_logged_in() ) {
		wp_enqueue_style(
			'ACT-District-CSO-woocommerce-account-auth',
			get_stylesheet_directory_uri() . '/assets/css/woocommerce/account-auth.css',
			array( 'ACT-District-CSO-woocommerce-base' ),
			act_district_cso_asset_version( '/assets/css/woocommerce/account-auth.css' )
		);
	}

	wp_enqueue_script(
		'ACT-District-CSO-navigation',
		get_stylesheet_directory_uri() . '/assets/js/navigation.js',
		array(),
		act_district_cso_asset_version( '/assets/js/navigation.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'act_district_cso_enqueue_styles', 15 );

/**
 * Keep the mobile navigation controller available before the first menu tap.
 */
function act_district_cso_navigation_script_loader_tag( $tag, $handle, $src ) {
	$handles = array(
		'ACT-District-CSO-navigation',
		'ACT-District-CSO-shop-filters',
	);

	if ( ! in_array( $handle, $handles, true ) ) {
		return $tag;
	}

	if ( false !== strpos( $tag, 'data-no-optimize=' ) ) {
		return $tag;
	}

	return str_replace(
		'<script ',
		'<script data-no-defer="1" data-no-optimize="1" ',
		$tag
	);
}
add_filter( 'script_loader_tag', 'act_district_cso_navigation_script_loader_tag', 10, 3 );

/**
 * Add stable body classes used by template-specific CSS.
 */
function act_district_cso_body_classes( $classes ) {
	if ( act_district_cso_is_woocommerce_context() ) {
		$classes[] = 'aac-woocommerce-page';
	}

	if ( function_exists( 'is_account_page' ) && is_account_page() && ! is_user_logged_in() ) {
		$classes[] = 'aac-account-auth-page';
	}

	return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'act_district_cso_body_classes' );

/**
 * Keep WooCommerce's automatic block hooks from adding account/cart controls to
 * the public header. The shop still has its normal account and cart pages.
 */
function act_district_cso_remove_woocommerce_header_hooked_blocks( $hooked_blocks, $position, $anchor_block, $context ) {
	if ( 'after' !== $position || 'core/navigation' !== $anchor_block || ! is_array( $hooked_blocks ) ) {
		return $hooked_blocks;
	}

	$is_header_context = false;

	if ( $context instanceof WP_Block_Template ) {
		$is_header_context = 'header' === $context->area || false !== strpos( $context->slug, '//header' );
	} elseif ( $context instanceof WP_Post && 'wp_template_part' === $context->post_type ) {
		$is_header_context = 'header' === get_post_meta( $context->ID, 'area', true ) || false !== strpos( $context->post_name, 'header' );
	} elseif ( is_array( $context ) ) {
		$is_header_context =
			( isset( $context['area'] ) && 'header' === $context['area'] ) ||
			( isset( $context['slug'] ) && false !== strpos( (string) $context['slug'], 'header' ) ) ||
			( isset( $context['blockTypes'] ) && in_array( 'core/template-part/header', (array) $context['blockTypes'], true ) ) ||
			( isset( $context['categories'] ) && in_array( 'header', (array) $context['categories'], true ) );
	}

	if ( ! $is_header_context ) {
		return $hooked_blocks;
	}

	return array_values(
		array_diff(
			$hooked_blocks,
			array(
				'woocommerce/customer-account',
				'woocommerce/mini-cart',
			)
		)
	);
}
add_filter( 'hooked_block_types', 'act_district_cso_remove_woocommerce_header_hooked_blocks', 20, 4 );

/**
 * Keep custom template parts styled in the block editor canvas.
 */
function act_district_cso_enqueue_block_editor_assets() {
	$relative_path = '/assets/css/editor.css';

	wp_enqueue_style(
		'ACT-District-CSO-editor',
		get_stylesheet_directory_uri() . $relative_path,
		array(),
		act_district_cso_asset_version( $relative_path )
	);
}
add_action( 'enqueue_block_editor_assets', 'act_district_cso_enqueue_block_editor_assets' );

/**
 * Enqueue a page/template-specific stylesheet.
 */
function act_district_cso_enqueue_page_style( $template, $stylesheet, $dependency ) {
	$relative_path = '/assets/css/' . $stylesheet;
	$asset_path    = get_stylesheet_directory() . $relative_path;

	if ( ! file_exists( $asset_path ) ) {
		return;
	}

	$handle_suffix = sanitize_title( str_replace( '.php', '', $template ) . '-' . str_replace( '.css', '', $stylesheet ) );

	wp_enqueue_style(
		'ACT-District-CSO-' . $handle_suffix,
		get_stylesheet_directory_uri() . $relative_path,
		array( $dependency ),
		act_district_cso_asset_version( $relative_path )
	);
}
