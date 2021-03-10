<?php
	/**
	 * Template to output the product search page
	 *
	 */

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	// Get the currency format for aliexpress
	$symbol_aliexpress = DSE_Import::Get_Currency_Symbol( 'aliexpress' );
	
?>
<!-- Begin Import Product Page -->
<div class="dse-content dse-grid-item dse-grid-item--fluid dse-grid dse-grid-horizontal">
	<div class="dse-container dse-container-fluid dse-grid-item dse-grid-item-fluid">

		<!-- Begin Import Product Form -->
		<div class="dse-box">

			<div class="dse-box-header">
				<div class="dse-box-header-wrapper">
					<h3 class="dse-box-header-title"><?php esc_html_e( 'Search Products', 'dropshipexpress' ); ?></h3>
				</div>
				<div class="dse-box-header-toolbar">
					<select id="dse_select_search_source" class="dse-select2" data-width="250px">
						<option value="0" disabled="disabled" selected="selected"><?php esc_html_e( 'Choose a Store ...', 'dropshipexpress' ); ?></option>
						<?php
							$stores = DSE_Import::Get_Sections();
							foreach ( $stores as $store => $value ) {
								echo "<option value='" . esc_html( $store ) . "'>" . esc_html( $value[ 'title' ] ) . "</option>";
							}
						?>
					</select>
				</div>
			</div>

			<div class="dse-search-form-wrapper">

				<!-- Begin Amazon Form -->
				<form method="get" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ) ?>" class="dse-search-product-form hidden" data-id="amazon">
					<div class="dse-box-body">
						<div class="dse-content-section dse-content-section-last">
							<div class="alert alert-solid-success alert-bold" role="alert">
								<div class="alert-text"><?php esc_html_e( 'Support for Amazon will be added soon. Be sure to always update the plugin to be the first to receive the new features.', 'dropshipexpress' ); ?></div>
							</div>
						</div>
					</div>
				</form>
				<!-- End Amazon Form -->

				<!-- Begin AliExpress Form -->
				<form method="get" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ) ?>" class="dse-search-product-form hidden" data-id="aliexpress">
					<div class="dse-box-body">
						<div class="dse-content-section dse-content-section-first">

							<!-- Begin Search Field -->
							<div class="form-group row">

								<div class="col-lg-6">
									<label><?php esc_html_e( 'Search Keyword', 'dropshipexpress' ); ?></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa fa-search"></i></span>
										</div>
										<input type="text" class="form-control" name="dse_product_search_keyword">
									</div>
									<span class="form-text text-muted"><?php esc_html_e( 'Enter a keyword to search in products (required) *', 'dropshipexpress' ); ?></span>
								</div>

								<div class="col-lg-2">
									<label><?php esc_html_e( 'Destination', 'dropshipexpress' ); ?></label>
									<div class="dse-input-group-icon">
										<select name="dse_product_search_destination" class="dse-select2">
											<option selected value="all"><?php esc_html_e( 'Any', 'dropshipexpress' ); ?></option>
											<option value="AT"><?php esc_html_e( 'Austria', 'dropshipexpress' ); ?></option>
											<option value="BE"><?php esc_html_e( 'Belgium', 'dropshipexpress' ); ?></option>
											<option value="CZ"><?php esc_html_e( 'Czech', 'dropshipexpress' ); ?></option>
											<option value="DE"><?php esc_html_e( 'Germany', 'dropshipexpress' ); ?></option>
											<option value="DK"><?php esc_html_e( 'Denmark', 'dropshipexpress' ); ?></option>
											<option value="ES"><?php esc_html_e( 'Spain', 'dropshipexpress' ); ?></option>
											<option value="FR"><?php esc_html_e( 'France', 'dropshipexpress' ); ?></option>
											<option value="HU"><?php esc_html_e( 'Hungary', 'dropshipexpress' ); ?></option>
											<option value="IT"><?php esc_html_e( 'Italy', 'dropshipexpress' ); ?></option>
											<option value="LU"><?php esc_html_e( 'Luxembourg', 'dropshipexpress' ); ?></option>
											<option value="NL"><?php esc_html_e( 'Netherland', 'dropshipexpress' ); ?></option>
											<option value="PL"><?php esc_html_e( 'Poland', 'dropshipexpress' ); ?></option>
											<option value="PT"><?php esc_html_e( 'Portugal', 'dropshipexpress' ); ?></option>
											<option value="RU"><?php esc_html_e( 'Russia', 'dropshipexpress' ); ?></option>
											<option value="SL"><?php esc_html_e( 'Slovenia', 'dropshipexpress' ); ?></option>
											<option value="SK"><?php esc_html_e( 'Slovakia', 'dropshipexpress' ); ?></option>
											<option value="UK"><?php esc_html_e( 'United Kingdom', 'dropshipexpress' ); ?></option>
										</select>
									</div>
									<span class="form-text text-muted"><?php esc_html_e( 'Optimized delivery for this country', 'dropshipexpress' ); ?></span>
								</div>

								<div class="col-lg-4">

									<label><?php esc_html_e( 'Categories', 'dropshipexpress' ); ?></label>
									<select class="form-control dse-select2-general dse-product-search" name="dse_product_search_cat">
										<option selected value="all"><?php esc_html_e( 'All Categories', 'dropshipexpress' ); ?></option>
										<?php
											$categories = DSE_Import::Get_Category_Index( 'aliexpress' );

											foreach ( $categories as $parent_key => $category ) {
												echo "<optgroup label='{$category[$parent_key]}'>";

												foreach ( $category as $sub_key => $sub_category ) {
													if ( $sub_key === $parent_key ) {
														echo "<option value='{$sub_key}'>" . sprintf( esc_html__( '%1$s (All)', 'dropshipexpress' ), $sub_category ) . "</option>";
													} else {
														echo "<option value='{$sub_key}'>" . esc_html( $sub_category ) . "</option>";
													}
												}

												echo "</optgroup>";
											}
										?>
									</select>
									<span class="form-text text-muted"><?php esc_html_e( 'Choose the categories to search', 'dropshipexpress' ); ?></span>
								</div>
							</div>
							<!-- End Search Field -->

							<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

							<!-- Begin Price Fields -->
							<div class="form-group form-group-last row">
								<div class="col-lg-2">
									<label><?php esc_html_e( 'Price From', 'dropshipexpress' ); ?></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><?php echo esc_html( $symbol_aliexpress ) ?></span>
										</div>
										<input class="form-control" type="number" min="0" name="dse_product_search_price_from">
									</div>
									<span class="form-text text-muted"><?php esc_html_e( 'Minimum price (optional)', 'dropshipexpress' ); ?></span>
								</div>

								<div class="col-lg-2">
									<label><?php esc_html_e( 'Price To', 'dropshipexpress' ); ?></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><?php echo esc_html( $symbol_aliexpress ) ?></span>
										</div>
										<input class="form-control" type="number" min="0" name="dse_product_search_price_to">
									</div>
									<span class="form-text text-muted"><?php esc_html_e( 'Maximum price (optional)', 'dropshipexpress' ); ?></span>
								</div>
								<!-- End Price Fields -->

								<!-- Begin Order Field -->
								<div class="col-lg-2">
									<label><?php esc_html_e( 'Order by', 'dropshipexpress' ); ?></label>
									<div class="dse-input-group-icon">
										<select name="dse_product_search_orderby" class="dse-select2">
											<option selected value="default"><?php esc_html_e( 'Default', 'dropshipexpress' ); ?></option>
											<option value="SALE_PRICE_ASC"><?php esc_html_e( 'Lowest Price', 'dropshipexpress' ); ?></option>
											<option value="SALE_PRICE_DESC"><?php esc_html_e( 'Highest Price', 'dropshipexpress' ); ?></option>
											<option value="LAST_VOLUME_ASC"><?php esc_html_e( 'Least Sold', 'dropshipexpress' ); ?></option>
											<option value="LAST_VOLUME_DESC"><?php esc_html_e( 'Most Sold', 'dropshipexpress' ); ?></option>
										</select>
									</div>
									<span class="form-text text-muted"><?php esc_html_e( 'Choose how to sort the queried products.', 'dropshipexpress' ); ?></span>
								</div>
								<!-- End Order Field -->

								<!-- Begin Features Field -->
								<div class="col-lg-2">
									<label><?php esc_html_e( 'Delivery Delay', 'dropshipexpress' ); ?></label>
									<div class="dse-input-group-icon">
										<select name="dse_product_search_delivery_delay" class="dse-select2">
											<option selected value="3"><?php esc_html_e( '3 Days', 'dropshipexpress' ); ?></option>
											<option value="7"><?php esc_html_e( '7 Days', 'dropshipexpress' ); ?></option>
											<option value="10"><?php esc_html_e( '10 Days', 'dropshipexpress' ); ?></option>
										</select>
									</div>
									<span class="form-text text-muted"><?php esc_html_e( 'Approximate waiting time for the package to be delivered at destination.', 'dropshipexpress' ); ?></span>
								</div>
								<!-- End Features Field -->

								<!-- Begin Product ID Field -->
								<div class="col-lg-4">
									<label><?php esc_html_e( 'Product ID or URL', 'dropshipexpress' ); ?></label>
									<div class="input-group">
										<input type="text" class="form-control" name="dse_product_search_single_id" placeholder="<?php esc_html_e( 'Example: 1234 or https://aliexpress.com/item/1234.html', 'dropshipexpress' ); ?>">
										<div class="input-group-append">
											<button class="dse-fetch-single-product btn btn-brand" type="submit">
												<i class="fa fa-sync-alt text-white"></i><?php esc_html_e( 'Fetch', 'dropshipexpress' ) ?>
											</button>
										</div>
									</div>
									<span class="form-text text-muted"><?php esc_html_e( 'You can import a single product directly by its AliExpress item ID or URL.', 'dropshipexpress' ); ?></span>
								</div>
								<!-- End Product ID Field -->
							</div>
						</div>
					</div>
					<!-- Begin Form Controls -->
					<div class="dse-box-footer">
						<div class="dse-form-actions dse-align-right">
							<button type="reset" class="btn btn-secondary">
								<i class="fa fa-eraser"></i>
								<?php esc_html_e( 'Reset', 'dropshipexpress' ); ?>
							</button>

							<button type="submit" class="btn btn-success">
								<i class="fa fa-search"></i>
								<?php esc_html_e( 'Search', 'dropshipexpress' ); ?>
							</button>
						</div>
					</div>
					<!-- End Form Controls -->
					<?php wp_nonce_field( 'dse-search-import-nonce-action', 'dse_search_stores_nonce' ); ?>
					<input type="hidden" name="action" value="dse_search_stores">
					<input type="hidden" name="dse_product_search_source" value="aliexpress">
					<input type="hidden" class="dse_product_search_page" name="dse_product_search_page" value="1">
				</form>
				<!-- End AliExpress Form -->

				<!-- Begin Walmart Form -->
				<form method="get" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ) ?>" class="dse-search-product-form hidden" data-id="walmart">
					<div class="dse-box-body">
						<div class="dse-content-section dse-content-section-last">
							<div class="alert alert-solid-success alert-bold" role="alert">
								<div class="alert-text"><?php esc_html_e( 'Support for Walmart will be added soon. Be sure to always update the plugin to be the first to receive the new features.', 'dropshipexpress' ); ?></div>
							</div>
						</div>
					</div>
				</form>
				<!-- End Walmart Form -->

				<!-- Begin Costco Form -->
				<form method="get" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ) ?>" class="dse-search-product-form hidden" data-id="costco">
					<div class="dse-box-body">
						<div class="dse-content-section dse-content-section-last">
							<div class="alert alert-solid-success alert-bold" role="alert">
								<div class="alert-text"><?php esc_html_e( 'Support for Costco will be added soon. Be sure to always update the plugin to be the first to receive the new features.', 'dropshipexpress' ); ?></div>
							</div>
						</div>
					</div>
				</form>
				<!-- End Costco Form -->

				<!-- Begin HomeDepot Form -->
				<form method="get" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ) ?>" class="dse-search-product-form hidden" data-id="homedepot">
					<div class="dse-box-body">
						<div class="dse-content-section dse-content-section-last">
							<div class="alert alert-solid-success alert-bold" role="alert">
								<div class="alert-text"><?php esc_html_e( 'Support for HomeDepot will be added soon. Be sure to always update the plugin to be the first to receive the new features.', 'dropshipexpress' ); ?></div>
							</div>
						</div>
					</div>
				</form>
				<!-- End HomeDepot Form -->

				<!-- Begin Lowe's Form -->
				<form method="get" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ) ?>" class="dse-search-product-form hidden" data-id="lowes">
					<div class="dse-box-body">
						<div class="dse-content-section dse-content-section-last">
							<div class="alert alert-solid-success alert-bold" role="alert">
								<div class="alert-text"><?php esc_html_e( 'Support for Lowe\'s will be added soon. Be sure to always update the plugin to be the first to receive the new features.', 'dropshipexpress' ); ?></div>
							</div>
						</div>
					</div>
				</form>
				<!-- End Lowe's Form -->

				<!-- Begin Ebay Form -->
				<form method="get" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ) ?>" class="dse-search-product-form hidden" data-id="ebay">
					<div class="dse-box-body">
						<div class="dse-content-section dse-content-section-last">
							<div class="alert alert-solid-success alert-bold" role="alert">
								<div class="alert-text"><?php esc_html_e( 'Support for Ebay will be added soon. Be sure to always update the plugin to be the first to receive the new features.', 'dropshipexpress' ); ?></div>
							</div>
						</div>
					</div>
				</form>
				<!-- End Ebay Form -->

				<!-- Begin Gearbest Form -->
				<form method="get" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ) ?>" class="dse-search-product-form hidden" data-id="gearbest">
					<div class="dse-box-body">
						<div class="dse-content-section dse-content-section-last">
							<div class="alert alert-solid-success alert-bold" role="alert">
								<div class="alert-text"><?php esc_html_e( 'Support for Gearbest will be added soon. Be sure to always update the plugin to be the first to receive the new features.', 'dropshipexpress' ); ?></div>
							</div>
						</div>
					</div>
				</form>
				<!-- End Gearbest Form -->

				<!-- Begin Vip.com Form -->
				<form method="get" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ) ?>" class="dse-search-product-form hidden" data-id="vip">
					<div class="dse-box-body">
						<div class="dse-content-section dse-content-section-last">
							<div class="alert alert-solid-success alert-bold" role="alert">
								<div class="alert-text"><?php esc_html_e( 'Support for Vip.com will be added soon. Be sure to always update the plugin to be the first to receive the new features.', 'dropshipexpress' ); ?></div>
							</div>
						</div>
					</div>
				</form>
				<!-- End Vip.com Form -->

				<!-- Begin JingDong Form -->
				<form method="get" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ) ?>" class="dse-search-product-form hidden" data-id="jingdong">
					<div class="dse-box-body">
						<div class="dse-content-section dse-content-section-last">
							<div class="alert alert-solid-success alert-bold" role="alert">
								<div class="alert-text"><?php esc_html_e( 'Support for JingDong will be added soon. Be sure to always update the plugin to be the first to receive the new features.', 'dropshipexpress' ); ?></div>
							</div>
						</div>
					</div>
				</form>
				<!-- End JingDong Form -->

			</div>
		</div>
		<!-- End Import Product Form -->

		<!-- Begin Import Results -->
		<div id="dse-import-results"></div>
		<!-- End Import Results -->

	</div>
</div>
<!-- End Import Product Page -->