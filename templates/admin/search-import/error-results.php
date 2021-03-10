<?php
	/**
	 * Template to output a error page
	 * for store search queries
	 *
	 */

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}
?>

<!-- Begin Error Section -->
<div class="dse-box dse-portlet--tab">
	<div class="dse-box-header">
		<div class="dse-box-header-wrapper">
			<h3 class="dse-box-header-title">
				<i class="fa fa-exclamation-circle"></i>
				<?php esc_html_e( 'Search Error', 'dropshipexpress' ); ?>
			</h3>
		</div>
	</div>

	<div class="dse-box-body">

		<div class="dse-content-section dse-content-section-last">
			<div class="dse-section__content">
				<?php
					echo wp_kses(
						$wp_error->get_error_message(),
						[
							'a' => [
								'href'   => [],
								'target' => [],
							],
						]
					);
				?>
			</div>
		</div>

	</div>
</div>
<!-- End Error Section -->