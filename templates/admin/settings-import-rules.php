<?php
	/**
	 * Template to output the import rules section
	 *
	 */
?>

<!-- Begin Import Rules -->
<div class="dse-content dse-grid-item dse-grid-item--fluid dse-grid dse-grid-horizontal">
	<div class="dse-container dse-container-fluid dse-grid-item dse-grid-item-fluid">

		<!-- Begin New Rule Form -->
		<div class="dse-box">
			<div class="dse-box-header">
				<div class="dse-box-header-wrapper">
					<h3 class="dse-box-header-title">
						<?php esc_html_e( 'Add New Import Rule', 'dropshipexpress' ); ?>
					</h3>
				</div>
			</div>

			<div class="dse-box-body dse-settings-apis">
				<div class="row">

					<div class="col-xl-3">
						<ul class="dse-navigation nav nav-tabs dse-settings-tabs dse-import-rules-tabs" role="tablist">
							<?php
								// Get a list of available apis
								$api_headers = DSE_Import::Get_Sections();

								if ( $api_headers ) {
									foreach ( $api_headers as $api_header => $api ) {
										?>
										<li class="dse-navigation-item dse-navigation-item-active inactive">
											<a class="dse-navigation-link nav-link<?php echo 'aliexpress' === $api_header ? ' active' : ''; ?>" role="tab" data-toggle="tab" id="dse_import_rule_tab_<?php echo esc_attr( $api_header ) ?>" href="#dse_import_rule_content_<?php echo esc_attr( $api_header ) ?>" aria-expanded="false">
												<span class="dse-navigation-link-text"><?php echo esc_html( $api[ 'title' ] ) ?></span>
												<img src="<?php echo esc_url_raw( $api[ 'logo' ] ) ?>" alt="<?php echo esc_html( $api[ 'title' ] ) ?>">
												<?php echo ! in_array( $api_header, [ 'aliexpress' ] ) ? '<span class="dse-badge dse-badge-error dse-inline-badge dse-badge-pill">' . esc_html__( 'Coming Soon', 'dropshipexpress' ) . '</span>' : ''; // Temporary badge while other apis are implemented ?>
											</a>
										</li>
										<?php
									}
								}
							?>
						</ul>
					</div>

					<div class="col-xl-9 tab-content">
						<?php
							// Include the setting file for each api
							if ( $api_headers ) {
								foreach ( $api_headers as $api_header => $name ) {
									include_once( DSE_PLUGIN_FOLDER . '/templates/admin/import-rules/' . $api_header . '.php' );
								}
							}
						?>
					</div>
				</div>
			</div>
		</div>
		<!-- End New Rule Form -->

		<!-- Begin Rules List -->
		<div class="dse-box">
			<div class="dse-box-header">
				<div class="dse-box-header-wrapper">
					<h3 class="dse-box-header-title">
						<?php esc_html_e( 'Existing Rules', 'dropshipexpress' ); ?>
					</h3>
				</div>
			</div>
			<div class="dse-box-body">

				<div class="dse-box-3">
					<div class="table-responsive">
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>">
							<table class="table">
								<thead>
								<tr>
									<td style="width:15%"><?php esc_html_e( 'Date Added', 'dropshipexpress' ); ?></td>
									<td style="width:20%"><?php esc_html_e( 'Keyword', 'dropshipexpress' ); ?></td>
									<td style="width:15%"><?php esc_html_e( 'Category', 'dropshipexpress' ); ?></td>
									<td style="width:20%"><?php esc_html_e( 'Amount', 'dropshipexpress' ); ?></td>
									<td style="width:15%"><?php esc_html_e( 'Store', 'dropshipexpress' ); ?></td>
									<td style="width:25%" class="dse-align-right"><?php esc_html_e( 'Actions', 'dropshipexpress' ); ?></td>
								</tr>
								</thead>
								<tbody>
								<!-- Begin Single Rule -->

								<?php
									// Get a list of existing rules
									$rules = get_option( 'dse_import_rules', '' );

									if ( ! empty( $rules ) ) {
										foreach ( $rules as $key => $rule ) { ?>
											<tr>
												<td><?php echo esc_html( $rule[ 'date' ] ) ?></td>
												<td><?php echo esc_html( $rule[ 'keyword_text' ] ) ?></td>
												<td><?php echo esc_html( $rule[ 'category' ] ) ?></td>
												<td>
													<?php
														printf(
															/* translators: %1$s is replaced with the schedule's interval ( number ) */
															/* translators: %2$s is replaced with the schedule's interval ( name ) */
															esc_html__( 'Every %1$s %2$s(s)', 'dropshipexpress' ),
															$rule[ 'timer' ],
															$rule[ 'delay' ]
														)
													?>
												</td>

												<td><?php echo esc_html( ucwords( $rule[ 'api' ] ) ) ?></td>

												<td class="dse-align-right">
													<button name="dse_remove_rule_value" value="<?php echo esc_html( $rule[ 'id' ] ) ?>" type="submit" class="btn btn-sm dse-badge dse-inline-badge dse-badge-error">
														<i class="fa fa-trash dse-p-r5"></i>
														<?php esc_html_e( 'Remove', 'dropshipexpress' ); ?>
													</button>
												</td>

											</tr>
											<?php
										}
									} else { ?>
										<tr>
											<td>
												<span><?php esc_html_e( 'No rule has been added yet.', 'dropshipexpress' ); ?></span>
											</td>
										</tr>
										<?php
									}
								?>
								<!-- End Single Rule -->

								</tbody>
							</table>
							<?php wp_nonce_field( 'dse-single-import-rules-nonce-action', 'dse_single_import_rules_nonce' ) ?>
							<input type="hidden" value="dse_single_import_rules" name="action">
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- End Rules List -->
	</div>
</div>
<!-- End Import Rules -->
