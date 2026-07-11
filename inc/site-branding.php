<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom child-theme logo markup.
 */
add_filter(
    'get_custom_logo',
    function ( $html ) {
        $logo_url = get_stylesheet_directory_uri() . '/assets/img/aa-logo.svg';

        return '
        <a href="' . esc_url( home_url( '/' ) ) . '" class="custom-logo-link aa-custom-logo" rel="home">
            <img src="' . esc_url( $logo_url ) . '" class="aa-logo-mark" alt="Alcoholics Anonymous logo">
            <span class="aa-logo-text">
                <span>Canberra &amp; District Central Service Office<br></span>
                <span>Alcoholics Anonymous</span>
            </span>
        </a>';
    }
);
