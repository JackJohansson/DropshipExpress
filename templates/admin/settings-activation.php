<?php
	/**
	 * Template to render the general options page
	 *
	 */

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}

	/**
	 * Set the proper icon and message
	 *
	 */
	if ( isset( $activated ) && TRUE === $activated ) {
		$message_class = 'alert alert-solid-success alert-bold';
		$message_icon  = 'fa fa-check-circle';
	} else {
		$message_class = 'alert alert-solid-danger alert-bold';
		$message_icon  = 'fa fa-exclamation-circle';
	}

?>

<div class="dse-content dse-grid-item dse-grid-item--fluid dse-grid dse-grid-horizontal">

	<div class="dse-container  dse-container-fluid dse-grid-item dse-grid-item-fluid">
		<div class="row">
			<div class="col-md-12">
				<!--begin::Portlet-->
				<div class="dse-box">
					<div class="dse-box-header">
						<div class="dse-box-header-wrapper">
							<h3 class="dse-box-header-title">
								<?php esc_html_e( 'Plugin Activation', 'dropshipexpress' ); ?>
							</h3>
						</div>
					</div>
					<!--begin::Form-->
					<form class="dse-form dse-form-label-right" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>" method="POST">

						<div class="dse-box-body">

							<div class="form-group form-group-last">
								<div class="<?php echo esc_attr( $message_class ) ?>" role="alert">
									<div class="alert-icon"><i class="<?php echo esc_attr( $message_icon ) ?>"></i>
									</div>
									<div class="alert-text">
										<?php
											if ( isset( $activated ) && FALSE === $activated ) {
												printf(
													wp_kses(
													/* translators: %1$s is replaced with the envato's help URL */
														__( 'Plugin is not activated yet. In order to use the plugin, you need to activate your copy. Click the button below to activate. If you need a subscription, please take a look at <a href="%1$s" target="_blank">here.</a>', 'dropshipexpress' ),
														[
															'a' => [
																'href'   => [],
																'target' => [],
															],
														]
													),
													'https://cydbytes.com/product/dropshipexpress-monthly-subscription/'
												);
											} else {
												esc_html_e( 'Plugin is activated.' );
											}
										?>
									</div>
								</div>
							</div>



							<?php
								if ( isset( $_GET[ 'dse-activation-response' ] ) && ! empty( $_GET[ 'dse-activation-response' ] ) ) {
									?>
									<div class="form-group form-group-last">
										<div class="alert alert-solid-<?php echo esc_attr( $_GET[ 'dse-activation-status' ] ); ?> alert-bold" role="alert">
											<div class="alert-icon">
												<i class="fa fa-<?php echo 'warning' === $_GET[ 'dse-activation-status' ] ? 'times-circle' : 'check' ?>"></i>
											</div>
											<div class="alert-text">
												<?php echo esc_html( base64_decode( $_GET[ 'dse-activation-response' ] ) ); ?>
											</div>
										</div>
									</div>
									<?php
								}

								if ( isset( $token ) && ! empty( $token ) ) { ?>
									<div class="form-group ">
										<label><?php esc_html_e( 'API Token', 'dropshipexpress' ); ?></label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text"><i class="fa fa-lock"></i></span></div>
											<input type="text" disabled="disabled" value="<?php echo esc_html( $token ) ?>" class="form-control">
										</div>
										<span class="form-text text-muted">
											<?php esc_html_e( 'This is your API token. It will be used to connect to our API and query stores.', 'dropshipexpress' ) ?>
										</span>
									</div>
									<?php
								}
							?>
						</div>

						<div class="dse-box-footer">
							<div class="dse-form-actions">
								<button type="submit" class="btn btn-success"><?php esc_html_e( 'Activate License', 'dropshipexpress' ); ?></button>
							</div>
						</div>
						<?php wp_nonce_field( 'dse-validation-page', 'dse_activation_nonce', ) ?>
						<input type="hidden" name="action" value="dse_update_activation">
					</form>
					<!--end::Form-->
				</div>
				<!--end::Portlet-->
			</div>
		</div>
	</div>
</div>
