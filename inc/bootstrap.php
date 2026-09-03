<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register standalone theme support and navigation locations.
 */
function act_district_cso_child_setup() {
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
			'primary'                              => __( 'Primary Menu', 'ACT_District_CSO' ),
			'ACT-District-CSO-Child-footer-menu-1' => __( 'Footer Menu 1', 'ACT_District_CSO' ),
			'ACT-District-CSO-Child-footer-menu-2' => __( 'Footer Menu 2', 'ACT_District_CSO' ),
			'ACT-District-CSO-Child-quick-links'  => __( 'Quick Links', 'ACT_District_CSO' ),
		)
	);
}
add_action( 'after_setup_theme', 'act_district_cso_child_setup' );

/**
 * Return a cache-busting asset version.
 */
function act_district_cso_child_asset_version( $relative_path ) {
	$asset_path = get_stylesheet_directory() . $relative_path;

	if ( file_exists( $asset_path ) ) {
		return filemtime( $asset_path );
	}

	return wp_get_theme()->get( 'Version' );
}

/**
 * Return the active page template slug for classic or block templates.
 */
function act_district_cso_child_current_page_template_slug() {
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
function act_district_cso_child_is_woocommerce_context() {
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
 * Enqueue standalone theme styles and scripts.
 */
function ACT_District_CSO_Child_enqueue_styles() {
	$child_handle      = 'ACT-District-CSO-Child-style';
	$components_handle = 'ACT-District-CSO-Child-components';

	wp_enqueue_style(
		$child_handle,
		get_stylesheet_uri(),
		array(),
		act_district_cso_child_asset_version( '/style.css' )
	);

	wp_enqueue_style(
		$components_handle,
		get_stylesheet_directory_uri() . '/assets/css/components.css',
		array( $child_handle ),
		act_district_cso_child_asset_version( '/assets/css/components.css' )
	);

	$template_styles  = array(
		'page-form-contact' => 'contact.css',
		'page-information'  => 'about-aa.css',
	);
	$current_template = act_district_cso_child_current_page_template_slug();

	foreach ( $template_styles as $template => $stylesheet ) {
		if ( $current_template === $template || is_page_template( $template . '.php' ) ) {
			act_district_cso_child_enqueue_page_style(
				$template,
				$stylesheet,
				$components_handle
			);
		}
	}

	wp_enqueue_script(
		'ACT-District-CSO-Child-navigation',
		get_stylesheet_directory_uri() . '/assets/js/navigation.js',
		array(),
		act_district_cso_child_asset_version( '/assets/js/navigation.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'ACT_District_CSO_Child_enqueue_styles', 15 );

/**
 * Add stable body classes used by template-specific CSS.
 */
function act_district_cso_child_body_classes( $classes ) {
	if ( act_district_cso_child_is_woocommerce_context() ) {
		$classes[] = 'aac-woocommerce-page';
	}

	if ( function_exists( 'is_account_page' ) && is_account_page() && ! is_user_logged_in() ) {
		$classes[] = 'aac-account-auth-page';
	}

	return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'act_district_cso_child_body_classes' );

/**
 * Keep WooCommerce's automatic block hooks from adding account/cart controls to
 * the public header. The shop still has its normal account and cart pages.
 */
function act_district_cso_child_remove_woocommerce_header_hooked_blocks( $hooked_blocks, $position, $anchor_block, $context ) {
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
add_filter( 'hooked_block_types', 'act_district_cso_child_remove_woocommerce_header_hooked_blocks', 20, 4 );

/**
 * Keep custom template parts styled in the block editor canvas.
 */
function act_district_cso_child_enqueue_block_editor_assets() {
	$relative_path = '/assets/css/editor.css';

	wp_enqueue_style(
		'ACT-District-CSO-Child-editor',
		get_stylesheet_directory_uri() . $relative_path,
		array(),
		act_district_cso_child_asset_version( $relative_path )
	);
}
add_action( 'enqueue_block_editor_assets', 'act_district_cso_child_enqueue_block_editor_assets' );

/**
 * Enqueue a page/template-specific stylesheet.
 */
function act_district_cso_child_enqueue_page_style( $template, $stylesheet, $dependency ) {
	$relative_path = '/assets/css/' . $stylesheet;
	$asset_path    = get_stylesheet_directory() . $relative_path;

	if ( ! file_exists( $asset_path ) ) {
		return;
	}

	$handle_suffix = sanitize_title( str_replace( '.php', '', $template ) . '-' . str_replace( '.css', '', $stylesheet ) );

	wp_enqueue_style(
		'ACT-District-CSO-Child-' . $handle_suffix,
		get_stylesheet_directory_uri() . $relative_path,
		array( $dependency ),
		act_district_cso_child_asset_version( $relative_path )
	);
}
