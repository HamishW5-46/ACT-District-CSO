<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the mobile shop-filter assets.
 */
add_action( 'wp_enqueue_scripts', 'aac_enqueue_mobile_shop_filter_assets' );

function aac_enqueue_mobile_shop_filter_assets() {
	if ( ! function_exists( 'is_shop' ) || ( ! is_shop() && ! is_product_taxonomy() ) ) {
		return;
	}

	$theme_dir = get_stylesheet_directory();
	$theme_uri = get_stylesheet_directory_uri();

	$script_path = $theme_dir . '/assets/js/mobile-shop-filters.js';
	$script_uri  = $theme_uri . '/assets/js/mobile-shop-filters.js';

	$style_path = $theme_dir . '/assets/css/mobile-shop-filters.css';
	$style_uri  = $theme_uri . '/assets/css/mobile-shop-filters.css';

	wp_enqueue_style(
		'aac-mobile-shop-filters',
		$style_uri,
		array(),
		file_exists( $style_path ) ? (string) filemtime( $style_path ) : '1.0.0'
	);

	wp_enqueue_script(
		'aac-mobile-shop-filters',
		$script_uri,
		array( 'jquery' ),
		file_exists( $script_path ) ? (string) filemtime( $script_path ) : '1.0.0',
		true
	);

	wp_localize_script(
		'aac-mobile-shop-filters',
		'aacShopFilters',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'aac_shop_filter_nonce' ),
		)
	);
}

/**
 * Render the mobile filter toggle button.
 */
add_action(
	'woocommerce_before_shop_loop',
	'aac_render_mobile_shop_filter_toggle',
	15
);

function aac_render_mobile_shop_filter_toggle() {
	if ( ! is_shop() && ! is_product_taxonomy() ) {
		return;
	}

	echo '<button type="button" class="aac-mobile-filter-toggle" aria-controls="aac-mobile-filter-drawer" aria-expanded="false">';
	echo '<span class="aac-filter-icon" aria-hidden="true">☰</span>';
	echo 'Filter products';
	echo '</button>';
}

/**
 * Render the mobile filter drawer.
 */
add_action( 'wp_footer', 'aac_render_mobile_shop_filters' );

function aac_render_mobile_shop_filters() {
	if ( ! is_shop() && ! is_product_taxonomy() ) {
		return;
	}

	$categories = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
		)
	);

	$prices = aac_get_shop_price_bounds();
	?>

	<div class="aac-mobile-filter-overlay" hidden></div>

	<aside
		id="aac-mobile-filter-drawer"
		class="aac-mobile-filter-drawer"
		aria-hidden="true"
	>
		<div class="aac-mobile-filter-header">
			<h2>Filter products</h2>

			<button
				type="button"
				class="aac-mobile-filter-close"
				aria-label="Close filters"
			>
				×
			</button>
		</div>

		<form
			class="aac-mobile-filter-form"
			method="post"
			action=""
		>
			<div class="aac-mobile-filter-content">

				<section class="aac-filter-section">
					<h3>Search</h3>

					<input
						type="search"
						name="search"
						class="aac-filter-search"
						placeholder="Search products..."
					>
				</section>

				<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
					<section class="aac-filter-section">
						<h3>Product categories</h3>

						<div class="aac-filter-options">
							<?php foreach ( $categories as $category ) : ?>
								<?php
								$category_name = html_entity_decode(
									$category->name,
									ENT_QUOTES | ENT_HTML5,
									'UTF-8'
								);

								$category_name = str_replace(
									';',
									' – ',
									$category_name
								);
								?>

								<label class="aac-filter-option">
									<input
										type="checkbox"
										name="aac_categories[]"
										value="<?php echo esc_attr( $category->slug ); ?>"
									>

									<span>
										<?php echo esc_html( $category_name ); ?>
									</span>
								</label>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endif; ?>

				<section class="aac-filter-section">
					<h3>Price</h3>

					<div class="aac-price-fields">
						<label>
							Min

							<input
								type="number"
								name="min_price"
								min="0"
								step="0.01"
								placeholder="<?php echo esc_attr( $prices['min'] ); ?>"
							>
						</label>

						<label>
							Max

							<input
								type="number"
								name="max_price"
								min="0"
								step="0.01"
								placeholder="<?php echo esc_attr( $prices['max'] ); ?>"
							>
						</label>
					</div>
				</section>

			</div>

			<div class="aac-mobile-filter-footer">
				<button type="button" class="aac-filter-clear">
					Clear
				</button>

				<button type="submit" class="aac-filter-apply">
					Apply filters
				</button>
			</div>
		</form>
	</aside>

	<?php
}

