<?php
	/**
	 * Miscellaneous options
	 *
	 */ ?>

<?php

	/**
	 * Get a list of user roles
	 *
	 */
	global $wp_roles;

	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles();
	}

?>
<div id="dse_api_content_misc" class="dse-api-settings-tabs tab-pane fade" role="tabpanel">

	<ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-2x nav-tabs-line-primary" role="tablist">
		<li class="nav-item">
			<a class="nav-link active" data-toggle="tab" href="#dse_settings_permissons" role="tab">
				<i class="fa fa-lock"></i>
				<?php esc_html_e( 'Permissions', 'dropshipexpress' ); ?>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#dse_settings_notifications" role="tab">
				<i class="fa fa-envelope"></i>
				<?php esc_html_e( 'Notifications', 'dropshipexpress' ); ?>
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#dse_settings_proxy" role="tab">
				<i class="fa fa-envelope"></i>
				<?php esc_html_e( 'Proxy', 'dropshipexpress' ); ?>
			</a>
		</li>
	</ul>

	<div class="tab-content">
		<!-- Begin Tab Permissions -->
		<div class="tab-pane active dse-api-settings-tab-content" id="dse_settings_permissons" role="tabpanel">

			<div class="dse-content-section dse-content-section-first">
				<div class="dse-content-section-body">
					<div class="row">
						<label class="col-xl-3"></label>
						<div class="col-lg-9 col-xl-6">
							<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'Security', 'dropshipexpress' ); ?></h3>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-form-label col-lg-3 col-sm-12"><?php esc_html_e( 'Import Access', 'dropshipexpress' ); ?></label>
						<div class="col-lg-4 col-md-9 col-sm-12">
							<select class="form-control dse-select2" name="dse_permission_import_access">
								<?php
									foreach ( $wp_roles->roles as $role => $value ) {
										echo "<option " . selected( DSE_Settings::Get_Setting( 'general', 'permission_import_access' ), $role, FALSE ) . " value='{$role}'>{$value['name']}</option>";
									}
								?>
							</select>
							<span class="form-text text-muted"><?php esc_html_e( 'Who can view and import products?', 'dropshipexpress' ); ?></span>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-form-label col-lg-3 col-sm-12"><?php esc_html_e( 'Publish Access', 'dropshipexpress' ); ?></label>
						<div class="col-lg-4 col-md-9 col-sm-12">
							<select class="form-control dse-select2" name="dse_permission_publish_access">
								<?php
									foreach ( $wp_roles->roles as $role => $value ) {
										echo "<option " . selected( DSE_Settings::Get_Setting( 'general', 'permission_publish_access' ), $role, FALSE ) . " value='{$role}'>{$value['name']}</option>";
									}
								?>
							</select>
							<span class="form-text text-muted"><?php esc_html_e( 'Who can publish or delete the imported products?', 'dropshipexpress' ); ?></span>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-form-label col-lg-3 col-sm-12"><?php esc_html_e( 'Automation Access', 'dropshipexpress' ); ?></label>
						<div class="col-lg-4 col-md-9 col-sm-12">
							<select class="form-control dse-select2" name="dse_permission_automation_access">
								<?php
									foreach ( $wp_roles->roles as $role => $value ) {
										echo "<option " . selected( DSE_Settings::Get_Setting( 'general', 'permission_automation_access' ), $role, FALSE ) . " value='{$role}'>{$value['name']}</option>";
									}
								?>
							</select>
							<span class="form-text text-muted"><?php esc_html_e( 'Who can define rules to automatically import and publish products?', 'dropshipexpress' ); ?></span>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-form-label col-lg-3 col-sm-12"><?php esc_html_e( 'Log Access', 'dropshipexpress' ); ?></label>
						<div class="col-lg-4 col-md-9 col-sm-12">
							<select class="form-control dse-select2" name="dse_permission_log_access">
								<?php
									foreach ( $wp_roles->roles as $role => $value ) {
										echo "<option " . selected( DSE_Settings::Get_Setting( 'general', 'permission_log_access' ), $role, FALSE ) . " value='{$role}'>{$value['name']}</option>";
									}
								?>
							</select>
							<span class="form-text text-muted"><?php esc_html_e( 'Who can view the plugin\'s logs? Log file might contain sensitive information.', 'dropshipexpress' ); ?></span>
						</div>
					</div>

				</div>
			</div>

		</div>
		<!-- End Tab Permissions -->

		<!-- Begin Tab Notifications -->
		<div class="tab-pane fade dse-api-settings-tab-content" id="dse_settings_notifications" role="tabpanel">

			<div class="dse-content-section dse-content-section-first">
				<div class="dse-content-section-body">
					<div class="row">
						<label class="col-xl-3"></label>
						<div class="col-lg-9 col-xl-6">
							<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'Mail Notifications', 'dropshipexpress' ); ?></h3>
						</div>
					</div>

					<!-- Begin Notifications -->
					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Enable Notifications?', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_notification_enable" <?php checked( DSE_Settings::Get_Setting( 'general', 'notification_enable' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'Disabling this will disable all the notification regardless of their individual settings.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End Notifications -->

					<!-- Begin Email Address -->
					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Email Address', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<input class="form-control" type="email" value="<?php echo esc_attr( DSE_Settings::Get_Setting( 'general', 'notification_mail_address' ) ) ?>" name="dse_notification_mail_address">
							<span class="form-text text-muted"><?php esc_html_e( 'Enter an email address to receive the notifications. If left blank, the default admin\'s email address will be used.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End Email Address -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<div class="row">
						<label class="col-xl-3"></label>
						<div class="col-lg-9 col-xl-6">
							<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'When to send E-Mail', 'dropshipexpress' ); ?></h3>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'On Import', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_notification_import" <?php checked( DSE_Settings::Get_Setting( 'general', 'notification_import' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'Notify me whenever a new product is imported to your website.', 'dropshipexpress' ); ?></span>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'On Publish', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_notification_publish" <?php checked( DSE_Settings::Get_Setting( 'general', 'notification_publish' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'Notify me whenever a new product is published on your website.', 'dropshipexpress' ); ?></span>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'On Update', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_notification_update" <?php checked( DSE_Settings::Get_Setting( 'general', 'notification_update' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'Notify me whenever a product is automatically updated.', 'dropshipexpress' ); ?></span>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'On Order', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_notification_order" <?php checked( DSE_Settings::Get_Setting( 'general', 'notification_order' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'Notify me whenever a product is automatically purchased via the order API.', 'dropshipexpress' ); ?></span>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'On Order Update', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_notification_order_update" <?php checked( DSE_Settings::Get_Setting( 'general', 'notification_order_update' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'Notify me whenever the status of a purchased product is updated via the order API.', 'dropshipexpress' ); ?></span>
						</div>
					</div>

				</div>
			</div>

		</div>
		<!-- End Tab Notifications -->

		<!-- Begin Proxy -->
		<div class="tab-pane fade dse-api-settings-tab-content" id="dse_settings_proxy" role="tabpanel">
			<div class="dse-content-section dse-content-section-first">
				<div class="dse-content-section-body">
					<div class="row">
						<label class="col-xl-3"></label>
						<div class="col-lg-9 col-xl-6">
							<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'Proxy Settings', 'dropshipexpress' ); ?></h3>
						</div>
					</div>

					<!-- Begin Proxy Option -->
					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Enable Proxy?', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<span class="dse-switch">
								<label>
									<input type="checkbox" name="dse_proxy" <?php checked( DSE_Settings::Get_Setting( 'general', 'proxy' ), 'yes' ) ?>>
									<span></span>
								</label>
							</span>
							<span class="form-text text-muted"><?php esc_html_e( 'You can enable this option to use a proxy for connecting to the retailer stores. This might be helpful if you are using a shared ip.', 'dropshipexpress' ); ?></span>
						</div>
					</div>
					<!-- End Proxy Option -->

					<div class="dse-line-separator dse-separator-dashed dse-separator-lg"></div>

					<div class="row">
						<label class="col-xl-3"></label>
						<div class="col-lg-9 col-xl-6">
							<h3 class="dse-content-title dse-content-section-small"><?php esc_html_e( 'Proxy Credentials', 'dropshipexpress' ); ?></h3>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Domain', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<input class="form-control" type="text" value="<?php echo esc_attr( DSE_Settings::Get_Setting( 'general', 'proxy_domain' ) ) ?>" name="dse_proxy_domain">
						</div>
					</div>

					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Username', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<input class="form-control" type="text" value="<?php echo esc_attr( DSE_Settings::Get_Setting( 'general', 'proxy_login' ) ) ?>" name="dse_proxy_login">
						</div>
					</div>

					<div class="form-group row">
						<label class="col-xl-3 col-lg-3 col-form-label"><?php esc_html_e( 'Password', 'dropshipexpress' ); ?></label>
						<div class="col-lg-9 col-xl-6">
							<input class="form-control" type="password" value="<?php echo esc_attr( DSE_Settings::Get_Setting( 'general', 'proxy_password' ) ) ?>" name="dse_proxy_password">
							<span class="form-text text-muted"><?php esc_html_e( 'Enter your proxy credentials. Use HTTPS proxies only.', 'dropshipexpress' ); ?></span>
						</div>
					</div>

				</div>
			</div>
		</div>
		<!-- End Proxy -->
	</div>
</div>

