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
			'primary'                              => __( 'Primary Menu', 'ACT_District_CSO_Child' ),
			'ACT-District-CSO-Child-footer-menu-1' => __( 'Footer Menu 1', 'ACT_District_CSO_Child' ),
			'ACT-District-CSO-Child-footer-menu-2' => __( 'Footer Menu 2', 'ACT_District_CSO_Child' ),
			'ACT-District-CSO-Child-quick-links'  => __( 'Quick Links', 'ACT_District_CSO_Child' ),
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

	$template_styles = array(
		'page-form-contact.php'	=> 'contact.css',
		'page-information.php'	=> 'about-aa.css',
		'page-resources.php'	=> 'members.css',
	);

	foreach ( $template_styles as $template => $stylesheet ) {
		if ( is_page_template( $template ) ) {
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
 * Simple fallback when no primary menu has been assigned yet.
 */
function act_district_cso_child_primary_menu_fallback() {
	echo '<ul id="primary-menu" class="aa-header-menu nav-links">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'ACT_District_CSO_Child' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/meetings/' ) ) . '">' . esc_html__( 'Meetings', 'ACT_District_CSO_Child' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'Contact', 'ACT_District_CSO_Child' ) . '</a></li>';
	echo '</ul>';
}

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

if ( class_exists( 'WooCommerce' ) ) {
	require_once get_stylesheet_directory() . '/inc/woocommerce/bootstrap.php';
}

require_once get_stylesheet_directory() . '/inc/forms.php';

require_once get_stylesheet_directory() . '/inc/login-page.php';

require_once get_stylesheet_directory() . '/templates/gutenberg/tribe_events.php';
