<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a
    class="skip-link screen-reader-text"
    href="#content"
    title="<?php esc_attr_e( 'Skip to content', 'ACT_District_CSO_Child' ); ?>">
        <?php esc_html_e( 'Skip to content', 'ACT_District_CSO_Child' ); ?>
</a>

<div id="page" class="hfeed site">
    <header class="site-header aa-site-header" role="banner">
        <div class="aa-header-nav">
            <div class="aa-header-nav__inner aa-header-container">
                <div class="site-branding aa-site-branding">
                    <?php the_custom_logo(); ?>
                </div>

                <button
                    class="aa-menu-toggle"
                    type="button"
                    aria-controls="primary-menu"
                    aria-expanded="false">
                    <?php esc_html_e( 'Menu', 'ACT_District_CSO_Child' ); ?>
                </button>

                <nav id="site-navigation" class="main-navigation aa-main-navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'ACT_District_CSO_Child' ); ?>">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'menu_id'        => 'primary-menu',
                            'menu_class'     => 'aa-header-menu nav-links',
                            'container'      => false,
                            'depth'          => 2,
                            'fallback_cb'    => 'act_district_cso_child_primary_menu_fallback',
                        )
                    );
                    ?>
                </nav>
            </div>
        </div>
    </header>

    <div id="content" class="site-content">
        <div class="aa-site-container">
