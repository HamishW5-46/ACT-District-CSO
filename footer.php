<?php

/** 
 * Custom Footer Template
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php astra_content_bottom(); ?>

</div><!-- ast-container -->
</div><!-- #content -->

<?php astra_content_after(); ?>

<footer class="aa-footer">
    <div class="aa-footer-container">

        <!-- Logo + About -->
        <div class="aa-footer-col aa-footer-brand">
            <?php if ( function_exists( 'custom_site_details_footer_image_url' ) ) : ?>
	            <?php $footer_image_url = custom_site_details_footer_image_url(); ?>
                <?php if ( $footer_image_url ) : ?>
                    <img class="aa-footer-logo" id="aa-footer-logo" src="<?php echo esc_url( $footer_image_url ); ?>" alt="Footer logo" >
                <?php endif; ?>
            <?php endif; ?>
            <?php if ( function_exists( 'custom_site_details_footer_text' ) ) : ?>
                <?php $custom_site_details_footer_text = custom_site_details_footer_text(); ?>
                <?php if ( $custom_site_details_footer_text ) : ?>
                    <div class="aa-footer-about"><?php echo $custom_site_details_footer_text; ?></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Sitemap -->
         <div class="sitemap-container">
            <div class="aa-footer-col">
                <h4>Sitemap</h4>
                <?php
                if ( has_nav_menu( 'ACT-District-CSO-Child-footer-menu-1' ) ) {
                    wp_nav_menu( array(
                        'theme_location' => 'ACT-District-CSO-Child-footer-menu-1',
                        'container'      => 'nav',
                        'container_class'=> 'footer-navigation',
                        'menu_class'     => 'footer-menu-links',
                        'depth'          => 1,
                    ) );
                }
                ?>
            </div>
            <div class="aa-footer-col" id="sitemap-2">
                <?php
                if ( has_nav_menu( 'ACT-District-CSO-Child-footer-menu-2' ) ) {
                    wp_nav_menu( array(
                        'theme_location' => 'ACT-District-CSO-Child-footer-menu-2',
                        'container'      => 'nav',
                        'container_class'=> 'footer-navigation',
                        'menu_class'     => 'footer-menu-links',
                        'depth'          => 1,
                    ) );
                }
                ?>
            </div>
        </div>
        <!-- Quick Links -->
        <div class="aa-footer-col">
            <h4>Quick Links</h4>
            <?php
            if ( has_nav_menu( 'ACT-District-CSO-Child-quick-links' ) ) {
                wp_nav_menu( array(
                    'theme_location' => 'ACT-District-CSO-Child-quick-links',
                    'container'      => 'nav',
                    'container_class'=> 'footer-navigation',
                    'menu_class'     => 'footer-menu-links',
                    'depth'          => 1,
                ) );
            }
            ?>
        </div>
        <!-- Contact -->
        <div class="aa-footer-col">
            <h4 class="aa-footer-heading">Contact</h4>
            <ul class="aa-footer-contact">
                <?php if ( function_exists( 'custom_site_details_email_link' ) ) : ?>
                    <?php $site_email = custom_site_details_email_link( array( 'class' => 'footer-email' ) ); ?>
                    <?php if ( $site_email ) : ?>
                        <li>Email: <?php echo $site_email; ?></li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ( function_exists( 'custom_site_details_phone_link' ) ) : ?>
                    <?php $site_phone = custom_site_details_phone_link( array( 'class' => 'footer-phone' ) ); ?>
                    <?php if ( $site_phone ) : ?>
                        <li>Phone: <?php echo $site_phone; ?> <strong>(24/7)</strong></li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ( function_exists( 'custom_site_details_opening_hours' ) ) : ?>
                    <?php $site_opening_hours = custom_site_details_opening_hours(); ?>
                    <?php if ( $site_opening_hours ) : ?>
                        <li>Office Hours: <?php echo $site_opening_hours; ?></li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ( function_exists( 'custom_site_details_address' ) ) : ?>
                    <?php $site_address = custom_site_details_address(); ?>
                    <?php if ( $site_address ) : ?>
                        <li><?php echo $site_address; ?></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

        </div>
    </div>

    <!-- Bottom bar -->
    <div class="aa-footer-bottom">
        <p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> ACT & District Central Service Office of Alcoholics Anonymous. All rights reserved.</p>
        <?php if ( function_exists( 'custom_site_details_footer_disclaimer' ) ) : ?>
            <?php $custom_site_details_footer_disclaimer = custom_site_details_footer_disclaimer(); ?>
            <?php if ( $custom_site_details_footer_disclaimer ) : ?>
                <div class="aa-footer-note">
                    <?php echo $custom_site_details_footer_disclaimer; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
