<?php
	/**
	 * Render the page used to upgrade the plugin
	 *
	 */

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}
?>

<div class="dse-content dse-grid-item dse-grid-item--fluid dse-grid dse-grid-horizontal">
	<div class="dse-container dse-container-fluid dse-grid-item dse-grid-item-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="dse-box">
					<div class="dse-box-body">
						<div class="dse-pricing">
							<div class="dse-pricing-top">
								<div class="dse-pricing-header-wrapper dse-pricing-header-wrapper-fixed">
									<div class="dse-pricing-header-top">
										<div class="dse-pricing-title-top dse-font-light">
											<h1><?php esc_html_e( 'Upgrade to Premium', 'dropshipexpress' ); ?></h1>
										</div>
									</div>
									<div class="dse-pricing-body-top">

										<div class="dse-pricing-items-top">

											<div class="dse-pricing-item-top">
												<span class="dse-pricing-icon dse-font-info">
													<i class="fa fa-check"></i>
												</span>
												<h2 class="dse-pricing-sub"><?php esc_html_e( 'Basic', 'dropshipexpress' ); ?></h2>
												<div class="dse-pricing-features">
													<span><?php esc_html_e( 'Basic features. Always free.', 'dropshipexpress' ); ?></span><br>
													<span><?php esc_html_e( 'Good place to start.', 'dropshipexpress' ); ?></span>
												</div>
												<span class="dse-pricing-price"><?php esc_html_e( 'Free', 'dropshipexpress' ); ?></span>
												<div class="dse-pricing-button">
													<button type="button" class="btn btn-pill btn-info btn-upper btn-bold">
														<?php esc_html_e( 'Owned', 'dropshipexpress' ); ?>
													</button>
												</div>

												<div class="dse-pricing-top-items-mobile">
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Manual Import', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Product Sync', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Image Import', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Review Import', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Automatic Import', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-times"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Official API Support', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-times"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Priority Support', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-times"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Import Rules', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-times"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Auto Publish', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-times"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Query Retailers', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-times"></i></span>
													</div>
													<div class="dse-pricing-mobile-button">
														<button type="button" class="btn btn-pill btn-info btn-upper btn-bold"><?php esc_html_e( 'Owned', 'dropshipexpress' ); ?></button>
													</div>
												</div>

											</div>

											<div class="dse-pricing-item-top">
												<span class="dse-pricing-icon dse-font-info">
													<i class="fa fa-certificate"></i>
												</span>
												<h2 class="dse-pricing-sub"><?php esc_html_e( 'Premium', 'dropshipexpress' ); ?></h2>
												<div class="dse-pricing-features">
													<span><?php esc_html_e( 'Most automated tasks', 'dropshipexpress' ); ?></span><br>
													<span><?php esc_html_e( 'Supports official API', 'dropshipexpress' ); ?></span>
												</div>
												<span class="dse-pricing-price">19</span>
												<span class="dse-pricing-label">$</span>
												<div class="dse-pricing-button">
													<a class="btn btn-pill btn-info btn-upper btn-bold" href="https://codecanyon.net/item/automated-dropshipping-for-woocommerce/29789921" target="_blank">
														<?php esc_html_e( 'Purchase', 'dropshipexpress' ); ?>
													</a>
												</div>

												<div class="dse-pricing-top-items-mobile">
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Manual Import', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Product Sync', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Image Import', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Review Import', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Automatic Import', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Official API Support', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Priority Support', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Import Rules', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Auto Publish', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Query Retailers', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-times"></i></span>
													</div>
													<div class="dse-pricing-mobile-button">
														<a href="https://codecanyon.net/item/automated-dropshipping-for-woocommerce/29789921" target="_blank" class="btn btn-pill  btn-info btn-upper btn-bold">
															<?php esc_html_e( 'Purchase', 'dropshipexpress' ); ?>
														</a>
													</div>
												</div>

											</div>

											<div class="dse-pricing-item-top">
												<span class="dse-pricing-icon dse-font-info">
													<i class="fa fa-gem"></i>
												</span>
												<h2 class="dse-pricing-sub"><?php esc_html_e( 'Subscription', 'dropshipexpress' ); ?></h2>
												<div class="dse-pricing-features">
													<span><?php esc_html_e( 'Unlock everything, Charged monthly', 'dropshipexpress' ); ?></span><br>
													<span><?php esc_html_e( 'Requires premium extension', 'dropshipexpress' ); ?></span>
												</div>
												<span class="dse-pricing-price">11</span>
												<span class="dse-pricing-label">$/ <?php esc_html_e( 'Month', 'dropshipexpress' ); ?></span>
												<div class="dse-pricing-button">
													<a href="https://cydbytes.com" target="_blank" class="btn btn-pill  btn-info btn-upper btn-bold">
														<?php esc_html_e( 'Subscribe', 'dropshipexpress' ); ?>
													</a>
												</div>

												<div class="dse-pricing-top-items-mobile">
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Manual Import', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Product Sync', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Image Import', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Review Import', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Automatic Import', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Official API Support', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Priority Support', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Import Rules', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Auto Publish', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-top-item-mobile">
														<span><?php esc_html_e( 'Query Retailers', 'dropshipexpress' ); ?></span>
														<span><i class="fa fa-check"></i></span>
													</div>
													<div class="dse-pricing-mobile-button">
														<a href="https://cydbytes.com" target="_blank" class="btn btn-pill btn-info btn-upper btn-bold">
															<?php esc_html_e( 'Subscribe', 'dropshipexpress' ); ?>
														</a>
													</div>
												</div>

											</div>

										</div>

									</div>
								</div>
							</div>

							<div class="dse-pricing-bttom">
								<div class="dse-pricing-bottom-wrapper dse-pricing-bottom-wrapper-fixed">
									<div class="dse-pricing-items-bottom">
										<div class="dse-pricing-item-bottom"><?php esc_html_e( 'Manual Import', 'dropshipexpress' ); ?></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
									</div>
									<div class="dse-pricing-items-bottom">
										<div class="dse-pricing-item-bottom"><?php esc_html_e( 'Product Sync', 'dropshipexpress' ); ?></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
									</div>
									<div class="dse-pricing-items-bottom">
										<div class="dse-pricing-item-bottom"><?php esc_html_e( 'Image Import', 'dropshipexpress' ); ?></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
									</div>
									<div class="dse-pricing-items-bottom">
										<div class="dse-pricing-item-bottom"><?php esc_html_e( 'Review Import', 'dropshipexpress' ); ?></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
									</div>
									<div class="dse-pricing-items-bottom">
										<div class="dse-pricing-item-bottom"><?php esc_html_e( 'Automatic Import', 'dropshipexpress' ); ?></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-times"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
									</div>
									<div class="dse-pricing-items-bottom">
										<div class="dse-pricing-item-bottom"><?php esc_html_e( 'Official API Support', 'dropshipexpress' ); ?></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-times"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
									</div>
									<div class="dse-pricing-items-bottom">
										<div class="dse-pricing-item-bottom"><?php esc_html_e( 'Priority Support', 'dropshipexpress' ); ?></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-times"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
									</div>
									<div class="dse-pricing-items-bottom">
										<div class="dse-pricing-item-bottom"><?php esc_html_e( 'Import Rules', 'dropshipexpress' ); ?></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-times"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
									</div>
									<div class="dse-pricing-items-bottom">
										<div class="dse-pricing-item-bottom"><?php esc_html_e( 'Auto Publish', 'dropshipexpress' ); ?></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-times"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
									</div>
									<div class="dse-pricing-items-bottom">
										<div class="dse-pricing-item-bottom"><?php esc_html_e( 'Query Retailers', 'dropshipexpress' ); ?></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-times"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-times"></i></div>
										<div class="dse-pricing-item-bottom"><i class="fa fa-check"></i></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