/**
 * Get the minimum and maximum shop prices.
 *
 * @return array{min: float|int|string, max: float|int|string}
 */
function aac_get_shop_price_bounds() {
	global $wpdb;

	$prices = $wpdb->get_row(
		"
		SELECT
			FLOOR(MIN(CAST(meta_value AS DECIMAL(10,2)))) AS min_price,
			CEIL(MAX(CAST(meta_value AS DECIMAL(10,2)))) AS max_price
		FROM {$wpdb->postmeta}
		WHERE meta_key = '_price'
		AND meta_value != ''
		"
	);

	return array(
		'min' => $prices && null !== $prices->min_price
			? $prices->min_price
			: 0,

		'max' => $prices && null !== $prices->max_price
			? $prices->max_price
			: 100,
	);
}

/**
 * Handle mobile product-filter AJAX requests.
 */
add_action( 'wp_ajax_aac_filter_products', 'aac_filter_products' );
add_action( 'wp_ajax_nopriv_aac_filter_products', 'aac_filter_products' );

function aac_filter_products() {
	check_ajax_referer( 'aac_shop_filter_nonce', 'nonce' );

	$search = isset( $_POST['search'] )
		? sanitize_text_field( wp_unslash( $_POST['search'] ) )
		: '';

	$categories = isset( $_POST['aac_categories'] )
		? array_map(
			'sanitize_title',
			wp_unslash( (array) $_POST['aac_categories'] )
		)
		: array();

	$min_price = isset( $_POST['min_price'] ) && '' !== $_POST['min_price']
		? (float) wp_unslash( $_POST['min_price'] )
		: null;

	$max_price = isset( $_POST['max_price'] ) && '' !== $_POST['max_price']
		? (float) wp_unslash( $_POST['max_price'] )
		: null;

	$orderby = isset( $_POST['orderby'] )
		? sanitize_text_field( wp_unslash( $_POST['orderby'] ) )
		: 'menu_order';

	$paged = isset( $_POST['paged'] )
		? max( 1, absint( $_POST['paged'] ) )
		: 1;

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => apply_filters(
			'loop_shop_per_page',
			get_option( 'posts_per_page' )
		),
		'paged'          => $paged,
		's'              => $search,
		'tax_query'      => array(),
		'meta_query'     => array(),
	);

	if ( ! empty( $categories ) ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $categories,
			'operator' => 'IN',
		);
	}

	if ( null !== $min_price || null !== $max_price ) {
		$args['meta_query'][] = array(
			'key'     => '_price',
			'type'    => 'DECIMAL(10,2)',
			'compare' => 'BETWEEN',
			'value'   => array(
				null !== $min_price ? $min_price : 0,
				null !== $max_price ? $max_price : 999999,
			),
		);
	}

	$ordering = WC()->query->get_catalog_ordering_args( $orderby );

	if ( ! empty( $ordering['orderby'] ) ) {
		$args['orderby'] = $ordering['orderby'];
	}

	if ( ! empty( $ordering['order'] ) ) {
		$args['order'] = $ordering['order'];
	}

	if ( ! empty( $ordering['meta_key'] ) ) {
		$args['meta_key'] = $ordering['meta_key'];
	}

	$query = new WP_Query( $args );

	ob_start();

	if ( $query->have_posts() ) {
		woocommerce_product_loop_start();

		while ( $query->have_posts() ) {
			$query->the_post();
			wc_get_template_part( 'content', 'product' );
		}

		woocommerce_product_loop_end();
	} else {
		echo '<p class="woocommerce-info">';
		echo esc_html__(
			'No products found matching your filters.',
			'act-district-cso-child'
		);
		echo '</p>';
	}

	$products_html = ob_get_clean();

	$pagination_html = paginate_links(
		array(
			'total'   => $query->max_num_pages,
			'current' => $paged,
			'type'    => 'list',
		)
	);

	if ( ! is_string( $pagination_html ) ) {
		$pagination_html = '';
	}

	$from = $query->found_posts > 0
		? ( ( $paged - 1 ) * $args['posts_per_page'] ) + 1
		: 0;

	$to = min(
		$paged * $args['posts_per_page'],
		$query->found_posts
	);

	$result_count = sprintf(
		/* translators: 1: first product number, 2: last product number, 3: total products. */
		__( 'Showing %1$d–%2$d of %3$d results', 'act-district-cso-child' ),
		$from,
		$to,
		$query->found_posts
	);

	wp_reset_postdata();

	wp_send_json_success(
		array(
			'products'    => $products_html,
			'pagination'  => $pagination_html,
			'resultCount' => $result_count,
		)
	);
}