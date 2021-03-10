<?php
	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	// Product data
	$product = DSE_Import::Post_to_Product( get_the_ID(), get_post_meta( get_the_ID(), 'dse_source', TRUE ) );

	// Validate the product
	if ( is_wp_error( $product ) ) {
		return;
	}

?>

<!-- Begin Single Imported Product Content -->
<div class="dse-box dse-box-tabs">
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ) ?>" class="dse-single-publish-form" data-id="<?php the_ID(); ?>">
		<!-- Begin Tab Headers -->
		<div class="dse-box-header">
			<div class="dse-box-header-toolbar">
				<ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#dse_single_imported_info_<?php echo esc_attr( $product->get_id() ); ?>" role="tab" aria-selected="false">
							<i class="fa fa-file-alt"></i>
							<?php esc_html_e( 'Basic Info', 'dropshipexpress' ); ?>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#dse_single_imported_desc_<?php echo esc_attr( $product->get_id() ); ?>" role="tab" aria-selected="false">
							<i class="fa fa-edit"></i>
							<?php esc_html_e( 'Description', 'dropshipexpress' ); ?>
						</a>
					</li>
					<li class="nav-item dse-tab-has-badge">
						<a class="nav-link" data-toggle="tab" href="#dse_single_imported_img_<?php echo esc_attr( $product->get_id() ); ?>" role="tab" aria-selected="false">
							<i class="fa fa-images"></i>
							<?php esc_html_e( 'Images', 'dropshipexpress' ); ?>
							<span class="dse-badge dse-badge-info  dse-badge-circle"><?php echo intval( count( $product->get_images() ) ) ?></span>
						</a>
					</li>
					<li class="nav-item dse-tab-has-badge">
						<a class="nav-link" data-toggle="tab" href="#dse_single_imported_variation_<?php echo esc_attr( $product->get_id() ); ?>" role="tab" aria-selected="false">
							<i class="fa fa-layer-group"></i>
							<?php esc_html_e( 'Variations', 'dropshipexpress' ); ?>
							<span class="dse-badge dse-badge-info  dse-badge-circle"><?php echo intval( count( $product->get_variations() ) ) ?></span>
						</a>
					</li>
					<li class="nav-item dse-tab-has-badge">
						<a class="nav-link" data-toggle="tab" href="#dse_single_imported_reviews_<?php echo esc_attr( $product->get_id() ); ?>" role="tab" aria-selected="false">
							<i class="fa fa-comments"></i>
							<?php esc_html_e( 'Reviews', 'dropshipexpress' ); ?>
							<span class="dse-badge dse-badge-info  dse-badge-circle"><?php echo intval( count( $product->get_reviews() ) ) ?></span>
						</a>
					</li>
				</ul>
			</div>
		</div>
		<!-- End Tab Headers -->

		<!-- Begin Tab Content -->
		<div class="dse-box-body">
			<div class="tab-content">

				<!--Begin Product Info Tab-->
				<div class="tab-pane dse-publish-single-info-tab active" id="dse_single_imported_info_<?php echo esc_attr( $product->get_id() ); ?>" role="tabpanel">
					<div class="dse-minibox dse-minibox-1">
						<div class="dse-box-top">
							<div class="dse-box-media dse-hidden-">
								<?php
									if ( $image_data = $product->get_images() ) {
										// Get product's thumbnail
										echo "<img class='img-responsive' src='{$image_data[0]}'>";
									} else {
										// If there is no thumbnail
										echo "<img src='" . DSE_PLUGIN_URL . "/assets/images/no-image.jpg' class='img-responsive' alt='" . esc_html__( 'No Image Available', 'dropshipexpress' ) . "'>";
									}
								?>
							</div>
							<div class="dse-minibox-content">
								<div class="form-group">
									<label for="dse_single_imported_title_<?php echo esc_attr( $product->get_id() ); ?>"><?php esc_html_e( 'Title (*):', 'dropshipexpress' ) ?></label>
									<input id="dse_single_imported_title_<?php echo esc_attr( $product->get_id() ); ?>" class="form-control" type="text" name="dse_imported_product_title" value="<?php echo esc_attr( $product->get_title() ); ?>" required>
								</div>
								<div class="form-group">
									<label for="dse_single_imported_category_<?php echo esc_attr( $product->get_id() ); ?>"><?php esc_html_e( 'Product Category (*):', 'dropshipexpress' ) ?></label>
									<?php
										// Get the default category
										$default_cat = get_term_by(
											'id',
											DSE_Settings::Get_Setting( $product->get_source(), 'default_cat' ),
											'product_cat'
										);

										$default_cat = FALSE !== $default_cat ? '(' . $default_cat->name . ')' : '';
									?>
									<select class="dse-select2" id="dse_single_imported_category_<?php echo esc_attr( $product->get_id() ); ?>" name="dse_single_imported_category" class="form-control">
										<option value="dse_default"><?php /* translators: %1$s is replaced with category's name */ printf( esc_html__( 'Default Category %1$s', 'dropshipexpress' ), $default_cat ); ?></option>
										<?php echo DSE_Import::Get_Categories(); ?>
									</select>
								</div>
							</div>
						</div>
						<div class="dse-minibox-bottom">
							<div class="dse-minibox-item">
								<div class="dse-minibox-extra">
									<span class="dse-minibox-title"><?php esc_html_e( 'Basic Information', 'dropshipexpress' ); ?></span>
								</div>
							</div>
							<div class="dse-minibox-item">
								<div class="dse-minibox-icon">
									<i class="fa fa-store"></i>
								</div>
								<div class="dse-minibox-extra">
									<span class="dse-minibox-title"><?php esc_html_e( 'Store', 'dropshipexpress' ); ?></span>
									<a href="<?php echo esc_url( $product->get_url() ) ?>" title="<?php esc_html_e( 'Click to view the product', 'dropshipexpress' ); ?>" target="_blank" class="dse-minibox-value"><?php echo strtoupper( $product->get_source() ); ?></a>
								</div>
							</div>
							<div class="dse-minibox-item">
								<div class="dse-minibox-icon">
									<i class="fa fa-dollar-sign"></i>
								</div>
								<div class="dse-minibox-extra">
									<span class="dse-minibox-title"><?php esc_html_e( 'Original Price', 'dropshipexpress' ); ?></span>
									<span class="dse-minibox-value"><?php echo esc_html( $product->get_price_formatted() ) ?></span>
								</div>
							</div>
							<div class="dse-minibox-item">
								<div class="dse-minibox-icon">
									<i class="fa fa-calendar-plus"></i>
								</div>
								<div class="dse-minibox-extra">
									<span class="dse-minibox-title"><?php esc_html_e( 'Added On', 'dropshipexpress' ); ?></span>
									<span class="dse-minibox-value"><?php echo get_the_date( get_option( 'date_format' ) ); ?></span>
								</div>
							</div>
							<div class="dse-minibox-item">
								<div class="dse-minibox-icon">
									<i class="fa fa-clock"></i>
								</div>
								<div class="dse-minibox-extra">
									<span class="dse-minibox-title"><?php esc_html_e( 'Scheduled', 'dropshipexpress' ); ?></span>
									<span class="dse-minibox-value"><?php echo 'yes' === $product->is_scheduled() ? esc_html__( 'Yes', 'dropshipexpress' ) : esc_html__( 'No', 'dropshipexpress' ) ?></span>
								</div>
							</div>
							<div class="dse-minibox-item">
								<div class="dse-minibox-icon">
									<i class="fa fa-star"></i>
								</div>
								<div class="dse-minibox-extra">
									<span class="dse-minibox-title"><?php esc_html_e( 'Rating', 'dropshipexpress' ); ?></span>
									<span class="dse-minibox-value"><?php echo sprintf( '%1$s %2$s', floatval( $product->get_rating() ), esc_html__( 'Out of 5', 'dropshipexpress' ) ); ?></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--End Product Info Tab-->

				<!-- Begin Product Description -->
				<div class="tab-pane dse-publish-single-desc-tab" id="dse_single_imported_desc_<?php echo esc_attr( $product->get_id() ); ?>" role="tabpanel">
					<div class="form-group">
						<?php wp_editor( $product->get_desc(), 'dse_single_imported_description_' . get_the_ID() ) ?>
					</div>
				</div>
				<!-- End Product Description -->

				<!-- Begin Product Images -->
				<div class="tab-pane dse-publish-single-images-tab" id="dse_single_imported_img_<?php echo esc_attr( $product->get_id() ); ?>" role="tabpanel">
					<div class="dse-images-wrapper dse-p-r-20">
						<div class="dse-box-4">
							<?php
								if ( $image_data ) {
									foreach ( $image_data as $image ) { ?>
										<div class="dse-box-4-item dse-single-image-item">
											<div class="dse-box-4-content">
												<div class="dse-box-4-img">
													<img src="<?php echo esc_url( $image ) ?>">
													<input type="hidden" name="dse_single_imported_img[]" value="<?php echo esc_url( $image ) ?>">
												</div>
												<div class="dse-widget5__section">
												<span class="dse-box-4-title">
													<?php echo esc_html( basename( $image ) ) ?>
												</span>
													<div>
														<span><?php esc_html_e( 'External URL:', 'dropshipexpress' ); ?></span>
														<a href="<?php echo esc_url( $image ) ?>" target="_blank"><?php esc_html_e( 'Click to view', 'dropshipexpress' ); ?></a>
													</div>
												</div>
											</div>
											<div class="dse-box-4-content">
												<a href="#" class="btn btn-sm btn-label-danger btn-bold dse-remove-single-imported-image"><?php esc_html_e( 'Remove', 'dropshipexpress' ); ?></a>
											</div>
										</div>
										<?php
									}
								} else { ?>
									<div class="alert alert-solid-brand alert-bold" role="alert">
										<div class="alert-icon"><i class="fa fa-exclamation-triangle"></i></div>
										<div class="alert-text"><?php esc_html_e( 'There are no images available for this product. If you have disabled the option to import the images, you can enable this option and import the product again. Alternatively, you can enable image sync on publish. This will fetch and import the images after you click the publish button.', 'dropshipexpress' ); ?></div>
									</div>
									<?php
								}
							?>
						</div>
					</div>
				</div>
				<!-- End Product Images -->

				<!-- Begin Product Variations -->
				<div class="tab-pane dse-publish-single-variations-tab" id="dse_single_imported_variation_<?php echo esc_attr( $product->get_id() ); ?>" role="tabpanel">
					<?php
						$product_attributes = $product->get_attributes();
						$product_variations = $product->get_variations();

						if ( $product_attributes && $product_variations ) { ?>
							<div class="dse-variation-wrapper dse-p-r-20">
								<?php
									foreach ( $product_variations as $variation_key => $variation ) {
										$thumbnail = '';
										$image     = '';
										if ( $variation[ 'combination' ] ) {
											foreach ( $variation[ 'combination' ] as $combination_key => $combination_value ) {
												// Check if the product has a thumbnail
												if ( $thumbnail == '' && $product_attributes[ $combination_key ][ 'values' ][ $combination_value ][ 'thumbnail' ] != '' ) {
													// Thumbnail
													$thumbnail = $product_attributes[ $combination_key ][ 'values' ][ $combination_value ][ 'thumbnail' ];
													// Full size image
													$image = $product_attributes[ $combination_key ][ 'values' ][ $combination_value ][ 'image' ];
												}
											}
										} ?>

										<div class="dse-single-variation-wrapper">
											<!-- Begin Variation Thumbnail -->
											<div class="dse-variation-img">
												<?php
													if ( isset( $thumbnail ) ) {
														echo "<img src='" . esc_url( $thumbnail ) . "' class='img-responsive'>";
													} else {
														echo "<img src='" . DSE_PLUGIN_URL . "/assets/images/no-image.jpg' class='img-responsive' alt='" . esc_html__( 'No Image Available', 'dropshipexpress' ) . "'>";
													}
												?>
											</div>
											<!-- End Variation Thumbnail -->

											<!-- Begin Variation Input Boxes -->
											<div class="dse-variation-details dse-form dse-form-label-right">
												<div class="form-group row">
													<label class="col-2 col-form-label"><?php esc_html_e( 'Price', 'dropshipexpress' ); ?></label>
													<div class="col-3">
														<div class="input-group input-group-sm">
															<input name="dse_single_imported_variation[<?php echo esc_attr( $variation_key ) ?>][price]" class="form-control form-control-sm dse-variation-price" type="number" min="0.01" step="0.01" value="<?php echo esc_attr( $variation[ 'price' ] ); ?>">
															<div class="input-group-append">
																<span class="input-group-text"><?php echo esc_html( $variation[ 'price_currency' ] ); ?></span>
															</div>
														</div>
													</div>

													<label class="col-2 col-form-label"><?php esc_html_e( 'SKU', 'dropshipexpress' ); ?></label>
													<div class="col-3">
														<input class="form-control form-control-sm dse-variation-price" type="text" value="<?php echo esc_attr( $variation[ 'sku' ] ); ?>" disabled="disabled">
													</div>
												</div>
												<div class="form-group form-group-last row">
													<label class="col-2 col-form-label"><?php esc_html_e( 'Discounted Price', 'dropshipexpress' ); ?></label>
													<div class="col-3">
														<div class="input-group input-group-sm">
															<input name="dse_single_imported_variation[<?php echo esc_attr( $variation_key ) ?>][discounted_price]" class="form-control form-control-sm dse-variation-price" type="number" min="0.01" step="0.01" value="<?php echo esc_attr( $variation[ 'discounted_value' ] ); ?>">
															<div class="input-group-append">
																<span class="input-group-text"><?php echo esc_html( $variation[ 'discount_currency' ] ); ?></span>
															</div>
														</div>
													</div>

													<label class="col-2 col-form-label"><?php esc_html_e( 'Quantity', 'dropshipexpress' ); ?></label>
													<div class="col-3">
														<input name="dse_single_imported_variation[<?php echo esc_attr( $variation_key ) ?>][quantity]" class="form-control form-control-sm dse-variation-price" type="number" min="0" step="1" value="<?php echo esc_attr( $variation[ 'quantity' ] ); ?>">
													</div>
												</div>
											</div>
											<!-- End Variation Input Boxes -->

											<!-- Begin Variation Details -->
											<?php
												if ( $variation[ 'combination' ] ) { ?>
													<div class="dse-variation-info">
														<div class="table-responsive">
															<table class="table table-bordered table-head-solid">

																<tbody>
																<?php
																	foreach ( $variation[ 'combination' ] as $key => $product_combination ) { ?>
																		<tr>
																			<td>
																				<?php echo esc_html( $product_attributes[ $key ][ 'name' ] ); ?>
																				<input type="hidden" name="dse_single_imported_variation[<?php echo esc_attr( $variation_key ) ?>][details][<?php echo esc_attr( $key ) ?>]" value="<?php echo esc_attr( $product_combination ); ?>">
																			</td>
																			<td>
																				<span class="dse-plain-font-color"><?php echo esc_html( $product_attributes[ $key ][ 'values' ][ $product_combination ][ 'name' ] ) ?></span>
																			</td>
																		</tr>
																		<?php
																	}
																?>
																</tbody>
															</table>
														</div>
													</div>
													<?php
												}
											?>
											<!-- End Variation Details -->
											<input type="hidden" name="dse_single_imported_variation[<?php echo esc_attr( $variation_key ) ?>][key]" value="<?php echo esc_attr( $variation_key ) ?>">
											<div class="dse-variation-controls">
												<a href="#" class="btn btn-sm btn-label-danger btn-bold"><?php esc_html_e( 'Remove', 'dropshipexpress' ); ?></a>
											</div>
										</div>
										<?php
									}
								?>
							</div>
							<?php
						} else { ?>
							<div class="alert alert-solid-brand alert-bold" role="alert">
								<div class="alert-icon"><i class="fa fa-exclamation-triangle"></i></div>
								<div class="alert-text"><?php esc_html_e( 'There are no variations available for this product. If you have disabled the option to import the variations, you can enable this option and import the product again. Alternatively, you can enable the variation sync on publish. This will fetch and import the variations after you click the publish button.', 'dropshipexpress' ); ?></div>
							</div>
							<?php
						}
					?>
				</div>
				<!-- End Product Variations -->

				<!-- Begin Product Reviews -->
				<div class="tab-pane dse-publish-single-reviews-tab" id="dse_single_imported_reviews_<?php echo esc_attr( $product->get_id() ); ?>" role="tabpanel">

					<div class="dse-reviews-wrapper dse-p-r-20">
						<?php
							if ( $product_reviews = $product->get_reviews() ) { ?>
								<div class="dse-note">
									<div class="dse-note-items">
										<?php
											foreach ( $product_reviews as $review ) { ?>
												<div class="dse-note-item dse-single-review">
													<div class="dse-note-media">
														<!--<img class="dse-hidden-" src="" alt="<?php echo esc_html( $review[ 'username' ] ) ?>">-->
														<span class="dse-note-icon dse-notes__icon--danger dse-bold-max">
														<i class="fa fa-image dse-font-error"></i>
													</span>
													</div>
													<div class="dse-note-content">
														<div class="dse-note-section">
															<div class="dse-note-info">
																<a href="#" class="dse-note-title"><?php echo esc_html( $review[ 'username' ] ) ?></a>
																<span class="dse-note-description"><?php echo esc_html( $review[ 'date' ] ) ?></span>
															</div>
															<div class="dse-notes__dropdown">
																<button class="btn btn-danger btn-bold btn btn-sm dse-remove-single-imported-review" type="button">
																	<i class="fa fa-times"></i>
																	<?php esc_html_e( 'Remove', 'dropshipexpress' ); ?>
																</button>
															</div>
														</div>
														<span class="dse-note-body"><?php echo esc_html( $review[ 'content' ] ); ?></span>
													</div>
													<?php echo "<input type='hidden' name='dse_single_imported_review[]' value='{$review['key']}'>"; ?>
												</div>
												<?php
											} ?>
									</div>
								</div>
								<?php
							} else { ?>
								<div class="alert alert-solid-brand alert-bold" role="alert">
									<div class="alert-icon"><i class="fa fa-exclamation-triangle"></i></div>
									<div class="alert-text"><?php esc_html_e( 'There are no reviews available for this product. If you have disabled the option to import the reviews, you can enable this option and import the product again. Alternatively, you can enable the review sync on publish. This will fetch and import the reviews after you click the publish button.', 'dropshipexpress' ); ?></div>
								</div>
								<?php
							}
						?>
					</div>
				</div>
				<!-- End Product Reviews -->

			</div>
		</div>
		<!-- End Tab Content -->

		<div class="dse-box-footer">
			<div class="row">
				<div class="col-lg-12 text-right">
					<button class="btn btn-danger btn-bold dse-single-publish-submit" type="submit" name="dse_single_publish_submit" value="delete">
						<i class="fa fa-times"></i> <?php esc_html_e( 'Delete Product', 'dropshipexpress' ); ?>
					</button>
					<button class="btn btn-primary btn-bold dse-single-publish-submit" type="submit" name="dse_single_publish_submit" value="publish">
						<i class="fa fa-pen-nib"></i> <?php esc_html_e( 'Save and Publish', 'dropshipexpress' ); ?>
					</button>
				</div>
			</div>
		</div>
		<input type="hidden" name="dse_single_import_id" value="<?php the_ID(); ?>">
		<input type="hidden" name="action" value="dse_single_publish">
		<input type="hidden" name="dse_single_import_source" value="<?php echo esc_attr( $product->get_source() ) ?>">
		<?php wp_nonce_field( 'dse-publish-product-nonce-3312', 'dse_single_publish_nonce_' . get_the_ID() ); ?>
	</form>
</div>
<!-- End Single Imported Product Content -->
