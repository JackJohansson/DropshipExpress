<?php
	/**
	 * Output the setting tab content for aliexpress
	 * api
	 *
	 */
?>

<div id="dse_api_content_aliexpress" class="dse-api-settings-tabs tab-pane fade show active" role="tabpanel">

	<!-- Begin AliExpress Tab Headers -->
	<ul class="nav nav-tabs nav-tabs-line nav-tabs-line-2x nav-tabs-line-primary" role="tablist">

		<li class="nav-item">
			<a class="nav-link active" data-toggle="tab" href="#dse_aliexpress_settings_general" role="tab">
				<i class="fa fa-user-circle"></i>
				<?php esc_html_e( 'General', 'dropshipexpress' ); ?>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#dse_aliexpress_settings_automation" role="tab">
				<i class="fa fa-clock"></i>
				<?php esc_html_e( 'Schedule', 'dropshipexpress' ); ?>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#dse_aliexpress_settings_sync" role="tab">
				<i class="fa fa-sync-alt"></i>
				<?php esc_html_e( 'Sync', 'dropshipexpress' ); ?>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#dse_aliexpress_settings_shipping" role="tab">
				<i class="fa fa-shipping-fast"></i>
				<?php esc_html_e( 'Shipping', 'dropshipexpress' ); ?>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#dse_aliexpress_settings_pricing" role="tab">
				<i class="fa fa-dollar-sign"></i>
				<?php esc_html_e( 'Price', 'dropshipexpress' ); ?>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#dse_aliexpress_settings_stock" role="tab">
				<i class="fa fa-layer-group"></i>
				<?php esc_html_e( 'Stock Manager', 'dropshipexpress' ); ?>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#dse_aliexpress_settings_reviews" role="tab">
				<i class="fa fa-comments"></i>
				<?php esc_html_e( 'Reviews', 'dropshipexpress' ); ?>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#dse_aliexpress_settings_replace" role="tab">
				<i class="fa fa-search"></i>
				<?php esc_html_e( 'Search & Replace', 'dropshipexpress' ); ?>
			</a>
		</li>

		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#dse_aliexpress_settings_extra" role="tab">
				<i class="fa fa-cogs"></i>
				<?php esc_html_e( 'Misc', 'dropshipexpress' ); ?>
			</a>
		</li>

	</ul>
	<!-- End AliExpress Tab Headers -->

	<!-- Begin AliExpress Tab Content -->
	<div class="tab-content">

		<!-- Begin AliExpress API Credentials -->
		<div class="tab-pane fade show active dse-api-settings-tab-content" id="dse_aliexpress_settings_general" role="tabpanel">

			<div class="dse-content-section dse-content-section-first">
				<div class="dse-content-section-body">
					<div class="row">
						<label class="col-xl-3"></label>
						<div class="col-lg-9 col-xl-6">
							<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'AliExpress Automation', 'dropshipexpress' ); ?></h3>
						</div>
					</div>

					<!-- Begin AliExpress Activation -->
					<div class="form-group form-group-sm row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Enable AliExpress Integration?', 'dropshipexpress' ); ?></label>

						<div class="col-lg-9 col-xl-6">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_aliexpress_enable" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'enable' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'Disabling this option will turn off most automatic tasks. You can still manually perform those.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End AliExpress Activation -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<!-- Begin API Credentials -->
					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Use Official API?', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_aliexpress_official_api" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'official_api' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'Enable this option to use your official API key and secret from AliExpress.', 'dropshipexpress' ); ?></span>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'API Key', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-key"></i></span>
								</div>
								<input class="form-control" type="text" value="<?php echo DSE_Settings::Get_Setting( 'aliexpress', 'api_key' ); ?>" name="dse_aliexpress_api_key">
							</div>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'API Secret', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-lock"></i></span>
								</div>
								<input class="form-control dse-italic-input" type="password" placeholder="<?php esc_html_e( 'API secret is hidden for security. Enter a new value to change.', 'dropshipexpress' ); ?>" name="dse_aliexpress_api_secret">
							</div>
							<span class="form-text text-muted"><?php echo wp_kses( __( '<strong>Note:</strong> In order to use the official API, you need to create an app on AliExpress and enter your credentials here. Providing the wrong credentials will stop the plugin from working.', 'dropshipexpress' ), [ 'strong' => [] ] ); ?></span>
						</div>
					</div>
					<!-- End API Credentials -->

				</div>
			</div>

		</div>
		<!-- End AliExpress API Credentials -->

		<!-- Begin AliExpress Automation -->
		<div class="tab-pane fade dse-api-settings-tab-content" id="dse_aliexpress_settings_automation" role="tabpanel">
			<div class="dse-content-section dse-content-section-first">
				<div class="dse-content-section-body">

					<!-- Begin Automatic Publish -->
					<div class="row">
						<label class="col-xl-3"></label>
						<div class="col-lg-9 col-xl-6">
							<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'Automatic Publish', 'dropshipexpress' ); ?></h3>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Publish Automatically', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_aliexpress_auto_publish" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'auto_publish' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'Enabling this option will automatically publish the queued products based on the defined rules below.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End Automatic Publish -->

					<!-- Begin Publish Schedule -->
					<div class="row">
						<label class="col-xl-3"></label>
						<div class="col-lg-9 col-xl-6">
							<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'Publish Schedule', 'dropshipexpress' ); ?></h3>
						</div>
					</div>

					<div class="form-group row">

						<label class="col-xl-3 col-lg-3"></label>

						<div class="col-lg-2 col-md-9 col-sm-12">
							<select class="form-control dse-select2" data-size="5" name="dse_aliexpress_schedule_count">
								<?php
									for ( $i = 1; $i < 11; $i++ ) {
										echo "<option " . selected( DSE_Settings::Get_Setting( 'aliexpress', 'schedule_count' ), $i, FALSE ) . " value='{$i}'>" . esc_html__( 'Publish : ', 'dropshipexpress' ) . "{$i}</option>";
									}
								?>
							</select>
						</div>

						<div class="col-lg-2 dse-vertical-align text-center">
							<?php esc_html_e( 'Product(s) Every', 'dropshipexpress' ); ?>
						</div>

						<div class="col-lg-2">
							<input id="dse-aliexpress-schedule-value" type="text" class="form-control text-center dse-selector-schedule-value" value="<?php echo esc_html( DSE_Settings::Get_Setting( 'aliexpress', 'schedule_every' ) ) ?>" name="dse_aliexpress_schedule_every" placeholder="<?php esc_html_e( 'Select Delay', 'dropshipexpress' ); ?>">
						</div>

						<div class="col-lg-2">
							<select class="form-control dse-select2" name="dse_aliexpress_schedule_delay">
								<option <?php selected( DSE_Settings::Get_Setting( 'aliexpress', 'schedule_delay' ), 'minute' ) ?> value="minute"><?php esc_html_e( 'Minute(s)', 'dropshipexpress' ); ?></option>
								<option <?php selected( DSE_Settings::Get_Setting( 'aliexpress', 'schedule_delay' ), 'hour' ) ?> value="hour"><?php esc_html_e( 'Hour(s)', 'dropshipexpress' ); ?></option>
								<option <?php selected( DSE_Settings::Get_Setting( 'aliexpress', 'schedule_delay' ), 'day' ) ?> value="day"><?php esc_html_e( 'Day(s)', 'dropshipexpress' ); ?></option>
							</select>
						</div>

						<div class="dse-clearfix"></div>

						<label class="col-xl-3 col-lg-3 col-form-label"></label>
						<div class="col-lg-9 dse-m-t-10">
							<span class="form-text text-muted"><?php esc_html_e( 'Choose how often should the queued products be published automatically. Note that publishing a high number of products in a short time can result in a failure, if the host is not fast enough.', 'dropshipexpress' ); ?></span>
						</div>
					</div>

					<!-- End Publish Schedule -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<!-- Begin Import Rules -->
					<div class="row">
						<label class="col-xl-3"></label>
						<div class="col-lg-9 col-xl-6">
							<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'Automatic Import', 'dropshipexpress' ); ?></h3>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Import Automatically', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_aliexpress_auto_import" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'auto_import' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'Enabling this will automatically import products from AliExpress to your import queue based on below rules.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End Import Rules -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<!-- Begin Import Rules List -->
					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Import Rules', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<div class="alert alert-solid-success alert-bold" role="alert">
								<div class="alert-text"><?php esc_html_e( 'To add custom rules for importing products automatically, navigate to "DropshipExpress > Import Rules"', 'dropshipexpress' ); ?></div>
							</div>
						</div>
					</div>
					<!-- End Import Rules List -->

				</div>
			</div>
		</div>
		<!-- End AliExpress Automation -->

		<!-- Begin AliExpress Sync -->
		<div class="tab-pane fade dse-api-settings-tab-content" id="dse_aliexpress_settings_sync" role="tabpanel">
			<div class="dse-content-section dse-content-section-first">
				<div class="dse-content-section-body">

					<!-- Begin Automatic Sync -->
					<div class="row">
						<label class="col-xl-3 col-lg-3 col-form-label"></label>
						<div class="col-lg-9 col-xl-6">
							<div class="alert alert-solid-danger alert-bold" role="alert">
								<div class="alert-icon"><i class="fa fa-exclamation-circle"></i></div>
								<div class="alert-text"><?php esc_html_e( 'IMPORTANT NOTE: Enabling these options will override the changes you\'ve manually made to the product, based on your selection.', 'dropshipexpress' ); ?></div>
							</div>
						</div>
					</div>

					<div class="row">
						<label class="col-xl-3"></label>
						<div class="col-lg-9 col-xl-6">
							<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'Sync on View', 'dropshipexpress' ); ?></h3>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-3 col-form-label"><?php esc_html_e( 'On View', 'dropshipexpress' ); ?></label>
						<div class="col-9">
							<div class="dse-inline-checkbox">
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_auto_sync_title" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'auto_sync_title' ), 'yes' ) ?>> <?php esc_html_e( 'Title', 'dropshipexpress' ); ?>
									<span></span>
								</label>
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_auto_sync_images" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'auto_sync_images' ), 'yes' ) ?>> <?php esc_html_e( 'Images', 'dropshipexpress' ); ?>
									<span></span>
								</label>
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_auto_sync_desc" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'auto_sync_desc' ), 'yes' ) ?>> <?php esc_html_e( 'Description', 'dropshipexpress' ); ?>
									<span></span>
								</label>
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_auto_sync_price" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'auto_sync_price' ), 'yes' ) ?>> <?php esc_html_e( 'Price', 'dropshipexpress' ); ?>
									<span></span>
								</label>
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_auto_sync_stock" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'auto_sync_stock' ), 'yes' ) ?>> <?php esc_html_e( 'Stock', 'dropshipexpress' ); ?>
									<span></span>
								</label>
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_auto_sync_reviews" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'auto_sync_reviews' ), 'yes' ) ?>> <?php esc_html_e( 'Reviews', 'dropshipexpress' ); ?>
									<span></span>
								</label>
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_auto_sync_variations" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'auto_sync_variations' ), 'yes' ) ?>> <?php esc_html_e( 'Variations', 'dropshipexpress' ); ?>
									<span></span>
								</label>
							</div>
							<span class="form-text text-muted"><?php esc_html_e( '1- Choose which of the above should be synchronized automatically each time a product is viewed by a logged-in user.', 'dropshipexpress' ); ?></span>
							<span class="form-text text-muted"><?php echo wp_kses( __( '2- <strong>Note: </strong>Enabling variation syncing might decrease the performance of your website.', 'dropshipexpress' ), [ 'strong' => [] ] ); ?></span>
						</div>
					</div>
					<!-- End Automatic Sync -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<!-- Begin Sync On Publish -->
					<div class="row">
						<label class="col-xl-3"></label>
						<div class="col-lg-9 col-xl-6">
							<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'Sync on Publish', 'dropshipexpress' ); ?></h3>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-3 col-form-label"><?php esc_html_e( 'On Publish', 'dropshipexpress' ); ?></label>
						<div class="col-9">
							<div class="dse-inline-checkbox">
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_publish_sync_title" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'publish_sync_title' ), 'yes' ) ?>> <?php esc_html_e( 'Title', 'dropshipexpress' ); ?>
									<span></span>
								</label>
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_publish_sync_images" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'publish_sync_images' ), 'yes' ) ?>> <?php esc_html_e( 'Images', 'dropshipexpress' ); ?>
									<span></span>
								</label>
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_publish_sync_desc" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'publish_sync_desc' ), 'yes' ) ?>> <?php esc_html_e( 'Description', 'dropshipexpress' ); ?>
									<span></span>
								</label>
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_publish_sync_price" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'publish_sync_price' ), 'yes' ) ?>> <?php esc_html_e( 'Price', 'dropshipexpress' ); ?>
									<span></span>
								</label>
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_publish_sync_stock" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'publish_sync_stock' ), 'yes' ) ?>> <?php esc_html_e( 'Stock', 'dropshipexpress' ); ?>
									<span></span>
								</label>
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_publish_sync_reviews" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'publish_sync_reviews' ), 'yes' ) ?>> <?php esc_html_e( 'Reviews', 'dropshipexpress' ); ?>
									<span></span>
								</label>
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_publish_sync_variations" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'publish_sync_variations' ), 'yes' ) ?>> <?php esc_html_e( 'Variations', 'dropshipexpress' ); ?>
									<span></span>
								</label>
							</div>
							<span class="form-text text-muted"><?php esc_html_e( 'Choose which of the above should be synchronized automatically when an imported product is published on your website.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End Sync On Publish -->

				</div>
			</div>
		</div>
		<!-- End AliExpress Sync -->

		<!-- Begin AliExpress Shipping -->
		<div class="tab-pane fade dse-api-settings-tab-content" id="dse_aliexpress_settings_shipping" role="tabpanel">

			<div class="dse-content-section dse-content-section-first">
				<div class="dse-content-section-body">

					<!-- Begin AliExpress Automatic Shipment -->
					<div class="form-group form-group-sm row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Automatic Shipment', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_aliexpress_auto_ship" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'auto_ship' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted">
								<?php echo wp_kses( __( 'Enabling this option will try to automatically purchase the product from AliExpress and ship it to the customer. To use this feature, you need to sing up for an account at <a href="https://login.zinc.io/signup" target="_blank">ZincApi.</a>', 'dropshipexpress' ), [ 'a' => [ 'href' => [], 'target' => [], ], ] ); ?>
							</span>
						</div>
					</div>
					<!-- End AliExpress Automatic Shipment -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<!-- Begin ZincAPI Credentials -->
					<div class="dse-content-section dse-content-section-first">
						<div class="dse-content-section-body">
							<div class="row">
								<label class="col-xl-3"></label>
								<div class="col-lg-9 col-xl-6">
									<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'ZincAPI Credentials', 'dropshipexpress' ); ?></h3>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'API Token', 'dropshipexpress' ); ?></label>
								<div class="col-lg-9 col-xl-6">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa fa-key"></i></span>
										</div>
										<input class="form-control dse-italic-input" type="password" placeholder="<?php esc_html_e( 'Type a new token to change. Token is hidden due to security reasons.', 'dropshipexpress' ); ?>" name="dse_aliexpress_zinc_token">
									</div>
									<span class="form-text text-muted"><?php esc_html_e( 'In order to integrate with your ZincAPI account, you need to enter your account\'s credentials above.', 'dropshipexpress' ); ?></span>
								</div>
							</div>

						</div>
					</div>
					<!-- End ZincAPI Credentials -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<!-- Begin AliExpress Login Info -->
					<div class="dse-content-section dse-content-section-first">
						<div class="dse-content-section-body">
							<div class="row">
								<label class="col-xl-3"></label>
								<div class="col-lg-9 col-xl-6">
									<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'AliExpress Credentials', 'dropshipexpress' ); ?></h3>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Username or E-mail', 'dropshipexpress' ); ?></label>
								<div class="col-lg-9 col-xl-6">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa fa-user"></i></span>
										</div>
										<input class="form-control" type="text" value="<?php echo DSE_Settings::Get_Setting( 'aliexpress', 'login_username' ); ?>" name="dse_aliexpress_login_username">
									</div>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Password', 'dropshipexpress' ); ?></label>
								<div class="col-lg-9 col-xl-6">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa fa-lock"></i></span>
										</div>
										<input class="form-control dse-italic-input" type="password" placeholder="<?php esc_html_e( 'Type a new password to change. Password is hidden due to security reasons.', 'dropshipexpress' ); ?>" name="dse_aliexpress_login_pass">
									</div>
									<span class="form-text text-muted"><?php esc_html_e( 'In order to place a purchase, you need to enter the login information for your AliExpress account. The orders will be placed using this account.', 'dropshipexpress' ); ?></span>
								</div>
							</div>

						</div>
					</div>
					<!-- End AliExpress Login Info -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<!-- Begin Credit Card Credentials -->
					<div class="dse-content-section dse-content-section-first">
						<div class="dse-content-section-body">
							<div class="row">
								<label class="col-xl-3"></label>
								<div class="col-lg-9 col-xl-6">
									<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'Credit Card Credentials', 'dropshipexpress' ); ?></h3>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label"></label>
								<div class="col-lg-9 col-xl-6">
									<label class=""><?php esc_html_e( 'Name on the card', 'dropshipexpress' ); ?></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa fa-id-card"></i></span>
										</div>
										<input class="form-control" type="text" value="<?php echo esc_html( DSE_Settings::Get_Setting( 'aliexpress', 'card_name' ) ) ?>" name="dse_aliexpress_card_name">
									</div>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label"></label>
								<div class="col-lg-9 col-xl-6">
									<label class=""><?php esc_html_e( 'Credit Card Number', 'dropshipexpress' ); ?></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa fa-credit-card"></i></span>
										</div>
										<input class="form-control dse_cc_number" value="<?php echo esc_html( DSE_Settings::Get_Setting( 'aliexpress', 'card_number' ) ) ?>" placeholder="xxxx xxxx xxxx xxxx" name="dse_aliexpress_card_number">
									</div>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label"></label>
								<div class="col-lg-2 col-xl-2">
									<label class=""><?php esc_html_e( 'Expiry Year', 'dropshipexpress' ); ?></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa fa-calendar-week"></i></span>
										</div>
										<input class="form-control dse_cc_year" placeholder="YYYY" type="tel" value="<?php echo esc_html( DSE_Settings::Get_Setting( 'aliexpress', 'card_expiry_year' ) ) ?>" name="dse_aliexpress_card_expiry_year">
									</div>
								</div>
								<div class="col-lg-2 col-xl-2">
									<label class=""><?php esc_html_e( 'Expiry Month', 'dropshipexpress' ); ?></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa fa-calendar-day"></i></span>
										</div>
										<input class="form-control dse_cc_month" placeholder="MM" type="tel" value="<?php echo esc_html( DSE_Settings::Get_Setting( 'aliexpress', 'card_expiry_month' ) ) ?>" name="dse_aliexpress_card_expiry_month">
									</div>
								</div>
								<div class="col-lg-2 col-xl-2">
									<label class=""><?php esc_html_e( 'Security Code (CVV2)', 'dropshipexpress' ); ?></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa fa-fingerprint"></i></span>
										</div>
										<input class="form-control" max="3" placeholder="***" type="password" name="dse_aliexpress_card_cvv">
									</div>
								</div>
								<div class="col-3"></div>
								<div class="col-3"></div>
								<div class="col-9">
									<span class="form-text text-muted"><?php esc_html_e( 'To automatically purchase the products from the source store, please enter your credit card details.', 'dropshipexpress' ); ?></span>
								</div>
							</div>

						</div>
					</div>
					<!-- End Credit Card Credentials -->
				</div>
			</div>

		</div>
		<!-- End AliExpress Shipping -->

		<!-- Begin AliExpress Pricing -->
		<div class="tab-pane fade dse-api-settings-tab-content" id="dse_aliexpress_settings_pricing" role="tabpanel">

			<div class="dse-content-section dse-content-section-first">
				<div class="dse-content-section-body">

					<!-- Begin AliExpress Price Rules -->
					<div class="form-group row">

						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Product Price', 'dropshipexpress' ); ?></label>

						<div class="col-lg-9 col-xl-6">

							<div class="row">
								<label class="dse-option-group dse-option-group dse-option-group-default dse-m-b-0">
								<span class="dse-option-group-control">
									<span class="dse-radio dse-radio--check-bold">
										<input type="radio" name="dse_aliexpress_price_type" value="original" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'price_type' ), 'original' ) ?>>
										<span></span>
									</span>
								</span>
									<span class="dse-option-group-label">
									<span class="dse-option-group-header">
										<span class="dse-option-group-title">
											<?php esc_html_e( 'Original Price (Not Recommended)', 'dropshipexpress' ); ?>
										</span>
									</span>
									<span class="dse-option-group-body">
										<?php esc_html_e( 'Keep the original price. Useful for testing purposes.', 'dropshipexpress' ); ?>
									</span>
								</span>
								</label>
							</div>

							<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

							<div class="row">
								<div class="col-sm-12 dse-p-0">
									<label class="dse-option-group dse-option-group dse-option-group-default">
										<span class="dse-option-group-control">
											<span class="dse-radio dse-radio--check-bold">
												<input type="radio" name="dse_aliexpress_price_type" value="flat" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'price_type' ), 'flat' ) ?>>
												<span></span>
											</span>
										</span>
										<span class="dse-option-group-label">
											<span class="dse-option-group-header">
												<span class="dse-option-group-title">
													<?php esc_html_e( 'Apply Flat Rate', 'dropshipexpress' ); ?>
												</span>
											</span>
											<span class="dse-option-group-body">
												<?php esc_html_e( 'Add a specific amount to each imported product\'s price.', 'dropshipexpress' ); ?>
											</span>
										</span>
									</label>
								</div>

								<div class="form-group form-group-marginless">
									<div class="col-lg-12 dse-p-0">
										<input id="dse-aliexpress-price-flat" type="text" class="form-control text-center dse-selector-price-flat" value="<?php echo esc_html( DSE_Settings::Get_Setting( 'aliexpress', 'price_flat_value' ) ) ?>" name="dse_aliexpress_price_flat_value" placeholder="<?php esc_html_e( 'Enter a number', 'dropshipexpress' ); ?>">
									</div>
								</div>

							</div>

							<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

							<div class="row">
								<div class="col-sm-12 dse-p-0">
									<label class="dse-option-group dse-option-group dse-option-group-default">
										<span class="dse-option-group-control">
											<span class="dse-radio dse-radio--check-bold">
												<input type="radio" name="dse_aliexpress_price_type" value="percent" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'price_type' ), 'percent' ) ?>>
												<span></span>
											</span>
										</span>
										<span class="dse-option-group-label">
											<span class="dse-option-group-header">
												<span class="dse-option-group-title">
													<?php esc_html_e( 'Apply Percentage', 'dropshipexpress' ); ?>
												</span>
											</span>
											<span class="dse-option-group-body">
												<?php esc_html_e( 'Add a percentage of product\'s price to its final price.', 'dropshipexpress' ); ?>
											</span>
										</span>
									</label>
								</div>

								<div class="form-group form-group-marginless">
									<div class="input-group">
										<input type="number" min="1" name="dse_aliexpress_price_percent_value" value="<?php echo esc_html( DSE_Settings::Get_Setting( 'aliexpress', 'price_percent_value' ) ) ?>" class="form-control" placeholder="<?php esc_html_e( 'Value in %', 'dropshipexpress' ); ?>">
										<div class="input-group-append">
											<span class="input-group-text">%</span>
										</div>
									</div>
								</div>

							</div>

						</div>
					</div>
					<!-- End AliExpress Price Rules -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<!-- Begin AliExpress Currency -->
					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Currency', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-md-12 col-sm-12 dse-p-0">
							<div class="col-lg-4 col-md-6 col-sm-12 dse-p-0">
								<select class="form-control dse-select2" data-size="5" name="dse_aliexpress_currency">
									<?php
										// Get the current currency and a list of available currencies
										$current_currency = DSE_Settings::Get_Setting( 'aliexpress', 'currency' );
										$currencies       = DSE_Settings::Get_Currencies( 'aliexpress' );

										// Default option
										echo "<option " . selected( $current_currency, 'auto' ) . " value='auto'>" . esc_html__( 'Automatic', 'dropshipexpress' ) . "</option>";

										// Options
										foreach ( $currencies as $code => $name ) {
											echo "<option " . selected( $current_currency, $code ) . " value='{$code}'>{$name}</option>";
										}
									?>
								</select>
							</div>
							<span class="form-text text-muted"><?php esc_html_e( 'Setting this to automatic will use the default currency of your store.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End AliExpress Currency -->

				</div>
			</div>

		</div>
		<!-- End AliExpress Pricing -->

		<!-- Begin AliExpress Stock Management -->
		<div class="tab-pane fade dse-api-settings-tab-content" id="dse_aliexpress_settings_stock" role="tabpanel">

			<div class="dse-content-section dse-content-section-first">
				<div class="dse-content-section-body">

					<div class="row">
						<label class="col-xl-3 col-lg-3 col-form-label"></label>
						<div class="col-lg-9 col-xl-6">
							<div class="alert alert-solid-warning alert-bold" role="alert">
								<div class="alert-text"><?php esc_html_e( 'In order for this feature to work, you need to enable stock management under "WooCommerce > Settings > Products > Inventory"', 'dropshipexpress' ); ?></div>
							</div>
						</div>
					</div>

					<!-- Begin AliExpress Enable Stock Management -->
					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Import Stock', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_aliexpress_enable_stock_manager" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'enable_stock_manager' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'Enabling this will also import the stock count from AliExpress.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End AliExpress Enable Stock Management -->

					<!-- Begin AliExpress Stock Update -->
					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Out of Stock', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-md-12 col-sm-12">
							<div class="col-lg-4 col-md-6 col-sm-12 dse-p-0">
								<select class="form-control dse-select2" name="dse_aliexpress_stock_update">
									<option <?php selected( DSE_Settings::Get_Setting( 'aliexpress', 'stock_update' ), 'outofstuck' ) ?> value="outofstuck"><?php esc_html_e( 'Set to "Out of Stock"', 'dropshipexpress' ); ?></option>
									<option <?php selected( DSE_Settings::Get_Setting( 'aliexpress', 'stock_update' ), 'draft' ) ?> value="draft"><?php esc_html_e( 'Set to "Draft"', 'dropshipexpress' ); ?></option>
									<option <?php selected( DSE_Settings::Get_Setting( 'aliexpress', 'stock_update' ), 'trash' ) ?> value="trash"><?php esc_html_e( 'Move to trash', 'dropshipexpress' ); ?></option>
									<option <?php selected( DSE_Settings::Get_Setting( 'aliexpress', 'stock_update' ), 'nothing' ) ?> value="nothing"><?php esc_html_e( 'Do nothing', 'dropshipexpress' ); ?></option>
								</select>
							</div>
							<span class="form-text text-muted"><?php esc_html_e( 'What to do when an imported product goes out of stock on AliExpress?', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End AliExpress Stock Update -->

				</div>
			</div>

		</div>
		<!-- End AliExpress Stock Management -->

		<!-- Begin AliExpress Review Settings -->
		<div class="tab-pane fade dse-api-settings-tab-content" id="dse_aliexpress_settings_reviews" role="tabpanel">

			<div class="dse-content-section dse-content-section-first">
				<div class="dse-content-section-body">

					<!-- Begin Review Import Settings -->
					<div class="row">
						<label class="col-xl-3"></label>
						<div class="col-lg-9 col-xl-6">
							<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'Review Import', 'dropshipexpress' ); ?></h3>
						</div>
					</div>

					<div class="form-group form-group-sm row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Enable Review Import?', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_aliexpress_import_reviews" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'import_reviews' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'Enabling this option will import the product reviews as comments.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End Review Import Settings -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<!-- Begin Review Limits Settings -->
					<div class="dse-content-section dse-content-section-first">
						<div class="dse-content-section-body">
							<div class="row">
								<label class="col-xl-3"></label>
								<div class="col-lg-9 col-xl-6">
									<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'Review Limits', 'dropshipexpress' ); ?></h3>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Number of Reviews', 'dropshipexpress' ); ?></label>
								<div class="col-lg-8">
									<div class="col-lg-3 dse-p-0">
										<input id="dse-aliexpress-review-count" type="text" class="form-control text-center dse-selector-review-count" value="<?php echo esc_html( DSE_Settings::Get_Setting( 'aliexpress', 'review_import_count' ) ) ?>" name="dse_aliexpress_review_import_count" placeholder="<?php esc_html_e( 'Enter a number', 'dropshipexpress' ); ?>">
									</div>
									<span class="form-text text-muted"><?php esc_html_e( 'How many of the product\'s reviews should be imported?', 'dropshipexpress' ); ?></span>
								</div>
							</div>

						</div>
					</div>
					<!-- End Review Limits Settings -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<!-- Begin Review Import Options -->
					<div class="dse-content-section dse-content-section-first">
						<div class="dse-content-section-body">

							<div class="form-group row">
								<label class="col-lg-3 col-form-label"><?php esc_html_e( 'Import Settings', 'dropshipexpress' ); ?></label>
								<div class="col-lg-6">
									<div class="dse-inline-checkbox">
										<label class="dse-checkbox">
											<input type="checkbox" name="dse_aliexpress_import_review_images" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'import_review_images' ), 'yes' ) ?>><?php esc_html_e( 'Import Images', 'dropshipexpress' ); ?>
											<span></span>
										</label>
										<span class="form-text text-muted"><?php esc_html_e( 'Import and include the images from the original review.', 'dropshipexpress' ); ?></span>
									</div>

									<div class="dse-inline-checkbox">
										<label class="dse-checkbox">
											<input type="checkbox" name="dse_aliexpress_import_review_translate" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'import_review_translate' ), 'Y' ) ?>><?php esc_html_e( 'Translate Reviews', 'dropshipexpress' ); ?>
											<span></span>
										</label>
										<span class="form-text text-muted"><?php esc_html_e( 'Try to translate the reviews to English.', 'dropshipexpress' ); ?></span>
									</div>
								</div>
							</div>

						</div>
					</div>
					<!-- End Review Import Options -->

				</div>
			</div>

		</div>
		<!-- End AliExpress Review Settings -->

		<!-- Begin AliExpress Search & Replace -->
		<div class="tab-pane fade dse-api-settings-tab-content" id="dse_aliexpress_settings_replace" role="tabpanel">

			<div class="dse-content-section dse-content-section-first">
				<div class="dse-content-section-body">

					<div class="row">
						<label class="col-xl-3 col-lg-3 col-form-label"></label>
						<div class="col-lg-9 col-xl-6">
							<div class="alert alert-solid-success alert-bold" role="alert">
								<div class="alert-text"><?php esc_html_e( 'This feature allows you to run a "Search & Replace" on the title and the content of products. You can use this to replace certain words and phrases, such as brand names.', 'dropshipexpress' ); ?></div>
							</div>
						</div>
					</div>

					<!-- Begin AliExpress Search & Replace Enable -->
					<div class="form-group form-group-sm row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Enable', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_aliexpress_enable_replacements" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'enable_replacements' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'Enabling this will run a "Search & Replace" on the imported products based on defined rules below.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End AliExpress Search & Replace Enable -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<!-- Begin AliExpress Search & Replace Rules -->
					<div id="dse-aliexpress-replace-rules" class="dse-replace-rules-wrapper">

						<div class="row">
							<label class="col-xl-3"></label>
							<div class="col-lg-9 col-xl-6">
								<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'Search & Replace rules', 'dropshipexpress' ); ?></h3>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-lg-3 col-form-label"><?php esc_html_e( 'Rules', 'dropshipexpress' ); ?></label>

							<div data-repeater-list="dse_aliexpress_replace_rule" class="col-lg-9">
								<div data-repeater-item class="form-group-last dse-p-b-15 row align-items-center dse-aliexpress-replace-rule">

									<div class="col-md-4">
										<div class="dse-form-group-inline">
											<div class="dse-form-control">
												<div class="input-group">
													<div class="input-group-prepend">
														<span class="input-group-text"><i class="fa fa-search"></i></span>
													</div>
													<input type="text" name="search" class="form-control" placeholder="<?php esc_html_e( 'Search for', 'dropshipexpress' ); ?>">
												</div>
											</div>
										</div>
										<div class="d-md-none dse-m-b-10"></div>
									</div>

									<div class="col-md-4">
										<div class="dse-form-group-inline">

											<div class="dse-form-control">
												<div class="input-group">
													<div class="input-group-prepend">
														<span class="input-group-text"><i class="fa fa-pen"></i></span>
													</div>
													<input type="text" name="value" class="form-control" placeholder="<?php esc_html_e( 'Replace with', 'dropshipexpress' ); ?>">
												</div>
											</div>
										</div>
										<div class="d-md-none dse-m-b-10"></div>
									</div>

									<div class="col-md-4">
										<a href="javascript:void(0);" data-repeater-delete class="btn-sm btn btn-label-danger btn-bold">
											<i class="fa fa-trash"></i><?php esc_html_e( 'Remove', 'dropshipexpress' ); ?>
										</a>
									</div>

									<div class="col-md-12 row">
										<label class="col-2 col-form-label"><?php esc_html_e( 'Apply to :', 'dropshipexpress' ); ?></label>
										<div class="col-10">
											<div class="dse-inline-checkbox">
												<label class="dse-checkbox">
													<input type="checkbox" name="apply_title" class="dse-apply-title"><?php esc_html_e( 'Title', 'dropshipexpress' ); ?>
													<span></span>
												</label>
												<label class="dse-checkbox">
													<input type="checkbox" name="apply_desc" class="dse-apply-desc"><?php esc_html_e( 'Description', 'dropshipexpress' ); ?>
													<span></span>
												</label>
												<label class="dse-checkbox">
													<input type="checkbox" name="apply_attr" class="dse-apply-attr"><?php esc_html_e( 'Attributes', 'dropshipexpress' ); ?>
													<span></span>
												</label>
												<label class="dse-checkbox">
													<input type="checkbox" name="apply_tags" class="dse-apply-tags"><?php esc_html_e( 'Tags', 'dropshipexpress' ); ?>
													<span></span>
												</label>
												<label class="dse-checkbox">
													<input type="checkbox" name="apply_reviews" class="dse-apply-reviews"><?php esc_html_e( 'Reviews', 'dropshipexpress' ); ?>
													<span></span>
												</label>
											</div>
										</div>
									</div>

								</div>

							</div>
							<div class="col-md-12 form-group row">
								<label class="col-lg-3 col-form-label"></label>
								<div class="col-lg-9">
									<span class="form-text text-muted"><?php esc_html_e( 'You can define multiple rules to be applied on imported products.', 'dropshipexpress' ); ?></span>
								</div>
							</div>
						</div>
						<div class="form-group form-group-last row">
							<label class="col-lg-3 col-form-label"></label>
							<div class="col-lg-4">
								<a href="javascript:void(0);" data-repeater-create class="btn btn-bold btn-sm btn-label-brand">
									<i class="fa fa-plus"></i><?php esc_html_e( 'New Rule', 'dropshipexpress' ); ?>
								</a>
							</div>
						</div>
					</div>
					<!-- End AliExpress Search & Replace Rules -->

				</div>
			</div>

		</div>
		<!-- End AliExpress Search & Replace -->

		<!-- Begin AliExpress Extra Settings -->
		<div class="tab-pane fade dse-api-settings-tab-content" id="dse_aliexpress_settings_extra" role="tabpanel">

			<div class="dse-content-section dse-content-section-first">
				<div class="dse-content-section-body">

					<!-- Begin AliExpress language -->
					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Language', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-md-12 col-sm-12">
							<div class="col-lg-4 col-md-6 col-sm-12 dse-p-0">
								<select class="form-control dse-select2" data-size="7" name="dse_aliexpress_language">
									<?php
										// Get the current and available languages
										$current_language = DSE_Settings::Get_Setting( 'aliexpress', 'language' );
										$languages        = DSE_Settings::Get_Languages( 'aliexpress' );

										// Default language
										echo "<option " . selected( $current_language, 'auto' ) . " value='auto'>" . esc_html__( 'Automatic', 'dropshipexpress' ) . "</option>";

										// Options
										foreach ( $languages as $code => $name ) {
											echo "<option " . selected( $current_language, $code ) . " value='{$code}'>{$name}</option>";
										}
									?>


								</select>
							</div>
							<span class="form-text text-muted"><?php esc_html_e( 'The language used to search and import products. If set to automatic, the store\'s default language will be used.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End AliExpress language -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<!-- Begin AliExpress Import Contents -->
					<div class="form-group row">
						<label class="col-3 col-form-label"><?php esc_html_e( 'Import Content', 'dropshipexpress' ); ?></label>
						<div class="col-9">
							<div class="dse-inline-checkbox">
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_import_content_desc" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'import_content_desc' ), 'yes' ) ?>> <?php esc_html_e( 'Description', 'dropshipexpress' ); ?>
									<span></span>
								</label>
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_import_content_attr" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'import_content_attr' ), 'yes' ) ?>> <?php esc_html_e( 'Attributes', 'dropshipexpress' ); ?>
									<span></span>
								</label>
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_import_content_cat" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'import_content_cat' ), 'yes' ) ?>> <?php esc_html_e( 'Categories', 'dropshipexpress' ); ?>
									<span></span>
								</label>
								<label class="dse-checkbox">
									<input type="checkbox" name="dse_aliexpress_import_content_tags" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'import_content_tags' ), 'yes' ) ?>> <?php esc_html_e( 'Tags', 'dropshipexpress' ); ?>
									<span></span>
								</label>
							</div>
							<span class="form-text text-muted"><?php esc_html_e( 'Choose the contents that should be imported.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End AliExpress Import Contents -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<!-- Begin AliExpress Image Downloads -->
					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Check Duplicate Images', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-md-12 col-sm-12">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_aliexpress_check_duplicate_images" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'check_duplicate_images' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'Enabling this option will check if the images already exist on the website. Recommended to save space.', 'dropshipexpress' ); ?></span>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Product Images', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-md-12 col-sm-12">
							<div class="col-lg-4 col-md-6 col-sm-12 dse-p-0">
								<select class="form-control dse-select2" name="dse_aliexpress_import_product_images">
									<option <?php selected( DSE_Settings::Get_Setting( 'aliexpress', 'import_product_images' ), 'download' ) ?> value="download"><?php esc_html_e( 'Download and Import', 'dropshipexpress' ); ?></option>
									<option <?php selected( DSE_Settings::Get_Setting( 'aliexpress', 'import_product_images' ), 'external' ) ?> value="external"><?php esc_html_e( 'Use external URL', 'dropshipexpress' ); ?></option>
									<option <?php selected( DSE_Settings::Get_Setting( 'aliexpress', 'import_product_images' ), 'none' ) ?> value="none"><?php esc_html_e( 'Do not import', 'dropshipexpress' ); ?></option>
								</select>
							</div>
							<span class="form-text text-muted"><?php esc_html_e( 'Choose how to manage importing the product\'s images.', 'dropshipexpress' ); ?></span>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Description Images', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-md-12 col-sm-12">
							<div class="col-lg-4 col-md-6 col-sm-12 dse-p-0">
								<select class="form-control dse-select2" name="dse_aliexpress_import_desc_images">
									<option <?php selected( DSE_Settings::Get_Setting( 'aliexpress', 'import_desc_images' ), 'download' ) ?> value="download"><?php esc_html_e( 'Download and Import', 'dropshipexpress' ); ?></option>
									<option <?php selected( DSE_Settings::Get_Setting( 'aliexpress', 'import_desc_images' ), 'external' ) ?> value="external"><?php esc_html_e( 'Import as is', 'dropshipexpress' ); ?></option>
									<option <?php selected( DSE_Settings::Get_Setting( 'aliexpress', 'import_desc_images' ), 'drop' ) ?> value="drop"><?php esc_html_e( 'Drop', 'dropshipexpress' ); ?></option>
								</select>
							</div>
							<span class="form-text text-muted"><?php esc_html_e( 'What to do with the images that are in the product description?', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- Begin AliExpress Image Downloads -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<!-- Begin AliExpress Default Product Type -->
					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Default Product Type', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-md-12 col-sm-12">
							<div class="col-lg-4 col-md-6 col-sm-12 dse-p-0">
								<select class="form-control dse-select2" name="dse_aliexpress_default_product_type">
									<option <?php selected( DSE_Settings::Get_Setting( 'aliexpress', 'default_product_type' ), 'simple' ) ?> value="simple"><?php esc_html_e( 'Simple / Variable', 'dropshipexpress' ); ?></option>
									<option <?php selected( DSE_Settings::Get_Setting( 'aliexpress', 'default_product_type' ), 'external' ) ?> value="external"><?php esc_html_e( 'External / Affiliate', 'dropshipexpress' ); ?></option>
								</select>
							</div>
							<span class="form-text text-muted"><?php esc_html_e( 'Choose the default product type to be used for importing products.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End AliExpress Default Product Type -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<!-- Begin AliExpress Default Category -->
					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Dynamic Categories', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-md-12 col-sm-12">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_aliexpress_dynamic_cat" <?php checked( DSE_Settings::Get_Setting( 'aliexpress', 'dynamic_cat' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'Selecting this will attempt to dynamically create a new category based on the product\'s information.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Default Category', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-md-12 col-sm-12">
							<div class="col-lg-4 col-md-6 col-sm-12 dse-p-0">
								<select class="form-control dse-select2" name="dse_aliexpress_default_cat">
									<?php echo DSE_Import::Get_Categories( DSE_Settings::Get_Setting( 'aliexpress', 'default_cat' ) ); ?>
								</select>
							</div>
							<span class="form-text text-muted"><?php esc_html_e( 'Select a default category to publish the products in. This option only works if the Dynamic Category option is disabled, or if the plugin can\'t find a proper category to create.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End AliExpress Default Category -->

				</div>
			</div>

		</div>
		<!-- End AliExpress Extra Settings -->

	</div>
	<!-- End AliExpress Tab Content -->
</div>
