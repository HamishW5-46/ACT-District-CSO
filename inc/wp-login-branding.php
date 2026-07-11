<?php
/**
 * Custom Login Branding
 */

function aacbr_custom_login_styles() {
    $css_path = get_stylesheet_directory() . '/assets/css/login.css';
    $logo_url = get_stylesheet_directory_uri() . '/assets/img/aa-logo.svg';

    wp_enqueue_style(
        'aacbr-login',
        get_stylesheet_directory_uri() . '/assets/css/login.css',
        array(),
        file_exists( $css_path ) ? filemtime( $css_path ) : wp_get_theme()->get( 'Version' )
    );

    wp_add_inline_style(
        'aacbr-login',
        ':root { --aacbr-login-logo: url("' . esc_url( $logo_url ) . '"); }'
    );
}
add_action( 'login_enqueue_scripts', 'aacbr_custom_login_styles' );

/**
 * Change login logo URL 
 */ 
function aacbr_login_logo_url() { 
	return home_url(); 
} 
add_filter('login_headerurl', 'aacbr_login_logo_url');

/**
 * Change logo title text
 */
function aacbr_login_logo_title() {
    return 'ACT & District Central Service Office - Alcoholics Anonymous';
}
add_filter('login_headertext', 'aacbr_login_logo_title');