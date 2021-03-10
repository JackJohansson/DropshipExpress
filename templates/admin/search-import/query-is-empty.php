<?php
	/**
	 * Template to alert the users about their empty query
	 *
	 */

	// Block direct access to the file
	if ( ! defined( 'ABSPATH' ) ) {
		exit();
	}
?>
<!-- Begin Empty Query Section -->
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
			<span class="dse-content-title"><?php esc_html_e( 'Nothing to Search!', 'dropshipexpress' ); ?></span>
			<div class="dse-section__content">
				<?php esc_html_e( 'Please enter either a search keyword or a product ID/URL to search.', 'dropshipexpress' ); ?>
			</div>
		</div>

	</div>
</div>
<!-- End Empty Query Section -->