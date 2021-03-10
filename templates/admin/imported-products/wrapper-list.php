<?php
	/**
	 * Render a single imported product
	 */

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	// Get the current page
	$current_page = isset( $_GET[ 'current_page' ] ) ? intval( $_GET[ 'current_page' ] ) : 1;

	// Query a list of imported procuts
	$imported_products_query_args = [
		'post_type'      => 'dse_imported',
		'post_status'    => 'draft',
		'posts_per_page' => 10,
		'paged'          => $current_page,

	];

	$imported_products_query = new WP_Query( $imported_products_query_args );

	if ( $imported_products_query->have_posts() ) {
		while ( $imported_products_query->have_posts() ) {
			$imported_products_query->the_post();
			// Get the template that renders a single product
			require( DSE_PLUGIN_FOLDER . '/templates/admin/imported-products/single-product.php' );
		}
	} else {
		// If no product is imported, or they all have been published
		require_once( DSE_PLUGIN_FOLDER . '/templates/admin/imported-products/no-products.php' );
	}
	?>
	<!-- Begin Imported Products Pagination -->
	<?php DSE_Import::Get_Import_Pagination( $current_page, ceil( $imported_products_query->found_posts / 10 ) ); ?>
	<!-- End Imported Products Pagination -->
	<?php
	wp_reset_postdata();