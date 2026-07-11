<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Return the footer sitemap links.
 *
 * The sitemap intentionally combines public top-level pages with a small set of
 * archive landing pages that behave like primary site sections. Individual
 * posts, products, events, meetings, and child pages are excluded by design.
 *
 * @return array<int, array{label: string, url: string, order: int}>
 */
function act_district_cso_child_footer_sitemap_items() {
    $items = array(
        'home' => array(
            'label' => 'Home',
            'url'   => home_url( '/' ),
            'order' => 0,
        ),
    );

    $excluded_page_slugs = apply_filters(
        'act_district_cso_child_footer_sitemap_excluded_page_slugs',
        array(
            'cart',
            'checkout',
            'cookie-policy',
            'home-page',
            'my-account',
            'privacy-policy',
            'refund-and-returns-policy',
        )
    );

    $page_label_overrides = apply_filters(
        'act_district_cso_child_footer_sitemap_page_label_overrides',
        array(
            'about-aa'        => 'About AA',
            'contact'         => 'Contact Us',
            'literature-shop' => 'Literature Shop',
            'members'         => 'Member Information',
        )
    );

    $preferred_order = apply_filters(
        'act_district_cso_child_footer_sitemap_preferred_order',
        array(
            'home'                 => 0,
            'page:about-aa'        => 10,
            'archive:tsml_meeting' => 20,
            'page:members'         => 30,
            'page:literature-shop' => 40,
            'archive:notices'      => 50,
            'archive:tribe_events' => 60,
            'page:contact'         => 70,
        )
    );

    $pages = get_pages(
        array(
            'parent'      => 0,
            'post_status' => 'publish',
            'post_type'   => 'page',
            'sort_column' => 'menu_order,post_title',
            'sort_order'  => 'ASC',
        )
    );

    foreach ( $pages as $page ) {
        if ( in_array( $page->post_name, $excluded_page_slugs, true ) ) {
            continue;
        }

        $key = 'page:' . $page->post_name;

        $items[ $key ] = array(
            'label' => isset( $page_label_overrides[ $page->post_name ] )
                ? $page_label_overrides[ $page->post_name ]
                : get_the_title( $page ),
            'url'   => get_permalink( $page ),
            'order' => isset( $preferred_order[ $key ] )
                ? (int) $preferred_order[ $key ]
                : 100 + (int) $page->menu_order,
        );
    }

    $archive_sections = apply_filters(
        'act_district_cso_child_footer_sitemap_archive_sections',
        array(
            'tsml_meeting' => 'Meetings',
            'notices'      => 'Notices',
            'tribe_events' => 'Events',
        )
    );

    foreach ( $archive_sections as $post_type => $label ) {
        $archive_url = get_post_type_archive_link( $post_type );

        if ( ! $archive_url ) {
            continue;
        }

        $key = 'archive:' . $post_type;

        $items[ $key ] = array(
            'label' => $label,
            'url'   => $archive_url,
            'order' => isset( $preferred_order[ $key ] )
                ? (int) $preferred_order[ $key ]
                : 200,
        );
    }

    $unique_items = array();

    foreach ( $items as $item ) {
        $url_key = untrailingslashit( $item['url'] );

        if ( '' === $url_key ) {
            $url_key = '/';
        }

        if ( ! isset( $unique_items[ $url_key ] ) || $item['order'] < $unique_items[ $url_key ]['order'] ) {
            $unique_items[ $url_key ] = $item;
        }
    }

    $items = array_values( $unique_items );

    usort(
        $items,
        static function ( $a, $b ) {
            if ( $a['order'] === $b['order'] ) {
                return strcasecmp( $a['label'], $b['label'] );
            }

            return $a['order'] <=> $b['order'];
        }
    );

    return $items;
}

/**
 * Render the footer sitemap list markup.
 *
 * @return string
 */
function act_district_cso_child_footer_sitemap_list() {
    $items = act_district_cso_child_footer_sitemap_items();

    if ( empty( $items ) ) {
        return '';
    }

    $output = '<ul>';

    foreach ( $items as $item ) {
        $output .= sprintf(
            '<li><a href="%1$s">%2$s</a></li>',
            esc_url( $item['url'] ),
            esc_html( $item['label'] )
        );
    }

    $output .= '</ul>';

    return $output;
}
