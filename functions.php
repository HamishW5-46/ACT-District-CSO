<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Theme-owned functionality.
 *
 * Site-wide behaviour lives in MU plugins so changing themes does not silently
 * remove privacy controls, archive rules, or WooCommerce features.
 */
$act_district_cso_child_includes = array(
    '/inc/bootstrap.php',
    '/inc/footer-sitemap.php',
    '/inc/site-branding.php',
    '/inc/wp-login-branding.php',
);

foreach ( $act_district_cso_child_includes as $act_district_cso_child_include ) {
    require_once get_stylesheet_directory() . $act_district_cso_child_include;
}