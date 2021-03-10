<?php
	/**
	 * Template to output the search results for a
	 * store query
	 *
	 */

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

?>
<!-- Begin Results Header -->
<div class="dse-box dse-portlet--tab">
	<div class="dse-box-header">
		<div class="dse-box-header-wrapper">
			<h3 class="dse-box-header-title">
				<i class="fa fa-angle-down dse-mr-10"></i>
				<?php printf(
					/* translators: %1$s is replaced with the cronjob timer in seconds */
					esc_html__( 'Search Results ( Total items found: %1$s )', 'dropshipexpress' ),
					number_format( $results_array[ 'total' ] )
				); ?>
			</h3>
		</div>
	</div>
</div>
<!-- End Results Header -->

<div class="row">
	<?php
		foreach ( $results_array[ 'products' ] as $result ) {
			?>
			<!-- Begin Single Result -->
			<div class="col-xl-3">
				<form class="dse-search-result" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ) ?>">
					<div class="dse-box dse-box-fluid-height dse-ribbon dse-ribbon-error dse-ribbon-shadow dse-ribbon-left dse-ribbon-circle">
						<div class="dse-box-body">
							<div class="dse-minibox dse-minibox-2">
								<div class="dse-minibox-head">
									<?php
										if ( 0 != $result->discount_perc ) {
											/* translators: %1$s is replaced with the discount value */
											echo '<div class="dse-ribbon-content" style="top: 12px; right: -2px;">' . sprintf( esc_html__( '-%1$s%% Off', 'dropshipexpress' ), esc_html( $result->discount_perc ) ) . '</div>';
										}
									?>
									<div class="dse-box-media">
										<img class="dse-search-thumbnail" src="<?php echo esc_url( $result->thumbnail ) ?>" alt="<?php echo esc_html( $result->title ); ?>">
									</div>
								</div>
								<div class="dse-minibox-body">
									<div class="dse-minibox-section" title="<?php echo esc_html( $result->title ) ?>"><?php echo esc_html( $result->title ) ?></div>
									<div class="dse-line-separator dse-separator-dashed dse-separator-medium"></div>
									<ul class="dse-search-result-details">
										<li>
											<span><?php esc_html_e( 'Price', 'dropshipexpress' ); ?></span>
											<?php
												// If product is on sale
												if ( 0 !== $result->discount_value ) {
													echo "<span>";
													echo esc_html( $result->discount_value . ' ' . $result->discount_curr );
													echo "<strike>" . esc_html( $result->price . ' ' . $result->currency ) . "</strike>";
													echo "</span>";
												} else {
													echo "<span>" . esc_html( $result->price . ' ' . $result->currency ) . "</span>";
												}

											?>
										</li>
									</ul>
									<div class="dse-line-separator dse-separator-dashed dse-separator-medium"></div>
								</div>
								<div class="text-center">
									<div class="dse-search-result-actions">
										<?php
											if ( DSE_Import::Is_On_Shop( $result->product_id, $search_input[ 'source' ] ) ) {
												echo '<button type="submit" class="btn btn-danger btn-bold btn-sm btn-upper dse-mb-10" disabled="disabled">' . esc_html__( "Imported", "dropshipexpress" ) . '</button>';
											} else {
												echo '<button type="submit" class="btn btn-success btn-bold btn-sm btn-upper dse-mb-10">' . esc_html__( "Import", "dropshipexpress" ) . '</button>';
											}
										?>
										<a href="<?php echo esc_url( $result->url ) ?>" class="btn btn-primary btn-bold btn-sm btn-upper" target="_blank"><?php /* translators: %1$s is replaced with the retailer's name */ printf( esc_html__( 'View on %1$s', 'dropshipexpress' ), $search_input[ 'source' ] ) ?></a>
									</div>
								</div>
								<?php wp_nonce_field( 'dse-import-single-item-action', 'dse_import_product_nonce_' . esc_attr( $result->product_id ) ) ?>
								<input type="hidden" value="<?php echo esc_attr( $result->product_id ) ?>" name="dse_product_id">
								<input type="hidden" value="<?php echo esc_attr( $search_input[ 'source' ] ) ?>" name="dse_import_source">
								<input type="hidden" name="action" value="dse_queue_import">
							</div>
						</div>
					</div>
				</form>
			</div>
			<!-- End Single Result -->
			<?php
		}
	?>
	<input type="hidden" id="dse-search-results-source" name="dse_search_results_source" value="<?php echo esc_attr( $search_input[ 'source' ] ) ?>">
</div>

<?php
	if ( FALSE === $search_input[ 'is_single' ] ) {
		DSE_Import::Get_Search_Pagination( $results_array[ 'current_page' ], $results_array[ 'pages' ] );
	}
?>
