<?php
/**
 * Theme-owned functionality.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Load theme bootstrap and includes.
 */
$act_district_cso_includes = array(
    '/inc/bootstrap.php',
    '/inc/site-branding.php',
);

foreach ( $act_district_cso_includes as $act_district_cso_include ) {
    require_once get_stylesheet_directory() . $act_district_cso_include;
}

/**
 * Sort the homepage Events query by TEC event start date
 * and exclude events that have already started.
 */
/**
 * Query upcoming TEC events in chronological event-date order.
 */
function aa_upcoming_events_query( $query, $block, $page ) {

	$block_query = $block->context['query'] ?? [];

	// Only modify the Query Loop explicitly marked for event-date ordering.
	if (
		empty( $block_query['eventDateOrder'] ) ||
		'tribe_events' !== ( $query['post_type'] ?? '' )
	) {
		return $query;
	}

	$query['meta_query'] = [
		'event_start' => [
			'key'     => '_EventStartDate',
			'value'   => current_time( 'mysql' ),
			'compare' => '>=',
			'type'    => 'DATETIME',
		],
	];

	$query['orderby'] = [
		'event_start' => 'ASC',
	];

	$query['order'] = 'ASC';

	return $query;
}

add_filter(
	'query_loop_block_query_vars',
	'aa_upcoming_events_query',
	10,
	3
);

/**
 * Display the TEC event start date in designated Post Date blocks.
 *
 * This lets the core Post Date block work correctly inside a Query Loop,
 * while displaying the actual event date instead of the post publication date.
 */
function aa_render_event_date_block( $block_content, $block, $instance ) {

	$class_name = $block['attrs']['className'] ?? '';

	// Only modify Post Date blocks specifically marked for event dates.
	if ( ! str_contains( $class_name, 'tec-event-date' ) ) {
		return $block_content;
	}

	$post_id = $instance->context['postId'] ?? 0;

	if ( ! $post_id || 'tribe_events' !== get_post_type( $post_id ) ) {
		return $block_content;
	}

	if ( ! function_exists( 'tribe_get_start_date' ) ) {
		return $block_content;
	}

	$format = $block['attrs']['format'] ?? 'j M Y g:ia';

	$display_date = tribe_get_start_date(
		$post_id,
		true,
		$format
	);

	$datetime = tribe_get_start_date(
		$post_id,
		true,
		'c'
	);

	$wrapper_attributes = get_block_wrapper_attributes(
		[
			'class' => $class_name,
		]
	);

	return sprintf(
		'<div %1$s><time datetime="%2$s">%3$s</time></div>',
		$wrapper_attributes,
		esc_attr( $datetime ),
		esc_html( $display_date )
	);
}

add_filter(
	'render_block_core/post-date',
	'aa_render_event_date_block',
	10,
	3
);