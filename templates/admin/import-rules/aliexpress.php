<?php
	/**
	 * Output the setting tab content for aliexpress
	 * api
	 *
	 */

	// Get the currency format
	$symbol = DSE_Import::Get_Currency_Symbol( 'aliexpress' );

?>

<div id="dse_import_rule_content_aliexpress" class="dse-api-settings-tabs tab-pane fade show active" data-rule-api="aliexpress" role="tabpanel">
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>" method="post">
		<div class="dse-content-section dse-content-section-last">
			<div class="dse-section-body">
				<!-- Begin Import Rules List -->
				<div class="form-group form-group-last row">
					<div class="col-lg-12 col-xl-12">
						<div class="dse-box dse-box-bordered">
							<div class="dse-box-body">

								<div class="form-group row">
									<div class="col-md-12">
										<label>
											<?php esc_html_e( 'Keyword (required)', 'dropshipexpress' ); ?>
										</label>
										<input type="text" name="dse_import_rules_keyword_text" class="form-control" placeholder="<?php esc_html_e( 'Enter a keyword to search. e.g. Dress', 'dropshipexpress' ); ?>">
									</div>
								</div>

								<div class="form-group form-group-last row">
									<div class="col-md-12">

										<div class="form-group">
											<div class="dse-list-checkbox">
												<label class="dse-checkbox dse-checkbox--success">
													<input type="checkbox" name="dse_import_rules_type"> <?php esc_html_e( 'Only search the following category?', 'dropshipexpress' ); ?>
													<span></span>
												</label>
											</div>
											<?php include( DSE_PLUGIN_FOLDER . '/templates/admin/import-rules/aliexpress/category-index.php' ); ?>

											<select id="dse_import_rules_cat" name="dse_import_rules_cat" class="form-control">
												<option value="dse_all" selected="selected"><?php esc_html_e( 'All Categories', 'dropshipexpress' ); ?></option>
												<?php
													$categories = DSE_Import::Get_Category_Index( 'aliexpress' );

													foreach ( $categories as $parent_key => $category ) {
														echo "<optgroup label='{$category[$parent_key]}'>";

														foreach ( $category as $sub_key => $sub_category ) {
															if ( $sub_key === $parent_key ) {
																/* translators: %1$s is replaced with the category's name */
																echo "<option value='{$sub_key}^{$sub_category}'>" . sprintf( esc_html__( '%1$s (All)', 'dropshipexpress' ), $sub_category ) . "</option>";
															} else {
																echo "<option value='{$sub_key}^{$sub_category}'>" . esc_html( $sub_category ) . "</option>";
															}
														}

														echo "</optgroup>";
													}
												?>
											</select>
										</div>
									</div>
								</div>

								<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

								<div class="form-group form-group-last row">
									<!-- Begin Import Price From -->
									<div class="col-lg-3">
										<label><?php esc_html_e( 'Price From', 'dropshipexpress' ); ?></label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><?php echo esc_html( $symbol ) ?></span>
											</div>
											<input class="form-control" type="number" min="0" name="dse_import_rules_price_from">
										</div>
									</div>
									<!-- End Import Price From -->

									<!-- Begin Import Price To -->
									<div class="col-lg-3">
										<label><?php esc_html_e( 'Price To', 'dropshipexpress' ); ?></label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><?php echo esc_html( $symbol ) ?></span>
											</div>
											<input class="form-control" type="number" min="0" name="dse_import_rules_price_to">
										</div>
									</div>
									<!-- End Import Price To -->

									<!-- Begin Delivery Field -->
									<div class="col-lg-3">
										<label><?php esc_html_e( 'Delivery Delay', 'dropshipexpress' ); ?></label>
										<div class="dse-input-group-icon">
											<select name="dse_import_rules_delivery" class="dse-select2">
												<option selected value="3"><?php esc_html_e( '3 Days', 'dropshipexpress' ); ?></option>
												<option value="7"><?php esc_html_e( '7 Days', 'dropshipexpress' ); ?></option>
												<option value="10"><?php esc_html_e( '10 Days', 'dropshipexpress' ); ?></option>
											</select>
										</div>
									</div>
									<!-- End Delivery Field -->

									<!-- Begin Destination Field -->
									<div class="col-lg-3">
										<label><?php esc_html_e( 'Destination', 'dropshipexpress' ); ?></label>
										<div class="dse-input-group-icon">
											<select name="dse_import_rules_destination" class="dse-select2">
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
									</div>
									<!-- End Destination Field -->
								</div>

								<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

								<div class="form-group row">

									<div class="col-lg-6 dse-vertical-align text-left">
										<?php esc_html_e( 'Automatically import a product every :', 'dropshipexpress' ); ?>
									</div>

									<div class="col-lg-3">
										<input required type="text" class="form-control text-center dse-import-rules-timer" value="10" name="dse_import_rules_timer" placeholder="<?php esc_html_e( 'Select Delay', 'dropshipexpress' ); ?>">
									</div>

									<div class="col-lg-3">
										<select required class="form-control dse-import-rules-select" name="dse_import_rules_delay">
											<option selected value="minute"><?php esc_html_e( 'Minute(s)', 'dropshipexpress' ); ?></option>
											<option value="hour"><?php esc_html_e( 'Hour(s)', 'dropshipexpress' ); ?></option>
											<option value="day"><?php esc_html_e( 'Day(s)', 'dropshipexpress' ); ?></option>
										</select>
									</div>

								</div>

							</div>
						</div>
					</div>
				</div>
				<!-- End Import Rules List -->
			</div>
			<div class="dse-form-actions dse-form-actions-right">
				<button type="submit" class="btn btn-bold btn-label-success" <?php echo ! DSE_Settings::Is_Premium() ? 'disabled' : '' ?>>
					<i class="fa fa-plus"></i><?php esc_html_e( 'Add Rule', 'dropshipexpress' ); ?>
				</button>
			</div>
		</div>

		<?php wp_nonce_field( 'dse-save-import-rules-nonce-action', 'dse_import_rules_nonce' ) ?>
		<input type="hidden" value="dse_save_import_rules" name="action">
		<input type="hidden" value="aliexpress" name="dse_import_rule_api">
	</form>
</div>