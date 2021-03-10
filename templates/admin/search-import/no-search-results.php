<?php
	/**
	 * Template to output a no-result page
	 * for store search queries
	 *
	 */

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}
?>
<!-- Begin No Results Section -->
<div class="dse-box dse-portlet--tab">
	<div class="dse-box-header">
		<div class="dse-box-header-wrapper">
			<h3 class="dse-box-header-title">
				<?php esc_html_e( 'Search Results', 'dropshipexpress' ); ?>
			</h3>
		</div>
	</div>

	<div class="dse-box-body">

		<div class="dse-content-section">
			<span class="dse-content-title"><?php esc_html_e( 'No Results', 'dropshipexpress' ); ?></span>
			<div class="dse-section__content">
				<?php esc_html_e( 'Nothing matched your search criteria. Please try searching again using different parameters.', 'dropshipexpress' ); ?>
			</div>
		</div>

	</div>
</div>
<!-- End No Results Section -->