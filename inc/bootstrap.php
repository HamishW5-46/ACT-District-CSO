<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
 * Register footer menus.
 */

function ACT_District_CSO_Child_footer_menus() {
    register_nav_menu('ACT-District-CSO-Child-footer-menu-1', __( 'Footer Menu 1', 'ACT_District_CSO_Child' ));
	register_nav_menu('ACT-District-CSO-Child-footer-menu-2', __( 'Footer Menu 2', 'ACT_District_CSO_Child' ));
	register_nav_menu('ACT-District-CSO-Child-quick-links', __( 'Quick Links', 'ACT_District_CSO_Child' ));
}
add_action( 'init', 'ACT_District_CSO_Child_footer_menus' );


/**
 * Enqueue child theme styles.
 */
function ACT_District_CSO_Child_enqueue_styles() {
	$parent_handle     = 'astra-parent-theme-css';
	$child_handle      = 'ACT-District-CSO-Child-style';
	$components_handle = 'ACT-District-CSO-Child-components';

	wp_enqueue_style(
		$parent_handle,
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( get_template() )->get( 'Version' )
	);

	wp_enqueue_style(
		$child_handle,
		get_stylesheet_uri(),
		array( $parent_handle ),
		act_district_cso_child_asset_version( '/style.css' )
	);

	wp_enqueue_style(
		$components_handle,
		get_stylesheet_directory_uri() . '/assets/css/components.css',
		array( $child_handle ),
		act_district_cso_child_asset_version( '/assets/css/components.css' )
	);

	$template_styles = array(
		'page-form-contact.php'		=> 'contact.css',
		'page-form-beginners.php'	=> 'beginners.css',
		'page-information.php'		=> 'about-aa.css',
		'page-resources.php'		=> 'members.css',
		'page-directory.php'		=> 'directory.css',
		'page-simple.php'			=> 'simple-page.css',
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
}
add_action( 'wp_enqueue_scripts', 'ACT_District_CSO_Child_enqueue_styles', 15 );

/**
 * Keep Astra's mobile menu container renderable for the dropdown toggle.
 *
 * LiteSpeed's generated unused CSS can preserve Astra's default hidden state
 * while dropping the dynamic open state. Printing this directly keeps the
 * parent wrapper available, while Astra's JavaScript still controls the inner
 * navigation display when the hamburger is toggled.
 */
function act_district_cso_child_mobile_menu_visibility_fix() {
	?>
	<style id="act-district-cso-child-mobile-menu-visibility">
		.ast-header-break-point #ast-mobile-header .ast-mobile-header-content {
			display: block !important;
		}

		.ast-header-break-point #ast-mobile-header .ast-mobile-header-content .main-header-bar-navigation {
			display: none;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'act_district_cso_child_mobile_menu_visibility_fix', 100 );

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
	require_once get_stylesheet_directory() . '/inc/woocommerce/shop-filters.php';
}
