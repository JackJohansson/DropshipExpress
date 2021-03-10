<?php
	/**
	 * Template file to render the general options
	 * page
	 *
	 */

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

?>

<!-- Begin API Options -->
<div class="dse-content dse-grid-item dse-grid-item--fluid dse-grid dse-grid-horizontal">
	<div class="dse-container dse-container-fluid dse-grid-item dse-grid-item-fluid">
		<!-- Begin Settings Form -->
		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>" method="post">

			<div class="dse-box">

				<div class="dse-box-header">
					<div class="dse-box-header-wrapper">
						<h3 class="dse-box-header-title">
							<?php esc_html_e( 'Settings', 'dropshipexpress' ); ?>
						</h3>
					</div>
				</div>

				<div class="dse-box-body dse-settings-apis">
					<div class="row">

						<div class="col-xl-3">
							<ul class="dse-navigation nav nav-tabs dse-settings-tabs" id="kt_nav" role="tablist">
								<?php
									// Get a list of available apis
									$api_headers = DSE_Import::Get_Sections();

									if ( $api_headers ) {
										foreach ( $api_headers as $api_header => $api ) {
											?>
											<li class="dse-navigation-item nav-item">
												<a class="dse-navigation-link nav-link<?php echo 'aliexpress' === $api_header ? ' active' : ''; ?>" role="tab" data-toggle="tab" id="dse_api_tab_<?php echo esc_attr( $api_header ) ?>" href="#dse_api_content_<?php echo esc_attr( $api_header ) ?>" aria-expanded="false">
													<span class="dse-navigation-link-text"><?php echo esc_html( $api[ 'title' ] ) ?></span>
													<img src="<?php echo esc_url_raw( $api[ 'logo' ] ) ?>" alt="<?php echo esc_html( $api[ 'title' ] ) ?>">
													<?php echo ! in_array( $api_header, [ 'aliexpress' ] ) ? '<span class="dse-badge dse-badge-error dse-inline-badge dse-badge-pill">' . esc_html__( 'Coming Soon', 'dropshipexpress' ) . '</span>' : ''; // todo:Temporary badge while other apis are implemented ?>
												</a>
											</li>
											<?php
										}
									}
								?>
								<!-- Begin Misc Section -->
								<li class="dse-navigation-item nav-item dse-general-options">
									<a class="dse-navigation-link" role="tab" id="dse_api_tab_misc" href="#dse_api_content_misc" aria-expanded="false">
										<span class="dse-navigation-link-text"><?php esc_html_e( 'General Settings', 'dropshipexpress' ); ?></span>
									</a>
								</li>
								<!-- End Misc Section -->
							</ul>
						</div>

						<div class="col-xl-9 tab-content">
							<?php
								// Include the setting file for each api
								if ( $api_headers ) {
									foreach ( $api_headers as $api_header => $name ) {
										include_once( DSE_PLUGIN_FOLDER . '/templates/admin/tabs-content/' . $api_header . '.php' );
									}
								}
							?>
							<!-- Begin Misc Section -->
							<?php include_once( DSE_PLUGIN_FOLDER . '/templates/admin/tabs-content/misc.php' ); ?>
							<!-- End Misc Section -->
						</div>
					</div>
				</div>

				<div class="dse-box-footer">
					<div class="dse-form-actions dse-form-actions-right">
						<button type="submit" class="btn btn-brand"><?php esc_html_e( 'Save', 'dropshipexpress' ); ?></button>
					</div>
				</div>

				<?php wp_nonce_field( 'dse-save-settings-nonce-action', 'dse_settings_nonce' ) ?>
				<input type="hidden" value="dse_save_settings" name="action">

			</div>

		</form>
		<!-- End Settings Form -->
	</div>
</div>
<!-- End API Options -->